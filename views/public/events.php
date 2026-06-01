<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$eventModel = new Event();
$status     = sanitize_input($_GET['status'] ?? '');
$page       = max(1, (int)($_GET['page'] ?? 1));
$result     = $eventModel->all($page, 9, array_filter(['status' => $status, 'is_public' => 1]));
$events     = $result['data'];
$totalPages = (int)ceil($result['total'] / $result['per_page']);

$pageTitle  = 'Events';
include ROOT_PATH . '/components/head.php';
include ROOT_PATH . '/components/navbar.php';
?>
<div style="padding-top:var(--nav-h);">
  <section class="section-sm" style="background:linear-gradient(135deg,var(--blue-900),var(--blue-800));">
    <div class="container text-center">
      <h1 style="color:var(--white);">Alumni Events</h1>
      <p style="color:rgba(255,255,255,.7);">Reunions, networking, and community gatherings – stay engaged.</p>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <!-- Filters -->
      <div style="display:flex;gap:10px;margin-bottom:32px;flex-wrap:wrap;">
        <?php foreach ([''=>'All Events','upcoming'=>'Upcoming','ongoing'=>'Ongoing','completed'=>'Completed'] as $k => $v): ?>
          <a href="?status=<?= $k ?>" class="btn <?= $status === $k ? 'btn-blue' : 'btn-outline' ?> btn-sm"><?= $v ?></a>
        <?php endforeach; ?>
      </div>

      <?php if (empty($events)): ?>
        <div style="text-align:center;padding:60px;color:var(--gray-500);">
          <i class="fa fa-calendar-xmark" style="font-size:3rem;opacity:.3;display:block;margin-bottom:16px;"></i>
          <p>No events found for this filter.</p>
        </div>
      <?php else: ?>
      <div class="events-grid">
        <?php foreach ($events as $ev): ?>
        <article class="card">
          <?php if ($ev['image']): ?>
            <img src="<?= UPLOADS_URL ?>/events/<?= h($ev['image']) ?>" alt="<?= h($ev['title']) ?>" class="card__img" loading="lazy">
          <?php else: ?>
            <div class="card__img" style="background:linear-gradient(135deg,var(--blue-800),var(--blue-600));display:grid;place-items:center;">
              <i class="fa fa-calendar-days" style="font-size:2.5rem;color:var(--gold-400)"></i>
            </div>
          <?php endif; ?>
          <div class="card__body">
            <div class="card__meta">
              <span class="event-badge badge-<?= h($ev['status']) ?>"><?= ucfirst(h($ev['status'])) ?></span>
              <span class="text-sm text-muted"><i class="fa fa-calendar"></i> <?= date('M j, Y', strtotime($ev['starts_at'])) ?></span>
            </div>
            <h3 class="card__title"><?= h($ev['title']) ?></h3>
            <?php if ($ev['location']): ?>
              <p class="text-sm text-muted"><i class="fa fa-location-dot"></i> <?= h($ev['location']) ?></p>
            <?php endif; ?>
            <?php if ($ev['description']): ?>
              <p class="card__text mt-2"><?= h(mb_strimwidth($ev['description'], 0, 100, '...')) ?></p>
            <?php endif; ?>
          </div>
          <div class="card__footer">
            <span class="text-sm text-muted"><i class="fa fa-users"></i> <?= $ev['registrations'] ?></span>
            <a href="<?= APP_URL ?>/events/<?= h($ev['slug']) ?>" class="btn btn-blue btn-sm">View Details</a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?status=<?= h($status) ?>&page=<?= $i ?>" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
      </div>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php
include ROOT_PATH . '/components/footer.php';
include ROOT_PATH . '/components/scripts.php';
?>
