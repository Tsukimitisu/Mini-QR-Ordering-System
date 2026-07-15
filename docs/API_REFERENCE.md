# API Reference

All API responses are JSON and use this basic shape:

```json
{
  "success": true
}
```

Error responses set `success` to `false` and include a `message` value.

## Products

### `GET api/products.php`

Returns menu products sorted by category and id.

```json
{
  "success": true,
  "data": []
}
```

## Orders

### `GET api/orders.php`

Returns orders with item lines. Pass `status` to filter admin dashboard results.

Allowed status filter values:

- `all`
- `pending`
- `preparing`
- `completed`
- `cancelled`

### `POST api/orders.php`

Creates an order and decrements stock in one database transaction.

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

Validation limits:

- Customer name: 80 characters
- Table number: 1 to 999
- Quantity per product: 1 to 99

## Status Updates

### `POST api/update_order_status.php`

```json
{
  "order_id": 1,
  "order_status": "preparing"
}
```

Allowed order status values:

- `pending`
- `preparing`
- `completed`
- `cancelled`

### `POST api/update_payment_status.php`

```json
{
  "order_id": 1,
  "payment_status": "paid",
  "payment_result": "success"
}
```

Allowed payment status values:

- `unpaid`
- `paid`
- `failed`

Allowed payment result values:

- `success`
- `failed`
- `null`

## QR Image

### `GET api/qr.php`

Generates an SVG QR image.

Example:

```text
api/qr.php?size=170&data=http://localhost/Mini-Ordering-System/customer/order.php?table=1
```
