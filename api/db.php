<?php
// api/db.php

$host = 'localhost';
$db   = 'mini_qr_ordering_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$timeout = 10; // seconds

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => $timeout,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);

     $columnStmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'stock_quantity'");
     if (!$columnStmt->fetch()) {
         $pdo->exec("ALTER TABLE products ADD COLUMN stock_quantity INT NOT NULL DEFAULT 20 COMMENT 'Available stock count' AFTER availability_status");
         $pdo->exec("UPDATE products SET stock_quantity = 0 WHERE availability_status = 0");
         error_log('Database: stock_quantity column added to products table');
     }
     
     // Verify recommended indexes exist for optimal performance
     // Recommended indexes:
     // - CREATE INDEX idx_orders_status ON orders(order_status);
     // - CREATE INDEX idx_orders_payment_status ON orders(payment_status);
     // - CREATE INDEX idx_orders_created ON orders(created_at DESC);
     // - CREATE INDEX idx_order_items_order_id ON order_items(order_id);
     // - CREATE INDEX idx_products_category ON products(category);
} catch (\PDOException $e) {
     error_log('Database connection failed: ' . $e->getMessage());

     // Return JSON error response if connection fails
     header('Content-Type: application/json');
     header('X-Content-Type-Options: nosniff');
     http_response_code(500);
     echo json_encode([
         'success' => false,
         'message' => 'Database connection failed. Please check the server configuration.'
     ], JSON_UNESCAPED_SLASHES);
     exit;
}
?>
