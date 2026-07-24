<?php
/**
 * Logger - Centralized logging utility for the application
 * Handles debug, info, warning, and error level logging
 */

class Logger
{
    private static $instance = null;
    private $logFile;
    private $logLevel;
    const LEVELS = ['DEBUG' => 1, 'INFO' => 2, 'WARNING' => 3, 'ERROR' => 4];

    private function __construct()
    {
        require_once __DIR__ . '/config.php';
        
        // Create logs directory if it doesn't exist
        $logDir = dirname(LOG_FILE);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $this->logFile = LOG_FILE;
        $this->logLevel = LOG_LEVEL;
    }

    /**
     * Get singleton instance of Logger
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Log a debug message
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Log an info message
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    /**
     * Log a warning message
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Log an error message
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Main logging method
     */
    private function log(string $level, string $message, array $context = []): void
    {
        // Check if we should log based on level
        if (self::LEVELS[$level] < self::LEVELS[$this->logLevel]) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logEntry = "[$timestamp] $level: $message$contextStr" . PHP_EOL;

        // Rotate log file if it exceeds max size
        if (file_exists($this->logFile) && filesize($this->logFile) > LOG_MAX_SIZE) {
            $this->rotateLogFile();
        }

        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Rotate log file when it exceeds max size
     */
    private function rotateLogFile(): void
    {
        $timestamp = date('Y-m-d-H-i-s');
        $rotatedFile = $this->logFile . '.' . $timestamp;
        rename($this->logFile, $rotatedFile);
        
        // Keep only last 5 rotated files
        $logDir = dirname($this->logFile);
        $files = glob($logDir . '/app.log.*');
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
        
        foreach (array_slice($files, 5) as $file) {
            unlink($file);
        }
    }
}

// Create global helper function
function log_debug(string $message, array $context = []): void
{
    Logger::getInstance()->debug($message, $context);
}

function log_info(string $message, array $context = []): void
{
    Logger::getInstance()->info($message, $context);
}

function log_warning(string $message, array $context = []): void
{
    Logger::getInstance()->warning($message, $context);
}

function log_error(string $message, array $context = []): void
{
    Logger::getInstance()->error($message, $context);
}
?>
