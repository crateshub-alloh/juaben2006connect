<?php
require_login();
require_role(ROLE_ADMIN);

$testimonialModel = new Testimonial();

$editItem = null;
if (isset($_GET['edit'])) {
    $editItem = $testimonialModel->findById((int)$_GET['edit']);
}

$testimonials = $testimonialModel->all();
$flash = get_flash('success') ?? get_flash('error');

$classes = ['Science', 'Home Economics', 'Business', 'Arts'];

$pageTitle = 'Manage Testimonials';
include ROOT_PATH . '/components/dashboard-layout.php';
?>

<div class="dash-page-header">
  <div>
    <h1 class="dash-page-title">Testimonials</h1>
    <p class="dash-page-sub">Manage quotes shown on the homepage.</p>
  </div>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= h($flash['type']) ?> mb-4"><?= h($flash['message']) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:28px;align-items:start;">

  <!-- ── Form (Add / Edit) ── -->
  <div class="card">
    <div class="card__body">
      <h3 style="margin-bottom:20px;"><?= $editItem ? 'Edit Testimonial' : 'Add Testimonial' ?></h3>
      <form method="POST" action="<?= APP_URL ?>/admin/testimonials/<?= $editItem ? 'update' : 'store' ?>">
        <?= csrf_field() ?>
        <?php if ($editItem): ?>
          <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
        <?php endif; ?>

        <div class="form-group">
          <label class="form-label">Full Name *</label>
          <input type="text" name="name" class="form-control"
                 value="<?= h($editItem['name'] ?? '') ?>" required placeholder="e.g. Dr. Kwame Asante">
        </div>

        <div class="form-group">
          <label class="form-label">Nickname <span style="color:var(--gray-400);font-weight:400;">(optional)</span></label>
          <input type="text" name="nickname" class="form-control"
                 value="<?= h($editItem['nickname'] ?? '') ?>" placeholder="e.g. Kwame the Great">
        </div>

        <div class="form-group">
          <label class="form-label">Class *</label>
          <select name="class_name" class="form-control" required>
            <option value="">— Select class —</option>
            <?php foreach ($classes as $c): ?>
              <option value="<?= $c ?>" <?= ($editItem['class_name'] ?? '') === $c ? 'selected' : '' ?>>
                <?= $c ?> Class
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Testimonial *</label>
          <textarea name="message" class="form-control" rows="4" required
                    placeholder="What they said about the association..."><?= h($editItem['message'] ?? '') ?></textarea>
        </div>

        <div style="display:flex;gap:20px;align-items:center;margin-bottom:20px;">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
            <input type="checkbox" name="is_featured" value="1"
                   <?= ($editItem['is_featured'] ?? 0) ? 'checked' : '' ?>>
            <span class="form-label" style="margin:0;">Show on homepage</span>
          </label>
          <div>
            <label class="form-label" style="display:block;margin-bottom:4px;">Sort order</label>
            <input type="number" name="sort_order" class="form-control" style="width:80px;"
                   value="<?= h($editItem['sort_order'] ?? '0') ?>" min="0">
          </div>
        </div>

        <div style="display:flex;gap:12px;">
          <button type="submit" class="btn btn-primary"><?= $editItem ? 'Save Changes' : 'Add Testimonial' ?></button>
          <?php if ($editItem): ?>
            <a href="<?= APP_URL ?>/admin/testimonials" class="btn btn-outline">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <!-- ── List ── -->
  <div class="card">
    <div class="card__body">
      <h3 style="margin-bottom:20px;">All Testimonials (<?= count($testimonials) ?>)</h3>
      <?php if (empty($testimonials)): ?>
        <p class="text-muted">No testimonials yet. Add one on the left.</p>
      <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:14px;">
          <?php foreach ($testimonials as $t): ?>
          <div style="padding:14px;border:1px solid var(--gray-200);border-radius:var(--radius-md);background:var(--gray-50);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
              <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:2px;">
                  <strong style="font-size:.9rem;"><?= h($t['name']) ?></strong>
                  <?php if ($t['nickname']): ?>
                    <span style="font-size:.82rem;color:var(--gray-500);">"<?= h($t['nickname']) ?>"</span>
                  <?php endif; ?>
                  <?php if ($t['is_featured']): ?>
                    <span style="background:var(--blue-100);color:var(--blue-700);font-size:.7rem;padding:2px 8px;border-radius:20px;">Homepage</span>
                  <?php endif; ?>
                </div>
                <?php if ($t['class_name']): ?>
                  <p style="font-size:.78rem;color:var(--blue-600);margin:0 0 4px;font-weight:600;"><?= h($t['class_name']) ?> Class</p>
                <?php endif; ?>
                <p style="font-size:.82rem;color:var(--gray-600);margin:0;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                  "<?= h($t['message']) ?>"
                </p>
              </div>
              <div style="display:flex;gap:6px;flex-shrink:0;">
                <a href="<?= APP_URL ?>/admin/testimonials?edit=<?= $t['id'] ?>"
                   class="btn btn-outline btn-sm" title="Edit">
                  <i class="fa fa-pen"></i>
                </a>
                <form method="POST" action="<?= APP_URL ?>/admin/testimonials/delete"
                      onsubmit="return confirm('Delete this testimonial?')">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= $t['id'] ?>">
                  <button type="submit" class="btn btn-sm"
                          style="background:var(--red-50,#fef2f2);color:var(--red-600,#dc2626);border:1px solid var(--red-200,#fecaca);"
                          title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>
