<?php
/**
 * Auth - Authentication and authorization management
 * Handles user authentication, session management, and permission checks
 */

require_once __DIR__ . '/Logger.php';

class Auth
{
    private const SESSION_KEY = 'authenticated_user';
    private const ADMIN_ROLE = 'admin';
    private const CUSTOMER_ROLE = 'customer';

    /**
     * Initialize authentication (start session if needed)
     */
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Authenticate an admin user
     */
    public static function authenticateAdmin(string $username, string $password): bool
    {
        // In a real application, this would check against a database
        // For now, we use hardcoded admin credentials
        require __DIR__ . '/../config.php';

        $adminUsername = getenv('ADMIN_USERNAME') ?: 'admin';
        $adminPassword = getenv('ADMIN_PASSWORD') ?: 'admin123';

        if ($username === $adminUsername && $password === $adminPassword) {
            self::init();
            $_SESSION[self::SESSION_KEY] = [
                'id' => 1,
                'username' => $username,
                'role' => self::ADMIN_ROLE,
                'authenticated_at' => time(),
            ];

            log_info('Admin authentication successful', ['username' => $username]);
            return true;
        }

        log_warning('Admin authentication failed', ['username' => $username]);
        return false;
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated(): bool
    {
        self::init();
        return isset($_SESSION[self::SESSION_KEY]) && self::isSessionValid();
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin(): bool
    {
        return self::isAuthenticated() && self::getRole() === self::ADMIN_ROLE;
    }

    /**
     * Get current user data
     */
    public static function getUser(): ?array
    {
        self::init();

        if (!self::isAuthenticated()) {
            return null;
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Get current user ID
     */
    public static function getUserId(): ?int
    {
        $user = self::getUser();
        return $user['id'] ?? null;
    }

    /**
     * Get current user role
     */
    public static function getRole(): ?string
    {
        $user = self::getUser();
        return $user['role'] ?? null;
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        self::init();

        $username = $_SESSION[self::SESSION_KEY]['username'] ?? 'unknown';
        unset($_SESSION[self::SESSION_KEY]);

        log_info('User logged out', ['username' => $username]);
    }

    /**
     * Check if session is valid (not expired)
     */
    private static function isSessionValid(): bool
    {
        $user = $_SESSION[self::SESSION_KEY] ?? null;

        if ($user === null) {
            return false;
        }

        $authenticatedAt = $user['authenticated_at'] ?? 0;
        $sessionTimeout = SESSION_TIMEOUT;

        return (time() - $authenticatedAt) < $sessionTimeout;
    }

    /**
     * Require authentication (redirect to login if not authenticated)
     */
    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            log_warning('Access denied: Not authenticated', ['path' => $_SERVER['REQUEST_URI'] ?? '']);
            header('Location: /admin/login.php');
            exit;
        }
    }

    /**
     * Require admin role (redirect if not admin)
     */
    public static function requireAdmin(): void
    {
        self::requireAuth();

        if (!self::isAdmin()) {
            log_warning('Access denied: Not admin', ['username' => self::getUser()['username'] ?? 'unknown']);
            header('Location: /');
            exit;
        }
    }

    /**
     * Middleware for API authentication
     */
    public static function apiAuthMiddleware(): void
    {
        if (!self::isAuthenticated()) {
            Response::unauthorized('Authentication required');
        }
    }

    /**
     * Middleware for API admin authentication
     */
    public static function apiAdminMiddleware(): void
    {
        if (!self::isAdmin()) {
            Response::forbidden('Admin access required');
        }
    }

    /**
     * Hash a password using bcrypt
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_HASH_ALGO);
    }

    /**
     * Verify a password against a hash
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Regenerate session ID (prevent session fixation)
     */
    public static function regenerateSessionId(): void
    {
        self::init();
        session_regenerate_id(true);
        log_debug('Session ID regenerated');
    }
}
?>
