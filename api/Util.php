<?php
/**
 * Util - Utility functions and helpers
 * Collection of commonly used utility functions
 */

class Util
{
    /**
     * Format bytes to human-readable size
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Format number with thousand separator
     */
    public static function formatNumber(float $number, int $decimals = 0): string
    {
        return number_format($number, $decimals);
    }

    /**
     * Format price with currency symbol
     */
    public static function formatPrice(float $price, string $currency = '$'): string
    {
        return $currency . number_format($price, 2);
    }

    /**
     * Format date in readable format
     */
    public static function formatDate(string $date, string $format = 'M d, Y'): string
    {
        $timestamp = strtotime($date);
        return $timestamp ? date($format, $timestamp) : $date;
    }

    /**
     * Format relative time (e.g., "2 hours ago")
     */
    public static function formatRelativeTime(string $date): string
    {
        $timestamp = strtotime($date);
        $now = time();
        $diff = $now - $timestamp;

        if ($diff < 0) {
            return 'in the future';
        }

        if ($diff < 60) {
            return $diff . ' second' . ($diff != 1 ? 's' : '') . ' ago';
        }

        if ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' minute' . ($minutes != 1 ? 's' : '') . ' ago';
        }

        if ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours != 1 ? 's' : '') . ' ago';
        }

        if ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days != 1 ? 's' : '') . ' ago';
        }

        if ($diff < 2592000) {
            $weeks = floor($diff / 604800);
            return $weeks . ' week' . ($weeks != 1 ? 's' : '') . ' ago';
        }

        return date('M d, Y', $timestamp);
    }

    /**
     * Truncate string with ellipsis
     */
    public static function truncate(string $string, int $length = 100, string $ellipsis = '...'): string
    {
        if (strlen($string) <= $length) {
            return $string;
        }

        return substr($string, 0, $length - strlen($ellipsis)) . $ellipsis;
    }

    /**
     * Slugify string (convert to URL-friendly)
     */
    public static function slug(string $string): string
    {
        $slug = strtolower(trim($string));
        $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    /**
     * Capitalize string
     */
    public static function capitalize(string $string): string
    {
        return ucfirst(strtolower($string));
    }

    /**
     * Title case string
     */
    public static function titleCase(string $string): string
    {
        return ucwords(strtolower($string));
    }

    /**
     * Convert array to CSV string
     */
    public static function arrayToCsv(array $data): string
    {
        $output = fopen('php://memory', 'w');
        fputcsv($output, $data);
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return trim($csv);
    }

    /**
     * Convert CSV string to array
     */
    public static function csvToArray(string $csv): array
    {
        $input = fopen('php://memory', 'r+');
        fwrite($input, $csv);
        rewind($input);
        $data = [];

        while (($row = fgetcsv($input)) !== false) {
            $data[] = $row;
        }

        fclose($input);
        return $data;
    }

    /**
     * Generate UUID v4
     */
    public static function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Check if array is associative
     */
    public static function isAssociativeArray(array $array): bool
    {
        if (empty($array)) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Merge arrays recursively
     */
    public static function mergeRecursive(array $array1, array $array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::mergeRecursive($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Array pluck (extract specific keys)
     */
    public static function pluck(array $array, string $key): array
    {
        return array_map(fn($item) => $item[$key] ?? null, $array);
    }

    /**
     * Group array by key
     */
    public static function groupBy(array $array, string $key): array
    {
        $result = [];

        foreach ($array as $item) {
            $groupKey = $item[$key] ?? 'undefined';
            $result[$groupKey][] = $item;
        }

        return $result;
    }

    /**
     * Get client's IP address
     */
    public static function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }

    /**
     * Check if request is HTTPS
     */
    public static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
               (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
               (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    }

    /**
     * Get current URL
     */
    public static function getCurrentUrl(): string
    {
        $protocol = self::isHttps() ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return $protocol . '://' . $host . $uri;
    }
}
?>
