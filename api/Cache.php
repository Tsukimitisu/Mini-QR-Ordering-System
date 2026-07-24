<?php
/**
 * Cache - Simple file-based caching system
 * Stores and retrieves cached data with expiration support
 */

require_once __DIR__ . '/Logger.php';

class Cache
{
    private const CACHE_DIR = __DIR__ . '/../cache';
    private const DEFAULT_TTL = 3600; // 1 hour

    /**
     * Initialize cache system
     */
    public static function init(): void
    {
        if (!is_dir(self::CACHE_DIR)) {
            mkdir(self::CACHE_DIR, 0755, true);
            log_info('Cache directory created', ['path' => self::CACHE_DIR]);
        }
    }

    /**
     * Store value in cache
     */
    public static function set(string $key, mixed $value, int $ttl = self::DEFAULT_TTL): bool
    {
        self::init();

        $cacheFile = self::getCacheFile($key);
        $cacheData = [
            'value' => $value,
            'expires_at' => time() + $ttl,
            'created_at' => time(),
        ];

        $result = file_put_contents($cacheFile, json_encode($cacheData), LOCK_EX);

        if ($result) {
            log_debug('Cache set', ['key' => $key, 'ttl' => $ttl]);
            return true;
        }

        log_error('Failed to set cache', ['key' => $key]);
        return false;
    }

    /**
     * Retrieve value from cache
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheFile = self::getCacheFile($key);

        if (!file_exists($cacheFile)) {
            log_debug('Cache miss', ['key' => $key]);
            return $default;
        }

        $cacheData = json_decode(file_get_contents($cacheFile), true);

        if ($cacheData === null) {
            self::delete($key);
            return $default;
        }

        // Check if cache has expired
        if ($cacheData['expires_at'] < time()) {
            self::delete($key);
            log_debug('Cache expired', ['key' => $key]);
            return $default;
        }

        log_debug('Cache hit', ['key' => $key]);
        return $cacheData['value'];
    }

    /**
     * Check if key exists in cache
     */
    public static function has(string $key): bool
    {
        $value = self::get($key);
        return $value !== null;
    }

    /**
     * Delete cache entry
     */
    public static function delete(string $key): bool
    {
        $cacheFile = self::getCacheFile($key);

        if (!file_exists($cacheFile)) {
            return false;
        }

        $result = unlink($cacheFile);

        if ($result) {
            log_debug('Cache deleted', ['key' => $key]);
        }

        return $result;
    }

    /**
     * Clear all cache entries
     */
    public static function flush(): bool
    {
        self::init();

        $files = glob(self::CACHE_DIR . '/*.cache');

        if (empty($files)) {
            return true;
        }

        foreach ($files as $file) {
            unlink($file);
        }

        log_info('Cache flushed', ['count' => count($files)]);
        return true;
    }

    /**
     * Get cache statistics
     */
    public static function stats(): array
    {
        self::init();

        $files = glob(self::CACHE_DIR . '/*.cache');
        $totalSize = 0;
        $expiredCount = 0;

        foreach ($files as $file) {
            $totalSize += filesize($file);

            $cacheData = json_decode(file_get_contents($file), true);
            if ($cacheData && $cacheData['expires_at'] < time()) {
                $expiredCount++;
            }
        }

        return [
            'entries' => count($files),
            'expired' => $expiredCount,
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
        ];
    }

    /**
     * Get or set cache value
     */
    public static function remember(string $key, mixed $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        $value = self::get($key);

        if ($value !== null) {
            return $value;
        }

        $value = is_callable($callback) ? call_user_func($callback) : $callback;
        self::set($key, $value, $ttl);

        return $value;
    }

    /**
     * Cleanup expired cache entries
     */
    public static function cleanup(): int
    {
        self::init();

        $files = glob(self::CACHE_DIR . '/*.cache');
        $deletedCount = 0;

        foreach ($files as $file) {
            $cacheData = json_decode(file_get_contents($file), true);

            if ($cacheData && $cacheData['expires_at'] < time()) {
                unlink($file);
                $deletedCount++;
            }
        }

        log_debug('Cache cleanup completed', ['deleted' => $deletedCount]);
        return $deletedCount;
    }

    /**
     * Get cache file path
     */
    private static function getCacheFile(string $key): string
    {
        $hash = hash('sha256', $key);
        return self::CACHE_DIR . '/' . $hash . '.cache';
    }
}
?>
