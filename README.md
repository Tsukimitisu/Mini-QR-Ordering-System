# Gourmet Express Mini QR Ordering System

## Project Overview

Gourmet Express is a simple restaurant QR ordering system for a small restaurant. Customers can scan a table QR code, view the menu, add products to a cart, place an order, and simulate payment. Admin users can view orders, update order status, update payment status, and generate QR codes for tables.

Live site:

```text
https://mini-qr-ordering.infinityfreeapp.com/
```

Credits:

```text
Developed by James Andrei N Revilla
```

## Tech Stack

- Frontend: HTML, CSS, JavaScript, Bootstrap 5
- Backend: PHP with PDO
- Database: MySQL
- QR Generation: PHP SVG endpoint
- Local Server: XAMPP Apache and MySQL

## Features

- Display menu products
- Group products by category
- Add item to cart
- Update item quantity
- Remove item from cart
- Compute subtotal and total amount
- Mobile responsive customer page
- Near real-time admin dashboard refresh
- Generate QR code for table ordering page
- Confirm table number before QR generation
- GET products API
- POST order API
- GET orders API
- Products table
- Orders table
- Order items table
- Admin dashboard for viewing orders
- Admin add menu item page
- Admin edit product stock and availability
- Admin order status update
- Admin payment status update
- Product stock validation during checkout
- Peso currency display
- Mock payment success and failure flow

## Folder Structure

```text
Mini-Ordering-System
|-- admin
|   |-- dashboard.php
|   |-- menu.php
|   `-- qr_generator.php
|-- api
|   |-- db.php
|   |-- orders.php
|   |-- products.php
|   |-- qr.php
|   |-- update_order_status.php
|   `-- update_payment_status.php
|-- assets
|   |-- css
|   |   `-- style.css
|   `-- images
|-- customer
|   |-- cart.js
|   |-- order.php
|   `-- payment.js
|-- database
|   `-- mini_qr_ordering_db.sql
|-- index.php
`-- README.md
```

## Installation Guide

### Step 1: Copy Project To XAMPP

Place the project folder here:

```text
C:\xampp\htdocs\Mini-Ordering-System
```

### Step 2: Start XAMPP

Open XAMPP Control Panel and start:

- Apache
- MySQL

### Step 3: Import Database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin/
```

Import this SQL file:

```text
C:\xampp\htdocs\Mini-Ordering-System\database\mini_qr_ordering_db.sql
```

This creates the database:

```text
mini_qr_ordering_db
```

### Step 4: Open The System

Open the portal page:

```text
https://mini-qr-ordering.infinityfreeapp.com/
```

Customer ordering page example:

```text
https://mini-qr-ordering.infinityfreeapp.com/customer/order.php?table=1
```

Admin dashboard:

```text
https://mini-qr-ordering.infinityfreeapp.com/admin/dashboard.php
```

Admin menu management:

```text
https://mini-qr-ordering.infinityfreeapp.com/admin/menu.php
```

QR generator:

```text
https://mini-qr-ordering.infinityfreeapp.com/admin/qr_generator.php
```

## Database

The database SQL file is:

```text
database/mini_qr_ordering_db.sql
```

Main tables:

- products
- orders
- order_items
- tables

Product records include price, image, category, availability, and stock quantity.

## API Endpoints

### GET Products

```text
api/products.php
```

Returns all products from the database.

### GET Orders

```text
api/orders.php
```

Returns all orders with their ordered items.

### POST Order

```text
api/orders.php
```

Creates a new customer order.

Example request:

```json
{
  "customer_name": "John Doe",
  "table_number": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ]
}
```

### POST Update Order Status

```text
api/update_order_status.php
```

Updates order status from the admin dashboard.

Allowed values:

- pending
- preparing
- completed
- cancelled

### POST Update Payment Status

```text
api/update_payment_status.php
```

Updates payment status from the admin dashboard or customer mock payment flow.

Allowed payment status values:

- unpaid
- paid
- failed

Allowed payment result values:

- success
- failed
- null

### GET QR Image

```text
api/qr.php
```

Generates a QR code as an SVG image using PHP.

Example:

```text
api/qr.php?size=170&data=https://mini-qr-ordering.infinityfreeapp.com/customer/order.php?table=1
```

## Testing Guide

### Customer Test

1. Open:

```text
https://mini-qr-ordering.infinityfreeapp.com/customer/order.php?table=1
```

2. Add products to cart.

3. Change quantities.

4. Remove an item.

5. Confirm total amount updates.

6. Enter customer name.

7. Place the order.

8. Simulate payment success.

9. Repeat and simulate payment failure.

### Admin Test

1. Open:

```text
https://mini-qr-ordering.infinityfreeapp.com/admin/dashboard.php
```

2. Confirm the order appears.

3. Change order status.

4. Change payment status.

5. Test status filters.

6. Open the menu management page.

7. Add a new product with name, description, price, stock, category, availability, and image.

8. Confirm the new product appears on the customer menu.

9. Edit product stock and availability from the menu management table.

10. Confirm stock and availability affect the customer menu.

11. Keep the admin dashboard open.

12. Place a new order from the customer page.

13. Confirm the admin dashboard refreshes automatically after the order changes.

### QR Test

1. Open:

```text
https://mini-qr-ordering.infinityfreeapp.com/admin/qr_generator.php
```

2. Enter a table number.

3. Click Confirm Table & Generate QR.

4. Accept the confirmation prompt.

5. Confirm the PHP-generated QR code and table number update.

6. Print the table card if needed.

## Interview Notes

Interview preparation notes are separated into:

```text
INTERVIEW_READINESS.md
```

## GitHub Note

No GitHub push command has been run for this documentation update.
