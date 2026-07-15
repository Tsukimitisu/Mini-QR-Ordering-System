# Local Setup

This project is designed to run from XAMPP with Apache, PHP, MySQL, and phpMyAdmin.

## Requirements

- XAMPP with Apache and MySQL enabled
- PHP PDO MySQL extension
- A browser that can access `http://localhost`

## Install Steps

1. Copy this repository to `C:\xampp\htdocs\Mini-Ordering-System`.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin/`.
4. Import `database/mini_qr_ordering_db.sql`.
5. Open `http://localhost/Mini-Ordering-System/`.

## Local Routes

- Portal: `http://localhost/Mini-Ordering-System/`
- Customer menu: `http://localhost/Mini-Ordering-System/customer/order.php?table=1`
- Admin dashboard: `http://localhost/Mini-Ordering-System/admin/dashboard.php`
- Menu management: `http://localhost/Mini-Ordering-System/admin/menu.php`
- QR generator: `http://localhost/Mini-Ordering-System/admin/qr_generator.php`

## Database Defaults

The default connection in `api/db.php` uses:

- Host: `localhost`
- Database: `mini_qr_ordering_db`
- User: `root`
- Password: blank

Update those values only for a local environment that uses different MySQL credentials.
