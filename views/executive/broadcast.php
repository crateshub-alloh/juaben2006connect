<?php
$pageTitle = 'Broadcast Messages';
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
require_once ROOT_PATH . '/components/dashboard-layout.php';

$msgModel = new Message();
$sent     = $msgModel->sent((int)auth()['id']);
$broadcasts = array_filter($sent, fn($m) => $m['is_broadcast']);
?>

<div class="page-header">
  <div><h1>Broadcast Messages</h1><p>Send announcements to all members.</p></div>
</div>

<?php $s = get_flash('success'); if ($s): ?>
  <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($s['message']) ?></div>
<?php endif; ?>

<div class="grid-2" style="align-items:start;">
  <div class="card" style="padding:28px;">
    <h3 style="margin-bottom:20px;">New Broadcast</h3>
    <form method="POST" action="<?= APP_URL ?>/executive/broadcast/send">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label">Subject</label>
        <input type="text" name="subject" class="form-control" required placeholder="Important Announcement">
      </div>
      <div class="form-group">
        <label class="form-label">Message</label>
        <textarea name="body" class="form-control" rows="8" required placeholder="Write your message to all members..."></textarea>
      </div>
      <div style="background:var(--gold-100);border-radius:var(--radius-sm);padding:12px;margin-bottom:16px;font-size:.87rem;color:#7c6200;">
        <i class="fa fa-triangle-exclamation"></i> This message will be sent to <strong>all members</strong>.
      </div>
      <button type="submit" class="btn btn-blue btn-block">
        <i class="fa fa-bullhorn"></i> Send Broadcast
      </button>
    </form>
  </div>

  <div class="table-card">
    <div class="table-card__header"><span class="table-card__title">Sent Broadcasts</span></div>
    <?php if (empty($broadcasts)): ?>
      <p style="padding:24px;text-align:center;color:var(--gray-500);">No broadcasts sent yet.</p>
    <?php else: ?>
      <div class="activity-feed">
        <?php foreach ($broadcasts as $msg): ?>
        <div class="activity-item">
          <div class="activity-icon icon-blue" style="background:var(--blue-100);color:var(--blue-700);">
            <i class="fa fa-bullhorn"></i>
          </div>
          <div class="activity-body">
            <p class="fw-semibold" style="font-size:.9rem;"><?= h($msg['subject']) ?></p>
            <p class="text-sm text-muted"><?= h(mb_strimwidth($msg['body'], 0, 80, '...')) ?></p>
            <time class="text-xs text-muted"><?= date('M j, Y g:i a', strtotime($msg['created_at'])) ?></time>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
