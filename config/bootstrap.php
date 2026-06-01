<?php
/**
 * Bootstrap – loaded by every entry point
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

// Auto-load classes from models/ and controllers/
spl_autoload_register(function (string $class) {
    $dirs = [
        ROOT_PATH . '/models/',
        ROOT_PATH . '/controllers/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Start session once
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Regenerate session ID on login (called explicitly in AuthController)
function regenerate_session(): void
{
    session_regenerate_id(true);
}

// ------------------------------------------------------------
// CSRF helpers
// ------------------------------------------------------------
function csrf_token(): string
{
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function csrf_field(): string
{
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . csrf_token() . '">';
}

function verify_csrf(): void
{
    $token = $_POST[CSRF_TOKEN_NAME] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        die('Invalid CSRF token.');
    }
}

// ------------------------------------------------------------
// Auth helpers
// ------------------------------------------------------------
function auth(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('/login');
    }
}

function require_role(int ...$roles): void
{
    require_login();
    $user = auth();
    if (!in_array((int)$user['role_id'], $roles, true)) {
        http_response_code(403);
        include VIEWS_PATH . '/errors/403.php';
        exit;
    }
}

function can(string $permission): bool
{
    $user = auth();
    if (!$user) return false;

    $roleId = (int)$user['role_id'];

    $map = [
        'manage_members'  => [ROLE_EXECUTIVE, ROLE_ADMIN],
        'manage_events'   => [ROLE_EXECUTIVE, ROLE_ADMIN],
        'manage_projects' => [ROLE_EXECUTIVE, ROLE_ADMIN],
        'broadcast'       => [ROLE_EXECUTIVE, ROLE_ADMIN],
        'view_finances'   => [ROLE_FINANCIAL, ROLE_ADMIN],
        'approve_donations' => [ROLE_FINANCIAL, ROLE_ADMIN],
        'export_reports'  => [ROLE_FINANCIAL, ROLE_ADMIN],
        'manage_gallery'  => [ROLE_EXECUTIVE, ROLE_ADMIN],
        'admin_panel'     => [ROLE_ADMIN],
    ];

    return in_array($roleId, $map[$permission] ?? [], true);
}

// ------------------------------------------------------------
// Redirect helper
// ------------------------------------------------------------
function redirect(string $path): never
{
    header('Location: ' . APP_URL . $path);
    exit;
}

// ------------------------------------------------------------
// Flash messages
// ------------------------------------------------------------
function flash(string $key, string $message, string $type = 'info'): void
{
    $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
}

function get_flash(string $key): ?array
{
    if (isset($_SESSION['flash'][$key])) {
        $data = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $data;
    }
    return null;
}

// ------------------------------------------------------------
// Sanitisation helpers
// ------------------------------------------------------------
function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sanitize_input(mixed $value): string
{
    return trim(strip_tags((string)$value));
}

// ------------------------------------------------------------
// Slug generator
// ------------------------------------------------------------
function make_slug(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') . '-' . substr(uniqid(), -6);
}

// ------------------------------------------------------------
// Currency format
// ------------------------------------------------------------
function money(float $amount, string $currency = 'GHS'): string
{
    return '₵' . number_format($amount, 2);
}

// ------------------------------------------------------------
// Simple mailer (PHPMailer-compatible stub; swap for PHPMailer)
// ------------------------------------------------------------
function send_mail(string $to, string $subject, string $html): bool
{
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
    return mail($to, $subject, $html, $headers);
}
