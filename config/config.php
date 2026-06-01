<?php
/**
 * Juaben2006 Connect – Application Configuration
 */

define('APP_NAME',    'Juaben2006 Connect');
define('APP_VERSION', '1.0.0');
define('APP_URL',     getenv('APP_URL') ?: 'http://localhost:8888/alumni');
define('APP_ENV',     getenv('APP_ENV') ?: 'development'); // development | production

// Paths
define('ROOT_PATH',       dirname(__DIR__));
define('VIEWS_PATH',      ROOT_PATH . '/views');
define('UPLOADS_PATH',    ROOT_PATH . '/assets/uploads');
define('UPLOADS_URL',     APP_URL   . '/assets/uploads');

// Session
define('SESSION_NAME',    'njosa_sess');
define('SESSION_LIFETIME', 7200); // 2 hours

// Security
define('CSRF_TOKEN_NAME', '_csrf_token');
define('PASSWORD_MIN_LEN', 8);

// Pagination
define('PAGE_SIZE', 20);

// File uploads
define('MAX_UPLOAD_MB',  5);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// Email (override via environment)
define('SMTP_HOST',     getenv('SMTP_HOST') ?: '');
define('SMTP_PORT',     getenv('SMTP_PORT') ?: 587);
define('SMTP_USER',     getenv('SMTP_USER') ?: '');
define('SMTP_PASS',     getenv('SMTP_PASS') ?: '');
define('MAIL_FROM',     getenv('MAIL_FROM') ?: 'noreply@juaben2006connect.com');
define('MAIL_FROM_NAME', APP_NAME);

// Roles
define('ROLE_MEMBER',    1);
define('ROLE_EXECUTIVE', 2);
define('ROLE_FINANCIAL', 3);
define('ROLE_ADMIN',     4);

// Error display
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
    set_exception_handler(function ($e) {
        error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        http_response_code(500);
        include ROOT_PATH . '/views/errors/500.php';
        exit;
    });
}
