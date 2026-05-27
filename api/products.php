<?php
// api/products.php
require_once 'db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Fetch all products, sorted by category and then by id
    $stmt = $pdo->query("SELECT * FROM products ORDER BY category, id");
    $products = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $products
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch products: ' . $e->getMessage()
    ]);
}
?>
