<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$projectModel = new Project();
$result       = $projectModel->all(1, 9);
$pageTitle    = 'Projects';
include ROOT_PATH . '/components/head.php';
include ROOT_PATH . '/components/navbar.php';
?>
<div style="padding-top:var(--nav-h);">
  <section class="section-sm" style="background:linear-gradient(135deg,var(--blue-900),var(--blue-800));">
    <div class="container text-center">
      <h1 style="color:var(--white);">Community Projects</h1>
      <p style="color:rgba(255,255,255,.7);">Initiatives funded by our alumni to create lasting impact.</p>
    </div>
  </section>
  <section class="section">
    <div class="container">
      <?php if (empty($result['data'])): ?>
        <p class="text-center text-muted">No projects yet.</p>
      <?php else: ?>
      <div class="events-grid">
        <?php foreach ($result['data'] as $p):
          $pct = $p['goal_amount'] > 0 ? min(100, round($p['raised_amount']/$p['goal_amount']*100)) : 0;
        ?>
        <div class="card">
          <?php if ($p['image']): ?>
            <img src="<?= UPLOADS_URL ?>/projects/<?= h($p['image']) ?>" class="card__img" loading="lazy" alt="<?= h($p['title']) ?>">
          <?php else: ?>
            <div class="card__img" style="background:linear-gradient(135deg,var(--blue-900),var(--blue-700));display:grid;place-items:center;"><i class="fa fa-diagram-project" style="font-size:2.5rem;color:var(--gold-400)"></i></div>
          <?php endif; ?>
          <div class="card__body">
            <span class="status status-<?= h($p['status']) ?>"><?= ucfirst(str_replace('_',' ',h($p['status']))) ?></span>
            <h3 class="card__title mt-2"><?= h($p['title']) ?></h3>
            <?php if ($p['description']): ?><p class="card__text"><?= h(mb_strimwidth($p['description'],0,100,'...')) ?></p><?php endif; ?>
            <div class="mt-3">
              <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                <span class="text-sm">₵<?= number_format($p['raised_amount']) ?> raised</span>
                <span class="text-sm text-muted"><?= $pct ?>%</span>
              </div>
              <div class="progress"><div class="progress__bar" style="width:<?= $pct ?>%"></div></div>
              <p class="text-xs text-muted mt-1">Goal: ₵<?= number_format($p['goal_amount']) ?></p>
            </div>
          </div>
          <div class="card__footer">
            <a href="<?= APP_URL ?>/donate" class="btn btn-primary btn-sm">Support</a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>
</div>
<?php include ROOT_PATH . '/components/footer.php'; include ROOT_PATH . '/components/scripts.php'; ?>
