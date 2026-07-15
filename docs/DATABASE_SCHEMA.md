# Database Schema

Database name: `mini_qr_ordering_db`

The schema is defined in `database/mini_qr_ordering_db.sql`.

## Tables

### `products`

Stores customer-facing menu items.

Important fields:

- `product_name`: Menu item display name
- `description`: Optional menu item description
- `price`: Unit price used during checkout
- `image`: Image filename under `assets/images`
- `category`: Menu grouping label
- `availability_status`: `1` for available, `0` for out of stock
- `stock_quantity`: Current stock count

### `orders`

Stores each customer checkout.

Important fields:

- `customer_name`: Customer display name
- `table_number`: Table attached to the order
- `total_amount`: Final checkout total
- `order_status`: Kitchen workflow status
- `payment_status`: Payment workflow status
- `payment_result`: Mock gateway result

### `order_items`

Stores line items for each order.

Important fields:

- `order_id`: Parent order
- `product_id`: Product selected at checkout
- `product_name`: Product name snapshot
- `quantity`: Purchased quantity
- `price`: Unit price snapshot
- `subtotal`: Quantity multiplied by unit price

`order_items.order_id` cascades on delete when an order is removed.

### `tables`

Stores known restaurant table numbers and their active state.

The current QR generator builds QR URLs dynamically and does not require `qr_code_path` to be populated.

## Seed Data

The seed inserts five sample menu items and five active tables. Re-importing the SQL into a database that already contains those explicit ids can create duplicate key conflicts unless the database is reset first.
