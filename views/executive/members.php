<?php
$pageTitle = 'Manage Members';
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
require_once ROOT_PATH . '/components/dashboard-layout.php';

$userModel = new User();
$page   = max(1, (int)($_GET['page'] ?? 1));
$search = sanitize_input($_GET['search'] ?? '');
$roleF  = (int)($_GET['role_id'] ?? 0);
$result = $userModel->all($page, PAGE_SIZE, array_filter([
    'search'  => $search,
    'role_id' => $roleF ?: null,
]));
$roles  = Database::getInstance()->query('SELECT * FROM roles')->fetchAll();
?>

<div class="page-header">
  <div><h1>Members (<?= $result['total'] ?>)</h1><p>Manage all registered alumni members.</p></div>
  <?php if (auth()['role_id'] == ROLE_ADMIN): ?>
    <a href="<?= APP_URL ?>/executive/members/create" class="btn btn-blue btn-sm"><i class="fa fa-user-plus"></i> Add Member</a>
  <?php endif; ?>
</div>

<?php $s = get_flash('success'); if ($s): ?>
  <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($s['message']) ?></div>
<?php endif; ?>

<!-- Filters -->
<div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:center;">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap;">
    <input type="text" name="search" class="form-control" placeholder="Search name or email..." style="flex:1;min-width:200px;" value="<?= h($search) ?>">
    <select name="role_id" class="form-control" style="width:180px;">
      <option value="">All Roles</option>
      <?php foreach ($roles as $r): ?>
        <option value="<?= $r['id'] ?>" <?= $roleF == $r['id'] ? 'selected' : '' ?>><?= h($r['label']) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-blue btn-sm"><i class="fa fa-search"></i> Filter</button>
    <a href="<?= APP_URL ?>/executive/members" class="btn btn-outline btn-sm">Clear</a>
  </form>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="dash-table">
      <thead>
        <tr><th>Member</th><th>Role</th><th>Profile</th><th>Status</th><th>Last Login</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($result['data'] as $m): ?>
        <tr>
          <td>
            <div class="user-cell">
              <img src="<?= !empty($m['avatar']) ? UPLOADS_URL . '/avatars/' . h($m['avatar']) : APP_URL . '/assets/images/avatar-placeholder.png' ?>" alt="<?= h($m['first_name']) ?>">
              <div>
                <div class="user-cell__name"><?= h($m['first_name'] . ' ' . $m['last_name']) ?></div>
                <div class="user-cell__email"><?= h($m['email']) ?></div>
              </div>
            </div>
          </td>
          <td>
            <?php if (auth()['role_id'] == ROLE_ADMIN): ?>
              <form method="POST" action="<?= APP_URL ?>/executive/members/<?= $m['id'] ?>/role">
                <?= csrf_field() ?>
                <select name="role_id" class="form-control" style="padding:4px 8px;font-size:.8rem;" onchange="this.form.submit()">
                  <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $m['role_id'] == $r['id'] ? 'selected' : '' ?>><?= h($r['label']) ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            <?php else: ?>
              <?= h($m['role_label']) ?>
            <?php endif; ?>
          </td>
          <td>
            <div class="progress" style="width:70px;"><div class="progress__bar" style="width:<?= $m['is_profile_complete'] ? 100 : 40 ?>%;"></div></div>
          </td>
          <td>
            <form method="POST" action="<?= APP_URL ?>/executive/members/<?= $m['id'] ?>/toggle">
              <?= csrf_field() ?>
              <input type="hidden" name="active" value="<?= $m['is_active'] ? 0 : 1 ?>">
              <button class="status status-<?= $m['is_active'] ? 'active' : 'inactive' ?>" style="border:none;cursor:pointer;" title="Click to toggle">
                <?= $m['is_active'] ? 'Active' : 'Inactive' ?>
              </button>
            </form>
          </td>
          <td class="text-sm text-muted"><?= $m['last_login_at'] ? date('M j', strtotime($m['last_login_at'])) : 'Never' ?></td>
          <td style="display:flex;gap:6px;align-items:center;">
            <a href="<?= APP_URL ?>/executive/members/<?= $m['id'] ?>" class="btn btn-outline btn-sm"><i class="fa fa-eye"></i></a>
            <?php if (auth()['role_id'] == ROLE_ADMIN && $m['id'] !== auth()['id']): ?>
              <form method="POST" action="<?= APP_URL ?>/executive/members/<?= $m['id'] ?>/delete" style="display:inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this member permanently?');">
                  <i class="fa fa-trash"></i>
                </button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($result['data'])): ?>
          <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--gray-500);">No members found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$totalPages = (int)ceil($result['total'] / $result['per_page']);
if ($totalPages > 1): ?>
<div class="pagination">
  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?search=<?= urlencode($search) ?>&role_id=<?= $roleF ?>&page=<?= $i ?>" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
