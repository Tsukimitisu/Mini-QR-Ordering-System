<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gourmet Express Portal</title>
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
            padding: 2rem 1rem;
        }
        .portal-card {
            border: 1px solid var(--border-color) !important;
            max-width: 850px;
            width: 100%;
        }
        .flow-step {
            position: relative;
            padding-left: 45px;
        }
        .flow-number {
            position: absolute;
            left: 0;
            top: 0;
            width: 30px;
            height: 30px;
            background: var(--primary-color);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
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
    </style>
</head>
<body>

    <div class="portal-container">
        <div class="card portal-card rounded-4 p-4 p-md-5 bg-white text-dark shadow-sm">
            
            <!-- Branding Header -->
            <div class="text-center mb-5">
                <div class="brand-logo mb-3 justify-content-center">
                    <i class="bi bi-egg-fried text-primary me-2 fs-1"></i>
                    <h1 class="fw-bold text-dark tracking-wide mb-0 display-6">GOURMET<span class="text-primary">EXPRESS</span></h1>
                </div>
                <p class="text-muted">Complete Mini QR Ordering System Prototype</p>
                <div class="badge border text-secondary px-3 py-1.5 rounded-pill fs-8">
                    <i class="bi bi-code-slash me-1"></i> PHP + MySQL + Node.js
                </div>
            </div>

            <!-- Portal Routes -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="h-100 p-4 rounded-4 bg-light border d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle p-2.5 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-phone fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-0 text-dark">Customer App</h4>
                        </div>
                        <p class="text-muted fs-7 flex-grow-1">
                            Simulate scanning a tabletop QR code. Browse the menu, manage items in the cart, place orders, and simulate payment decisions.
                        </p>
                        <a href="customer/order.php?table=1" class="btn btn-portal-primary py-2.5 rounded-pill mt-3">
                            <i class="bi bi-qr-code-scan me-2"></i> Open Menu (Table 1)
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="h-100 p-4 rounded-4 bg-light border d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-white text-primary rounded-circle p-2.5 me-3 border d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-speedometer2 fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-0 text-dark">Admin Panel</h4>
                        </div>
                        <p class="text-muted fs-7 flex-grow-1">
                            Access order management dashboard. Track kitchen stats, filter orders, update preparation and payment statuses, and print table QR cards.
                        </p>
                        <a href="admin/dashboard.php" class="btn btn-portal-secondary py-2.5 rounded-pill mt-3">
                            <i class="bi bi-shield-lock me-2"></i> Open Admin Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- How it Works Flowchart Section -->
            <div class="border-top pt-5">
                <h5 class="fw-bold text-dark mb-4 text-center">Prototype Workflow</h5>
                <div class="row g-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="flow-step">
                            <div class="flow-number">1</div>
                            <h6 class="fw-bold text-dark mb-1">Scan QR</h6>
                            <p class="text-muted fs-8 mb-0">Navigate to customer order page with table query parameter.</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="flow-step">
                            <div class="flow-number">2</div>
                            <h6 class="fw-bold text-dark mb-1">Place Order</h6>
                            <p class="text-muted fs-8 mb-0">Browse meals, adjust cart quantities, and submit details.</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="flow-step">
                            <div class="flow-number">3</div>
                            <h6 class="fw-bold text-dark mb-1">Pay Mock</h6>
                            <p class="text-muted fs-8 mb-0">Simulate success or failure gateway response outcomes.</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="flow-step">
                            <div class="flow-number">4</div>
                            <h6 class="fw-bold text-dark mb-1">Manage</h6>
                            <p class="text-muted fs-8 mb-0">Track kitchen metrics, update order stages, and print cards.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Setup Note -->
            <div class="text-center text-muted mt-5 pt-3 border-top fs-8">
                <i class="bi bi-info-circle me-1"></i> Make sure to import the database file <code class="text-primary fw-semibold">database/mini_qr_ordering_db.sql</code> into phpMyAdmin before running.
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
