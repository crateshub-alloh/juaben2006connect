<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$pageTitle = 'Forgot Password';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password – <?= APP_NAME ?></title>
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
        <i class="fa fa-key"></i>
      </div>
      <h2>Reset Password</h2>
      <p>Enter your email and we'll send a reset link.</p>
    </div>

    <?php $s = get_flash('success'); if ($s): ?>
      <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($s['message']) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/forgot-password">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required>
      </div>
      <button type="submit" class="btn btn-blue btn-block">Send Reset Link</button>
    </form>

    <div class="auth-footer">
      <a href="<?= APP_URL ?>/login"><i class="fa fa-arrow-left"></i> Back to login</a>
    </div>
  </div>
</div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
