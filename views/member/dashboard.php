<?php
$pageTitle = 'Member Dashboard';
$loadCharts = true;
require_once dirname(__DIR__, 2) . '/components/dashboard-layout.php';

$eventModel    = new Event();
$donationModel = new Donation();
$msgModel      = new Message();
$userModel     = new User();

$userId       = (int)$user['id'];
$userFull     = $userModel->findById($userId);
$upcomingEvts = $eventModel->upcoming(3);
$myDonations  = $donationModel->byUser($userId);
$totalDonated = array_sum(array_column(
    array_filter($myDonations, fn($d) => $d['status'] === 'completed'),
    'amount'
));
$unreadMsgs   = $msgModel->unreadCount($userId);
$profilePct   = $userFull['is_profile_complete'] ? 100 : 40;
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h1>Welcome back, <?= h($user['first_name']) ?>! 👋</h1>
    <p>Here's what's happening in the Juaben2006 Connect community.</p>
  </div>
  <a href="<?= APP_URL ?>/member/profile" class="btn btn-blue btn-sm">
    <i class="fa fa-user-pen"></i> Edit Profile
  </a>
</div>

<!-- Profile Completion Banner -->
<?php if (!$userFull['is_profile_complete']): ?>
<div class="alert alert-info" style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
  <div>
    <strong><i class="fa fa-circle-info"></i> Complete your profile</strong><br>
    <span style="font-size:.88rem;">A complete profile helps other alumni connect with you.</span>
  </div>
  <a href="<?= APP_URL ?>/member/profile" class="btn btn-blue btn-sm">Complete Profile</a>
</div>
<?php endif; ?>

<!-- Stat Widgets -->
<div class="stats-row">
  <div class="stat-widget">
    <div class="stat-widget__icon icon-blue"><i class="fa fa-calendar-check"></i></div>
    <div>
      <div class="stat-widget__num"><?= count($upcomingEvts) ?></div>
      <div class="stat-widget__label">Upcoming Events</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-gold"><i class="fa fa-hand-holding-heart"></i></div>
    <div>
      <div class="stat-widget__num">₵<?= number_format($totalDonated) ?></div>
      <div class="stat-widget__label">Total Donated</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-green"><i class="fa fa-envelope"></i></div>
    <div>
      <div class="stat-widget__num"><?= $unreadMsgs ?></div>
      <div class="stat-widget__label">Unread Messages</div>
    </div>
  </div>
  <div class="stat-widget">
    <div class="stat-widget__icon icon-blue"><i class="fa fa-user-check"></i></div>
    <div>
      <div class="stat-widget__num"><?= $profilePct ?>%</div>
      <div class="stat-widget__label">Profile Complete</div>
    </div>
  </div>
</div>

