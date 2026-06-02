<?php
$pageTitle = 'Add Member';
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
require_once ROOT_PATH . '/components/dashboard-layout.php';

$errors = $_SESSION['admin_member_errors'] ?? [];
$old = $_SESSION['admin_member_old'] ?? [];
unset($_SESSION['admin_member_errors'], $_SESSION['admin_member_old']);
?>

<div class="page-header">
  <div>
    <h1>Add Member</h1>
    <p>Create a new member account and assign a role.</p>
  </div>
  <a href="<?= APP_URL ?>/executive/members" class="btn btn-outline btn-sm"><i class="fa fa-arrow-left"></i> Back to Members</a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-error">
    <ul>
      <?php foreach ($errors as $error): ?>
        <li><?= h($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card card-form">
  <form method="POST" action="<?= APP_URL ?>/executive/members/store">
    <?= csrf_field() ?>

    <div class="form-grid">
      <div class="form-group">
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" class="form-control" required value="<?= h($old['email'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" class="form-control" required minlength="8" autocomplete="new-password">
      </div>

      <div class="form-group">
        <label for="password_confirm">Confirm Password</label>
        <input id="password_confirm" name="password_confirm" type="password" class="form-control" required minlength="8" autocomplete="new-password">
      </div>

      <div class="form-group">
        <label for="first_name">First Name</label>
        <input id="first_name" name="first_name" type="text" class="form-control" required value="<?= h($old['first_name'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="last_name">Last Name</label>
        <input id="last_name" name="last_name" type="text" class="form-control" required value="<?= h($old['last_name'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="role_id">Role</label>
        <select id="role_id" name="role_id" class="form-control" required>
          <option value="<?= ROLE_MEMBER ?>" <?= (int)($old['role_id'] ?? ROLE_MEMBER) === ROLE_MEMBER ? 'selected' : '' ?>>Member</option>
          <option value="<?= ROLE_EXECUTIVE ?>" <?= (int)($old['role_id'] ?? 0) === ROLE_EXECUTIVE ? 'selected' : '' ?>>Executive</option>
          <option value="<?= ROLE_ADMIN ?>" <?= (int)($old['role_id'] ?? 0) === ROLE_ADMIN ? 'selected' : '' ?>>Admin</option>
        </select>
      </div>

      <div class="form-group">
        <label for="occupation">Occupation</label>
        <input id="occupation" name="occupation" type="text" class="form-control" value="<?= h($old['occupation'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="employer">Employer</label>
        <input id="employer" name="employer" type="text" class="form-control" value="<?= h($old['employer'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="city">City</label>
        <input id="city" name="city" type="text" class="form-control" value="<?= h($old['city'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="country">Country</label>
        <input id="country" name="country" type="text" class="form-control" value="<?= h($old['country'] ?? '') ?>">
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-blue">Create Member</button>
      <a href="<?= APP_URL ?>/executive/members" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
