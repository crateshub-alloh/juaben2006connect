<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$pageTitle = 'Login';
$errors    = get_flash('error');
$success   = get_flash('success');
$warning   = get_flash('warning');
$registered = !empty($_GET['registered']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – <?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-card__logo">
      <div style="width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,var(--blue-800),var(--blue-600));display:grid;place-items:center;margin:0 auto 16px;font-family:var(--font-heading);color:var(--gold-400);font-size:1.5rem;font-weight:900;">N</div>
      <h2>New Juaben Old Student Association – Year Group 2006</h2>
      <p>Welcome back, classmate – sign in to your account</p>
    </div>

    <?php if ($registered): ?>
      <div class="alert alert-success"><i class="fa fa-circle-check"></i> Registration successful. Please sign in to continue to your profile.</div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($success['message']) ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-error"><i class="fa fa-circle-xmark"></i> <?= h($errors['message']) ?></div>
    <?php endif; ?>
    <?php if ($warning): ?>
      <div class="alert alert-warning"><i class="fa fa-triangle-exclamation"></i> <?= h($warning['message']) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/login" novalidate>
      <?= csrf_field() ?>

      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control"
               placeholder="you@example.com" required autocomplete="email"
               value="<?= h($_POST['email'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label class="form-label" for="password">
          Password
          <a href="<?= APP_URL ?>/forgot-password" style="float:right;font-weight:500;color:var(--blue-600);font-size:.85rem;">Forgot password?</a>
        </label>
        <div style="position:relative;">
          <input type="password" id="password" name="password" class="form-control"
                 placeholder="••••••••" required autocomplete="current-password">
          <button type="button" onclick="togglePass(this)" tabindex="-1"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gray-500);cursor:pointer;">
            <i class="fa fa-eye"></i>
          </button>
        </div>
      </div>

      <input type="hidden" name="next" value="<?= h($_GET['next'] ?? '') ?>">
      <button type="submit" class="btn btn-blue btn-block btn-lg">
        <i class="fa fa-sign-in"></i> Sign In
      </button>
    </form>

    <div class="auth-footer">
      Don't have an account? <a href="<?= APP_URL ?>/register">Create one</a>
    </div>
    <div class="auth-footer mt-2">
      <a href="<?= APP_URL ?>/" style="color:var(--gray-500)"><i class="fa fa-arrow-left"></i> Back to website</a>
    </div>
  </div>
</div>

<script>
function togglePass(btn) {
  const input = btn.previousElementSibling;
  const icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'fa fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fa fa-eye';
  }
}
</script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