<div class="grid-2">
  <!-- Profile Card -->
  <div>
    <div class="profile-card">
      <div class="profile-card__cover"></div>
      <div style="padding:0 20px 20px;text-align:center;">
        <div class="profile-card__avatar-wrap" style="margin-top:-40px;">
          <img src="<?= !empty($userFull['avatar'])
            ? UPLOADS_URL . '/avatars/' . h($userFull['avatar'])
            : APP_URL . '/assets/images/avatar-placeholder.png' ?>"
               alt="<?= h($user['first_name']) ?>"
               class="profile-card__avatar">
        </div>
        <h3 class="profile-card__name"><?= h($user['first_name'] . ' ' . $user['last_name']) ?></h3>
        <p class="profile-card__role"><?= h($user['role_name']) ?></p>
        <?php if ($userFull['graduation_year']): ?>
          <p class="text-sm text-muted mt-1"><i class="fa fa-graduation-cap"></i> Year Group <?= $userFull['graduation_year'] ?></p>
        <?php endif; ?>
        <?php if ($userFull['occupation']): ?>
          <p class="text-sm text-muted"><i class="fa fa-briefcase"></i> <?= h($userFull['occupation']) ?></p>
        <?php endif; ?>

        <div class="profile-card__meta">
          <div class="profile-card__stat">
            <div class="profile-card__stat-num"><?= count($myDonations) ?></div>
            <div class="profile-card__stat-label">Donations</div>
          </div>
          <div class="profile-card__stat">
            <div class="profile-card__stat-num">₵<?= number_format($totalDonated/1000, 1) ?>k</div>
            <div class="profile-card__stat-label">Contributed</div>
          </div>
        </div>

        <div class="mt-3">
          <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
            <span class="text-xs text-muted">Profile completion</span>
            <span class="text-xs fw-bold"><?= $profilePct ?>%</span>
          </div>
          <div class="progress">
            <div class="progress__bar" style="width:<?= $profilePct ?>%"></div>
          </div>
        </div>

        <a href="<?= APP_URL ?>/member/profile" class="btn btn-outline btn-sm btn-block mt-3">
          <i class="fa fa-pen"></i> Edit Profile
        </a>
      </div>
    </div>
  </div>

  <!-- Upcoming Events -->
  <div>
    <div class="table-card">
      <div class="table-card__header">
        <span class="table-card__title"><i class="fa fa-calendar text-blue"></i> Upcoming Events</span>
        <a href="<?= APP_URL ?>/member/events" class="btn btn-outline btn-sm">View All</a>
      </div>
      <?php if (empty($upcomingEvts)): ?>
        <p style="padding:24px;text-align:center;color:var(--gray-500);">No upcoming events.</p>
      <?php else: ?>
        <div style="padding:12px 20px;display:flex;flex-direction:column;gap:12px;">
          <?php foreach ($upcomingEvts as $ev): ?>
          <div style="display:flex;gap:14px;align-items:flex-start;padding:12px;border-radius:var(--radius-sm);background:var(--gray-50);">
            <div style="width:48px;height:48px;border-radius:10px;background:var(--blue-100);display:grid;place-items:center;flex-shrink:0;color:var(--blue-700);">
              <span style="font-family:var(--font-heading);font-weight:800;font-size:.95rem;"><?= date('d', strtotime($ev['starts_at'])) ?></span>
              <span style="font-size:.65rem;text-transform:uppercase;"><?= date('M', strtotime($ev['starts_at'])) ?></span>
            </div>
            <div style="flex:1;min-width:0;">
              <p class="fw-semibold" style="font-size:.9rem;"><?= h($ev['title']) ?></p>
              <?php if ($ev['location']): ?>
              <p class="text-xs text-muted"><i class="fa fa-location-dot"></i> <?= h($ev['location']) ?></p>
              <?php endif; ?>
            </div>
            <a href="<?= APP_URL ?>/events/<?= h($ev['slug']) ?>" class="btn btn-blue btn-sm" style="flex-shrink:0;">Register</a>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Recent Donations -->
<?php if (!empty($myDonations)): ?>
<div class="table-card mt-4">
  <div class="table-card__header">
    <span class="table-card__title"><i class="fa fa-hand-holding-heart text-gold"></i> Recent Donations</span>
    <a href="<?= APP_URL ?>/member/donations" class="btn btn-outline btn-sm">View All</a>
  </div>
  <div class="table-responsive">
    <table class="dash-table" data-searchable>
      <thead>
        <tr>
          <th>Date</th>
          <th>Campaign</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Reference</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (array_slice($myDonations, 0, 5) as $d): ?>
        <tr>
          <td><?= date('M j, Y', strtotime($d['created_at'])) ?></td>
          <td><?= h($d['campaign_title'] ?? 'General Donation') ?></td>
          <td class="fw-semibold">₵<?= number_format($d['amount']) ?></td>
          <td><span class="status status-<?= h($d['status']) ?>"><?= ucfirst(h($d['status'])) ?></span></td>
          <td class="text-xs text-muted"><?= h(substr($d['reference'], 0, 12)) ?>…</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
