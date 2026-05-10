# Deploy

The site is plain static HTML / CSS / JS. No WordPress, no PHP, no
database. To go live, rsync the repo's `*.html` and `/assets/` to a
directory the web server serves.

## On the server

```bash
cd /var/www/hernextmission/site            # wherever the repo is checked out
git fetch origin
git checkout main
git pull --ff-only

WEB_ROOT=/var/www/hernextmission/htdocs \
HNM_REPO_ROOT=/var/www/hernextmission/site \
  bash deploy/deploy.sh
```

The script:
- copies every top-level `*.html` to `$WEB_ROOT/`
- mirrors `/assets/` (with `--delete`) to `$WEB_ROOT/assets/`
- never touches anything else under `$WEB_ROOT`

## Web server

Nginx config (minimal):

```nginx
server {
    server_name hernextmission.org www.hernextmission.org;
    root /var/www/hernextmission/htdocs;
    index index.html;

    location / {
        try_files $uri $uri/ $uri.html =404;
    }

    location /assets/ {
        expires 7d;
        add_header Cache-Control "public, max-age=604800, must-revalidate";
    }
}
```

Apache (if applicable) — `.htaccess`:

```
DirectoryIndex index.html
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([^.]+)$ $1.html [NC,L]
```

## Rollback

Static. Snapshot before deploy:

```bash
tar czf /tmp/hnm-pre-deploy.tgz -C $WEB_ROOT .
```

Restore:

```bash
rm -rf $WEB_ROOT/* && tar xzf /tmp/hnm-pre-deploy.tgz -C $WEB_ROOT
```
