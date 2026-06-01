<?php
$pageTitle = 'Executive Dashboard';
$loadCharts = true;
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
require_once ROOT_PATH . '/components/dashboard-layout.php';

$userModel    = new User();
$eventModel   = new Event();
$projectModel = new Project();
$donationModel= new Donation();

$totalMembers  = $userModel->totalCount();
$activeMembers = $userModel->activeCount();
$totalEvents   = $eventModel->totalCount();
$totalProjects = $projectModel->totalCount();
$fundsRaised   = $donationModel->totalRaised();

$growthData    = $userModel->monthlyGrowth();
$attendData    = $eventModel->monthlyAttendance();
$recentMembers = $userModel->all(1, 8)['data'];

// Prepare chart data
$growthLabels = array_column($growthData, 'month');
$growthCounts = array_column($growthData, 'count');
$attendLabels = array_column($attendData, 'month');
$attendCounts = array_column($attendData, 'attendees');

$inlineJs = "
  renderLineChart('growthChart',
    " . json_encode($growthLabels) . ",
    " . json_encode($growthCounts) . ",
    'New Members', '#1a3c6e'
  );
  renderBarChart('attendChart',
    " . json_encode($attendLabels) . ",
    " . json_encode($attendCounts) . ",
    'Event Attendance', '#c9a227'
  );
";
?>

<div class="page-header">
  <div>
    <h1>Executive Dashboard</h1>
    <p>Overview of community activity and engagement.</p>
  </div>
  <div style="display:flex;gap:10px;">
    <a href="<?= APP_URL ?>/executive/events/create" class="btn btn-blue btn-sm"><i class="fa fa-plus"></i> New Event</a>
    <a href="<?= APP_URL ?>/executive/broadcast" class="btn btn-outline btn-sm"><i class="fa fa-bullhorn"></i> Broadcast</a>
  </div>
</div>

<!-- Stat Widgets -->
<div class="stats-row">
  <div class="stat-widget">
    <div class="stat-widget__icon icon-blue"><i class="fa fa-users"></i></div>
    <div>
      <div class="stat-widget__num"><?= number_format($totalMembers) ?></div>
      <div class="stat-widget__label">Total Members</div>
      <span class="stat-widget__delta delta-up"><i class="fa fa-arrow-up"></i> <?= $activeMembers ?> active</span>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-gold"><i class="fa fa-calendar-days"></i></div>
    <div>
      <div class="stat-widget__num"><?= number_format($totalEvents) ?></div>
      <div class="stat-widget__label">Total Events</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-green"><i class="fa fa-diagram-project"></i></div>
    <div>
      <div class="stat-widget__num"><?= number_format($totalProjects) ?></div>
      <div class="stat-widget__label">Projects</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-gold"><i class="fa fa-coins"></i></div>
    <div>
      <div class="stat-widget__num">₵<?= number_format($fundsRaised/1000, 1) ?>k</div>
      <div class="stat-widget__label">Funds Raised</div>
    </div>
  </div>
</div>

<!-- Charts -->
<div class="grid-2">
  <div class="chart-card">
    <h3><i class="fa fa-chart-line text-blue"></i> Monthly Member Growth</h3>
    <div style="height:260px;">
      <canvas id="growthChart"></canvas>
    </div>
  </div>
  <div class="chart-card">
    <h3><i class="fa fa-chart-bar text-gold"></i> Event Attendance (Monthly)</h3>
    <div style="height:260px;">
      <canvas id="attendChart"></canvas>
    </div>
  </div>
</div>

<!-- Recent Members -->
<div class="table-card">
  <div class="table-card__header">
    <span class="table-card__title">Recent Members</span>
    <div style="display:flex;gap:10px;">
      <input type="text" id="tableSearch" class="form-control" placeholder="Search..." style="width:200px;padding:7px 12px;font-size:.85rem;">
      <a href="<?= APP_URL ?>/executive/members" class="btn btn-outline btn-sm">View All</a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="dash-table" data-searchable>
      <thead>
        <tr>
          <th>Member</th>
          <th>Graduation</th>
          <th>Role</th>
          <th>Status</th>
          <th>Joined</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentMembers as $m): ?>
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
          <td><?= $m['graduation_year'] ?? '–' ?></td>
          <td><?= h($m['role_label']) ?></td>
          <td><span class="status status-<?= $m['is_active'] ? 'active' : 'inactive' ?>"><?= $m['is_active'] ? 'Active' : 'Inactive' ?></span></td>
          <td><?= date('M j, Y', strtotime($m['created_at'])) ?></td>
          <td>
            <a href="<?= APP_URL ?>/executive/members/<?= $m['id'] ?>" class="btn btn-outline btn-sm">View</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
