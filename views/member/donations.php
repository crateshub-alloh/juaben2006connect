<?php
$pageTitle = 'My Donations';
require_once dirname(__DIR__, 2) . '/components/dashboard-layout.php';

$donationModel = new Donation();
$userId        = (int)$user['id'];
$donations     = $donationModel->byUser($userId);
$totalDonated  = array_sum(array_column(
    array_filter($donations, fn($d) => $d['status'] === 'completed'),
    'amount'
));
?>

<div class="page-header">
  <div><h1>My Donations</h1><p>Your donation history and contributions.</p></div>
  <a href="<?= APP_URL ?>/donate" class="btn btn-primary btn-sm"><i class="fa fa-hand-holding-heart"></i> Donate Again</a>
</div>

<!-- Summary -->
<div class="stats-row">
  <div class="stat-widget">
    <div class="stat-widget__icon icon-gold"><i class="fa fa-coins"></i></div>
    <div>
      <div class="stat-widget__num">₵<?= number_format($totalDonated) ?></div>
      <div class="stat-widget__label">Total Contributed</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-blue"><i class="fa fa-list-check"></i></div>
    <div>
      <div class="stat-widget__num"><?= count($donations) ?></div>
      <div class="stat-widget__label">Total Donations</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-green"><i class="fa fa-check-circle"></i></div>
    <div>
      <div class="stat-widget__num"><?= count(array_filter($donations, fn($d) => $d['status'] === 'completed')) ?></div>
      <div class="stat-widget__label">Completed</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-gold"><i class="fa fa-clock"></i></div>
    <div>
      <div class="stat-widget__num"><?= count(array_filter($donations, fn($d) => $d['status'] === 'pending')) ?></div>
      <div class="stat-widget__label">Pending</div>
    </div>
  </div>
</div>

<div class="table-card">
  <div class="table-card__header"><span class="table-card__title">Donation History</span></div>
  <?php if (empty($donations)): ?>
    <p style="padding:40px;text-align:center;color:var(--gray-500);">No donations yet. <a href="<?= APP_URL ?>/donate" style="color:var(--blue-700);">Make your first donation</a></p>
  <?php else: ?>
  <div class="table-responsive">
    <table class="dash-table">
      <thead><tr><th>Date</th><th>Campaign</th><th>Amount</th><th>Reference</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($donations as $d): ?>
        <tr>
          <td><?= date('M j, Y', strtotime($d['created_at'])) ?></td>
          <td><?= h($d['campaign_title'] ?? 'General Donation') ?></td>
          <td class="fw-semibold">₵<?= number_format($d['amount']) ?></td>
          <td><code style="font-size:.8rem;background:var(--gray-100);padding:2px 8px;border-radius:4px;"><?= h($d['reference']) ?></code></td>
          <td><span class="status status-<?= h($d['status']) ?>"><?= ucfirst(h($d['status'])) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
