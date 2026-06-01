# NJOSA Alumni Portal

A production-ready alumni management platform built with PHP 8+, MySQL, and vanilla JS.

---

## Tech Stack

| Layer      | Technology                         |
|------------|------------------------------------|
| Backend    | PHP 8.1+                           |
| Database   | MySQL 8.0+                         |
| Frontend   | HTML5, CSS3, Vanilla JavaScript    |
| Icons      | Font Awesome 6                     |
| Charts     | Chart.js 4 (CDN)                   |
| Typography | Inter + Poppins (Google Fonts)     |
| Hosting    | Apache + cPanel / any shared host  |

---

## Quick Start (Local – XAMPP/Laragon)

### 1. Place files
```
htdocs/alumni/   ← project root
```

### 2. Create database
```sql
-- In phpMyAdmin or MySQL CLI:
SOURCE /path/to/alumni/database/schema.sql;
```

### 3. Configure environment
```bash
cp .env.example .env
# Edit .env with your DB credentials and APP_URL
```

### 4. Load env vars (add to index.php top, development only)
```php
// Simple .env loader for local dev
foreach (file('.env') as $line) {
    $line = trim($line);
    if ($line && !str_starts_with($line, '#') && str_contains($line, '=')) {
        [$k, $v] = explode('=', $line, 2);
        putenv(trim($k) . '=' . trim($v));
    }
}
```

### 5. Visit
```
http://localhost/alumni/
```

---

## Default Admin Account

| Field    | Value                      |
|----------|----------------------------|
| Email    | admin@njosaalumni.org      |
| Password | Admin@1234                 |

**Change this immediately after first login.**

---

## Deployment on cPanel

### Step 1 – Upload
Upload the entire `/alumni` folder to `public_html/alumni/` via File Manager or FTP.

### Step 2 – Database
1. Open **MySQL Databases** in cPanel.
2. Create database: `njosa_alumni`
3. Create a user and grant all privileges.
4. Import `database/schema.sql` via phpMyAdmin.

### Step 3 – Environment
Set environment variables via cPanel → **PHP Variables** or add to `.htaccess`:
```apache
SetEnv DB_HOST     127.0.0.1
SetEnv DB_NAME     njosa_alumni
SetEnv DB_USER     your_db_user
SetEnv DB_PASS     your_db_pass
SetEnv APP_URL     https://yourdomain.com/alumni
SetEnv APP_ENV     production
```

### Step 4 – .htaccess
Ensure `mod_rewrite` is enabled (most hosts have this). The included `.htaccess` handles all routing.

### Step 5 – Uploads directory
Make `assets/uploads/` writable:
```bash
chmod -R 755 assets/uploads/
```

### Step 6 – PHP Requirements
- PHP 8.1+
- Extensions: `pdo_mysql`, `gd`, `mbstring`, `fileinfo`, `openssl`

---

## Folder Structure

```
/alumni
├── assets/
│   ├── css/          main.css · dashboard.css
│   ├── js/           main.js · dashboard.js
│   ├── images/       static images + favicon
│   └── uploads/      user-generated content (writable)
│       ├── avatars/
│       ├── events/
│       ├── projects/
│       ├── gallery/
│       └── campaigns/
├── components/       shared layout partials
├── config/           bootstrap · database · config
├── controllers/      AuthController · UploadController
├── database/         schema.sql
├── models/           User · Event · Donation · Project · Gallery · Message · Notification
├── views/
│   ├── auth/         login · register · forgot/reset password
│   ├── public/       home · about · events · projects · gallery · donate · contact
│   ├── member/       dashboard · profile · events · donations · messages · payments
│   ├── executive/    dashboard · members · events · projects · gallery · broadcast
│   ├── financial/    dashboard · donations · payments · reports
│   ├── admin/        dashboard · settings · audit log
│   └── errors/       403 · 404 · 500
├── api/              notifications.php (JSON endpoint)
├── index.php         front controller / router
└── .htaccess         URL rewriting + security headers
```

---

## Role Permissions

| Permission          | Member | Executive | Financial | Admin |
|---------------------|--------|-----------|-----------|-------|
| View & edit profile | ✓      | ✓         | ✓         | ✓     |
| Register for events | ✓      | ✓         | ✓         | ✓     |
| Donate              | ✓      | ✓         | ✓         | ✓     |
| Manage members      |        | ✓         |           | ✓     |
| Manage events       |        | ✓         |           | ✓     |
| Manage projects     |        | ✓         |           | ✓     |
| Broadcast messages  |        | ✓         |           | ✓     |
| View finances       |        |           | ✓         | ✓     |
| Approve donations   |        |           | ✓         | ✓     |
| Export CSV reports  |        |           | ✓         | ✓     |
| Full admin access   |        |           |           | ✓     |

---

## Security Features

- CSRF protection on all POST forms
- Prepared statements throughout (no raw SQL interpolation)
- `password_hash()` with BCRYPT cost 12
- Secure session configuration (HTTPOnly, SameSite=Lax)
- Session ID regeneration on login
- File upload MIME validation (content sniffing, not just extension)
- PHP execution blocked in uploads directory
- Security headers (X-Frame-Options, CSP, HSTS-ready)
- Role-based access control enforced server-side
- Email enumeration prevention on password reset

---

## Customisation

### Change brand colours
Edit CSS variables in `assets/css/main.css`:
```css
:root {
  --blue-800: #1a3c6e;  /* primary blue */
  --gold-500: #c9a227;  /* accent gold  */
}
```

### Add payment gateway (Paystack)
1. Set `PAYSTACK_SECRET_KEY` and `PAYSTACK_PUBLIC_KEY` in environment.
2. In the donate form, replace the manual `donationSubmit()` handler in `index.php`
   with a Paystack inline JS integration + webhook verification.

### Enable email (SMTP)
Replace the `send_mail()` stub in `config/bootstrap.php` with PHPMailer:
```bash
composer require phpmailer/phpmailer
```
Then swap the implementation using your SMTP credentials.

---

## License
MIT – free to use, modify, and distribute.
