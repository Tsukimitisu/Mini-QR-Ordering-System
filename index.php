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
        body.portal-page {
            overflow-x: hidden;
        }

        .portal-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.25rem 1rem;
            position: relative;
        }

        .portal-container::before,
        .portal-container::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
            filter: blur(16px);
            opacity: 0.75;
        }

        .portal-container::before {
            width: 260px;
            height: 260px;
            top: 2%;
            left: 2%;
            background: radial-gradient(circle, rgba(217, 119, 6, 0.22), transparent 68%);
        }

        .portal-container::after {
            width: 320px;
            height: 320px;
            right: -2%;
            bottom: -6%;
            background: radial-gradient(circle, rgba(15, 118, 110, 0.18), transparent 68%);
        }

        .portal-card {
            position: relative;
            z-index: 1;
            max-width: 1040px;
            width: 100%;
            border: 1px solid rgba(15, 23, 42, 0.08) !important;
            background: rgba(255, 255, 255, 0.88) !important;
            backdrop-filter: blur(20px);
        }

        .portal-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            padding: 0.55rem 1rem;
            border-radius: 999px;
            background: rgba(217, 119, 6, 0.08);
            border: 1px solid rgba(217, 119, 6, 0.14);
        }

        .portal-title {
            font-size: clamp(2.2rem, 4vw, 4rem);
            line-height: 0.98;
            letter-spacing: 0.02em;
        }

        .portal-copy {
            max-width: 720px;
            margin: 0 auto;
            font-size: 1.02rem;
        }

        .portal-action-card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(245, 248, 252, 0.95));
            transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
        }

        .portal-action-card:hover {
            border-color: rgba(217, 119, 6, 0.22);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
        }

        .portal-action-card::after {
            content: "";
            position: absolute;
            inset: auto -20% -20% auto;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(217, 119, 6, 0.12), transparent 70%);
            pointer-events: none;
        }

        .portal-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
        }

        .btn-portal-primary,
        .btn-portal-secondary {
            min-height: 48px;
        }

        .btn-portal-primary {
            background: linear-gradient(135deg, var(--primary-color), #f59e0b);
            color: #ffffff;
            font-weight: 700;
            border: 0;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 14px 28px rgba(217, 119, 6, 0.24);
        }

        .btn-portal-primary:hover {
            transform: translateY(-2px);
            color: #ffffff;
            box-shadow: 0 18px 34px rgba(217, 119, 6, 0.3);
        }

        .btn-portal-secondary {
            background: rgba(255, 255, 255, 0.92);
            color: var(--text-main);
            border: 1px solid rgba(15, 23, 42, 0.10);
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .btn-portal-secondary:hover {
            transform: translateY(-2px);
            background: #ffffff;
            color: var(--text-main);
            border-color: rgba(15, 23, 42, 0.16);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
        }

        .portal-credit {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        @media (max-width: 767.98px) {
            .portal-container {
                padding: 1.25rem 0.75rem;
            }
        }
    </style>
</head>
<body class="portal-page">

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
