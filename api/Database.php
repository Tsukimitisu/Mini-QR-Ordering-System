<?php
/**
 * Database - Database connection and query helper utility
 * Provides methods for common database operations and connection pooling
 */

require_once __DIR__ . '/Logger.php';

class Database
{
    private static $instance = null;
    private $pdo = null;
    private $lastError = '';
    private $queryCount = 0;

    private function __construct()
    {
        $this->connect();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establish database connection
     */
    private function connect(): void
    {
        try {
            require __DIR__ . '/../config.php';

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => DB_TIMEOUT,
            ];

            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            log_info('Database connection established', ['host' => DB_HOST, 'database' => DB_NAME]);
        } catch (\PDOException $e) {
            $this->lastError = $e->getMessage();
            log_error('Database connection failed', ['error' => $this->lastError]);
            throw $e;
        }
    }

    /**
     * Get PDO instance
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute a prepared statement
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $this->queryCount++;

            log_debug('Query executed', [
                'query' => $sql,
                'params' => $params,
                'count' => $this->queryCount,
            ]);

            return $stmt;
        } catch (\PDOException $e) {
            $this->lastError = $e->getMessage();
            log_error('Query execution failed', [
                'query' => $sql,
                'error' => $this->lastError,
            ]);
            throw $e;
        }
    }

    /**
     * Fetch single row as associative array
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch() ?: null;
    }

    /**
     * Fetch all rows as associative array
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Insert a record and return insert ID
     */
    public function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => '?', $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(',', $columns),
            implode(',', $placeholders)
        );

        $this->query($sql, array_values($data));
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update a record
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $updates = array_map(fn($col) => "$col = ?", array_keys($data));

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(',', $updates),
            $where
        );

        $params = array_merge(array_values($data), $whereParams);
        $stmt = $this->query($sql, $params);

        return $stmt->rowCount();
    }

    /**
     * Delete records
     */
    public function delete(string $table, string $where, array $whereParams = []): int
    {
        $sql = sprintf('DELETE FROM %s WHERE %s', $table, $where);
        $stmt = $this->query($sql, $whereParams);
        return $stmt->rowCount();
    }

    /**
     * Count records
     */
    public function count(string $table, string $where = '', array $whereParams = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM $table";

        if ($where) {
            $sql .= " WHERE $where";
        }

        $result = $this->fetchOne($sql, $whereParams);
        return (int)($result['count'] ?? 0);
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
        log_debug('Transaction started');
    }

    /**
     * Commit transaction
     */
    public function commit(): void
    {
        $this->pdo->commit();
        log_debug('Transaction committed');
    }

    /**
     * Rollback transaction
     */
    public function rollback(): void
    {
        $this->pdo->rollback();
        log_debug('Transaction rolled back');
    }

    /**
     * Get last error
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * Get query count
     */
    public function getQueryCount(): int
    {
        return $this->queryCount;
    }

    /**
     * Check connection health
     */
    public function ping(): bool
    {
        try {
            $this->pdo->query('SELECT 1');
            return true;
        } catch (\PDOException $e) {
            log_error('Database ping failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
?>
