<?php
require_once __DIR__ . '/helpers.php';

if (signedIn()) {
    echo '<div class="centered"><h5>You are already logged in</h5></div>';
} elseif (isset($_POST['submit'])) {
    $username = trim(inputText($_POST, 'username'));
    $password = inputText($_POST, 'password');
    $statement = mysqli_prepare($conx, 'SELECT username, password FROM users WHERE username = ? LIMIT 1');
    mysqli_stmt_bind_param($statement, 's', $username);
    mysqli_stmt_execute($statement);
    $credentials = mysqli_fetch_assoc(mysqli_stmt_get_result($statement));
    mysqli_stmt_close($statement);
    if ($credentials && validPassword($password) && password_verify($password, $credentials['password'])) {
        session_regenerate_id(true);
        $_SESSION = [
            'username' => $credentials['username'],
            'credential_version' => hash('sha256', $credentials['password']),
            'created_at' => time(), 'last_seen' => time(),
        ];
        $_SESSION['admin'] = 1;
        csrfToken();
        redirectPage('index.php');
    }
    redirectPage('index.php?action=login');
} else {
?>
<div class="centered">
    <fieldset class="compact-form">
        <legend>Login</legend>
        <form action="index.php?action=login" method="post">
            <?php echo csrfField(); ?>
            <p><label for="login-username">Username/Email:</label>
            <input type="text" name="username" id="login-username" autocomplete="username" required></p>
            <p><label for="login-password">Password:</label>
            <input type="password" name="password" id="login-password" autocomplete="current-password" required></p>
            <input type="submit" name="submit" value="Login">
        </form>
    </fieldset>
</div>
<?php
}
?>
