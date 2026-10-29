<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../erronka2_web/includes/helpers.php';

final class SecurityHelpersTest extends TestCase
{
    public function testCsrfTokensAreBoundToTheSession(): void
    {
        $saved = $_SESSION ?? [];
        try {
            $_SESSION = [];
            $first = csrfToken();
            self::assertSame(64, strlen($first));
            self::assertSame($first, csrfToken());
            verifyCsrf(['csrf_token' => $first]);
            $_SESSION = [];
            self::assertNotSame($first, csrfToken());
            $this->expectException(RequestException::class);
            verifyCsrf(['csrf_token' => $first]);
        } finally {
            $_SESSION = $saved;
        }
    }

    public function testMissingCsrfTokenIsRejected(): void
    {
        $this->expectException(RequestException::class);
        verifyCsrf([]);
    }

    public function testArrayInputIsRejectedInsteadOfConvertedToText(): void
    {
        $this->expectException(RequestException::class);
        inputText(['password' => ['unexpected']], 'password');
    }

    public function testIdleAndAbsoluteSessionExpiryAreIndependent(): void
    {
        self::assertFalse(sessionExpired(['created_at' => 100, 'last_seen' => 120], 129, 10, 60));
        self::assertTrue(sessionExpired(['created_at' => 100, 'last_seen' => 120], 130, 10, 60));
        self::assertTrue(sessionExpired(['created_at' => 100, 'last_seen' => 159], 160, 10, 60));
        self::assertTrue(sessionExpired([], 100, 10, 60));
    }

    public function testPasswordsCannotBeSilentlyTruncatedByBcrypt(): void
    {
        self::assertFalse(validPassword('short'));
        self::assertTrue(validPassword(str_repeat('a', 72)));
        self::assertFalse(validPassword(str_repeat('a', 73)));
        self::assertFalse(validPassword(str_repeat('é', 37)));
    }

    public function testHtmlEncodingCoversTextAndAttributeDelimiters(): void
    {
        self::assertSame('&lt;script&gt;&quot;&#039;&amp;', htmlText('<script>"\'&'));
    }
}
