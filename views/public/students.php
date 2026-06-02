<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';

$userModel = new User();
$pageTitle = 'Members';
$result = $userModel->all(1, 100, ['is_active' => 1, 'role_id' => ROLE_MEMBER]);
$members = $result['data'];

include ROOT_PATH . '/components/head.php';
include ROOT_PATH . '/components/navbar.php';
?>

<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="tag">Members</span>
      <h2>Our Year Group 2006 Members</h2>
      <p>Browse registered members and their profiles. Click a profile to view more details.</p>
    </div>

    <?php if (empty($members)): ?>
      <p class="text-center text-muted">No members found.</p>
    <?php else: ?>
      <div class="events-grid">
        <?php foreach ($members as $m): ?>
          <article class="card">
            <?php if (!empty($m['avatar'])): ?>
              <img src="<?= UPLOADS_URL ?>/avatars/<?= h($m['avatar']) ?>" alt="<?= h($m['first_name'] . ' ' . $m['last_name']) ?>" class="card__img" loading="lazy">
            <?php else: ?>
              <div class="card__img" style="display:grid;place-items:center;background:linear-gradient(135deg,var(--blue-800),var(--blue-600));color:white;font-weight:700;">
                <?= h(mb_substr($m['first_name'],0,1)) ?>
              </div>
            <?php endif; ?>
            <div class="card__body">
              <h3 class="card__title"><?= h($m['first_name'] . ' ' . $m['last_name']) ?></h3>
              <?php if ($m['graduation_year']): ?><p class="text-sm text-muted">Class of <?= h($m['graduation_year']) ?></p><?php endif; ?>
              <?php if ($m['occupation']): ?><p class="text-sm"><?= h($m['occupation']) ?> at <?= h($m['employer'] ?? '') ?></p><?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php
include ROOT_PATH . '/components/footer.php';
include ROOT_PATH . '/components/scripts.php';
?>
