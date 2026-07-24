<?php
/**
 * RateLimiter - API rate limiting and throttling
 * Prevents abuse by limiting request frequency from specific clients
 */

require_once __DIR__ . '/Logger.php';

class RateLimiter
{
    private const CACHE_DIR = __DIR__ . '/../cache/rate_limit';
    private $clientId;
    private $limit;
    private $window;

    public function __construct(string $clientId = null, int $limit = null, int $window = 60)
    {
        $this->clientId = $clientId ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $this->limit = $limit ?? API_RATE_LIMIT;
        $this->window = $window;

        // Create cache directory if it doesn't exist
        if (!is_dir(self::CACHE_DIR)) {
            mkdir(self::CACHE_DIR, 0755, true);
        }
    }

    /**
     * Check if request is allowed
     */
    public function isAllowed(): bool
    {
        $cacheFile = self::getCacheFile($this->clientId);
        $data = $this->getRequestData($cacheFile);

        // Check if window has expired
        if (time() - $data['start_time'] > $this->window) {
            $data = [
                'count' => 0,
                'start_time' => time(),
            ];
        }

        // Check if limit exceeded
        if ($data['count'] >= $this->limit) {
            log_warning('Rate limit exceeded', [
                'client_id' => $this->clientId,
                'limit' => $this->limit,
                'window' => $this->window,
            ]);
            return false;
        }

        // Increment counter
        $data['count']++;
        $this->saveRequestData($cacheFile, $data);

        return true;
    }

    /**
     * Get remaining requests in current window
     */
    public function getRemaining(): int
    {
        $cacheFile = self::getCacheFile($this->clientId);
        $data = $this->getRequestData($cacheFile);

        // Check if window has expired
        if (time() - $data['start_time'] > $this->window) {
            return $this->limit;
        }

        return max(0, $this->limit - $data['count']);
    }

    /**
     * Get reset time (when current window expires)
     */
    public function getResetTime(): int
    {
        $cacheFile = self::getCacheFile($this->clientId);
        $data = $this->getRequestData($cacheFile);

        return $data['start_time'] + $this->window;
    }

    /**
     * Reset rate limit for client
     */
    public function reset(): void
    {
        $cacheFile = self::getCacheFile($this->clientId);
        
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        log_info('Rate limit reset', ['client_id' => $this->clientId]);
    }

    /**
     * Get request data from cache file
     */
    private function getRequestData(string $cacheFile): array
    {
        if (!file_exists($cacheFile)) {
            return [
                'count' => 0,
                'start_time' => time(),
            ];
        }

        $data = json_decode(file_get_contents($cacheFile), true);
        return $data ?: [
            'count' => 0,
            'start_time' => time(),
        ];
    }

    /**
     * Save request data to cache file
     */
    private function saveRequestData(string $cacheFile, array $data): void
    {
        file_put_contents($cacheFile, json_encode($data), LOCK_EX);
    }

    /**
     * Get cache file path for client
     */
    private static function getCacheFile(string $clientId): string
    {
        $hash = hash('sha256', $clientId);
        return self::CACHE_DIR . '/' . $hash . '.json';
    }

    /**
     * Cleanup old cache files
     */
    public static function cleanup(int $maxAge = 3600): void
    {
        if (!is_dir(self::CACHE_DIR)) {
            return;
        }

        $now = time();
        $files = glob(self::CACHE_DIR . '/*.json');

        foreach ($files as $file) {
            if ($now - filemtime($file) > $maxAge) {
                unlink($file);
            }
        }

        log_debug('Rate limit cache cleaned up', ['removed_files' => count($files)]);
    }

    /**
     * Middleware for rate limiting
     */
    public static function middleware(int $limit = null, int $window = 60): void
    {
        $limiter = new self(null, $limit, $window);

        if (!$limiter->isAllowed()) {
            header('HTTP/1.1 429 Too Many Requests');
            header('Content-Type: application/json');
            header('Retry-After: ' . ($limiter->getResetTime() - time()));
            http_response_code(429);

            echo json_encode([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $limiter->getResetTime() - time(),
            ]);

            exit;
        }

        // Add rate limit headers to response
        header('X-RateLimit-Limit: ' . $limiter->limit);
        header('X-RateLimit-Remaining: ' . $limiter->getRemaining());
        header('X-RateLimit-Reset: ' . $limiter->getResetTime());
    }
}
?>
