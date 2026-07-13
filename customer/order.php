<?php
// customer/order.php
require_once '../api/db.php';

// Get table number from URL query parameter
$tableNumber = isset($_GET['table']) ? intval($_GET['table']) : '';

// Fetch products from database
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY category, id");
    $products = $stmt->fetchAll();
    
    // Group products by category
    $menu = [];
    foreach ($products as $product) {
        $menu[$product['category']][] = $product;
    }
} catch (\PDOException $e) {
    $menu = [];
    $error = "Unable to load menu. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gourmet Express - Menu & Order</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <a class="skip-link" href="#main-content">Skip to main content</a>

    <!-- Header / Navbar -->
    <header class="menu-header sticky-top py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                <i class="bi bi-egg-fried text-primary me-2 fs-4"></i>
                <span class="fw-bold text-dark tracking-wide fs-5">GOURMET<span class="text-primary">EXPRESS</span></span>
            </div>
            <div class="table-badge px-3 py-1.5 rounded-pill text-dark border bg-light" aria-label="Current table number">
                <i class="bi bi-tag-fill text-primary me-1"></i>
                <span class="fw-semibold">Table <?php echo $tableNumber ? $tableNumber : 'Not Specified'; ?></span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main id="main-content" class="container my-4">
        <div class="row g-4">
            <!-- Left Side: Menu Items -->
            <div class="col-lg-8">
                <!-- Welcome Banner -->
                <div class="welcome-banner p-4 mb-4 rounded-4 border bg-white">
                    <span class="badge bg-primary text-white mb-2 px-3 py-1.5 rounded-pill fw-semibold uppercase-tag">Welcome</span>
                    <h2 class="fw-bold text-dark mb-1">Freshly Prepared Order</h2>
                    <p class="mb-0 text-muted">Select your favorite dishes, customize your quantities, and order from your seat.</p>
                </div>

                <!-- Category Filters (Horizontal Scrollable) -->
                <div class="category-nav d-flex gap-2 overflow-auto pb-3 mb-4 scrollbar-none" aria-label="Menu categories">
                    <button type="button" class="btn btn-category active text-nowrap rounded-pill px-4" onclick="filterCategory('all')">All Menu</button>
                    <?php foreach (array_keys($menu) as $cat): ?>
                        <button type="button" class="btn btn-category text-nowrap rounded-pill px-4" onclick="filterCategory(<?php echo json_encode($cat, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>)">
                            <?php echo htmlspecialchars($cat); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Menu Sections -->
                <?php if (empty($menu)): ?>
                    <div class="alert alert-danger rounded-4 p-4 border" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                        <span><?php echo isset($error) ? $error : 'No menu items available.'; ?></span>
                    </div>
                <?php else: ?>
                    <?php foreach ($menu as $category => $items): ?>
                        <div class="menu-section mb-5" data-category="<?php echo htmlspecialchars($category); ?>">
                            <h3 class="category-title mb-4 pb-2 border-bottom fw-bold text-dark">
                                <span class="category-title-bar"></span>
                                <?php echo htmlspecialchars($category); ?>
                            </h3>
                            <div class="row g-4">
                                <?php foreach ($items as $item): 
                                    $productId = intval($item['id']);
                                    $productPrice = number_format((float) $item['price'], 2, '.', '');
                                    $productNameJson = json_encode($item['product_name'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                                    $productImageJson = json_encode('../assets/images/' . $item['image'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                                    $stockQuantity = isset($item['stock_quantity']) ? intval($item['stock_quantity']) : 0;
                                    $isAvailable = intval($item['availability_status']) === 1 && $stockQuantity > 0;
                                ?>
                                    <div class="col-md-6">
                                        <div class="card product-card h-100 rounded-4 overflow-hidden border bg-white <?php echo !$isAvailable ? 'out-of-stock-card' : ''; ?>">
                                            <div class="position-relative overflow-hidden product-image-wrapper">
                                                <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top product-img" alt="<?php echo htmlspecialchars($item['product_name']); ?>" loading="lazy" decoding="async">
                                                <?php if (!$isAvailable): ?>
                                                    <div class="out-of-stock-overlay d-flex align-items-center justify-content-center">
                                                        <span class="badge bg-danger text-white fs-6 px-3 py-2 rounded-pill shadow-sm">Sold Out</span>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="price-tag shadow-sm fw-semibold px-3 py-1 rounded-pill">
                                                    &#8369;<?php echo number_format($item['price'], 2); ?>
                                                </span>
                                            </div>
                                            <div class="card-body d-flex flex-column p-4">
                                                <h5 class="card-title fw-bold text-dark mb-2"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                                <p class="card-text text-muted mb-3 flex-grow-1 fs-6"><?php echo htmlspecialchars($item['description']); ?></p>
                                                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-light-subtle">
                                                    <span class="text-muted fs-7">
                                                        <i class="bi <?php echo $isAvailable ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger'; ?> me-1"></i>
                                                        <?php echo $isAvailable ? 'Stock: ' . $stockQuantity : 'Unavailable'; ?>
                                                    </span>
                                                    <?php if ($isAvailable): ?>
                                                        <button class="btn btn-warning btn-sm rounded-pill px-3 py-2 fw-semibold add-to-cart-btn" 
                                                                onclick="addToCart(<?php echo $productId; ?>, <?php echo $productNameJson; ?>, <?php echo $productPrice; ?>, <?php echo $productImageJson; ?>, <?php echo $stockQuantity; ?>)">
                                                            <i class="bi bi-plus-lg me-1"></i> Add
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-secondary btn-sm rounded-pill px-3 py-2 fw-semibold" disabled>
                                                            Out of stock
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Right Side: Floating / Checkout Cart -->
            <div class="col-lg-4">
                <div class="checkout-panel sticky-panel rounded-4 p-4 border bg-white text-dark">
                    <h4 class="fw-bold mb-4 d-flex justify-content-between align-items-center text-dark border-bottom pb-3">
                        <span><i class="bi bi-cart3 text-primary me-2"></i>My Cart</span>
                        <span class="badge bg-primary text-white rounded-pill fs-7" id="cart-count" aria-live="polite">0</span>
                    </h4>

                    <!-- Cart Item Container -->
                    <div id="cart-items" class="cart-items-container mb-4 overflow-auto scrollbar-thin" aria-live="polite">
                        <!-- Filled by JS -->
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-basket3 fs-1 text-muted mb-3 d-block"></i>
                            <span>Your cart is empty.</span>
                        </div>
                    </div>

                    <!-- Cart Totals -->
                    <div class="totals-section border-top pt-3 mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold text-dark" id="cart-subtotal" aria-live="polite">&#8369;0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Service Tax (0%)</span>
                            <span class="text-dark">&#8369;0.00</span>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">Total Amount</span>
                            <span class="fw-bold text-primary fs-4" id="cart-total" aria-live="polite">&#8369;0.00</span>
                        </div>
                    </div>

                    <!-- Checkout Fields -->
                    <form id="checkout-form" onsubmit="submitOrder(event)">
                        <div class="mb-3">
                            <label for="customerName" class="form-label text-muted fs-7">Customer Name *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border"><i class="bi bi-person-fill"></i></span>
                                <input type="text" class="form-control bg-light border text-dark" id="customerName" placeholder="Enter your name" maxlength="80" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="tableNumber" class="form-label text-muted fs-7">Table Number *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border"><i class="bi bi-tag-fill"></i></span>
                                <input type="number" class="form-control bg-light border text-dark" id="tableNumber" placeholder="Table #" min="1" max="999" value="<?php echo $tableNumber; ?>" required <?php echo $tableNumber ? 'readonly' : ''; ?>>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 py-3 rounded-pill fw-bold checkout-submit-btn">
                            <i class="bi bi-send-fill me-2"></i> Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Sticky Cart Button (Visible on screens <= 991px) -->
    <div class="mobile-cart-sticky-bar d-lg-none fixed-bottom p-3 border-top shadow-sm">
        <button class="btn btn-warning w-100 py-3 rounded-pill fw-bold d-flex justify-content-between align-items-center" onclick="scrollToCart()">
            <span><i class="bi bi-cart-fill me-2"></i> View Cart & Checkout</span>
            <span class="bg-white text-primary px-3 py-1 rounded-pill fs-7" id="mobile-cart-total" aria-live="polite">&#8369;0.00</span>
        </button>
    </div>

    <!-- Mock Payment Simulation Modal -->
    <div class="modal fade" id="paymentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white text-dark border rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header border-bottom p-4 bg-light">
                    <h5 class="modal-title fw-bold" id="paymentModalLabel">
                        <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>Payment Terminal
                    </h5>
                </div>
                <div class="modal-body p-4 text-center">
                    <!-- Step 1: Processing Screen -->
                    <div id="payment-processing" class="py-4">
                        <div class="spinner-border text-primary mb-4" role="status" style="width: 3.5rem; height: 3.5rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h4 class="fw-bold mb-2">Connecting to Gateway...</h4>
                        <p class="text-muted">Please simulate your payment decision below.</p>
                        <div class="d-grid gap-3 col-10 mx-auto mt-4">
                            <button class="btn btn-success py-3 rounded-3 fw-bold" onclick="simulatePayment('success')">
                                <i class="bi bi-check-circle-fill me-2"></i> Simulate Payment Success
                            </button>
                            <button class="btn btn-danger py-3 rounded-3 fw-bold" onclick="simulatePayment('failed')">
                                <i class="bi bi-x-circle-fill me-2"></i> Simulate Payment Failed
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Payment Success Screen -->
                    <div id="payment-success" class="d-none py-4">
                        <div class="success-icon mb-4">
                            <i class="bi bi-check-circle-fill text-success fs-1 animate-scale"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-2">Payment Successful!</h3>
                        <p class="text-muted">Your order has been received by the kitchen. Table <span id="success-table-number" class="text-dark fw-bold"></span>.</p>
                        <div class="border rounded-3 p-3 text-start bg-light my-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted fs-7">Order Reference:</span>
                                <span class="fw-bold fs-7" id="success-order-ref">#</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fs-7">Amount Paid:</span>
                                <span class="fw-bold text-primary fs-7" id="success-order-amount">&#8369;0.00</span>
                            </div>
                        </div>
                        <button class="btn btn-warning w-100 py-3 rounded-pill fw-bold" onclick="resetSystem()">
                            Order Something Else
                        </button>
                    </div>

                    <!-- Step 3: Payment Failed Screen -->
                    <div id="payment-failed" class="d-none py-4">
                        <div class="failed-icon mb-4">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-1 animate-bounce"></i>
                        </div>
                        <h3 class="fw-bold text-danger mb-2">Payment Declined</h3>
                        <p class="text-muted" id="failed-reason-text">The simulation response was set to failed.</p>
                        <div class="d-grid gap-3 col-10 mx-auto mt-4">
                            <button class="btn btn-warning py-3 rounded-pill fw-bold" onclick="retryPayment()">
                                <i class="bi bi-arrow-clockwise me-2"></i> Try Again
                            </button>
                            <button class="btn btn-link text-muted text-decoration-none" onclick="cancelPaymentSim()">
                                Cancel & Review Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Scripts -->
    <script src="cart.js"></script>
    <script src="payment.js"></script>
</body>
</html>
