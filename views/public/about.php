<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$pageTitle = 'About Us';
include ROOT_PATH . '/components/head.php';
include ROOT_PATH . '/components/navbar.php';
?>
<div style="padding-top:var(--nav-h);">
  <section class="section-sm" style="background:linear-gradient(135deg,var(--blue-900),var(--blue-800));">
    <div class="container text-center">
      <h1 style="color:var(--white);">About the Year Group 2006</h1>
      <p style="color:rgba(255,255,255,.7);">Who we are, what we stand for, and how far we have come.</p>
    </div>
  </section>
  <section class="section">
    <div class="container" style="max-width:800px;">
      <div class="section-header">
        <span class="tag">Our Story</span>
        <h2>New Juaben Old Student Association – Year Group 2006</h2>
        <p>We graduated together in 2006 and have been building our mark on the world ever since. This platform is our space — to reconnect, celebrate, and give back as one set.</p>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:40px;">
        <?php foreach ([
          ['icon'=>'fa-bullseye','title'=>'Mission','body'=>'To keep the Year Group 2006 united — supporting one another, contributing to society, and honouring the bonds we formed at New Juaben Old Student Association (NJOSA).'],
          ['icon'=>'fa-eye','title'=>'Vision','body'=>'A set of leaders, innovators, and changemakers who remain connected and continue to uplift each other and their communities.'],
          ['icon'=>'fa-heart','title'=>'Values','body'=>'Brotherhood, sisterhood, integrity, and a shared pride in the Year Group 2006 name.'],
          ['icon'=>'fa-users','title'=>'Our Set','body'=>'Members of the 2006 year group spread across Ghana and the diaspora, united by shared memories and a common identity.'],
        ] as $v): ?>
        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;border:1px solid var(--gray-200);box-shadow:var(--shadow-sm);">
          <div style="width:48px;height:48px;border-radius:12px;background:var(--blue-100);display:grid;place-items:center;color:var(--blue-700);font-size:1.2rem;margin-bottom:14px;"><i class="fa <?= $v['icon'] ?>"></i></div>
          <h4 style="margin-bottom:8px;"><?= $v['title'] ?></h4>
          <p class="text-sm text-muted"><?= $v['body'] ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</div>
<?php include ROOT_PATH . '/components/footer.php'; include ROOT_PATH . '/components/scripts.php'; ?>
