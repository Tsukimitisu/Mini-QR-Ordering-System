<?php
/**
 * Session - Session management utility
 * Handles session creation, storage, retrieval, and cleanup
 */

require_once __DIR__ . '/Logger.php';

class Session
{
    private const SESSION_PREFIX = 'app_';
    private static $initialized = false;

    /**
     * Initialize session management
     */
    public static function init(array $options = []): void
    {
        if (self::$initialized) {
            return;
        }

        // Set session options
        $defaultOptions = [
            'name' => 'GOURMET_SESSION',
            'cookie_lifetime' => SESSION_TIMEOUT,
            'cookie_path' => '/',
            'cookie_domain' => '',
            'cookie_secure' => !APP_DEBUG,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'use_strict_mode' => true,
        ];

        $options = array_merge($defaultOptions, $options);

        session_set_cookie_params([
            'lifetime' => $options['cookie_lifetime'],
            'path' => $options['cookie_path'],
            'domain' => $options['cookie_domain'],
            'secure' => $options['cookie_secure'],
            'httponly' => $options['cookie_httponly'],
            'samesite' => $options['cookie_samesite'],
        ]);

        session_name($options['name']);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        self::$initialized = true;
        log_debug('Session initialized');
    }

    /**
     * Set session value
     */
    public static function set(string $key, mixed $value): void
    {
        self::init();
        $_SESSION[self::SESSION_PREFIX . $key] = $value;
    }

    /**
     * Get session value
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::init();
        return $_SESSION[self::SESSION_PREFIX . $key] ?? $default;
    }

    /**
     * Check if session key exists
     */
    public static function has(string $key): bool
    {
        self::init();
        return isset($_SESSION[self::SESSION_PREFIX . $key]);
    }

    /**
     * Delete session value
     */
    public static function delete(string $key): void
    {
        self::init();
        unset($_SESSION[self::SESSION_PREFIX . $key]);
    }

    /**
     * Forget multiple keys
     */
    public static function forget(array $keys): void
    {
        foreach ($keys as $key) {
            self::delete($key);
        }
    }

    /**
     * Flush all session data
     */
    public static function flush(): void
    {
        self::init();
        $_SESSION = [];
        log_info('Session flushed');
    }

    /**
     * Set flash data (one-time data)
     */
    public static function flash(string $key, mixed $value): void
    {
        self::init();
        $_SESSION['_flash'][self::SESSION_PREFIX . $key] = $value;
    }

    /**
     * Get flash data
     */
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::init();

        if (!isset($_SESSION['_flash'][self::SESSION_PREFIX . $key])) {
            return $default;
        }

        $value = $_SESSION['_flash'][self::SESSION_PREFIX . $key];
        unset($_SESSION['_flash'][self::SESSION_PREFIX . $key]);

        return $value;
    }

    /**
     * Check if flash key exists
     */
    public static function hasFlash(string $key): bool
    {
        self::init();
        return isset($_SESSION['_flash'][self::SESSION_PREFIX . $key]);
    }

    /**
     * Get all flash data
     */
    public static function getAllFlash(): array
    {
        self::init();
        $flash = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $flash;
    }

    /**
     * Get session ID
     */
    public static function getId(): string
    {
        self::init();
        return session_id();
    }

    /**
     * Regenerate session ID
     */
    public static function regenerate(): void
    {
        self::init();
        session_regenerate_id(true);
        log_debug('Session ID regenerated');
    }

    /**
     * Get session data
     */
    public static function all(): array
    {
        self::init();
        return $_SESSION;
    }

    /**
     * Get session age in seconds
     */
    public static function getAge(): int
    {
        self::init();
        $createdAt = $_SESSION['_created_at'] ?? time();
        return time() - $createdAt;
    }

    /**
     * Check if session is expired
     */
    public static function isExpired(): bool
    {
        return self::getAge() > SESSION_TIMEOUT;
    }

    /**
     * Destroy session
     */
    public static function destroy(): void
    {
        self::init();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        log_info('Session destroyed');
    }

    /**
     * Migrate session (for security)
     */
    public static function migrate(): void
    {
        self::init();
        $data = $_SESSION;
        session_regenerate_id(true);
        $_SESSION = $data;
        $_SESSION['_migrated_at'] = time();

        log_debug('Session migrated');
    }
}
?>
