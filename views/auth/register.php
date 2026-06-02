<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$pageTitle = 'Create Account';
$regErrors = $_SESSION['register_errors'] ?? [];
$old       = $_SESSION['register_old']    ?? [];
unset($_SESSION['register_errors'], $_SESSION['register_old']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register – <?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>

<div class="auth-page">
  <div class="auth-card" style="max-width:520px;">
    <div class="auth-card__logo">
      <div style="width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,var(--blue-800),var(--blue-600));display:grid;place-items:center;margin:0 auto 16px;font-family:var(--font-heading);color:var(--gold-400);font-size:1.5rem;font-weight:900;">N</div>
      <h2>Join New Juaben Old Student Association – Year Group 2006</h2>
      <p>Create your account – for 2006 year group members only</p>
    </div>

    <?php if (!empty($regErrors)): ?>
      <div class="alert alert-error">
        <i class="fa fa-circle-xmark"></i>
        <ul style="margin:0;padding-left:16px;">
          <?php foreach ($regErrors as $err): ?>
            <li><?= h($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/register" novalidate enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="form-group">
          <label class="form-label" for="first_name">First Name</label>
          <input type="text" id="first_name" name="first_name" class="form-control"
                 placeholder="Amina" required value="<?= h($old['firstName'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="last_name">Last Name</label>
          <input type="text" id="last_name" name="last_name" class="form-control"
                 placeholder="Yusuf" required value="<?= h($old['lastName'] ?? '') ?>">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control"
               placeholder="you@example.com" required value="<?= h($old['email'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <div style="position:relative;">
          <input type="password" id="password" name="password" class="form-control"
                 placeholder="Min. 8 characters" required minlength="8">
          <button type="button" onclick="togglePass(this)" tabindex="-1"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gray-500);cursor:pointer;">
            <i class="fa fa-eye"></i>
          </button>
        </div>
        <div class="password-strength mt-1" id="strengthBar"></div>
      </div>

      <div class="form-group">
        <label class="form-label" for="password_confirm">Confirm Password</label>
        <input type="password" id="password_confirm" name="password_confirm" class="form-control"
               placeholder="Repeat password" required>
      </div>

      <div class="form-group" style="display:flex;align-items:flex-start;gap:10px;">
        <input type="checkbox" id="agree" name="agree" required style="margin-top:3px;flex-shrink:0;">
        <label for="agree" style="font-size:.88rem;color:var(--gray-700);">
          I agree to the <a href="#" style="color:var(--blue-700);">Terms of Service</a> and
          <a href="#" style="color:var(--blue-700);">Privacy Policy</a>
        </label>
      </div>

      <hr style="margin:18px 0;border:none;border-top:1px solid var(--gray-100)">
      <h3 style="margin:0 0 12px;font-size:1rem;color:var(--gray-700)">Profile</h3>
      <p style="margin:.25rem 0 16px;color:var(--gray-600);font-size:.95rem;">Please fill in all fields below to complete registration.</p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
          <label class="form-label" for="phone">Phone</label>
          <input type="text" id="phone" name="phone" class="form-control" required value="<?= h($old['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="occupation">Occupation</label>
          <input type="text" id="occupation" name="occupation" class="form-control" required value="<?= h($old['occupation'] ?? '') ?>">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
          <label class="form-label" for="employer">Employer</label>
          <input type="text" id="employer" name="employer" class="form-control" required value="<?= h($old['employer'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="city">City</label>
          <input type="text" id="city" name="city" class="form-control" required value="<?= h($old['city'] ?? '') ?>">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
          <label class="form-label" for="country">Country</label>
          <input type="text" id="country" name="country" class="form-control" required value="<?= h($old['country'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="house">House</label>
          <select id="house" name="house" class="form-control" required>
            <option value="" <?= empty($old['house']) ? 'selected' : '' ?>>Select House</option>
            <option value="House 1" <?= ($old['house'] ?? '') === 'House 1' ? 'selected' : '' ?>>House 1</option>
            <option value="House 2" <?= ($old['house'] ?? '') === 'House 2' ? 'selected' : '' ?>>House 2</option>
            <option value="House 3" <?= ($old['house'] ?? '') === 'House 3' ? 'selected' : '' ?>>House 3</option>
            <option value="House 4" <?= ($old['house'] ?? '') === 'House 4' ? 'selected' : '' ?>>House 4</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="avatar">Upload Photo</label>
          <input type="file" id="avatar" name="avatar" accept="image/*" class="form-control" required>
        </div>
      </div>

      <button type="submit" class="btn btn-blue btn-block btn-lg">
        <i class="fa fa-user-plus"></i> Create Account
      </button>
    </form>

    <div class="auth-footer">
      Already a member? <a href="<?= APP_URL ?>/login">Sign in</a>
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
  input.type  = input.type === 'password' ? 'text' : 'password';
  icon.className = input.type === 'text' ? 'fa fa-eye-slash' : 'fa fa-eye';
}

// Simple password strength indicator
document.getElementById('password').addEventListener('input', function() {
  const bar = document.getElementById('strengthBar');
  const val = this.value;
  let score = 0;
  if (val.length >= 8)              score++;
  if (/[A-Z]/.test(val))           score++;
  if (/[0-9]/.test(val))           score++;
  if (/[^A-Za-z0-9]/.test(val))    score++;

  const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
  const colors = ['', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
  bar.innerHTML = val ? `<div style="height:4px;border-radius:4px;background:var(--gray-200);overflow:hidden;margin-top:4px;"><div style="height:100%;width:${score * 25}%;background:${colors[score]};transition:width .3s,background .3s;border-radius:4px;"></div></div><p style="font-size:.75rem;color:${colors[score]};margin-top:2px;">${labels[score]}</p>` : '';
});
</script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
