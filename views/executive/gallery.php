<?php
$pageTitle = 'Manage Gallery';
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
require_once ROOT_PATH . '/components/dashboard-layout.php';

$galleryModel = new Gallery();
$albums       = $galleryModel->albums(false); // show all including private

$selectedAlbum = null;
$images        = [];
if (!empty($_GET['album'])) {
    $selectedAlbum = $galleryModel->findAlbum((int)$_GET['album']);
    if ($selectedAlbum) $images = $galleryModel->albumImages((int)$_GET['album']);
}
?>

<div class="page-header">
  <div><h1>Gallery Management</h1><p>Upload and organise alumni photos.</p></div>
</div>

<?php $s = get_flash('success'); if ($s): ?>
  <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($s['message']) ?></div>
<?php endif; ?>

<div class="grid-2" style="align-items:start;">
  <!-- Upload Form -->
  <div class="card" style="padding:28px;">
    <h3 style="margin-bottom:20px;">Upload Images</h3>
    <form method="POST" action="<?= APP_URL ?>/executive/gallery/upload" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="form-group">
        <label class="form-label">Album</label>
        <select name="album_id" id="album_id" class="form-control" onchange="toggleNewAlbum(this.value)">
          <option value="">+ Create New Album</option>
          <?php foreach ($albums as $a): ?>
            <option value="<?= $a['id'] ?>" <?= (isset($_GET['album']) && $_GET['album'] == $a['id']) ? 'selected' : '' ?>><?= h($a['title']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div id="newAlbumField" class="form-group">
        <label class="form-label">New Album Title</label>
        <input type="text" name="album_title" class="form-control" placeholder="e.g. Annual Dinner 2026">
      </div>

      <div class="form-group">
        <label class="form-label">Caption (optional)</label>
        <input type="text" name="caption" class="form-control" placeholder="Brief description">
      </div>

      <div class="form-group">
        <label class="form-label">Select Images (max 10 at once, 5MB each)</label>
        <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
        <p class="form-hint">JPEG, PNG, WebP supported. Images are auto-compressed.</p>
      </div>

      <button type="submit" class="btn btn-blue btn-block">
        <i class="fa fa-cloud-arrow-up"></i> Upload Images
      </button>
    </form>
  </div>

  <!-- Albums List -->
  <div>
    <div class="table-card mb-4">
      <div class="table-card__header"><span class="table-card__title">Albums</span></div>
      <div class="table-responsive">
        <table class="dash-table">
          <thead><tr><th>Album</th><th>Photos</th><th>Visibility</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($albums as $a): ?>
            <tr>
              <td class="fw-semibold"><?= h($a['title']) ?></td>
              <td><?= $a['image_count'] ?></td>
              <td><?= $a['is_public'] ? '<span class="status status-active">Public</span>' : '<span class="status status-inactive">Private</span>' ?></td>
              <td>
                <a href="?album=<?= $a['id'] ?>" class="btn btn-outline btn-sm"><i class="fa fa-eye"></i> View</a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($albums)): ?>
              <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--gray-500);">No albums yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Selected album images -->
    <?php if ($selectedAlbum && !empty($images)): ?>
    <div class="table-card">
      <div class="table-card__header"><span class="table-card__title">Photos – <?= h($selectedAlbum['title']) ?></span></div>
      <div style="padding:16px;display:grid;grid-template-columns:repeat(4,1fr);gap:8px;">
        <?php foreach ($images as $img): ?>
        <div style="position:relative;border-radius:8px;overflow:hidden;">
          <img src="<?= UPLOADS_URL ?>/gallery/<?= h($img['filename']) ?>" style="width:100%;height:80px;object-fit:cover;" loading="lazy">
          <form method="POST" action="<?= APP_URL ?>/executive/gallery/images/<?= $img['id'] ?>/delete" style="position:absolute;top:4px;right:4px;">
            <?= csrf_field() ?>
            <button class="btn btn-danger btn-sm" style="padding:4px 8px;" data-confirm="Delete this photo?" title="Delete"><i class="fa fa-trash" style="font-size:.75rem;"></i></button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
function toggleNewAlbum(val) {
  document.getElementById('newAlbumField').style.display = val ? 'none' : 'block';
}
toggleNewAlbum(document.getElementById('album_id').value);
</script>

<?php require_once ROOT_PATH . '/components/dashboard-layout-end.php'; ?>
