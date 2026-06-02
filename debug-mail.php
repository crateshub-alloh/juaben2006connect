<?php
/**
 * Temporary SMTP debug — delete this file after email is confirmed working
 */
require_once __DIR__ . '/config/bootstrap.php';

$to = $_GET['to'] ?? '';
if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
    echo '<p>Usage: /debug-mail.php?to=your@email.com</p>';
    exit;
}

require_once ROOT_PATH . '/lib/phpmailer/Exception.php';
require_once ROOT_PATH . '/lib/phpmailer/PHPMailer.php';
require_once ROOT_PATH . '/lib/phpmailer/SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);
$mail->SMTPDebug = 2;

ob_start();
try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = 'SMTP Test – ' . APP_NAME;
    $mail->Body    = '<p>SMTP is working correctly.</p>';
    $mail->send();
    $log = ob_get_clean();
    echo '<h2 style="color:green">&#10003; Email sent to ' . htmlspecialchars($to) . '</h2>';
} catch (\Exception $e) {
    $log = ob_get_clean();
    echo '<h2 style="color:red">&#10007; Failed: ' . htmlspecialchars($mail->ErrorInfo) . '</h2>';
}

echo '<pre style="background:#f4f4f4;padding:1em;font-size:12px">' . htmlspecialchars($log) . '</pre>';
echo '<p><strong>Config:</strong> Host=' . SMTP_HOST . ' | Port=' . SMTP_PORT . ' | User=' . SMTP_USER . '</p>';
