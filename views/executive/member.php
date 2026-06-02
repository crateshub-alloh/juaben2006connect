<?php
$pageTitle = 'Member Details';
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
require_once ROOT_PATH . '/components/dashboard-layout.php';

$userId = (int)($_GET['id'] ?? 0);
$userModel = new User();
$userFull = $userModel->findById($userId);
if (!$userFull) {
    flash('error', 'Member not found.', 'error');
    redirect('/executive/members');
}

$saved = get_flash('success');
$error = get_flash('error');
$avatarUrl = !empty($userFull['avatar'])
    ? UPLOADS_URL . '/avatars/' . h($userFull['avatar'])
    : APP_URL . '/assets/images/avatar-placeholder.png';
?>

<div class="page-header">
  <div>
    <h1>Member Details</h1>
    <p>Detailed profile for <?= h($userFull['first_name'] . ' ' . $userFull['last_name']) ?>.</p>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap;">
    <a href="<?= APP_URL ?>/executive/members" class="btn btn-outline btn-sm">Back to members</a>
    <?php if (auth()['role_id'] == ROLE_ADMIN && $userFull['id'] !== auth()['id']): ?>
      <form method="POST" action="<?= APP_URL ?>/executive/members/<?= $userFull['id'] ?>/delete" style="display:inline;">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this member permanently?');">
          <i class="fa fa-trash"></i> Delete Member
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php if ($saved): ?>
  <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($saved['message']) ?></div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="alert alert-error"><i class="fa fa-circle-xmark"></i> <?= h($error['message']) ?></div>
<?php endif; ?>

<div class="grid-2" style="gap:24px;">
  <div class="table-card" style="padding:28px;text-align:center;">
    <img src="<?= $avatarUrl ?>" alt="Avatar" style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid var(--blue-200);margin-bottom:16px;">
    <h2><?= h($userFull['first_name'] . ' ' . $userFull['last_name']) ?></h2>
    <p class="text-sm text-muted"><?= h($userFull['role_name']) ?></p>
    <p class="text-xs text-muted mt-2">Member since <?= date('M Y', strtotime($userFull['created_at'])) ?></p>
    <p class="text-xs text-muted">Status: <?= $userFull['is_active'] ? 'Active' : 'Inactive' ?></p>
    <p class="text-xs text-muted">Last login: <?= $userFull['last_login_at'] ? date('M j, Y', strtotime($userFull['last_login_at'])) : 'Never' ?></p>
  </div>

  <div class="table-card" style="padding:28px;">
    <h3 style="margin-bottom:20px;font-size:1.1rem;">Profile Information</h3>
    <div style="display:grid;gap:14px;">
      <div>
        <strong>Email</strong>
        <p><?= h($userFull['email']) ?></p>
      </div>
      <div>
        <strong>Occupation</strong>
        <p><?= h($userFull['occupation'] ?? '—') ?></p>
      </div>
      <div>
        <strong>Employer</strong>
        <p><?= h($userFull['employer'] ?? '—') ?></p>
      </div>
      <div>
        <strong>City</strong>
        <p><?= h($userFull['city'] ?? '—') ?></p>
      </div>
      <div>
        <strong>Country</strong>
        <p><?= h($userFull['country'] ?? '—') ?></p>
      </div>
      <div>
        <strong>Profile complete</strong>
        <p><?= $userFull['is_profile_complete'] ? 'Yes' : 'No' ?></p>
      </div>
      <div>
        <strong>Bio</strong>
        <p><?= nl2br(h($userFull['bio'] ?? '—')) ?></p>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
          <strong>LinkedIn</strong>
          <p><?= $userFull['linkedin_url'] ? '<a href="' . h($userFull['linkedin_url']) . '" target="_blank">Link</a>' : '—' ?></p>
        </div>
        <div>
          <strong>Twitter</strong>
          <p><?= $userFull['twitter_url'] ? '<a href="' . h($userFull['twitter_url']) . '" target="_blank">Link</a>' : '—' ?></p>
        </div>
        <div>
          <strong>Facebook</strong>
          <p><?= $userFull['facebook_url'] ? '<a href="' . h($userFull['facebook_url']) . '" target="_blank">Link</a>' : '—' ?></p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
