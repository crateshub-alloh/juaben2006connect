<?php
$pageTitle = ucfirst(str_replace('-',' ','projects'));
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
require_once ROOT_PATH . '/components/dashboard-layout.php';
$projectModel = new Project();
$result = $projectModel->all();
?>
<div class="page-header">
  <div><h1>Projects</h1></div>
  <a href="<?= APP_URL ?>/executive/projects/create" class="btn btn-blue btn-sm"><i class="fa fa-plus"></i> New Project</a>
</div>
<?php $s = get_flash('success'); if ($s): ?>
  <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($s['message']) ?></div>
<?php endif; ?>
<div class="events-grid">
<?php foreach ($result['data'] as $p): ?>
<?php $pct = $p['goal_amount'] > 0 ? min(100, round($p['raised_amount']/$p['goal_amount']*100)) : 0; ?>
<div class="card">
  <div class="card__body">
    <h3 class="card__title"><?= h($p['title']) ?></h3>
    <span class="status status-<?= h($p['status']) ?>"><?= ucfirst(str_replace('_',' ',h($p['status']))) ?></span>
    <div class="progress mt-3"><div class="progress__bar" style="width:<?= $pct ?>%"></div></div>
    <p class="text-xs text-muted">₵<?= number_format($p['raised_amount']) ?> of ₵<?= number_format($p['goal_amount']) ?></p>
  </div>
  <div class="card__footer">
    <span><?= $pct ?>%</span>
    <a href="<?= APP_URL ?>/projects/<?= h($p['slug']) ?>" class="btn btn-outline btn-sm">View</a>
  </div>
</div>
<?php endforeach; ?>
</div>
<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
