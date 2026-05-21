# Server Configuration

## Nginx — Deny script execution under uploads

Add the following location block inside your server block, before the main PHP handler:

```nginx
location ~* ^/assets/uploads/.*\.(php|phtml|phar|html|htm|shtml|cgi|pl|py|rb|sh|asp|aspx|cfm|exe)$ {
    deny all;
    return 403;
}
```

This mirrors the `.htaccess` rule for Apache and prevents uploaded files from being executed as scripts.

## Recommended headers (already set by the Csp middleware)

```
Content-Security-Policy: (generated per-request with nonce)
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
```
