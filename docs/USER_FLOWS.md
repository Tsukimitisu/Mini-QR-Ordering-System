# User Flows

## Customer Order Flow

1. Customer opens `customer/order.php?table={number}` from a QR code.
2. PHP loads products from MySQL and groups them by category.
3. Customer adds available products to the browser cart.
4. JavaScript stores the cart in `localStorage`.
5. Customer submits name, table number, and cart items to `api/orders.php`.
6. The API validates input, checks product stock, creates the order, inserts order items, and decrements stock.
7. The payment modal opens with a mock payment choice.
8. Payment simulation updates `api/update_payment_status.php`.

## Admin Order Flow

1. Staff opens `admin/dashboard.php`.
2. PHP loads order totals, paid totals, and the order list.
3. Staff filters orders by status when needed.
4. Staff changes order status from the dropdown.
5. JavaScript posts the update to `api/update_order_status.php`.
6. The dashboard reloads after a successful update.
7. Background polling checks `api/orders.php` for new or changed orders.

## Menu Management Flow

1. Staff opens `admin/menu.php`.
2. Staff adds a product with text fields, price, stock, category, availability, and an image.
3. PHP validates field lengths, price, stock, MIME type, and upload size.
4. The product is inserted into MySQL.
5. Staff can update stock and availability from the current menu table.
6. Customer menu availability reflects the stored stock state.

## QR Generation Flow

1. Staff opens `admin/qr_generator.php`.
2. The page detects the current host and customer endpoint.
3. Staff enters a table number from 1 to 999.
4. The page generates an SVG QR URL through `api/qr.php`.
5. Staff prints the table card.
