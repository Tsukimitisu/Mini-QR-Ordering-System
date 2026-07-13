<?php
// admin/dashboard.php
require_once '../api/helpers.php';
require_once '../api/db.php';

// Fetch Statistics
try {
    // 1. Total Orders
    $stmtTotal = $pdo->query("SELECT COUNT(*) as total FROM orders");
    $totalOrders = $stmtTotal->fetch()['total'];

    // 2. Pending Orders
    $stmtPending = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE order_status = 'pending'");
    $pendingOrders = $stmtPending->fetch()['total'];

    // 3. Paid Orders
    $stmtPaid = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE payment_status = 'paid'");
    $paidOrders = $stmtPaid->fetch()['total'];

    // 4. Total Sales
    $stmtSales = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid'");
    $totalSales = floatval($stmtSales->fetch()['total'] ?? 0.00);
} catch (\PDOException $e) {
    $totalOrders = $pendingOrders = $paidOrders = 0;
    $totalSales = 0.00;
}

// Fetch Filter status
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : 'all';
if ($statusFilter !== 'all' && !in_array($statusFilter, allowedOrderStatuses(), true)) {
    $statusFilter = 'all';
    $filterWarning = 'Invalid status filter ignored.';
}

// Fetch Orders
try {
    $sql = "SELECT * FROM orders";
    $params = [];
    
    if ($statusFilter !== 'all') {
        $sql .= " WHERE order_status = ?";
        $params[] = $statusFilter;
    }
    
    $sql .= " ORDER BY id DESC"; // Newest first
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
    
    // Attach items to each order
    foreach ($orders as &$order) {
        $itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC");
        $itemStmt->execute([$order['id']]);
        $order['items'] = $itemStmt->fetchAll();
    }
} catch (\PDOException $e) {
    $orders = [];
    $error = "Failed to load orders: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gourmet Express</title>
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
<body class="admin-body">

    <a class="skip-link" href="#main-content">Skip to main content</a>

    <!-- Top Admin Header -->
    <header class="admin-header sticky-top py-3">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                <i class="bi bi-shield-lock-fill text-primary me-2 fs-4"></i>
                <span class="fw-bold text-dark tracking-wide fs-5">GOURMET<span class="text-primary">ADMIN</span></span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="menu.php" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold fs-7" aria-label="Open menu management">
                    <i class="bi bi-card-list me-1"></i> Menu
                </a>
                <a href="qr_generator.php" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold fs-7" aria-label="Open QR generator">
                    <i class="bi bi-qr-code me-1"></i> QR Generator
                </a>
                <a href="../customer/order.php" target="_blank" rel="noopener" class="btn btn-warning rounded-pill px-3 py-1.5 fw-semibold fs-7" aria-label="Open customer menu in a new tab">
                    <i class="bi bi-shop me-1"></i> Customer Menu
                </a>
            </div>
        </div>
    </header>

    <main id="main-content" class="container-fluid px-4 my-4">
        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Orders -->
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card rounded-4 p-3 border bg-white text-dark shadow-sm h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fs-7 fw-medium text-uppercase tracking-wider">Total Orders</span>
                            <h2 class="fw-bold mt-1 mb-0 text-dark"><?php echo $totalOrders; ?></h2>
                        </div>
                        <div class="stat-icon bg-light rounded-3 p-3 text-secondary">
                            <i class="bi bi-receipt fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pending Orders -->
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card rounded-4 p-3 border bg-white text-dark shadow-sm h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fs-7 fw-medium text-uppercase tracking-wider">Pending Orders</span>
                            <h2 class="fw-bold mt-1 mb-0 text-primary"><?php echo $pendingOrders; ?></h2>
                        </div>
                        <div class="stat-icon bg-light rounded-3 p-3 text-primary">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Paid Orders -->
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card rounded-4 p-3 border bg-white text-dark shadow-sm h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fs-7 fw-medium text-uppercase tracking-wider">Paid Orders</span>
                            <h2 class="fw-bold mt-1 mb-0 text-success"><?php echo $paidOrders; ?></h2>
                        </div>
                        <div class="stat-icon bg-light rounded-3 p-3 text-success">
                            <i class="bi bi-currency-dollar fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Sales -->
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card rounded-4 p-3 border bg-white text-dark shadow-sm h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fs-7 fw-medium text-uppercase tracking-wider">Total Sales</span>
                            <h2 class="fw-bold mt-1 mb-0 text-dark">&#8369;<?php echo number_format($totalSales, 2); ?></h2>
                        </div>
                        <div class="stat-icon bg-light rounded-3 p-3 text-dark">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Orders List Section -->
        <div class="card border rounded-4 bg-white text-dark shadow-sm p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-list-stars text-primary me-2"></i>Order Management
                    <span class="badge bg-success text-white rounded-pill ms-2 fs-8">
                        <i class="bi bi-broadcast-pin me-1"></i>Live
                    </span>
                </h4>
                <!-- Filters -->
                <div class="d-flex gap-2 overflow-auto scrollbar-none pb-1">
                    <a href="?status=all" class="btn btn-filter text-nowrap rounded-pill px-3 fs-7 <?php echo $statusFilter === 'all' ? 'active' : ''; ?>">All</a>
                    <a href="?status=pending" class="btn btn-filter text-nowrap rounded-pill px-3 fs-7 <?php echo $statusFilter === 'pending' ? 'active' : ''; ?>">Pending</a>
                    <a href="?status=preparing" class="btn btn-filter text-nowrap rounded-pill px-3 fs-7 <?php echo $statusFilter === 'preparing' ? 'active' : ''; ?>">Preparing</a>
                    <a href="?status=completed" class="btn btn-filter text-nowrap rounded-pill px-3 fs-7 <?php echo $statusFilter === 'completed' ? 'active' : ''; ?>">Completed</a>
                    <a href="?status=cancelled" class="btn btn-filter text-nowrap rounded-pill px-3 fs-7 <?php echo $statusFilter === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
                </div>
            </div>

            <?php if (isset($filterWarning)): ?>
                <div class="alert alert-warning rounded-3" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo htmlspecialchars($filterWarning); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php elseif (empty($orders)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-receipt-cutoff fs-1 d-block mb-3"></i>
                    <span>No orders found for this status.</span>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle custom-admin-table mb-0">
                        <thead>
                            <tr class="text-muted fs-7">
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Table #</th>
                                <th>Ordered Items Details</th>
                                <th>Total Amount</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                    $orderId = intval($order['id']);
                                    $tableNumberDisplay = intval($order['table_number']);
                                ?>
                                <tr id="order-row-<?php echo $orderId; ?>">
                                    <td class="fw-bold text-dark">#<?php echo $orderId; ?></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge border text-dark rounded-pill px-2.5 py-1.5 fw-bold">T-<?php echo $tableNumberDisplay; ?></span>
                                    </td>
                                    <td>
                                        <div class="order-items-summary py-1">
                                            <ul class="list-unstyled mb-0 fs-7">
                                                <?php foreach ($order['items'] as $item): ?>
                                                    <li class="text-muted mb-1">
                                                        <span class="text-dark fw-medium"><?php echo intval($item['quantity']); ?>x</span> 
                                                        <?php echo htmlspecialchars($item['product_name']); ?> 
                                                        <span class="text-secondary">(&#8369;<?php echo number_format((float) $item['price'], 2); ?>)</span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-primary fs-6">
                                        &#8369;<?php echo number_format((float) $order['total_amount'], 2); ?>
                                    </td>
                                    <td>
                                        <!-- Order Status Selector -->
                                        <select class="form-select select-status rounded-pill py-1.5 px-3 fs-7 border" 
                                                data-order-id="<?php echo $orderId; ?>" 
                                                onchange="updateOrderStatus(<?php echo $orderId; ?>, this)">
                                            <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="preparing" <?php echo $order['order_status'] === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                            <option value="completed" <?php echo $order['order_status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        <!-- Payment Status Selector -->
                                        <select class="form-select select-payment rounded-pill py-1.5 px-3 fs-7 border" 
                                                data-order-id="<?php echo $orderId; ?>" 
                                                onchange="updatePaymentStatus(<?php echo $orderId; ?>, this)">
                                            <option value="unpaid" <?php echo $order['payment_status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                                            <option value="paid" <?php echo $order['payment_status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                            <option value="failed" <?php echo $order['payment_status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                                        </select>
                                    </td>
                                    <td class="text-muted fs-7">
                                        <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Bootstrap Toast for alerts -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div id="statusToast" class="toast align-items-center text-dark bg-white border rounded-3 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <i class="bi bi-info-circle-fill text-primary me-2 fs-5" id="toast-icon"></i>
                    <span id="toast-msg">Status updated.</span>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Admin Operations JS -->
    <script>
        let statusToast;
        let dashboardUpdateInProgress = false;
        let lastOrdersSignature = '';
        const dashboardStatusFilter = <?php echo json_encode($statusFilter); ?>;
        const initialOrdersSnapshot = <?php echo json_encode($orders, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            const toastEl = document.getElementById('statusToast');
            statusToast = new bootstrap.Toast(toastEl, { delay: 3000 });
            
            document.querySelectorAll('.select-status').forEach(el => applyStatusColor(el));
            document.querySelectorAll('.select-payment').forEach(el => applyPaymentColor(el));

            lastOrdersSignature = buildOrdersSignature(initialOrdersSnapshot);
            setInterval(checkForDashboardUpdates, 5000);
        });

        function buildOrdersSignature(orders) {
            return JSON.stringify((orders || []).map(order => ({
                id: String(order.id),
                total_amount: String(order.total_amount),
                order_status: String(order.order_status),
                payment_status: String(order.payment_status),
                payment_result: order.payment_result === null ? null : String(order.payment_result),
                updated_at: String(order.updated_at),
                items: (order.items || []).map(item => ({
                    id: String(item.id),
                    product_id: String(item.product_id),
                    quantity: String(item.quantity),
                    subtotal: String(item.subtotal)
                }))
            })));
        }

        function checkForDashboardUpdates() {
            if (dashboardUpdateInProgress || document.hidden) {
                return;
            }

            const activeEl = document.activeElement;
            if (activeEl && (activeEl.classList.contains('select-status') || activeEl.classList.contains('select-payment'))) {
                return;
            }

            const apiUrl = '../api/orders.php?status=' + encodeURIComponent(dashboardStatusFilter);

            fetch(apiUrl, { cache: 'no-store' })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        return;
                    }

                    const nextSignature = buildOrdersSignature(data.data);
                    if (lastOrdersSignature && nextSignature !== lastOrdersSignature) {
                        showToast("Dashboard updated. Refreshing latest orders...", "bi-arrow-repeat text-success");
                        setTimeout(() => {
                            window.location.reload();
                        }, 800);
                    }
                })
                .catch(err => {
                    console.error("Dashboard polling error:", err);
                });
        }

        function updateOrderStatus(orderId, selectEl) {
            const status = selectEl.value;
            selectEl.disabled = true;
            dashboardUpdateInProgress = true;

            fetch('../api/update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId,
                    order_status: status
                })
            })
            .then(res => res.json())
            .then(data => {
                selectEl.disabled = false;
                if (data.success) {
                    applyStatusColor(selectEl);
                    showToast("Order #" + orderId + " status updated to " + status + "!", "bi-check-circle-fill text-success");
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    dashboardUpdateInProgress = false;
                    showToast("Failed: " + data.message, "bi-x-circle-fill text-danger");
                }
            })
            .catch(err => {
                selectEl.disabled = false;
                dashboardUpdateInProgress = false;
                console.error(err);
                showToast("Network error occurred.", "bi-exclamation-triangle-fill text-danger");
            });
        }

        function updatePaymentStatus(orderId, selectEl) {
            const status = selectEl.value;
            selectEl.disabled = true;
            dashboardUpdateInProgress = true;

            let result = null;
            if (status === 'paid') result = 'success';
            if (status === 'failed') result = 'failed';

            fetch('../api/update_payment_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId,
                    payment_status: status,
                    payment_result: result
                })
            })
            .then(res => res.json())
            .then(data => {
                selectEl.disabled = false;
                if (data.success) {
                    applyPaymentColor(selectEl);
                    showToast("Order #" + orderId + " payment updated to " + status + "!", "bi-check-circle-fill text-success");
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    dashboardUpdateInProgress = false;
                    showToast("Failed: " + data.message, "bi-x-circle-fill text-danger");
                }
            })
            .catch(err => {
                selectEl.disabled = false;
                dashboardUpdateInProgress = false;
                console.error(err);
                showToast("Network error occurred.", "bi-exclamation-triangle-fill text-danger");
            });
        }

        function applyStatusColor(el) {
            const val = el.value;
            el.classList.remove('bg-status-pending', 'bg-status-preparing', 'bg-status-completed', 'bg-status-cancelled');
            
            if (val === 'pending') el.classList.add('bg-status-pending');
            else if (val === 'preparing') el.classList.add('bg-status-preparing');
            else if (val === 'completed') el.classList.add('bg-status-completed');
            else if (val === 'cancelled') el.classList.add('bg-status-cancelled');
        }

        function applyPaymentColor(el) {
            const val = el.value;
            el.classList.remove('bg-payment-unpaid', 'bg-payment-paid', 'bg-payment-failed');

            if (val === 'unpaid') el.classList.add('bg-payment-unpaid');
            else if (val === 'paid') el.classList.add('bg-payment-paid');
            else if (val === 'failed') el.classList.add('bg-payment-failed');
        }

        function showToast(message, iconClass) {
            document.getElementById('toast-msg').innerText = message;
            const icon = document.getElementById('toast-icon');
            icon.className = "bi me-2 fs-5 " + iconClass;
            statusToast.show();
        }
    </script>
</body>
</html>
