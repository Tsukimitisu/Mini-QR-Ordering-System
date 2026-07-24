<?php
/**
 * Security - Additional security utilities
 * Provides encryption, hashing, and security-related functions
 */

class Security
{
    /**
     * Generate a secure random token
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Generate a secure random string
     */
    public static function generateRandomString(int $length = 16): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $string;
    }

    /**
     * Hash a value using SHA256
     */
    public static function hash(string $value): string
    {
        return hash('sha256', $value);
    }

    /**
     * Hash a password using bcrypt
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => 12,
        ]);
    }

    /**
     * Verify a password against a hash
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Encrypt a string using AES-256-CBC
     */
    public static function encrypt(string $data, string $key = null): string
    {
        $key = $key ?? (defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : '');

        if (empty($key)) {
            throw new \Exception('Encryption key not set');
        }

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);

        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt a string encrypted with encrypt()
     */
    public static function decrypt(string $data, string $key = null): string
    {
        $key = $key ?? (defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : '');

        if (empty($key)) {
            throw new \Exception('Encryption key not set');
        }

        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    }

    /**
     * Escape HTML special characters
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize HTML (remove dangerous tags)
     */
    public static function sanitizeHtml(string $html): string
    {
        $allowed = '<b><i><u><em><strong><p><br><a><ul><ol><li>';
        return strip_tags($html, $allowed);
    }

    /**
     * Verify a HMAC signature
     */
    public static function verifyHmac(string $data, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $data, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate a HMAC signature
     */
    public static function generateHmac(string $data, string $secret): string
    {
        return hash_hmac('sha256', $data, $secret);
    }

    /**
     * Check if IP is in whitelist
     */
    public static function isIpWhitelisted(string $ip, array $whitelist): bool
    {
        foreach ($whitelist as $pattern) {
            if (self::ipMatches($ip, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP matches a pattern (supports CIDR notation)
     */
    private static function ipMatches(string $ip, string $pattern): bool
    {
        // Exact match
        if ($ip === $pattern) {
            return true;
        }

        // CIDR notation
        if (strpos($pattern, '/') !== false) {
            list($subnet, $bits) = explode('/', $pattern);

            if (!filter_var($subnet, FILTER_VALIDATE_IP)) {
                return false;
            }

            $ip = ip2long($ip);
            $subnet = ip2long($subnet);
            $mask = -1 << (32 - $bits);
            $subnet &= $mask;
            $ip &= $mask;

            return $ip === $subnet;
        }

        // Wildcard pattern
        if (strpos($pattern, '*') !== false) {
            $pattern = str_replace('*', '.*', preg_quote($pattern));
            return preg_match('/^' . $pattern . '$/', $ip) === 1;
        }

        return false;
    }

    /**
     * Get secure headers array
     */
    public static function getSecureHeaders(): array
    {
        return [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'",
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
        ];
    }

    /**
     * Set secure headers
     */
    public static function setSecureHeaders(): void
    {
        foreach (self::getSecureHeaders() as $header => $value) {
            header($header . ': ' . $value);
        }
    }

    /**
     * Validate JWT token (basic implementation)
     */
    public static function validateJwt(string $token, string $secret): bool
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        $signature = hash_hmac('sha256', $parts[0] . '.' . $parts[1], $secret, true);
        $expectedSignature = base64_encode($signature);

        return hash_equals($expectedSignature, $parts[2]);
    }
}
?>
