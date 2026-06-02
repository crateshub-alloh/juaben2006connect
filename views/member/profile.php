<?php
$pageTitle = 'My Profile';
require_once dirname(__DIR__, 2) . '/components/dashboard-layout.php';

$userId   = (int)$user['id'];
$userModel = new User();
$userFull  = $userModel->findById($userId);

$saved = get_flash('success');
$error = get_flash('error');

$avatarUrl = !empty($userFull['avatar'])
    ? UPLOADS_URL . '/avatars/' . h($userFull['avatar'])
    : APP_URL . '/assets/images/avatar-placeholder.png';
?>

<div class="page-header">
  <div>
    <h1>My Profile</h1>
    <p>Keep your information up to date so other alumni can connect with you.</p>
  </div>
</div>

<?php if ($saved): ?>
  <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($saved['message']) ?></div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="alert alert-error"><i class="fa fa-circle-xmark"></i> <?= h($error['message']) ?></div>
<?php endif; ?>

<div style="display:flex;flex-direction:column;gap:24px;">
  <!-- Avatar Card (centered) -->
  <div class="table-card" style="padding:28px;text-align:center;max-width:340px;margin:0 auto;width:100%;">
    <img id="avatarPreview" src="<?= $avatarUrl ?>" alt="Avatar"
         style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:3px solid var(--blue-200);margin-bottom:14px;display:block;margin-left:auto;margin-right:auto;">
    <h3 style="font-size:1.1rem;"><?= h($userFull['first_name'] . ' ' . $userFull['last_name']) ?></h3>
    <?php if (!empty($userFull['nickname'])): ?>
      <p class="text-sm text-muted" style="font-style:italic;">"<?= h($userFull['nickname']) ?>"</p>
    <?php endif; ?>
    <p class="text-sm text-muted"><?= h($user['role_name']) ?></p>
    <p class="text-xs text-muted mt-2">Member since <?= date('M Y', strtotime($userFull['created_at'])) ?></p>

    <form method="POST" action="<?= APP_URL ?>/member/profile/avatar" enctype="multipart/form-data" class="mt-4">
      <?= csrf_field() ?>
      <label class="btn btn-outline btn-sm btn-block" style="cursor:pointer;">
        <i class="fa fa-camera"></i> Change Photo
        <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;" onchange="this.form.submit()">
      </label>
    </form>
    <a href="<?= APP_URL ?>/logout" class="btn btn-danger btn-sm btn-block mt-2" onclick="return confirm('Log out?');">
      <i class="fa fa-right-from-bracket"></i> Log Out
    </a>
  </div>

  <!-- Profile Form -->
  <div class="table-card" style="padding:28px;">
    <h3 style="margin-bottom:24px;font-size:1.1rem;">Personal Information</h3>
    <form method="POST" action="<?= APP_URL ?>/member/profile/update">
      <?= csrf_field() ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="form-group">
          <label class="form-label">First Name</label>
          <input type="text" name="first_name" class="form-control" value="<?= h($userFull['first_name']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Last Name</label>
          <input type="text" name="last_name" class="form-control" value="<?= h($userFull['last_name']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Nickname <span style="color:var(--gray-400);font-weight:400;font-size:.82rem;">(optional)</span></label>
          <input type="text" name="nickname" class="form-control" value="<?= h($userFull['nickname'] ?? '') ?>" placeholder="e.g. Kofi, Big B...">
        </div>
        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <input type="tel" name="phone" class="form-control" value="<?= h($userFull['phone'] ?? '') ?>" placeholder="+233 024...">
        </div>
        <div class="form-group">
          <label class="form-label">Occupation</label>
          <input type="text" name="occupation" class="form-control" value="<?= h($userFull['occupation'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Employer / Company</label>
          <input type="text" name="employer" class="form-control" value="<?= h($userFull['employer'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">City</label>
          <input type="text" name="city" class="form-control" value="<?= h($userFull['city'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Country</label>
          <input type="text" name="country" class="form-control" value="<?= h($userFull['country'] ?? 'Ghana') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">House</label>
          <select name="house" class="form-control">
            <option value="">Select House</option>
            <option value="House 1" <?= ($userFull['house'] ?? '') === 'House 1' ? 'selected' : '' ?>>House 1</option>
            <option value="House 2" <?= ($userFull['house'] ?? '') === 'House 2' ? 'selected' : '' ?>>House 2</option>
            <option value="House 3" <?= ($userFull['house'] ?? '') === 'House 3' ? 'selected' : '' ?>>House 3</option>
            <option value="House 4" <?= ($userFull['house'] ?? '') === 'House 4' ? 'selected' : '' ?>>House 4</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Bio</label>
        <textarea name="bio" class="form-control" rows="3" placeholder="Tell the community about yourself..."><?= h($userFull['bio'] ?? '') ?></textarea>
      </div>

      <h4 style="margin:20px 0 16px;font-size:1rem;">Social Links</h4>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
        <div class="form-group">
          <label class="form-label"><i class="fab fa-linkedin text-blue"></i> LinkedIn</label>
          <input type="url" name="linkedin_url" class="form-control" value="<?= h($userFull['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/in/...">
        </div>
        <div class="form-group">
          <label class="form-label"><i class="fab fa-twitter" style="color:#1da1f2"></i> Twitter</label>
          <input type="url" name="twitter_url" class="form-control" value="<?= h($userFull['twitter_url'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label"><i class="fab fa-facebook" style="color:#1877f2"></i> Facebook</label>
          <input type="url" name="facebook_url" class="form-control" value="<?= h($userFull['facebook_url'] ?? '') ?>">
        </div>
      </div>

      <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:8px;">
        <button type="reset" class="btn btn-outline btn-sm">Reset</button>
        <button type="submit" class="btn btn-blue">
          <i class="fa fa-floppy-disk"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
