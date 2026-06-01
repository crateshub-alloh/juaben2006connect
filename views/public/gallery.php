<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$galleryModel = new Gallery();
$albums       = $galleryModel->albums(true);

$pageTitle = 'Gallery';
include ROOT_PATH . '/components/head.php';
include ROOT_PATH . '/components/navbar.php';
?>
<div style="padding-top:var(--nav-h);">
  <section class="section-sm" style="background:linear-gradient(135deg,var(--blue-900),var(--blue-800));">
    <div class="container text-center">
      <h1 style="color:var(--white);">Photo Gallery</h1>
      <p style="color:rgba(255,255,255,.7);">Memories from our events, reunions, and community programmes.</p>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <?php if (empty($albums)): ?>
        <p class="text-center text-muted">No albums yet. Check back soon!</p>
      <?php else: ?>
        <!-- Albums Grid -->
        <div class="events-grid mb-5">
          <?php foreach ($albums as $album): ?>
          <a href="?album=<?= $album['id'] ?>" class="card" style="text-decoration:none;">
            <?php if ($album['cover_image']): ?>
              <img src="<?= UPLOADS_URL ?>/gallery/<?= h($album['cover_image']) ?>" class="card__img" alt="<?= h($album['title']) ?>">
            <?php else: ?>
              <div class="card__img" style="background:linear-gradient(135deg,var(--blue-800),var(--blue-600));display:grid;place-items:center;">
                <i class="fa fa-images" style="font-size:2.5rem;color:var(--gold-400)"></i>
              </div>
            <?php endif; ?>
            <div class="card__body">
              <h3 class="card__title"><?= h($album['title']) ?></h3>
              <p class="text-sm text-muted"><i class="fa fa-images"></i> <?= $album['image_count'] ?> photos</p>
            </div>
          </a>
          <?php endforeach; ?>
        </div>

        <!-- If album selected, show its images -->
        <?php if (!empty($_GET['album'])): ?>
          <?php
          $albumId   = (int)$_GET['album'];
          $albumData = $galleryModel->findAlbum($albumId);
          $images    = $albumData ? $galleryModel->albumImages($albumId) : [];
          ?>
          <?php if ($albumData && !empty($images)): ?>
          <h2 class="mb-4"><?= h($albumData['title']) ?></h2>
          <div class="gallery-masonry" id="galleryGrid">
            <?php foreach ($images as $img): ?>
            <div class="gallery-item">
              <img src="<?= UPLOADS_URL ?>/gallery/<?= h($img['filename']) ?>"
                   data-full="<?= UPLOADS_URL ?>/gallery/<?= h($img['filename']) ?>"
                   alt="<?= h($img['caption'] ?? 'Photo') ?>" loading="lazy">
              <div class="gallery-item__overlay"><i class="fa fa-expand"></i></div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>
</div>

<!-- Lightbox -->
<div id="lightbox" class="lightbox">
  <button class="lightbox-close"><i class="fa fa-xmark"></i></button>
  <button class="lightbox-nav lightbox-prev"><i class="fa fa-chevron-left"></i></button>
  <img src="" alt="Preview">
  <button class="lightbox-nav lightbox-next"><i class="fa fa-chevron-right"></i></button>
</div>

<?php
include ROOT_PATH . '/components/footer.php';
include ROOT_PATH . '/components/scripts.php';
?>
