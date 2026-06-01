<?php
$pageTitle = 'Financial Dashboard';
$loadCharts = true;
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_FINANCIAL, ROLE_ADMIN);
require_once ROOT_PATH . '/components/dashboard-layout.php';

$donationModel = new Donation();

$totalRaised   = $donationModel->totalRaised();
$pendingCount  = $donationModel->pendingCount();
$monthlyData   = $donationModel->monthlyRevenue();
$allDonations  = $donationModel->all(1, PAGE_SIZE);
$pendingDons   = $donationModel->all(1, 10, ['status' => 'pending']);

$revLabels  = array_column($monthlyData, 'month');
$revTotals  = array_map('floatval', array_column($monthlyData, 'total'));
$monthRev   = end($revTotals) ?: 0;
$prevRev    = prev($revTotals) ?: 0;

$inlineJs = "
  renderLineChart('revenueChart',
    " . json_encode($revLabels) . ",
    " . json_encode($revTotals) . ",
    'Monthly Revenue (₵)', '#1a3c6e'
  );
  renderBarChart('donationChart',
    " . json_encode($revLabels) . ",
    " . json_encode($revTotals) . ",
    'Donations (₵)', '#c9a227'
  );
";
?>

<div class="page-header">
  <div>
    <h1>Financial Dashboard</h1>
    <p>Track revenue, donations and generate financial reports.</p>
  </div>
  <a href="<?= APP_URL ?>/financial/reports/export" class="btn btn-blue btn-sm">
    <i class="fa fa-file-csv"></i> Export CSV
  </a>
</div>

<!-- Stat Widgets -->
<div class="stats-row">
  <div class="stat-widget">
    <div class="stat-widget__icon icon-green"><i class="fa fa-money-bill-trend-up"></i></div>
    <div>
      <div class="stat-widget__num">₵<?= number_format($totalRaised) ?></div>
      <div class="stat-widget__label">Total Raised</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-gold"><i class="fa fa-clock-rotate-left"></i></div>
    <div>
      <div class="stat-widget__num"><?= $pendingCount ?></div>
      <div class="stat-widget__label">Pending Donations</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-blue"><i class="fa fa-chart-line"></i></div>
    <div>
      <div class="stat-widget__num">₵<?= number_format($monthRev) ?></div>
      <div class="stat-widget__label">This Month</div>
      <?php if ($prevRev > 0): ?>
      <span class="stat-widget__delta <?= $monthRev >= $prevRev ? 'delta-up' : 'delta-down' ?>">
        <i class="fa fa-arrow-<?= $monthRev >= $prevRev ? 'up' : 'down' ?>"></i>
        <?= round(abs($monthRev - $prevRev) / max($prevRev, 1) * 100, 1) ?>% vs last month
      </span>
      <?php endif; ?>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-blue"><i class="fa fa-check-double"></i></div>
    <div>
      <div class="stat-widget__num"><?= $allDonations['total'] ?></div>
      <div class="stat-widget__label">Total Transactions</div>
    </div>
  </div>
</div>

<!-- Charts -->
<div class="grid-2">
  <div class="chart-card">
    <h3><i class="fa fa-chart-line text-blue"></i> Monthly Revenue Trend</h3>
    <div style="height:260px;"><canvas id="revenueChart"></canvas></div>
  </div>
  <div class="chart-card">
    <h3><i class="fa fa-chart-bar text-gold"></i> Donation Trends</h3>
    <div style="height:260px;"><canvas id="donationChart"></canvas></div>
  </div>
</div>

<!-- Pending Approvals -->
<?php if (!empty($pendingDons['data'])): ?>
<div class="table-card mb-4">
  <div class="table-card__header">
    <span class="table-card__title"><i class="fa fa-clock text-warning"></i> Pending Approvals</span>
  </div>
  <div class="table-responsive">
    <table class="dash-table">
      <thead>
        <tr><th>Donor</th><th>Amount</th><th>Reference</th><th>Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($pendingDons['data'] as $d): ?>
        <tr>
          <td><?= h($d['donor_name'] ?? ($d['first_name'] . ' ' . $d['last_name'])) ?></td>
          <td class="fw-semibold">₵<?= number_format($d['amount']) ?></td>
          <td class="text-xs text-muted"><?= h($d['reference']) ?></td>
          <td><?= date('M j, Y', strtotime($d['created_at'])) ?></td>
          <td style="display:flex;gap:6px;">
            <form method="POST" action="<?= APP_URL ?>/financial/donations/<?= $d['id'] ?>/approve" style="display:inline;">
              <?= csrf_field() ?>
              <button class="btn btn-sm" style="background:#d1fae5;color:#065f46;border:none;padding:6px 12px;border-radius:6px;">
                <i class="fa fa-check"></i> Approve
              </button>
            </form>
            <form method="POST" action="<?= APP_URL ?>/financial/donations/<?= $d['id'] ?>/cancel" style="display:inline;">
              <?= csrf_field() ?>
              <button class="btn btn-sm btn-danger" data-confirm="Cancel this donation?">
                <i class="fa fa-times"></i>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- All Transactions -->
<div class="table-card">
  <div class="table-card__header">
    <span class="table-card__title">All Transactions</span>
    <div style="display:flex;gap:10px;align-items:center;">
      <select class="form-control" style="width:150px;padding:7px 12px;" onchange="location='<?= APP_URL ?>/financial/donations?status='+this.value">
        <option value="">All Statuses</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>
  </div>
  <div class="table-responsive">
    <table class="dash-table">
      <thead>
        <tr><th>Donor</th><th>Campaign</th><th>Amount</th><th>Gateway</th><th>Status</th><th>Date</th></tr>
      </thead>
      <tbody>
        <?php foreach ($allDonations['data'] as $d): ?>
        <tr>
          <td>
            <div class="user-cell__name"><?= h($d['donor_name'] ?? ($d['first_name'] . ' ' . $d['last_name'])) ?></div>
            <div class="user-cell__email"><?= h($d['donor_email'] ?? '') ?></div>
          </td>
          <td><?= h($d['campaign_title'] ?? 'General') ?></td>
          <td class="fw-semibold">₵<?= number_format($d['amount']) ?></td>
          <td><?= h(ucfirst($d['gateway'])) ?></td>
          <td><span class="status status-<?= h($d['status']) ?>"><?= ucfirst(h($d['status'])) ?></span></td>
          <td><?= date('M j, Y', strtotime($d['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
