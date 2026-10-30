<?php
declare(strict_types=1);

// Integrazioa benetako HTTP eta MySQL bidez, DB isolatu eta esplizituan bakarrik.
if (getenv('APP_TESTING') !== '1' || !str_ends_with((string) getenv('DB_NAME'), '_test')) {
    fwrite(STDERR, "Use .env.test.example and an isolated Compose project; DB_NAME must end in _test.\n");
    exit(2);
}

final class SecurityHttpClient
{
    public array $cookies = [];

    public function __construct(private readonly ?string $baseUrl = null)
    {
    }

    public function request(string $path, ?array $data = null, ?array $file = null): array
    {
        $headers = ['Connection: close'];
        if ($this->cookies) {
            $headers[] = 'Cookie: ' . implode('; ', array_map(static fn ($k, $v) => $k . '=' . $v, array_keys($this->cookies), $this->cookies));
        }
        $body = '';
        if ($data !== null && $file === null) {
            $body = http_build_query($data);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        } elseif ($data !== null) {
            $boundary = 'arrue' . bin2hex(random_bytes(12));
            $fields = [];
            $flatten = static function (array $values, string $prefix = '') use (&$flatten, &$fields): void {
                foreach ($values as $name => $value) {
                    $key = $prefix === '' ? (string) $name : $prefix . '[' . $name . ']';
                    if (is_array($value)) {
                        $flatten($value, $key);
                    } else {
                        $fields[$key] = $value;
                    }
                }
            };
            $flatten($data);
            foreach ($fields as $name => $value) {
                $body .= "--$boundary\r\nContent-Disposition: form-data; name=\"$name\"\r\n\r\n$value\r\n";
            }
            $body .= "--$boundary\r\nContent-Disposition: form-data; name=\"{$file['field']}\"; filename=\"{$file['name']}\"\r\nContent-Type: {$file['type']}\r\n\r\n{$file['body']}\r\n--$boundary--\r\n";
            $headers[] = 'Content-Type: multipart/form-data; boundary=' . $boundary;
        }
        $context = stream_context_create(['http' => [
            'method' => $data === null ? 'GET' : 'POST', 'header' => implode("\r\n", $headers),
            'content' => $body, 'ignore_errors' => true, 'follow_location' => 0, 'timeout' => 15,
        ]]);
        $response = file_get_contents(rtrim($this->baseUrl ?? (string) getenv('APP_BASE_URL'), '/') . '/' . ltrim($path, '/'), false, $context);
        $responseHeaders = $http_response_header ?? [];
        if ($response === false || !isset($responseHeaders[0])) {
            throw new RuntimeException('HTTP request failed');
        }
        foreach ($responseHeaders as $line) {
            if (preg_match('/^Set-Cookie: ([^=]+)=([^;]*)/i', $line, $matches)) {
                $this->cookies[$matches[1]] = $matches[2];
            }
        }
        preg_match('/\s(\d{3})\s/', $responseHeaders[0], $status);
        return ['status' => (int) $status[1], 'body' => $response, 'headers' => implode("\n", $responseHeaders)];
    }

    public function token(string $path = 'index.php?action=login'): string
    {
        $response = $this->request($path);
        if (!preg_match('/name="csrf_token" value="([a-f0-9]{64})"/', $response['body'], $match)) {
            throw new RuntimeException('CSRF form unavailable: ' . $path . ' status=' . $response['status']);
        }
        return $match[1];
    }

