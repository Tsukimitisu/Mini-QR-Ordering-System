<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gourmet Express Portal</title>
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
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .portal-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
        }
        .portal-card {
            border: 1px solid var(--border-color) !important;
            max-width: 920px;
            width: 100%;
        }
        .portal-eyebrow {
            color: var(--primary-color);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .portal-title {
            font-size: clamp(2rem, 4vw, 3.5rem);
            line-height: 1.05;
        }
        .portal-copy {
            max-width: 680px;
            margin: 0 auto;
        }
        .portal-action-card {
            border: 1px solid var(--border-color);
            background: var(--bg-white);
            transition: border-color 0.2s ease, transform 0.2s ease;
        }
        .portal-action-card:hover {
            border-color: #cbd5e0;
            transform: translateY(-2px);
        }
        .portal-icon {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-portal-primary {
            background-color: var(--primary-color);
            color: #ffffff;
            font-weight: 600;
            border: 0;
            transition: all 0.2s ease;
        }
        .btn-portal-primary:hover {
            transform: translateY(-2px);
            background-color: var(--primary-hover);
            color: #ffffff;
        }
        .btn-portal-secondary {
            background-color: var(--bg-light);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-portal-secondary:hover {
            transform: translateY(-2px);
            background-color: #e2e8f0;
            color: var(--text-main);
        }
        .portal-credit {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <div class="portal-container">
        <div class="card portal-card rounded-4 p-4 p-md-5 bg-white text-dark shadow-sm">
            
            <!-- Branding Header -->
            <div class="text-center mb-5">
                <div class="portal-eyebrow mb-3">Restaurant QR Ordering</div>
                <div class="brand-logo mb-3 justify-content-center">
                    <i class="bi bi-egg-fried text-primary me-2 fs-1"></i>
                    <h1 class="fw-bold text-dark tracking-wide mb-0 portal-title">GOURMET<span class="text-primary">EXPRESS</span></h1>
                </div>
                <p class="text-muted portal-copy mb-4">
                    A PHP and MySQL ordering system for table-based restaurant service. Customers can order from a QR link while staff manage orders, payments, menu items, and QR cards from the admin area.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <span class="badge border text-secondary px-3 py-2 rounded-pill fs-8">PHP</span>
                    <span class="badge border text-secondary px-3 py-2 rounded-pill fs-8">MySQL</span>
                    <span class="badge border text-secondary px-3 py-2 rounded-pill fs-8">Bootstrap</span>
                    <span class="badge border text-secondary px-3 py-2 rounded-pill fs-8">PHP QR</span>
                </div>
            </div>

            <!-- Portal Routes -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="portal-action-card h-100 p-4 rounded-4 d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="portal-icon bg-primary text-white rounded-circle me-3">
                                <i class="bi bi-phone fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">Customer Ordering</h4>
                                <span class="text-muted fs-8">Table 1 sample link</span>
                            </div>
                        </div>
                        <p class="text-muted fs-7 flex-grow-1">
                            Browse available menu items, add products to cart, place an order, and complete the mock payment flow.
                        </p>
                        <a href="customer/order.php?table=1" class="btn btn-portal-primary py-2.5 rounded-pill mt-3">
                            <i class="bi bi-qr-code-scan me-2"></i> Open Customer Menu
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="portal-action-card h-100 p-4 rounded-4 d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="portal-icon bg-light text-primary rounded-circle me-3 border">
                                <i class="bi bi-speedometer2 fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">Admin Dashboard</h4>
                                <span class="text-muted fs-8">Orders, menu, and QR tools</span>
                            </div>
                        </div>
                        <p class="text-muted fs-7 flex-grow-1">
                            View live order activity, update status, manage menu items, and generate confirmed table QR cards.
                        </p>
                        <a href="admin/dashboard.php" class="btn btn-portal-secondary py-2.5 rounded-pill mt-3">
                            <i class="bi bi-shield-lock me-2"></i> Open Admin Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <div class="portal-credit text-center mt-5 pt-4 border-top">
                Developed by <span class="fw-semibold text-dark">James Andrei N Revilla</span>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
