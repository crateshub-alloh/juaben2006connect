<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$pageTitle = 'Create Account';
$regErrors = $_SESSION['register_errors'] ?? [];
$old       = $_SESSION['register_old']    ?? [];
unset($_SESSION['register_errors'], $_SESSION['register_old']);

$countryCodes = [
  'GH +233' => '+233', 'NG +234' => '+234', 'GB +44'  => '+44',
  'US +1'   => '+1',   'CA +1'   => '+1',   'DE +49'  => '+49',
  'FR +33'  => '+33',  'IT +39'  => '+39',  'NL +31'  => '+31',
  'AU +61'  => '+61',  'ZA +27'  => '+27',  'CI +225' => '+225',
  'SN +221' => '+221', 'CM +237' => '+237', 'AE +971' => '+971',
  'SA +966' => '+966', 'CN +86'  => '+86',  'IN +91'  => '+91',
];
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
  <style>
    .phone-row { display:flex; gap:8px; }
    .phone-row select { width:130px; flex-shrink:0; }
    .phone-row input  { flex:1; }
    .required-star { color:#ef4444; margin-left:2px; }
    .avatar-upload-box {
      border:2px dashed var(--blue-300);
      border-radius:12px;
      padding:20px;
      text-align:center;
      background:var(--blue-50,#eff6ff);
      cursor:pointer;
      transition:border-color .2s;
    }
    .avatar-upload-box:hover { border-color:var(--blue-600); }
    .avatar-upload-box input[type=file] { display:none; }
    .avatar-preview { width:80px;height:80px;border-radius:50%;object-fit:cover;margin:8px auto 0;display:none;border:3px solid var(--blue-400); }
  </style>
</head>
<body>

<div class="auth-page">
  <div class="auth-card" style="max-width:560px;">
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

    <form method="POST" action="<?= APP_URL ?>/register" novalidate enctype="multipart/form-data" id="regForm">
      <?= csrf_field() ?>

      <!-- ── Profile Photo (top & compulsory) ─────────────────── -->
      <div class="form-group">
        <label class="form-label">
          Profile Photo <span class="required-star">*</span>
          <span style="font-weight:400;color:var(--gray-500);font-size:.85rem;"> — required</span>
        </label>
        <div class="avatar-upload-box" id="avatarBox" onclick="document.getElementById('avatar').click()">
          <i class="fa fa-camera" style="font-size:2rem;color:var(--blue-400);"></i>
          <p style="margin:8px 0 4px;font-weight:600;color:var(--blue-700);">Click to upload your photo</p>
          <p style="font-size:.82rem;color:var(--gray-500);">JPG, PNG or WebP · max 5 MB</p>
          <input type="file" id="avatar" name="avatar" accept="image/*" required
                 onchange="previewAvatar(this)">
          <img id="avatarPreview" class="avatar-preview" alt="Preview">
        </div>
      </div>

      <hr style="margin:18px 0;border:none;border-top:1px solid var(--gray-100)">
      <h3 style="margin:0 0 14px;font-size:1rem;color:var(--gray-700)">Account Details</h3>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="form-group">
          <label class="form-label" for="first_name">First Name <span class="required-star">*</span></label>
          <input type="text" id="first_name" name="first_name" class="form-control"
                 placeholder="Amina" required value="<?= h($old['firstName'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="last_name">Last Name <span class="required-star">*</span></label>
          <input type="text" id="last_name" name="last_name" class="form-control"
                 placeholder="Yusuf" required value="<?= h($old['lastName'] ?? '') ?>">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Email Address <span class="required-star">*</span></label>
        <input type="email" id="email" name="email" class="form-control"
               placeholder="you@example.com" required value="<?= h($old['email'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password <span class="required-star">*</span></label>
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
        <label class="form-label" for="password_confirm">Confirm Password <span class="required-star">*</span></label>
        <input type="password" id="password_confirm" name="password_confirm" class="form-control"
               placeholder="Repeat password" required>
      </div>

      <hr style="margin:18px 0;border:none;border-top:1px solid var(--gray-100)">
      <h3 style="margin:0 0 14px;font-size:1rem;color:var(--gray-700)">Profile Information</h3>

      <!-- Phone with country code -->
      <div class="form-group">
        <label class="form-label" for="phone">Phone Number <span class="required-star">*</span></label>
        <div class="phone-row">
          <select name="phone_code" class="form-control">
            <?php foreach ($countryCodes as $label => $code): ?>
              <option value="<?= h($code) ?>"
                <?= ($old['phone_code'] ?? '+233') === $code ? 'selected' : '' ?>>
                <?= h($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <input type="tel" id="phone" name="phone" class="form-control"
                 placeholder="024 123 4567" required value="<?= h($old['phone'] ?? '') ?>">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
          <label class="form-label" for="occupation">Occupation <span class="required-star">*</span></label>
          <input type="text" id="occupation" name="occupation" class="form-control"
                 required value="<?= h($old['occupation'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="employer">Employer <span class="required-star">*</span></label>
          <input type="text" id="employer" name="employer" class="form-control"
                 required value="<?= h($old['employer'] ?? '') ?>">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
          <label class="form-label" for="city">City <span class="required-star">*</span></label>
          <input type="text" id="city" name="city" class="form-control"
                 required value="<?= h($old['city'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="country">Country <span class="required-star">*</span></label>
          <input type="text" id="country" name="country" class="form-control"
                 required value="<?= h($old['country'] ?? 'Ghana') ?>">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="house">House <span class="required-star">*</span></label>
        <select id="house" name="house" class="form-control" required>
          <option value="" <?= empty($old['house']) ? 'selected' : '' ?>>Select your house</option>
          <option value="House 1" <?= ($old['house'] ?? '') === 'House 1' ? 'selected' : '' ?>>House 1</option>
          <option value="House 2" <?= ($old['house'] ?? '') === 'House 2' ? 'selected' : '' ?>>House 2</option>
          <option value="House 3" <?= ($old['house'] ?? '') === 'House 3' ? 'selected' : '' ?>>House 3</option>
          <option value="House 4" <?= ($old['house'] ?? '') === 'House 4' ? 'selected' : '' ?>>House 4</option>
        </select>
      </div>

      <div class="form-group" style="display:flex;align-items:flex-start;gap:10px;margin-top:4px;">
        <input type="checkbox" id="agree" name="agree" required style="margin-top:3px;flex-shrink:0;">
        <label for="agree" style="font-size:.88rem;color:var(--gray-700);">
          I agree to the <a href="#" style="color:var(--blue-700);">Terms of Service</a> and
          <a href="#" style="color:var(--blue-700);">Privacy Policy</a>
        </label>
      </div>

      <button type="submit" class="btn btn-blue btn-block btn-lg" style="margin-top:8px;">
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

function previewAvatar(input) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const preview = document.getElementById('avatarPreview');
    preview.src = e.target.result;
    preview.style.display = 'block';
    document.getElementById('avatarBox').style.borderColor = 'var(--blue-600)';
    document.getElementById('avatarBox').style.background  = 'var(--blue-50,#eff6ff)';
  };
  reader.readAsDataURL(file);
}

document.getElementById('password').addEventListener('input', function() {
  const bar = document.getElementById('strengthBar');
  const val = this.value;
  let score = 0;
  if (val.length >= 8)           score++;
  if (/[A-Z]/.test(val))        score++;
  if (/[0-9]/.test(val))        score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
  const colors = ['', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
  bar.innerHTML = val
    ? `<div style="height:4px;border-radius:4px;background:var(--gray-200);overflow:hidden;margin-top:4px;"><div style="height:100%;width:${score*25}%;background:${colors[score]};transition:width .3s,background .3s;border-radius:4px;"></div></div><p style="font-size:.75rem;color:${colors[score]};margin-top:2px;">${labels[score]}</p>`
    : '';
});

// Client-side: block submit if no photo selected
document.getElementById('regForm').addEventListener('submit', function(e) {
  if (!document.getElementById('avatar').files.length) {
    e.preventDefault();
    document.getElementById('avatarBox').style.borderColor = '#ef4444';
    document.getElementById('avatarBox').scrollIntoView({behavior:'smooth',block:'center'});
    alert('Please upload your profile photo before continuing.');
  }
});
</script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
