<?php
$pageTitle = 'Create Event';
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
$editing = !empty($_GET['id']);
if ($editing) {
    $event = (new Event)->findById((int)$_GET['id']);
    if (!$event) redirect('/executive/events');
    $pageTitle = 'Edit Event';
}
require_once ROOT_PATH . '/components/dashboard-layout.php';
?>

<div class="page-header">
  <div><h1><?= $editing ? 'Edit Event' : 'Create New Event' ?></h1></div>
  <a href="<?= APP_URL ?>/executive/events" class="btn btn-outline btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div class="card" style="padding:32px;max-width:800px;">
  <form method="POST" action="<?= APP_URL ?>/executive/events/<?= $editing ? 'update' : 'store' ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($editing): ?>
      <input type="hidden" name="id" value="<?= $event['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
      <label class="form-label">Event Title *</label>
      <input type="text" name="title" class="form-control" required value="<?= h($event['title'] ?? '') ?>" placeholder="Annual Alumni Dinner 2026">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
      <div class="form-group">
        <label class="form-label">Start Date & Time *</label>
        <input type="datetime-local" name="starts_at" class="form-control" required
               value="<?= isset($event) ? date('Y-m-d\TH:i', strtotime($event['starts_at'])) : '' ?>">
      </div>
      <div class="form-group">
        <label class="form-label">End Date & Time *</label>
        <input type="datetime-local" name="ends_at" class="form-control" required
               value="<?= isset($event) ? date('Y-m-d\TH:i', strtotime($event['ends_at'])) : '' ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control" placeholder="Lagos, Ghana" value="<?= h($event['location'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Venue</label>
        <input type="text" name="venue" class="form-control" placeholder="Grand Hall, Eko Hotel" value="<?= h($event['venue'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Capacity (leave blank = unlimited)</label>
        <input type="number" name="capacity" class="form-control" min="1" value="<?= h($event['capacity'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Ticket Price (₵) – 0 = free</label>
        <input type="number" name="ticket_price" class="form-control" min="0" step="100" value="<?= h($event['ticket_price'] ?? 0) ?>">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="4" placeholder="Tell members about this event..."><?= h($event['description'] ?? '') ?></textarea>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <?php foreach (['draft','upcoming','ongoing','completed','cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= (($event['status'] ?? 'upcoming') === $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Visibility</label>
        <select name="is_public" class="form-control">
          <option value="1" <?= (($event['is_public'] ?? 1) == 1) ? 'selected' : '' ?>>Public</option>
          <option value="0" <?= (($event['is_public'] ?? 1) == 0) ? 'selected' : '' ?>>Members Only</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Event Image</label>
      <input type="file" name="image" class="form-control" accept="image/*">
      <?php if (!empty($event['image'])): ?>
        <img src="<?= UPLOADS_URL ?>/events/<?= h($event['image']) ?>" style="height:80px;border-radius:8px;margin-top:8px;object-fit:cover;">
      <?php endif; ?>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:12px;">
      <a href="<?= APP_URL ?>/executive/events" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-blue"><i class="fa fa-floppy-disk"></i> <?= $editing ? 'Update Event' : 'Create Event' ?></button>
    </div>
  </form>
</div>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
