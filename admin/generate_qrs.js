// admin/generate_qrs.js
const fs = require('fs');
const path = require('path');
const QRCode = require('qrcode');

// Configuration
const targetTables = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
const baseUrl = 'http://localhost/Mini-Ordering-System/customer/order.php';
const outputFolder = path.join(__dirname, '..', 'assets', 'images', 'qrcodes');

// Ensure target directory exists
if (!fs.existsSync(outputFolder)) {
    fs.mkdirSync(outputFolder, { recursive: true });
    console.log(`Created output directory: ${outputFolder}`);
}

console.log('--- Batch QR Code Generation Starting ---');

async function generateQRCodes() {
    for (const tableNum of targetTables) {
        const targetUrl = `${baseUrl}?table=${tableNum}`;
        const fileName = `table_${tableNum}.png`;
        const filePath = path.join(outputFolder, fileName);

        try {
            // Generate QR Code with standard branding colors (e.g. dark and light padding)
            await QRCode.toFile(filePath, targetUrl, {
                color: {
                    dark: '#000000',  // Black QR
                    light: '#FFFFFF'  // White background
                },
                width: 500,
                margin: 2
            });
            console.log(`[Success] Generated QR for Table ${tableNum} -> ${fileName}`);
        } catch (err) {
            console.error(`[Error] Failed to generate QR for Table ${tableNum}:`, err);
        }
    }
    console.log('--- Batch QR Code Generation Completed ---');
    console.log(`Generated QR codes are stored in: ${outputFolder}`);
}

generateQRCodes();
