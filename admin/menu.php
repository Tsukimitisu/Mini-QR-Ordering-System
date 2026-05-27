<?php
// admin/menu.php
require_once '../api/db.php';

$errors = [];
$successMessage = isset($_GET['added']) ? 'Menu item added successfully.' : '';
$uploadDir = realpath(__DIR__ . '/../assets/images');

if ($uploadDir === false) {
    $errors[] = 'Image upload directory is missing.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $availabilityStatus = isset($_POST['availability_status']) ? intval($_POST['availability_status']) : 1;
    $imageName = '';

    if ($productName === '') {
        $errors[] = 'Product name is required.';
    }

    if ($category === '') {
        $errors[] = 'Category is required.';
    }

    if (!is_numeric($price) || floatval($price) <= 0) {
        $errors[] = 'Price must be greater than 0.';
    }

    if (!in_array($availabilityStatus, [0, 1], true)) {
        $errors[] = 'Invalid availability status.';
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Product image is required.';
    } elseif ($uploadDir !== false) {
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($_FILES['image']['tmp_name']);

        if (!array_key_exists($mimeType, $allowedTypes)) {
            $errors[] = 'Image must be JPG, PNG, WEBP, or GIF.';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Image must be 2 MB or smaller.';
        } else {
            $safeBaseName = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $productName));
            $safeBaseName = trim($safeBaseName, '-');
            if ($safeBaseName === '') {
                $safeBaseName = 'menu-item';
            }

            $imageName = $safeBaseName . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowedTypes[$mimeType];
            $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $errors[] = 'Failed to upload product image.';
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO products (product_name, description, price, image, category, availability_status)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $productName,
                $description,
                floatval($price),
                $imageName,
                $category,
                $availabilityStatus,
            ]);

            header('Location: menu.php?added=1');
            exit;
        } catch (Exception $e) {
            if ($imageName !== '' && $uploadDir !== false) {
                $uploadedPath = $uploadDir . DIRECTORY_SEPARATOR . $imageName;
                if (is_file($uploadedPath)) {
                    unlink($uploadedPath);
                }
            }
            $errors[] = 'Failed to save menu item: ' . $e->getMessage();
        }
    }
}

try {
    $productStmt = $pdo->query("SELECT * FROM products ORDER BY category, id DESC");
    $products = $productStmt->fetchAll();

    $categoryStmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $products = [];
    $categories = [];
    $errors[] = 'Failed to load menu items: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Gourmet Express</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">

    <header class="admin-header sticky-top py-3">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                <i class="bi bi-shield-lock-fill text-primary me-2 fs-4"></i>
                <a href="dashboard.php" class="text-decoration-none">
                    <span class="fw-bold text-dark tracking-wide fs-5">GOURMET<span class="text-primary">ADMIN</span></span>
                </a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold fs-7">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
                <a href="qr_generator.php" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold fs-7">
                    <i class="bi bi-qr-code me-1"></i> QR Generator
                </a>
                <a href="../customer/order.php" target="_blank" class="btn btn-warning rounded-pill px-3 py-1.5 fw-semibold fs-7">
                    <i class="bi bi-shop me-1"></i> Customer Menu
                </a>
            </div>
        </div>
    </header>

    <main class="container-fluid px-4 my-4">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border rounded-4 bg-white text-dark shadow-sm p-4">
                    <h4 class="fw-bold mb-3">
                        <i class="bi bi-plus-circle-fill text-primary me-2"></i>Add Menu Item
                    </h4>

                    <?php if (!empty($successMessage)): ?>
                        <div class="alert alert-success rounded-3 fs-7">
                            <?php echo htmlspecialchars($successMessage); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger rounded-3 fs-7">
                            <?php foreach ($errors as $error): ?>
                                <div><?php echo htmlspecialchars($error); ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="product_name" class="form-label text-muted fs-7">Product Name</label>
                            <input type="text" class="form-control bg-light border text-dark" id="product_name" name="product_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label text-muted fs-7">Description</label>
                            <textarea class="form-control bg-light border text-dark" id="description" name="description" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label text-muted fs-7">Price</label>
                            <input type="number" class="form-control bg-light border text-dark" id="price" name="price" min="0.01" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label text-muted fs-7">Category</label>
                            <input type="text" class="form-control bg-light border text-dark" id="category" name="category" list="categoryOptions" required>
                            <datalist id="categoryOptions">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                        </div>

                        <div class="mb-3">
                            <label for="availability_status" class="form-label text-muted fs-7">Availability</label>
                            <select class="form-select bg-light border text-dark" id="availability_status" name="availability_status">
                                <option value="1">Available</option>
                                <option value="0">Out of stock</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label text-muted fs-7">Product Image</label>
                            <input type="file" class="form-control bg-light border text-dark" id="image" name="image" accept="image/jpeg,image/png,image/webp,image/gif" required>
                            <small class="text-secondary fs-8 d-block mt-1">Accepted: JPG, PNG, WEBP, GIF. Max size: 2 MB.</small>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 py-3 rounded-pill fw-bold">
                            <i class="bi bi-save-fill me-2"></i>Save Menu Item
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border rounded-4 bg-white text-dark shadow-sm p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
                        <h4 class="fw-bold mb-0">
                            <i class="bi bi-card-list text-primary me-2"></i>Current Menu
                        </h4>
                        <span class="badge border text-dark rounded-pill px-3 py-2 fs-7">
                            <?php echo count($products); ?> items
                        </span>
                    </div>

                    <?php if (empty($products)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                            <span>No menu items yet.</span>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle custom-admin-table mb-0">
                                <thead>
                                    <tr class="text-muted fs-7">
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="rounded-3 border" style="width: 58px; height: 58px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                                <div class="text-muted fs-7 text-truncate" style="max-width: 320px;">
                                                    <?php echo htmlspecialchars($product['description'] ?: 'No description'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge border text-dark rounded-pill px-3 py-1.5">
                                                    <?php echo htmlspecialchars($product['category']); ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold text-primary">
                                                $<?php echo number_format($product['price'], 2); ?>
                                            </td>
                                            <td>
                                                <?php if (intval($product['availability_status']) === 1): ?>
                                                    <span class="badge bg-success text-white rounded-pill px-3 py-1.5">Available</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary text-white rounded-pill px-3 py-1.5">Out of stock</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
