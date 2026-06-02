<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$pageTitle = 'Registration Successful';
$isDev = APP_ENV === 'development';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Successful – <?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>

<div class="auth-page">
  <div class="auth-card" style="max-width:520px;text-align:center;">
    <div class="auth-card__logo">
      <div style="width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,var(--blue-800),var(--blue-600));display:grid;place-items:center;margin:0 auto 16px;font-family:var(--font-heading);color:var(--gold-400);font-size:1.5rem;font-weight:900;">N</div>
      <h2>Registration Received</h2>
      <p>Your account has been created successfully.</p>
    </div>

    <?php if ($isDev): ?>
      <div class="alert alert-success" style="text-align:left;">
        <p><strong>You're all set!</strong> Your account is ready to use. Log in with your credentials.</p>
      </div>
    <?php else: ?>
      <div class="alert alert-success" style="text-align:left;">
        <p><strong>Next Step:</strong> Check your email for the verification link.</p>
        <p>Once your email is verified, you can sign in and access your profile.</p>
      </div>
    <?php endif; ?>

    <div style="display:grid;gap:12px;">
      <a href="<?= APP_URL ?>/login?next=/member/profile&registered=1" class="btn btn-blue btn-block btn-lg"><i class="fa fa-sign-in"></i> Sign In and View Profile</a>
      <a href="<?= APP_URL ?>/" class="btn btn-outline btn-block">Back to homepage</a>
    </div>

    <div class="auth-footer mt-3">
      <?php if (!$isDev): ?>
        Have questions? Reach out to the alumni admin if you don't get the verification email.
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
