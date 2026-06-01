<?php
$pageTitle = 'Manage Events';
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
require_once ROOT_PATH . '/components/dashboard-layout.php';

$eventModel = new Event();
$page       = max(1, (int)($_GET['page'] ?? 1));
$status     = sanitize_input($_GET['status'] ?? '');
$search     = sanitize_input($_GET['search'] ?? '');
$result     = $eventModel->all($page, PAGE_SIZE, array_filter(compact('status', 'search')));
?>

<div class="page-header">
  <div><h1>Events</h1><p>Create and manage alumni events.</p></div>
  <a href="<?= APP_URL ?>/executive/events/create" class="btn btn-blue"><i class="fa fa-plus"></i> New Event</a>
</div>

<?php $s = get_flash('success'); if ($s): ?>
  <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($s['message']) ?></div>
<?php endif; ?>

<!-- Filters -->
<div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
  <input type="text" id="tableSearch" class="form-control" placeholder="Search events..." style="width:240px;" value="<?= h($search) ?>">
  <?php foreach ([''=>'All','upcoming'=>'Upcoming','ongoing'=>'Ongoing','completed'=>'Completed','cancelled'=>'Cancelled'] as $k => $v): ?>
    <a href="?status=<?= $k ?>" class="btn <?= $status === $k ? 'btn-blue' : 'btn-outline' ?> btn-sm"><?= $v ?></a>
  <?php endforeach; ?>
</div>

<div class="table-card">
  <div class="table-card__header">
    <span class="table-card__title">Events (<?= $result['total'] ?>)</span>
  </div>
  <div class="table-responsive">
    <table class="dash-table" data-searchable>
      <thead>
        <tr><th>Event</th><th>Date</th><th>Location</th><th>Registrations</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($result['data'] as $ev): ?>
        <tr>
          <td>
            <div class="fw-semibold"><?= h($ev['title']) ?></div>
            <div class="text-xs text-muted">By <?= h($ev['first_name'] . ' ' . $ev['last_name']) ?></div>
          </td>
          <td class="text-sm"><?= date('M j, Y', strtotime($ev['starts_at'])) ?></td>
          <td class="text-sm"><?= h($ev['location'] ?? '–') ?></td>
          <td><?= $ev['registrations'] ?><?= $ev['capacity'] ? '/' . $ev['capacity'] : '' ?></td>
          <td><span class="event-badge badge-<?= h($ev['status']) ?>"><?= ucfirst(h($ev['status'])) ?></span></td>
          <td style="display:flex;gap:6px;">
            <a href="<?= APP_URL ?>/events/<?= h($ev['slug']) ?>" class="btn btn-outline btn-sm" target="_blank" title="View"><i class="fa fa-eye"></i></a>
            <a href="<?= APP_URL ?>/executive/events/<?= $ev['id'] ?>/edit" class="btn btn-outline btn-sm" title="Edit"><i class="fa fa-pen"></i></a>
            <form method="POST" action="<?= APP_URL ?>/executive/events/<?= $ev['id'] ?>/delete" style="display:inline;">
              <?= csrf_field() ?>
              <button class="btn btn-danger btn-sm" data-confirm="Delete this event? All registrations will be lost." title="Delete"><i class="fa fa-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($result['data'])): ?>
          <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--gray-500);">No events found.</td></tr>
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
    <a href="?status=<?= h($status) ?>&page=<?= $i ?>" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
