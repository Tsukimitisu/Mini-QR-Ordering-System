<?php
// api/products.php
require_once 'helpers.php';

header('Content-Type: application/json');
sendCorsHeaders('GET, OPTIONS');
sendNoStoreHeaders();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

require_once 'db.php';

try {
    // Fetch all products, sorted by category and then by id
    $stmt = $pdo->query("SELECT * FROM products ORDER BY category, id");
    $products = $stmt->fetchAll();
    
    sendJsonResponse([
        'success' => true,
        'data' => $products
    ]);
} catch (\PDOException $e) {
    sendJsonResponse([
        'success' => false,
        'message' => 'Failed to fetch products: ' . $e->getMessage()
    ], 500);
}
?>
