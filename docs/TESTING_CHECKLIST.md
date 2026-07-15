# Testing Checklist

Use this checklist after changing order, payment, menu, or QR behavior.

## Customer Ordering

- Open `customer/order.php?table=1`.
- Confirm the table badge and table input show table 1.
- Add one item to the cart.
- Increase and decrease quantity.
- Remove an item from the cart.
- Confirm subtotal and total update after each cart action.
- Submit an empty cart and confirm validation appears.
- Submit an order with a customer name and valid table number.
- Confirm the payment modal opens after a successful order.
- Simulate payment success and confirm the cart clears.
- Place another order and simulate payment failure.
- Retry the failed payment and confirm success can still be selected.

## Admin Dashboard

- Open `admin/dashboard.php`.
- Confirm orders load newest first.
- Filter by each order status.
- Change an order status and confirm the row updates after reload.
- Change a payment status and confirm the row updates after reload.
- Leave the dashboard open, place a new order, and confirm auto-refresh detects it.

## Menu Management

- Open `admin/menu.php`.
- Add a product with valid image, category, price, stock, and description.
- Confirm the new product appears in the admin list.
- Confirm the new product appears on the customer menu.
- Set stock to zero and confirm the product is shown as unavailable.
- Restore stock and confirm the product can be ordered again.

## QR Generator

- Open `admin/qr_generator.php`.
- Enter a valid table number.
- Confirm generation when prompted.
- Confirm the preview table number and QR target URL update.
- Try printing before confirming a table and confirm validation appears.
