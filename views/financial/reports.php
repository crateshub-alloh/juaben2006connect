<?php
$pageTitle = ucfirst('reports');
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_FINANCIAL, ROLE_ADMIN);
require_once ROOT_PATH . '/components/dashboard-layout.php';
?>
<div class="page-header"><h1>Financial <?= ucfirst('reports') ?></h1></div>
<div class="table-card" style="padding:40px;text-align:center;color:var(--gray-500);">
  <p>Section coming soon.</p>
</div>
<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
