<?php
$pageTitle = 'Events';
require_once dirname(__DIR__, 2) . '/components/dashboard-layout.php';

$eventModel = new Event();
$userId     = (int)$user['id'];
$upcoming   = $eventModel->upcoming(20);
?>

<div class="page-header">
  <div><h1>Events</h1><p>Browse and register for upcoming events.</p></div>
  <a href="<?= APP_URL ?>/events" class="btn btn-outline btn-sm" target="_blank">Public Events Page</a>
</div>

<div class="events-grid">
  <?php foreach ($upcoming as $ev): ?>
  <?php $registered = $eventModel->isRegistered((int)$ev['id'], $userId); ?>
  <div class="card">
    <?php if ($ev['image']): ?>
      <img src="<?= UPLOADS_URL ?>/events/<?= h($ev['image']) ?>" class="card__img" loading="lazy" alt="<?= h($ev['title']) ?>">
    <?php else: ?>
      <div class="card__img" style="background:linear-gradient(135deg,var(--blue-800),var(--blue-600));display:grid;place-items:center;">
        <i class="fa fa-calendar-days" style="font-size:2.5rem;color:var(--gold-400)"></i>
      </div>
    <?php endif; ?>
    <div class="card__body">
      <span class="event-badge badge-<?= h($ev['status']) ?>"><?= ucfirst(h($ev['status'])) ?></span>
      <h3 class="card__title mt-2"><?= h($ev['title']) ?></h3>
      <p class="text-sm text-muted"><i class="fa fa-calendar"></i> <?= date('M j, Y g:i a', strtotime($ev['starts_at'])) ?></p>
      <?php if ($ev['location']): ?>
        <p class="text-sm text-muted"><i class="fa fa-location-dot"></i> <?= h($ev['location']) ?></p>
      <?php endif; ?>
    </div>
    <div class="card__footer">
      <?php if ($registered): ?>
        <span class="status status-active"><i class="fa fa-check"></i> Registered</span>
      <?php else: ?>
        <form method="POST" action="<?= APP_URL ?>/member/events/<?= $ev['id'] ?>/register">
          <?= csrf_field() ?>
          <button class="btn btn-blue btn-sm"><i class="fa fa-calendar-check"></i> Register</button>
        </form>
      <?php endif; ?>
      <span class="text-xs text-muted"><?= $ev['registrations'] ?> attending</span>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($upcoming)): ?>
    <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--gray-500);">
      <i class="fa fa-calendar-xmark" style="font-size:3rem;opacity:.3;display:block;margin-bottom:16px;"></i>
      No upcoming events right now.
    </div>
  <?php endif; ?>
</div>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
