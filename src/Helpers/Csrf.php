<?php

namespace Azulphp\Helpers;

use Random\RandomException;

/**
 * Manage CSRF protection.
 */
class Csrf
{
    /**
     * Generate CSRF token.
     *
     * @throws RandomException
     */
    public static function generate(): string
    {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    /**
     * Retrieve CSRF token.
     *
     * @return string
     * @throws RandomException
     */
    public static function getToken(): string
    {
        return $_SESSION['_csrf_token'] ?? self::generate();
    }

    /**
     * Verify CSRF token.
     *
     * @param  string|null  $token
     * @return bool
     */
    public static function verify(?string $token): bool
    {
        return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token ?? '');
    }
}