# Contributing

## Change Guidelines

- Keep commits focused on one behavior, screen, or documentation topic.
- Prefer existing PHP, JavaScript, Bootstrap, and CSS patterns already used in the project.
- Keep database changes reflected in `database/mini_qr_ordering_db.sql`.
- Escape user-visible dynamic output in PHP templates.
- Validate API input on the server even when browser forms already have limits.

## Local Validation

Before opening a pull request or pushing a functional change:

1. Run PHP syntax checks on changed PHP files.
2. Import or update the local database if SQL changed.
3. Test the customer order flow.
4. Test admin status and payment updates.
5. Test menu stock and availability changes.
6. Test QR generation for at least one table.

Use `docs/TESTING_CHECKLIST.md` for the full manual flow.

## Commit Messages

Use short, imperative commit messages:

```text
Add order status validation
Fix cart quantity handling
Document local setup workflow
```
