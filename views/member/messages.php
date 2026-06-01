<?php
$pageTitle = 'Messages';
require_once dirname(__DIR__, 2) . '/components/dashboard-layout.php';

$msgModel  = new Message();
$userId    = (int)$user['id'];
$tab       = sanitize_input($_GET['tab'] ?? 'inbox');
$inbox     = $msgModel->inbox($userId);
$messages  = $tab === 'sent' ? $msgModel->sent($userId) : $inbox;

$composeSuccess = get_flash('success');
?>

<div class="page-header">
  <div><h1>Messages</h1></div>
  <button onclick="document.getElementById('composeModal').classList.toggle('open')" class="btn btn-blue btn-sm">
    <i class="fa fa-pen"></i> Compose
  </button>
</div>

<?php if ($composeSuccess): ?>
  <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($composeSuccess['message']) ?></div>
<?php endif; ?>

<!-- Tabs -->
<div style="display:flex;gap:8px;margin-bottom:20px;">
  <a href="?tab=inbox" class="btn <?= $tab === 'inbox' ? 'btn-blue' : 'btn-outline' ?> btn-sm"><i class="fa fa-inbox"></i> Inbox (<?= count($inbox) ?>)</a>
  <a href="?tab=sent"  class="btn <?= $tab === 'sent'  ? 'btn-blue' : 'btn-outline' ?> btn-sm"><i class="fa fa-paper-plane"></i> Sent</a>
</div>

<div class="table-card">
  <?php if (empty($messages)): ?>
    <p style="padding:40px;text-align:center;color:var(--gray-500);">
      <i class="fa fa-envelope-open" style="font-size:2rem;opacity:.3;display:block;margin-bottom:12px;"></i>
      No messages in your <?= $tab ?>.
    </p>
  <?php else: ?>
    <div class="activity-feed">
      <?php foreach ($messages as $msg): ?>
      <?php
        $isUnread = !$msg['read_at'] && $tab === 'inbox';
        if ($tab === 'inbox' && !$msg['read_at']) {
            $msgModel->markRead((int)$msg['id']);
        }
      ?>
      <div class="activity-item" style="<?= $isUnread ? 'background:var(--blue-100)' : '' ?>">
        <div class="activity-icon" style="background:<?= $msg['is_broadcast'] ? 'var(--gold-100)' : 'var(--blue-100)' ?>;color:<?= $msg['is_broadcast'] ? 'var(--gold-600)' : 'var(--blue-700)' ?>;">
          <i class="fa fa-<?= $msg['is_broadcast'] ? 'bullhorn' : 'envelope' ?>"></i>
        </div>
        <div class="activity-body" style="flex:1;">
          <div style="display:flex;justify-content:space-between;gap:10px;">
            <p class="fw-semibold text-sm"><?= h($msg['subject']) ?></p>
            <time class="text-xs text-muted" style="white-space:nowrap;"><?= date('M j, Y', strtotime($msg['created_at'])) ?></time>
          </div>
          <?php if ($tab === 'inbox'): ?>
            <p class="text-xs text-muted">From: <?= h($msg['first_name'] . ' ' . $msg['last_name']) ?><?= $msg['is_broadcast'] ? ' (Broadcast)' : '' ?></p>
          <?php endif; ?>
          <p class="text-sm" style="margin-top:4px;color:var(--gray-700);"><?= h(mb_strimwidth($msg['body'], 0, 120, '...')) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Compose Modal -->
<div id="composeModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;display:none;place-items:center;">
  <div style="background:var(--white);border-radius:var(--radius-md);padding:32px;width:100%;max-width:520px;box-shadow:var(--shadow-xl);margin:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
      <h3>Compose Message</h3>
      <button onclick="document.getElementById('composeModal').classList.remove('open')" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--gray-600);"><i class="fa fa-xmark"></i></button>
    </div>
    <form method="POST" action="<?= APP_URL ?>/member/messages/send">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label">Recipient Email</label>
        <input type="email" name="recipient_email" class="form-control" required placeholder="member@example.com">
      </div>
      <div class="form-group">
        <label class="form-label">Subject</label>
        <input type="text" name="subject" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Message</label>
        <textarea name="body" class="form-control" rows="5" required></textarea>
      </div>
      <button type="submit" class="btn btn-blue btn-block">Send Message</button>
    </form>
  </div>
</div>

<script>
const modal = document.getElementById('composeModal');
modal.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });
</script>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
