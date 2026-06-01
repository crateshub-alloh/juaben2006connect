<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$pageTitle = 'Set New Password';
$token     = h($_GET['token'] ?? '');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password – <?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-card__logo">
      <div style="width:56px;height:56px;border-radius:50%;background:var(--blue-100);display:grid;place-items:center;margin:0 auto 16px;color:var(--blue-700);font-size:1.5rem;">
        <i class="fa fa-lock"></i>
      </div>
      <h2>New Password</h2>
      <p>Choose a strong password for your account.</p>
    </div>

    <?php $e = get_flash('error'); if ($e): ?>
      <div class="alert alert-error"><i class="fa fa-circle-xmark"></i> <?= h($e['message']) ?></div>
    <?php endif; ?>

    <?php if (!$token): ?>
      <div class="alert alert-error">Invalid or missing reset token.</div>
    <?php else: ?>
    <form method="POST" action="<?= APP_URL ?>/reset-password">
      <?= csrf_field() ?>
      <input type="hidden" name="token" value="<?= $token ?>">
      <div class="form-group">
        <label class="form-label" for="password">New Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Min. 8 characters" required minlength="8">
      </div>
      <div class="form-group">
        <label class="form-label" for="password_confirm">Confirm Password</label>
        <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="Repeat password" required>
      </div>
      <button type="submit" class="btn btn-blue btn-block">Set New Password</button>
    </form>
    <?php endif; ?>

    <div class="auth-footer mt-3">
      <a href="<?= APP_URL ?>/login"><i class="fa fa-arrow-left"></i> Back to login</a>
    </div>
  </div>
</div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
