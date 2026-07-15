# Deployment Notes

This project can be hosted on a PHP and MySQL web host. The live README URL uses InfinityFree, but the same checklist applies to most shared hosts.

## Before Uploading

- Export the local MySQL database or import `database/mini_qr_ordering_db.sql` on the host.
- Update `api/db.php` with the production database host, name, username, and password.
- Confirm `assets/images` is writable only if menu image uploads are needed in production.
- Keep local-only files such as `.env`, logs, and dependency folders out of the upload.

## Web Root

Upload the repository contents so these routes are web-accessible:

- `/index.php`
- `/customer/order.php`
- `/admin/dashboard.php`
- `/admin/menu.php`
- `/admin/qr_generator.php`
- `/api/*.php`

The image and icon paths assume the project is served from a normal web root or a stable subdirectory.

## Post-Deploy Checks

1. Open the portal page.
2. Open a customer URL with `?table=1`.
3. Submit a test order.
4. Confirm the order appears in the admin dashboard.
5. Generate a QR code and scan it from a phone.
6. Add a temporary menu item with an image, then remove it manually if needed.

## Production Hardening

- Add authentication before exposing admin pages publicly.
- Use host-level HTTPS.
- Disable PHP error display in production.
- Keep database credentials outside public documentation.
- Restrict upload execution for `assets/images`.
