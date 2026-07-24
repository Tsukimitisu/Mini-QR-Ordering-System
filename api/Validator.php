<?php
/**
 * Validator - Input validation and sanitization utility
 * Provides methods for validating various input types
 */

class Validator
{
    /**
     * Validate email address
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate integer within range
     */
    public static function validateInteger(mixed $value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): bool
    {
        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        return $filtered !== false && $filtered >= $min && $filtered <= $max;
    }

    /**
     * Validate positive integer
     */
    public static function validatePositiveInteger(mixed $value, int $min = 1): bool
    {
        return self::validateInteger($value, $min);
    }

    /**
     * Validate float within range
     */
    public static function validateFloat(mixed $value, float $min = -INF, float $max = INF): bool
    {
        $filtered = filter_var($value, FILTER_VALIDATE_FLOAT);
        return $filtered !== false && $filtered >= $min && $filtered <= $max;
    }

    /**
     * Validate string length
     */
    public static function validateStringLength(string $value, int $minLength = 0, int $maxLength = PHP_INT_MAX): bool
    {
        $length = strlen($value);
        return $length >= $minLength && $length <= $maxLength;
    }

    /**
     * Validate table number (1-999)
     */
    public static function validateTableNumber(mixed $tableNumber): bool
    {
        return self::validateInteger($tableNumber, MIN_TABLE_NUMBER, MAX_TABLE_NUMBER);
    }

    /**
     * Validate product quantity
     */
    public static function validateQuantity(mixed $quantity): bool
    {
        return self::validatePositiveInteger($quantity, 1, 1000);
    }

    /**
     * Validate array contains required keys
     */
    public static function validateRequiredKeys(array $data, array $requiredKeys): bool
    {
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key]) || $data[$key] === '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Sanitize string input (remove HTML tags and trim)
     */
    public static function sanitizeString(string $value): string
    {
        return trim(htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Sanitize email
     */
    public static function sanitizeEmail(string $email): string
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize URL
     */
    public static function sanitizeUrl(string $url): string
    {
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize array by applying sanitization to all string values
     */
    public static function sanitizeArray(array $data, callable $sanitizer = null): array
    {
        $sanitizer = $sanitizer ?: [self::class, 'sanitizeString'];
        
        return array_map(function ($value) use ($sanitizer) {
            if (is_array($value)) {
                return self::sanitizeArray($value, $sanitizer);
            }
            return is_string($value) ? $sanitizer($value) : $value;
        }, $data);
    }

    /**
     * Validate UUID format
     */
    public static function validateUUID(string $uuid): bool
    {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        return preg_match($pattern, $uuid) === 1;
    }

    /**
     * Validate date format (YYYY-MM-DD)
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Validate credit card number (Luhn algorithm)
     */
    public static function validateCreditCard(string $cardNumber): bool
    {
        $cardNumber = preg_replace('/[^0-9]/', '', $cardNumber);
        
        if (strlen($cardNumber) < 13) {
            return false;
        }

        $sum = 0;
        $parity = strlen($cardNumber) % 2;
        
        for ($i = 0; $i < strlen($cardNumber); $i++) {
            $digit = (int)$cardNumber[$i];
            
            if ($i % 2 === $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
        }

        return $sum % 10 === 0;
    }
}
?>
