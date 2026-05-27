# Interview Readiness Guide

## Project Summary

This project is a mini restaurant QR ordering system. It lets a customer open a table ordering page from a QR code, browse products, manage a cart, place an order, and simulate payment. It also includes an admin dashboard for viewing orders and updating order or payment status.

## What To Say In The Interview

You can explain the project like this:

```text
I built a simple QR ordering system for a restaurant. The customer can open the menu through a QR link with a table number, add products to the cart, update quantities, remove items, submit an order, and simulate payment success or failure. On the admin side, staff can view all orders, update order status, update payment status, and generate QR codes for tables.
```

## Evaluation Area: Problem Solving Skills

The problem was divided into smaller flows:

- Customer ordering flow
- Cart management flow
- Backend order creation flow
- Payment simulation flow
- Admin order management flow
- QR generation flow

The important problem solving decision is that the frontend does not control the final price. The cart shows the total for user convenience, but the backend recalculates the total from database prices before saving the order.

This is important because browser data can be changed by users. If the system trusted frontend prices, a user could modify the price before submitting an order.

The order creation also uses a database transaction. This keeps the order and order items consistent. If saving an item fails, the main order is not left incomplete.

The admin dashboard also uses polling to check for order changes every few seconds. This gives the staff a near real-time process without needing a separate WebSocket server.

## Evaluation Area: Code Organization

The project is organized by responsibility:

- `customer` contains customer pages and scripts
- `admin` contains dashboard and QR tools
- `api` contains backend endpoints
- `database` contains the SQL setup file
- `assets` contains CSS and images

This separation makes the system easier to maintain. Customer logic, admin logic, API logic, and database setup are not mixed together.

Important files:

- `customer/order.php` displays the customer menu and checkout form
- `customer/cart.js` handles cart behavior and order submission
- `customer/payment.js` handles mock payment result updates
- `api/orders.php` handles GET orders and POST order creation
- `api/products.php` returns product data
- `api/update_order_status.php` updates order status
- `api/update_payment_status.php` updates payment status
- `admin/dashboard.php` displays and manages orders
- `admin/qr_generator.php` creates browser QR codes
- `admin/generate_qrs.js` generates static QR images
- `database/mini_qr_ordering_db.sql` creates tables and seed data

## Evaluation Area: UI And UX

The customer UI is designed for simple mobile ordering:

- Product cards are easy to scan
- Menu is grouped by category
- Out of stock items are disabled
- Cart updates immediately
- Total amount is always visible
- Mobile users have a sticky cart button
- Payment simulation uses a modal

The admin UI is designed for fast restaurant operations:

- Summary cards show order counts and sales
- Orders are shown in a table
- Staff can update status using dropdowns
- Status filters help focus on active orders
- Payment status can be managed directly
- The dashboard refreshes automatically when order data changes

## Evaluation Area: API Understanding

The system uses simple REST-style endpoints:

- GET `api/products.php` returns products
- GET `api/orders.php` returns orders
- POST `api/orders.php` creates a new order
- POST `api/update_order_status.php` updates order status
- POST `api/update_payment_status.php` updates payment status

The APIs return JSON so JavaScript can read success or error responses.

The backend uses PDO prepared statements. This reduces SQL injection risk.

The backend also validates allowed status values before updating the database. For example, order status must be pending, preparing, completed, or cancelled.

## Evaluation Area: Development Workflow

The development workflow was:

1. Identify required features.

2. Design database tables.

3. Create backend API endpoints.

4. Build customer menu page.

5. Add cart logic.

6. Connect cart submission to the backend.

7. Add mock payment simulation.

8. Build admin dashboard.

9. Add QR generator.

10. Add near real-time admin polling.

11. Add QR table confirmation before generation.

12. Test customer, admin, payment, and QR flows.

13. Fix UI issues and improve documentation.

Manual testing was done through the browser using XAMPP. PHP syntax can be checked with:

```text
php -l file.php
```

## Evaluation Area: Ability To Work Independently

This project shows independent work because it includes the complete end to end flow:

- Database schema
- Seed products
- Backend APIs
- Customer UI
- Cart logic
- Mock payment flow
- Admin dashboard
- QR generation
- Installation documentation
- Interview explanation

It also includes practical backend decisions such as server-side validation, server-side price recalculation, database transactions, and allowed status checking.

## How To Explain The Customer Flow

Say this:

```text
The customer opens the ordering page with a table number in the URL. The page loads products from the database and displays them by category. When the customer adds items, JavaScript stores the cart in localStorage and recalculates totals. When the order is submitted, only product IDs and quantities are sent to the backend. The backend validates the data, reloads product prices from the database, calculates the total, and saves the order and order items.
```

## How To Explain The Admin Flow

Say this:

```text
The admin dashboard loads orders from the database and displays the order items, total amount, order status, and payment status. The admin can update order status or payment status using dropdowns. Those dropdowns send POST requests to backend API files, which validate the values and update the database.
```

You can also say:

```text
The dashboard checks the orders API every few seconds. If it detects a new order or an updated order, it refreshes automatically so the admin can see the latest data without manually reloading the page.
```

## How To Explain The Payment Flow

Say this:

```text
The payment feature is a simulation because the requirement does not need real payment integration. After the order is created, a payment modal appears. The user chooses success or failure. The result is sent to the backend and stored in the order record as payment status and payment result.
```

## How To Explain The QR Feature

Say this:

```text
The QR generator creates a URL pointing to the customer order page with a table number query parameter. For example, table 1 opens customer/order.php?table=1. This allows each table to have its own QR code.
```

You can also say:

```text
The QR generator requires the admin to confirm the table number before generating the QR. This prevents accidentally printing a QR code for the wrong table.
```

## Common Interview Questions And Answers

### Why did you recalculate prices on the backend?

Because frontend data can be modified. The backend should not trust prices sent from the browser. The system only accepts product IDs and quantities, then loads the real prices from the database.

### Why did you use a transaction when saving orders?

An order has a main order record and multiple order item records. A transaction makes sure they are saved together. If one insert fails, the whole order is rolled back.

### Why is the payment only simulated?

The requirement only asks for mock payment success or failure. No real payment gateway is needed.

### What would you improve next?

- Add admin login
- Add product management
- Replace polling with WebSocket updates for stronger real-time behavior
- Add receipt printing
- Add real payment gateway
- Add automated tests
- Add CSRF protection
- Add stronger authentication and authorization

### What is the strongest part of this project?

The strongest part is the complete flow. A customer can order from a QR link, the backend validates and stores the order, payment can be simulated, and admin can manage the order afterward.

### What is one limitation?

There is no admin login yet. For a real restaurant system, admin authentication should be added before deployment.

## Short Final Interview Pitch

```text
This is a PHP and MySQL QR ordering prototype. I separated the project into customer, admin, API, assets, and database folders. The customer can order from a QR link, manage a cart, and simulate payment. The backend validates input, recalculates prices from the database, and stores orders with order items using a transaction. The admin dashboard can view orders and update order or payment status. The system also includes QR generation and setup documentation.
```

## GitHub Safety Note

No GitHub push command has been run for this documentation update. These files are local changes only unless someone manually commits and pushes them later.
