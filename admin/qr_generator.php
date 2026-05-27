<?php
// admin/qr_generator.php
// Determine the base URL dynamically so that the QR code automatically works on the current network IP / host
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$baseDir = str_replace('/admin', '/customer/order.php', dirname($_SERVER['PHP_SELF']));
$customerUrlPattern = $protocol . $host . $baseDir;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table QR Code Generator - Gourmet Express</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .preview-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
        }
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0;
                padding: 0;
            }
            .admin-header, 
            .no-print,
            .toast-container,
            button,
            footer {
                display: none !important;
            }
            .container-fluid, .row, .col-lg-5, .col-lg-7 {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .print-card-outer {
                border: 2px dashed #bbb !important;
                margin: 40px auto !important;
                box-shadow: none !important;
                background: #ffffff !important;
                color: #000000 !important;
                color-scheme: light;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="admin-body">

    <!-- Top Admin Header -->
    <header class="admin-header sticky-top py-3 no-print">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                <i class="bi bi-shield-lock-fill text-primary me-2 fs-4"></i>
                <a href="dashboard.php" class="text-decoration-none"><span class="fw-bold text-dark tracking-wide fs-5">GOURMET<span class="text-primary">ADMIN</span></span></a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold fs-7">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
                <a href="../customer/order.php" target="_blank" class="btn btn-warning rounded-pill px-3 py-1.5 fw-semibold fs-7">
                    <i class="bi bi-shop me-1"></i> Customer Menu
                </a>
            </div>
        </div>
    </header>

    <div class="container my-5">
        <div class="row g-5">
            <!-- Left Side: Controls -->
            <div class="col-lg-5 no-print">
                <div class="card border rounded-4 bg-white text-dark shadow-sm p-4">
                    <h4 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-qr-code text-primary me-2"></i>QR Generator
                    </h4>
                    <p class="text-muted fs-7 mb-4">
                        Generate QR codes for your tables. Print the generated cards and place them on the corresponding tables.
                    </p>

                    <div class="mb-3">
                        <label for="tableInput" class="form-label text-muted fs-7">Table Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border"><i class="bi bi-tag-fill text-primary"></i></span>
                            <input type="number" class="form-control bg-light border text-dark" id="tableInput" value="1" min="1">
                        </div>
                        <small class="text-secondary fs-8 mt-1 d-block">Set the table number, then confirm to generate the final QR code.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted fs-7">Configured Base URL</label>
                        <input type="text" class="form-control bg-light border text-muted fs-7" id="baseUrlInput" value="<?php echo htmlspecialchars($customerUrlPattern); ?>" readonly>
                        <small class="text-secondary fs-8 mt-1 d-block"><i class="bi bi-info-circle me-1"></i>Base URL dynamically points to client ordering endpoint.</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success py-3 rounded-pill fw-bold" onclick="confirmTableNumber()">
                            <i class="bi bi-check-circle-fill me-2"></i> Confirm Table & Generate QR
                        </button>
                        <button class="btn btn-warning py-3 rounded-pill fw-bold" onclick="printCard()">
                            <i class="bi bi-printer-fill me-2"></i> Print Table Card
                        </button>
                    </div>

                    <div class="alert alert-info mt-4 mb-0 rounded-3 fs-7" id="qr-confirmation-message">
                        No table confirmed yet.
                    </div>
                </div>
            </div>

            <!-- Right Side: Live Table Card Preview -->
            <div class="col-lg-7 d-flex justify-content-center">
                <div class="print-card-outer card rounded-4 text-center shadow-sm bg-white text-dark" style="width: 350px; border: 1px solid var(--border-color) !important;">
                    
                    <!-- Restaurant Branding -->
                    <div class="brand-logo mt-5 mb-4 text-dark">
                        <i class="bi bi-egg-fried text-primary me-2 fs-2"></i>
                        <span class="fw-bold text-dark tracking-wide fs-3">GOURMET<span class="text-primary">EXPRESS</span></span>
                    </div>

                    <!-- Scan Message -->
                    <h5 class="fw-bold mb-1 text-dark">SCAN TO ORDER</h5>
                    <p class="text-muted fs-7 px-4 mb-4">View our menu, customize items, and pay from your seat.</p>

                    <!-- QR Display Frame -->
                    <div class="d-flex justify-content-center mb-4">
                        <div class="qr-print-frame bg-white p-3 rounded-4 border d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; border-color: var(--border-color) !important;">
                            <div id="qrcode-canvas"></div>
                        </div>
                    </div>

                    <!-- Table Number Shield -->
                    <div class="table-shield-banner text-white py-2.5 mb-5 shadow-sm fw-bold tracking-wider fs-5">
                        TABLE <span id="preview-table-num">--</span>
                    </div>

                    <p class="text-muted fs-8 px-4 mb-3" id="confirmed-url-text">Confirm a table number to generate the QR target URL.</p>

                    <!-- Footer Details -->
                    <p class="text-muted fs-8 pb-3 mb-0">Powered by Gourmet Express Ordering System</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to manage dynamic QR rendering -->
    <script>
        let confirmedTableNumber = null;

        document.addEventListener('DOMContentLoaded', () => {
            showQrPlaceholder();

            document.getElementById('tableInput').addEventListener('keydown', event => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    confirmTableNumber();
                }
            });
        });

        function showQrPlaceholder() {
            const canvasContainer = document.getElementById('qrcode-canvas');
            canvasContainer.innerHTML = `
                <div class="text-muted fs-8 text-center px-2">
                    Confirm table first
                </div>
            `;
        }

        function getRequestedTableNumber() {
            const tableNumInput = document.getElementById('tableInput');
            let tableNum = parseInt(tableNumInput.value);

            if (isNaN(tableNum) || tableNum <= 0) {
                return null;
            }

            return tableNum;
        }

        function confirmTableNumber() {
            const tableNum = getRequestedTableNumber();

            if (!tableNum) {
                alert("Please enter a valid table number before generating the QR code.");
                return;
            }

            const confirmed = confirm("Generate QR code for Table " + tableNum + "?");
            if (!confirmed) {
                return;
            }

            confirmedTableNumber = tableNum;
            updateQrCode(confirmedTableNumber);
        }

        function updateQrCode(tableNum) {
            document.getElementById('preview-table-num').innerText = tableNum;

            // Generate full URL payload
            const baseUrl = document.getElementById('baseUrlInput').value;
            const fullTargetUrl = baseUrl + "?table=" + tableNum;
            document.getElementById('confirmed-url-text').innerText = fullTargetUrl;

            const canvasContainer = document.getElementById('qrcode-canvas');
            const qrSrc = "../api/qr.php?size=170&data=" + encodeURIComponent(fullTargetUrl);
            canvasContainer.innerHTML = '<img src="' + qrSrc + '" width="170" height="170" alt="QR code for Table ' + tableNum + '">';

            document.getElementById('qr-confirmation-message').className = "alert alert-success mt-4 mb-0 rounded-3 fs-7";
            document.getElementById('qr-confirmation-message').innerText = "Confirmed: QR code generated for Table " + tableNum + ".";
        }

        // Print Card
        function printCard() {
            if (!confirmedTableNumber) {
                alert("Please confirm a table number before printing the QR card.");
                return;
            }

            window.print();
        }
    </script>
</body>
</html>
