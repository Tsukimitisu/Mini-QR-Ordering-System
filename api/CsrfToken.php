<?php
/**
 * CsrfToken - CSRF (Cross-Site Request Forgery) token management
 * Generates, stores, and validates CSRF tokens for form submissions
 */

class CsrfToken
{
    private const SESSION_KEY = 'csrf_token';
    private const TIMESTAMP_KEY = 'csrf_timestamp';

    /**
     * Initialize CSRF token protection (start session if needed)
     */
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Generate a new CSRF token
     */
    public static function generate(): string
    {
        self::init();

        // Generate new token if doesn't exist or is expired
        if (!isset($_SESSION[self::SESSION_KEY]) || self::isTokenExpired()) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
            $_SESSION[self::TIMESTAMP_KEY] = time();
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Get the current CSRF token
     */
    public static function getToken(): ?string
    {
        self::init();
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    /**
     * Validate provided CSRF token
     */
    public static function validate(?string $token): bool
    {
        self::init();

        if ($token === null) {
            return false;
        }

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? null;

        if ($sessionToken === null) {
            return false;
        }

        // Use hash_equals to prevent timing attacks
        if (!hash_equals($sessionToken, $token)) {
            return false;
        }

        // Check if token is expired
        if (self::isTokenExpired()) {
            return false;
        }

        return true;
    }

    /**
     * Check if token is expired
     */
    private static function isTokenExpired(): bool
    {
        $timestamp = $_SESSION[self::TIMESTAMP_KEY] ?? 0;
        return (time() - $timestamp) > CSRF_TOKEN_LIFETIME;
    }

    /**
     * Refresh the CSRF token
     */
    public static function refresh(): string
    {
        self::init();
        unset($_SESSION[self::SESSION_KEY], $_SESSION[self::TIMESTAMP_KEY]);
        return self::generate();
    }

    /**
     * Get CSRF token from request (POST, JSON, or header)
     */
    public static function getTokenFromRequest(): ?string
    {
        // Check POST data
        if (!empty($_POST['csrf_token'])) {
            return $_POST['csrf_token'];
        }

        // Check JSON body
        $jsonData = json_decode(file_get_contents('php://input'), true);
        if (is_array($jsonData) && !empty($jsonData['csrf_token'])) {
            return $jsonData['csrf_token'];
        }

        // Check headers
        $headers = getallheaders();
        if (!empty($headers['X-CSRF-Token'])) {
            return $headers['X-CSRF-Token'];
        }

        return null;
    }

    /**
     * Middleware function to validate CSRF token on POST/PUT/DELETE
     */
    public static function middleware(): void
    {
        if (!ENABLE_CSRF_PROTECTION) {
            return;
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Only check for state-changing methods
        if (!in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            return;
        }

        $token = self::getTokenFromRequest();

        if (!self::validate($token)) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'CSRF token validation failed']);
            exit;
        }
    }

    /**
     * Generate CSRF token HTML input field for forms
     */
    public static function getHiddenInput(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Generate CSRF token meta tag for AJAX requests
     */
    public static function getMetaTag(): string
    {
        $token = self::generate();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
?>