    public function login(string $user, string $password): array
    {
        return $this->request('index.php?action=login', ['csrf_token' => $this->token(), 'username' => $user, 'password' => $password, 'submit' => 'Login']);
    }
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = mysqli_connect((string) getenv('DB_HOST'), (string) getenv('DB_USER'), (string) getenv('DB_PASSWORD'), (string) getenv('DB_NAME'), (int) (getenv('DB_PORT') ?: 3306));
mysqli_set_charset($db, 'utf8mb4');
$checks = [];
$prefix = 'sec' . bin2hex(random_bytes(4));
$user = $prefix . '@example.test';
$addedUser = $prefix . 'a@example.test';
$password = 'Test-Password-42!';
$newPassword = 'Changed-Password-43!';
$files = [];
$webRoot = getenv('APP_WEB_ROOT') ?: __DIR__ . '/../erronka2_web';
$query = static function (string $sql, string $types = '', array $values = []) use ($db): array {
    $statement = mysqli_prepare($db, $sql);
    if ($types !== '') {
        mysqli_stmt_bind_param($statement, $types, ...$values);
    }
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($statement);
    return $rows;
};
$check = static function (string $name, bool $passed) use (&$checks): void {
    $checks[] = ['test' => $name, 'passed' => $passed];
    echo ($passed ? 'PASS ' : 'FAIL ') . $name . "\n";
};

try {
    $guest = new SecurityHttpClient();
    $home = $guest->request('index.php');
    $check('Home with real database', $home['status'] === 200 && str_contains($home['body'], 'Hortz sintetikoa'));
    $check('Session cookie HttpOnly and SameSite', str_contains(strtolower($home['headers']), 'httponly') && str_contains(strtolower($home['headers']), 'samesite=lax'));
    $check('CSP and anti-sniff headers', str_contains($home['headers'], "default-src 'none'") && str_contains(strtolower($home['headers']), 'nosniff'));
    $check('Authenticated content is not cached', str_contains(strtolower($home['headers']), 'cache-control: no-store'));
    foreach (['account', 'updel', 'description&pic_id=1'] as $route) {
        $check('Guest cannot access ' . $route, $guest->request('index.php?action=' . $route)['status'] === 302);
    }
    foreach (['includes/main.php', 'includes/account.php', 'conf.php', 'images/test.php'] as $route) {
        $check('Direct sensitive path blocked: ' . $route, in_array($guest->request($route)['status'], [403, 404], true));
    }
    $xss = '<script>alert("arrue")</script>';
    $search = $guest->request('index.php?action=search&keyword=' . rawurlencode($xss));
    $check('Reflected search XSS encoded', !str_contains($search['body'], $xss) && str_contains($search['body'], '&lt;script&gt;'));
    $search = $guest->request('index.php?action=search&keyword=' . rawurlencode("' OR 1=1 -- "));
    $check('Search SQL injection does not reveal catalog', $search['status'] === 200 && !str_contains($search['body'], 'class="product-card"'));
    $check('Action is allowlisted', !str_contains($guest->request('index.php?action=../../../../etc/passwd')['body'], 'root:x:'));
    $pathInfo = $guest->request('index.php/' . rawurlencode('"><script>alert(1)</script>') . '?action=login');
    $check('Login PATH_INFO XSS protection (HTTP ' . $pathInfo['status'] . ')',
        in_array($pathInfo['status'], [400, 403, 404], true)
        || ($pathInfo['status'] === 200 && str_contains($pathInfo['body'], 'action="index.php?action=login"') && !str_contains($pathInfo['body'], '<script>alert(1)</script>')));
    $check('Missing login CSRF rejected', $guest->request('index.php?action=login', ['username' => 'admin@bdweb', 'password' => 'password', 'submit' => 'Login'])['status'] === 403);
    $check('Missing registration CSRF rejected', $guest->request('index.php?action=register', ['data' => []])['status'] === 403);
    $token = $guest->token('index.php?action=register');
    $foreign = new SecurityHttpClient();
    $check('Another session cannot reuse CSRF', $foreign->request('index.php?action=login', ['csrf_token' => $token, 'submit' => 'Login'])['status'] === 403);
    $fixed = new SecurityHttpClient();
    $fixed->cookies['ARRUESESSID'] = str_repeat('a', 32);
    $fixed->token();
    $check('Strict mode rejects attacker-chosen session IDs', $fixed->cookies['ARRUESESSID'] !== str_repeat('a', 32));
    $registration = ['csrf_token' => $token, 'data' => [
        'email' => $user, 'password' => $password, 'password2' => $password,
        'firstname' => "O'Brian", 'lastname' => '<img src=x onerror=alert(1)>',
        'city' => 'Eibar', 'stateProv' => 'Gipuzkoa', 'country' => 'EH', 'postcode' => '20600', 'telephone' => '600123456',
    ]];
    $avatar = ['field' => 'imagen', 'name' => 'avatar.php', 'type' => 'image/gif', 'body' => base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==')];
    $check('Register real account with profile image', $guest->request('index.php?action=register', $registration, $avatar)['status'] === 302);
    $stored = $query('SELECT password, izena, abizena, irudia FROM users WHERE username = ?', 's', [$user])[0];
    $files[] = $webRoot . '/perfiles/' . basename($stored['irudia']);
    $check('Profile image uses a safe random filename', preg_match('/^[a-f0-9]{32}\.gif$/', $stored['irudia']) === 1);
    $avatarResponse = $guest->request('perfiles/' . rawurlencode($stored['irudia']));
    $check('Profile upload served as image, with nosniff', $avatarResponse['status'] === 200 && str_contains(strtolower($avatarResponse['headers']), 'image/gif') && str_contains(strtolower($avatarResponse['headers']), 'nosniff'));
    $check('Registration password stored as verified hash', $stored['password'] !== $password && password_verify($password, $stored['password']));
    $check('Quotes and HTML stored as data, not SQL', $stored['izena'] === "O'Brian" && $stored['abizena'] === $registration['data']['lastname']);
    $check('Duplicate registration rejected', $guest->request('index.php?action=register', $registration)['status'] === 409);
    $invalid = $registration;
    $invalid['data']['email'] = $prefix . 'x@example.test';
    $invalid['data']['password'] = str_repeat('a', 73);
    $invalid['data']['password2'] = $invalid['data']['password'];
    $invalidResponse = $guest->request('index.php?action=register', $invalid);
    $check('Registration redisplay encodes stored form values', str_contains($invalidResponse['body'], '&lt;img') && !str_contains($invalidResponse['body'], '<img src=x onerror=alert(1)>'));
    $check('Oversized password cannot create a truncated credential', count($query('SELECT id FROM users WHERE username = ?', 's', [$invalid['data']['email']])) === 0);
    $invalid['data']['password'] = ['unexpected'];
    $check('Malformed password array rejected', $guest->request('index.php?action=register', $invalid)['status'] === 400);
    $attacker = new SecurityHttpClient();
    $attacker->login("' OR 1=1 -- ", $password);
    $check('Login SQL injection fails', $attacker->request('index.php?action=account')['status'] === 302);
    $member = new SecurityHttpClient();
    $memberToken = $member->token();
    $beforeId = $member->cookies['ARRUESESSID'];
    $check('Registered account can log in', $member->login($user, $password)['status'] === 302);
    $check('Session ID rotated at login', $member->cookies['ARRUESESSID'] !== $beforeId);
    $check('CSRF token rotated at login', $member->token('index.php?action=account') !== $memberToken);
    foreach (['updel', 'description&pic_id=1'] as $route) {
        $check('Member cannot read admin route ' . $route, $member->request('index.php?action=' . $route)['status'] === 302);
        $check('Member cannot POST admin route ' . $route, $member->request('index.php?action=' . $route, ['csrf_token' => $member->token('index.php?action=account'), 'deskripzioa' => 'denied'])['status'] === 302);
    }
    $check('GET logout cannot change session', $member->request('index.php?action=logout')['status'] === 405 && $member->request('index.php?action=account')['status'] === 200);
    $parallel = new SecurityHttpClient();
    $parallel->login($user, $password);
    $wrong = ['csrf_token' => $member->token('index.php?action=account'), 'currentpass' => 'incorrect', 'newpass' => $newPassword, 'confnewpass' => $newPassword];
    $check('Password change requires current password', $member->request('index.php?action=account&changepass=1', $wrong)['status'] === 400);
    $check('Password change without CSRF rejected', $member->request('index.php?action=account&changepass=1', ['currentpass' => $password, 'newpass' => $newPassword, 'confnewpass' => $newPassword])['status'] === 403);
    $oldCookies = $member->cookies;
    $wrong['currentpass'] = $password;
    $check('Valid password change succeeds', $member->request('index.php?action=account&changepass=1', $wrong)['status'] === 302);
    $stored = $query('SELECT password FROM users WHERE username = ?', 's', [$user])[0]['password'];
    $check('Changed password hashed and old password invalid', password_verify($newPassword, $stored) && !password_verify($password, $stored));
    $check('Parallel session invalidated after password change', $parallel->request('index.php?action=account')['status'] === 302);
    $replay = new SecurityHttpClient();
    $replay->cookies = $oldCookies;
    $check('Old session cannot be replayed after password change', $replay->request('index.php?action=account')['status'] === 302);
    $member->login($user, $password);
    $check('Old password cannot log in', $member->request('index.php?action=account')['status'] === 302);
    $member->login($user, $newPassword);
    $check('New password can log in', $member->request('index.php?action=account')['status'] === 200);

    $admin = new SecurityHttpClient();
    $admin->login('admin@bdweb', 'password');
    $adminToken = $admin->token('index.php?action=account');
    $check('Admin create account succeeds', $admin->request('index.php?action=account&adduser=1', ['csrf_token' => $adminToken, 'newuser' => $addedUser, 'newuserpass' => $password])['status'] === 302);
    $addedHash = $query('SELECT password FROM users WHERE username = ?', 's', [$addedUser])[0]['password'];
    $check('Admin-created password hashed', password_verify($password, $addedHash));
    $legacyName = $prefix . '<svg/onload=x>';
    $query('UPDATE users SET username = ? WHERE username = ?', 'ss', [$legacyName, $addedUser]);
    $adminTable = $admin->request('index.php?action=account');
    $check('Legacy stored username is encoded in admin table and delete action', str_contains($adminTable['body'], '&lt;svg/onload=x&gt;') && !str_contains($adminTable['body'], $legacyName));
    $query('UPDATE users SET username = ? WHERE username = ?', 'ss', [$addedUser, $legacyName]);
    $check('Admin cannot create duplicate account', $admin->request('index.php?action=account&adduser=1', ['csrf_token' => $adminToken, 'newuser' => $addedUser, 'newuserpass' => $password])['status'] === 409);
    $check('Admin create account requires CSRF', $admin->request('index.php?action=account&adduser=1', ['newuser' => $addedUser, 'newuserpass' => $password])['status'] === 403);
    $productToken = $admin->token('index.php?action=updel');
    $check('Product creation requires CSRF', $admin->request('index.php?action=updel&upload=1', ['izena' => $prefix])['status'] === 403);
    $product = ['csrf_token' => $productToken, 'izena' => $prefix . "'<>", 'deskripzioa' => $xss, 'salneurria' => '12.50', 'stock' => '2'];
    $gif = ['field' => 'upfile', 'name' => '../../shell.php', 'type' => 'image/gif', 'body' => base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==')];
    $check('Product and real image upload succeed', $admin->request('index.php?action=updel&upload=1', $product, $gif)['status'] === 302);
    $row = $query('SELECT id, pic FROM produktuak WHERE izena = ?', 's', [$product['izena']])[0];
    $productId = (int) $row['id'];
    $files[] = $webRoot . '/images/' . basename($row['pic']);
    $check('Upload filename replaced by safe random image name', preg_match('/^[a-f0-9]{32}\.gif$/', $row['pic']) === 1);
    $imageResponse = $guest->request('images/' . rawurlencode($row['pic']));
    $check('Product upload served as image, with nosniff', $imageResponse['status'] === 200 && str_contains(strtolower($imageResponse['headers']), 'image/gif') && str_contains(strtolower($imageResponse['headers']), 'nosniff'));
    $catalog = $guest->request('index.php?action=search&keyword=' . rawurlencode($prefix));
    $check('Stored catalog XSS encoded', !str_contains($catalog['body'], $xss) && str_contains($catalog['body'], '&lt;script&gt;'));
    $adminPage = $admin->request('index.php?action=updel');
    $check('Stored admin product name encoded', str_contains($adminPage['body'], '&#039;&lt;&gt;') && !str_contains($adminPage['body'], $product['izena']));
    $badFile = ['field' => 'upfile', 'name' => 'evil.png', 'type' => 'image/png', 'body' => '<?php echo "UNSAFE"; ?>'];
    $check('Fake image MIME and executable upload rejected', $admin->request('index.php?action=updel&upload=1', $product, $badFile)['status'] === 400);
    $largeFile = $gif;
    $largeFile['body'] = str_repeat('a', 2097153);
    $check('Oversized image rejected', $admin->request('index.php?action=updel&upload=1', $product, $largeFile)['status'] === 400);
    $check('Description requires CSRF', $admin->request('index.php?action=description&pic_id=' . $productId, ['deskripzioa' => 'denied'])['status'] === 403);
    $edited = '<svg onload=alert(1)>';
    $check('Description edit succeeds', $admin->request('index.php?action=description&pic_id=' . $productId, ['csrf_token' => $productToken, 'deskripzioa' => $edited, 'salneurria' => '13'])['status'] === 302);
    $description = $admin->request('index.php?action=description&pic_id=' . $productId);
    $check('Description textarea XSS encoded', str_contains($description['body'], '&lt;svg') && !str_contains($description['body'], $edited));
    $check('SQL injection in product ID rejected', $admin->request('index.php?action=description&pic_id=' . rawurlencode('1 OR 1=1'))['status'] === 400);
    $check('Malformed numeric input rejected', $admin->request('index.php?action=description&pic_id=' . $productId, ['csrf_token' => $productToken, 'salneurria' => ['bad'], 'deskripzioa' => 'value'])['status'] === 400);
    $check('Product deletion requires CSRF', $admin->request('index.php?action=updel&pic_id=' . $productId, [])['status'] === 403);
    $check('Product deletion succeeds', $admin->request('index.php?action=updel&pic_id=' . $productId, ['csrf_token' => $productToken])['status'] === 302);
    $check('Deleted product absent from database', count($query('SELECT id FROM produktuak WHERE id = ?', 'i', [$productId])) === 0);
    $count = count($query('SELECT id FROM users'));
    $admin->request('index.php?action=account&deleteuser=' . rawurlencode("' OR 1=1 -- "), ['csrf_token' => $adminToken]);
    $check('SQL injection in delete target cannot delete accounts', count($query('SELECT id FROM users')) === $count);
    $check('User deletion requires CSRF', $admin->request('index.php?action=account&deleteuser=' . rawurlencode($addedUser), [])['status'] === 403);
    $second = new SecurityHttpClient();
    $second->login($addedUser, $password);
    $check('Admin deletes account', $admin->request('index.php?action=account&deleteuser=' . rawurlencode($addedUser), ['csrf_token' => $adminToken])['status'] === 302);
    $check('Deleted account session immediately rejected', $second->request('index.php?action=account')['status'] === 302);
    $logoutToken = $member->token('index.php?action=account');
    $oldCookies = $member->cookies;
    $check('Logout requires CSRF', $member->request('index.php?action=logout', [])['status'] === 403);
    $check('POST logout succeeds', $member->request('index.php?action=logout', ['csrf_token' => $logoutToken])['status'] === 302);
    $replay->cookies = $oldCookies;
    $check('Logged-out session cannot be replayed', $replay->request('index.php?action=account')['status'] === 302);
    // Eskuzko HTTP bezeroak Secure cookiea bidaltzen du; HTTPS flagaren eta
    // zerbitzariaren iraungitzearen proba da, ez TLS garraioaren froga.
    $short = new SecurityHttpClient('http://session-web');
    $secureHeaders = $short->request('index.php?action=login')['headers'];
    $check('HTTPS deployment configuration sets Secure cookie', preg_match('/Set-Cookie: .*; secure;/i', $secureHeaders) === 1);
    $short->login($user, $newPassword);
    sleep(3);
    $check('Idle timeout enforced by server over HTTP', $short->request('index.php?action=account')['status'] === 302);
    $short->login($user, $newPassword);
    $activeRequests = 0;
    $expired = false;
    for ($i = 0; $i < 7; $i++) {
        sleep(1);
        if ($short->request('index.php?action=account')['status'] === 302) {
            $expired = true;
            break;
        }
        $activeRequests++;
    }
    $check('Absolute timeout enforced despite continuing activity', $expired && $activeRequests >= 2);
} catch (Throwable $error) {
    $check('Integration completes: ' . $error->getMessage(), false);
} finally {
    $query('DELETE FROM users WHERE username IN (?, ?)', 'ss', [$user, $addedUser]);
    $query('DELETE FROM users WHERE username = ?', 's', [$prefix . '<svg/onload=x>']);
    $query('DELETE FROM produktuak WHERE izena LIKE ?', 's', [$prefix . '%']);
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    $report = ['date' => gmdate('c'), 'scope' => 'Isolated Docker HTTP + MySQL security regression', 'count' => count($checks), 'passed' => !in_array(false, array_column($checks, 'passed'), true), 'checks' => $checks, 'temporary_data_cleaned' => true];
    $directory = is_dir('/evidence') ? '/evidence' : __DIR__ . '/../build';
    file_put_contents($directory . '/security-http.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    echo count($checks) . ' checks; ' . ($report['passed'] ? 'PASS' : 'FAIL') . "\n";
}
exit($report['passed'] ? 0 : 1);
