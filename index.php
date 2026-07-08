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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="portal-page">

    <a class="skip-link" href="#main-content">Skip to main content</a>

    <header class="portal-nav">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
            <a href="index.php" class="brand-logo text-decoration-none">
                <i class="bi bi-egg-fried text-primary me-2 fs-4"></i>
                <span class="fw-bold text-dark tracking-wide fs-5">GOURMET<span class="text-primary">EXPRESS</span></span>
            </a>
            <div class="d-flex align-items-center gap-2 portal-nav-actions">
                <a href="customer/order.php?table=1" class="btn btn-portal-secondary" aria-label="Open customer menu for table 1">
                    <i class="bi bi-qr-code-scan me-2"></i>Customer Menu
                </a>
                <a href="admin/dashboard.php" class="btn btn-portal-primary" aria-label="Open admin dashboard">
                    <i class="bi bi-speedometer2 me-2"></i>Admin
                </a>
            </div>
        </div>
    </header>

    <main id="main-content" class="portal-main">
        <section class="portal-hero">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <div class="portal-kicker mb-3">
                            <i class="bi bi-lightning-charge-fill"></i>
                            Restaurant QR Ordering
                        </div>
                        <h1 class="portal-title fw-bold text-dark mb-3">Gourmet Express</h1>
                        <p class="portal-copy text-muted mb-4">
                            Table-side ordering for customers, live order control for staff, and printable QR cards in one PHP and MySQL system.
                        </p>

                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <a href="customer/order.php?table=1" class="btn btn-portal-primary btn-lg" aria-label="Open table one customer menu">
                                <i class="bi bi-phone me-2"></i>Open Table Menu
                            </a>
                            <a href="admin/dashboard.php" class="btn btn-portal-secondary btn-lg" aria-label="Open staff admin dashboard">
                                <i class="bi bi-shield-lock me-2"></i>Open Admin
                            </a>
                        </div>

                        <div class="portal-metrics" aria-label="System sections">
                            <div>
                                <span>5</span>
                                <small>Sample Items</small>
                            </div>
                            <div>
                                <span>3</span>
                                <small>Admin Tools</small>
                            </div>
                            <div>
                                <span>QR</span>
                                <small>Table Access</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="portal-menu-preview" aria-label="Featured menu items">
                            <div class="portal-featured-item portal-featured-large">
                                <img src="assets/images/cheeseburger.png" alt="Classic Cheeseburger">
                                <div>
                                    <span>Featured</span>
                                    <strong>Classic Cheeseburger</strong>
                                </div>
                            </div>
                            <div class="portal-featured-stack">
                                <div class="portal-featured-item">
                                    <img src="assets/images/pizza.png" alt="Pepperoni Pizza">
                                    <div>
                                        <span>Popular</span>
                                        <strong>Pepperoni Pizza</strong>
                                    </div>
                                </div>
                                <div class="portal-featured-item">
                                    <img src="assets/images/iced_tea.png" alt="Sweet Iced Tea">
                                    <div>
                                        <span>Drink</span>
                                        <strong>Sweet Iced Tea</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="portal-routes py-4">
            <div class="container">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="customer/order.php?table=1" class="portal-route text-decoration-none" aria-label="Open customer ordering sample menu">
                            <i class="bi bi-phone"></i>
                            <span>Customer Ordering</span>
                            <small>Table 1 sample menu</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="admin/menu.php" class="portal-route text-decoration-none" aria-label="Open menu management">
                            <i class="bi bi-card-list"></i>
                            <span>Menu Management</span>
                            <small>Products, stock, availability</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="admin/qr_generator.php" class="portal-route text-decoration-none" aria-label="Open QR generator">
                            <i class="bi bi-qr-code"></i>
                            <span>QR Generator</span>
                            <small>Printable table cards</small>
                        </a>
                    </div>
                </div>
                <div class="portal-credit text-center mt-4">
                    Developed by <span class="fw-semibold text-dark">James Andrei N Revilla</span>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
