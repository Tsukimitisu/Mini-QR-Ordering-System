# Security Policy

## Supported Scope

Security fixes should target the current `main` branch.

## Reporting Issues

Report suspected vulnerabilities privately to the project maintainer before opening public issues or pull requests.

Include:

- A short summary of the issue
- Steps to reproduce
- Expected impact
- Affected files or endpoints

## Local Configuration

The default XAMPP database credentials in `api/db.php` are for local development only. Do not commit production database credentials, API keys, or hosting passwords.

Use local environment-specific configuration outside the repository when deploying to a public server.

## Uploads

Menu images are validated by MIME type and size before storage. Keep upload directories non-executable at the web server level when deploying outside XAMPP.

## Admin Access

This educational version does not include authentication. Add login protection before using the admin dashboard, menu management, or QR generator in a real restaurant environment.
