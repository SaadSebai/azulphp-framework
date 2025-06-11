<?php

namespace Azulphp;

class Session
{
    /**
     * Check if session's key exists.
     *
     * @param  $key
     * @return bool
     */
    public static function has($key): bool
    {
        return (bool) static::get($key);
    }

    /**
     * Save a value in the session.
     *
     * @param  $key
     * @param  $value
     * @return void
     */
    public static function put($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieve a record for session.
     *
     * @param  $key
     * @param  $default
     * @return mixed|null
     */
    public static function get($key, $default = null): mixed
    {
        return $_SESSION['_flash'][$key] ?? $_SESSION[$key] ?? $default;
    }

    /**
     * Add to session flash.
     *
     * @param  $key
     * @param  $value
     * @return void
     */
    public static function flash($key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Remove from session flash.
     *
     * @return void
     */
    public static function unflash(): void
    {
       unset($_SESSION['_flash']);
    }

    /**
     * Empty the session.
     *
     * @return void
     */
    public static function flush(): void
    {
        $_SESSION = [];
    }

    /**
     * Destroy session.
     *
     * @return void
     */
    public static function destroy(): void
    {
        static::flush();

        session_destroy();

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    /**
     * Start Session.
     *
     * @return void
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}