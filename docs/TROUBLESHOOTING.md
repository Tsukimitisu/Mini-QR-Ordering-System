# Troubleshooting

## Database Connection Failed

Check `api/db.php` and confirm:

- MySQL is running in XAMPP.
- The database name is `mini_qr_ordering_db`.
- The configured username and password match the local MySQL account.
- `database/mini_qr_ordering_db.sql` has been imported.

## Menu Is Empty

If the customer menu shows no products:

- Confirm the `products` table contains rows.
- Confirm product images exist in `assets/images`.
- Confirm `availability_status` and `stock_quantity` are set correctly.

Items with zero stock are shown as unavailable.

## Order Submission Fails

Common causes:

- Customer name is blank or longer than 80 characters.
- Table number is outside 1 to 999.
- Cart item quantity is outside 1 to 99.
- The selected product was disabled or depleted before checkout.

The server rechecks stock and availability during checkout, so browser-side cart data can still be rejected.

## Payment Simulation Does Not Update

Open the browser console and check the response from `api/update_payment_status.php`.

Valid combinations are:

- `paid` with `success`
- `failed` with `failed`
- `unpaid` with `null`

## QR Code Does Not Open The Menu

Confirm the generated URL in the QR preview:

- Uses the correct host or local network IP.
- Contains `/customer/order.php`.
- Includes a `table` query parameter.

If scanning from a phone during local testing, the phone must be on the same network as the XAMPP machine and the QR URL must use a reachable LAN IP instead of `localhost`.
