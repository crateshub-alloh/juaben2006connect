<?php
$pageTitle = 'Admin Panel';
$loadCharts = true;
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_ADMIN);
require_once ROOT_PATH . '/components/dashboard-layout.php';

$userModel     = new User();
$eventModel    = new Event();
$projectModel  = new Project();
$donationModel = new Donation();

// Audit log
$db       = Database::getInstance();
$auditLog = $db->query(
    "SELECT al.*, u.first_name, u.last_name
     FROM audit_logs al LEFT JOIN users u ON u.id = al.user_id
     ORDER BY al.created_at DESC LIMIT 20"
)->fetchAll();

$roles = $db->query('SELECT * FROM roles')->fetchAll();
?>

<div class="page-header">
  <div>
    <h1><i class="fa fa-shield" style="color:var(--gold-500)"></i> Super Admin Panel</h1>
    <p>Full system control – manage users, roles, settings, and audit logs.</p>
  </div>
  <div style="display:flex;gap:10px;">
    <a href="<?= APP_URL ?>/admin/settings" class="btn btn-outline btn-sm"><i class="fa fa-gear"></i> Settings</a>
    <a href="<?= APP_URL ?>/executive/members/create" class="btn btn-blue btn-sm"><i class="fa fa-user-plus"></i> Add Member</a>
  </div>
</div>

<!-- Stat Widgets -->
<div class="stats-row">
  <div class="stat-widget">
    <div class="stat-widget__icon icon-blue"><i class="fa fa-users"></i></div>
    <div>
      <div class="stat-widget__num"><?= $userModel->totalCount() ?></div>
      <div class="stat-widget__label">Total Users</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-gold"><i class="fa fa-calendar-days"></i></div>
    <div>
      <div class="stat-widget__num"><?= $eventModel->totalCount() ?></div>
      <div class="stat-widget__label">Total Events</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-green"><i class="fa fa-coins"></i></div>
    <div>
      <div class="stat-widget__num">₵<?= number_format($donationModel->totalRaised()) ?></div>
      <div class="stat-widget__label">Total Raised</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-blue"><i class="fa fa-diagram-project"></i></div>
    <div>
      <div class="stat-widget__num"><?= $projectModel->totalCount() ?></div>
      <div class="stat-widget__label">Projects</div>
    </div>
  </div>
</div>

<div class="grid-2">
  <!-- User Roles breakdown -->
  <div class="chart-card">
    <h3><i class="fa fa-user-shield text-blue"></i> Role Distribution</h3>
    <?php
    $roleCounts = [];
    foreach ($roles as $r) {
        $count = $db->prepare('SELECT COUNT(*) FROM users WHERE role_id = ?');
        $count->execute([$r['id']]);
        $roleCounts[$r['label']] = (int)$count->fetchColumn();
    }
    ?>
    <div style="height:220px;">
      <canvas id="rolesChart"></canvas>
    </div>
    <?php $ij = "renderDoughnut('rolesChart'," . json_encode(array_keys($roleCounts)) . "," . json_encode(array_values($roleCounts)) . ",['#1a3c6e','#c9a227','#10b981','#3b82f6']);"; ?>
  </div>

  <!-- Quick Actions -->
  <div class="chart-card">
    <h3><i class="fa fa-bolt text-gold"></i> Quick Actions</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:8px;">
      <a href="<?= APP_URL ?>/executive/members" class="btn btn-outline" style="justify-content:flex-start;">
        <i class="fa fa-users"></i> Manage Members
      </a>
      <a href="<?= APP_URL ?>/executive/events/create" class="btn btn-outline" style="justify-content:flex-start;">
        <i class="fa fa-calendar-plus"></i> Create Event
      </a>
      <a href="<?= APP_URL ?>/executive/projects/create" class="btn btn-outline" style="justify-content:flex-start;">
        <i class="fa fa-folder-plus"></i> New Project
      </a>
      <a href="<?= APP_URL ?>/financial/reports/export" class="btn btn-outline" style="justify-content:flex-start;">
        <i class="fa fa-file-csv"></i> Export Report
      </a>
      <a href="<?= APP_URL ?>/executive/gallery" class="btn btn-outline" style="justify-content:flex-start;">
        <i class="fa fa-images"></i> Gallery
      </a>
      <a href="<?= APP_URL ?>/executive/broadcast" class="btn btn-outline" style="justify-content:flex-start;">
        <i class="fa fa-bullhorn"></i> Broadcast
      </a>
    </div>
  </div>
</div>

<!-- Audit Log -->
<div class="table-card">
  <div class="table-card__header">
    <span class="table-card__title"><i class="fa fa-scroll text-blue"></i> Audit Log</span>
    <a href="<?= APP_URL ?>/admin/audit" class="btn btn-outline btn-sm">Full Log</a>
  </div>
  <div class="table-responsive">
    <table class="dash-table">
      <thead>
        <tr><th>User</th><th>Action</th><th>Entity</th><th>IP</th><th>Date</th></tr>
      </thead>
      <tbody>
        <?php foreach ($auditLog as $log): ?>
        <tr>
          <td><?= $log['first_name'] ? h($log['first_name'] . ' ' . $log['last_name']) : 'System' ?></td>
          <td><code style="background:var(--gray-100);padding:2px 8px;border-radius:4px;font-size:.8rem;"><?= h($log['action']) ?></code></td>
          <td class="text-sm"><?= h($log['entity_type'] ?? '–') ?> #<?= $log['entity_id'] ?? '' ?></td>
          <td class="text-xs text-muted"><?= h($log['ip_address'] ?? '') ?></td>
          <td class="text-sm"><?= date('M j, Y H:i', strtotime($log['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$inlineJs = $ij ?? '';
require_once ROOT_PATH . '/components/dashboard-layout-end.php';
?>
