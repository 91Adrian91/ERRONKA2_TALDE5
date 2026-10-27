<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AuthenticationViewsTest extends TestCase
{
    private array $savedSession;
    private array $savedGet;
    private array $savedPost;
    private array $savedServer;

    protected function setUp(): void
    {
        $this->savedSession = $_SESSION ?? [];
        $this->savedGet = $_GET;
        $this->savedPost = $_POST;
        $this->savedServer = $_SERVER;
        $_SESSION = [];
        $_GET = [];
        $_POST = [];
        $_SERVER['PHP_SELF'] = '/index.php';
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->savedSession;
        $_GET = $this->savedGet;
        $_POST = $this->savedPost;
        $_SERVER = $this->savedServer;
    }

    private function render(string $view): string
    {
        ob_start();
        try {
            require __DIR__ . '/../erronka2_web/includes/' . $view . '.php';
            return (string) ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    public function testGuestCanSeeLoginForm(): void
    {
        $html = $this->render('login');
        self::assertStringContainsString('method="post"', $html);
        self::assertStringContainsString('name="username"', $html);
        self::assertStringContainsString('type="password" name="password"', $html);
        self::assertStringContainsString('action="index.php?action=login"', $html);
    }

    public function testAuthenticatedUserDoesNotSeeLoginForm(): void
    {
        $_SESSION = ['admin' => 1, 'username' => 'user@example.test'];
        $html = $this->render('login');
        self::assertStringContainsString('You are already logged in', $html);
        self::assertStringNotContainsString('<form', $html);
    }

    public function testLoginActionDoesNotAllowHtmlInjectionFromRequestPath(): void
    {
        $_SERVER['PHP_SELF'] = '/index.php/"><script>document.title="XSS"</script><span data-x="';
        $html = $this->render('login');
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML($html);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        self::assertSame(0, $document->getElementsByTagName('script')->length);
        self::assertSame(1, $document->getElementsByTagName('form')->length);
        self::assertSame('index.php?action=login', $document->getElementsByTagName('form')->item(0)->getAttribute('action'));
    }

    public function testUnauthenticatedSessionCanStillSeeLoginForm(): void
    {
        $_SESSION = ['admin' => 0];
        $html = $this->render('login');
        self::assertStringContainsString('name="username"', $html);
        self::assertStringNotContainsString('You are already logged in', $html);
    }

    public function testRegularAccountHasPasswordFormWithoutUserManagement(): void
    {
        $_SESSION = ['admin' => 1, 'username' => 'user@example.test'];
        $html = $this->render('account');
        self::assertStringContainsString('type="password" name="newpass"', $html);
        self::assertStringContainsString('type="password" name="confnewpass"', $html);
        self::assertStringNotContainsString('name="newuser"', $html);
        self::assertStringNotContainsString('deleteuser=', $html);
    }
}
