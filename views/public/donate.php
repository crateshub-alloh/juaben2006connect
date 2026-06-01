<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$donationModel = new Donation();
$campaigns     = $donationModel->activeCampaigns();
$user          = auth();

$pageTitle = 'Donate';
include ROOT_PATH . '/components/head.php';
include ROOT_PATH . '/components/navbar.php';
?>
<div style="padding-top:var(--nav-h);">
  <!-- Hero -->
  <section style="background:linear-gradient(135deg,var(--blue-900),var(--blue-800));padding:80px 0;text-align:center;">
    <div class="container">
      <span class="tag" style="background:rgba(201,162,39,.2);color:var(--gold-400);margin-bottom:16px;display:inline-block;">Make a Difference</span>
      <h1 style="color:var(--white);margin-bottom:16px;">Support the Next Generation</h1>
      <p style="color:rgba(255,255,255,.75);max-width:560px;margin:0 auto;font-size:1.1rem;">
        Your donation funds scholarships, community infrastructure, and programmes that transform lives.
      </p>
    </div>
  </section>

  <!-- Campaigns -->
  <?php if (!empty($campaigns)): ?>
  <section class="section">
    <div class="container">
      <div class="section-header">
        <span class="tag">Active Campaigns</span>
        <h2>Choose a Campaign to Support</h2>
      </div>
      <div class="events-grid">
        <?php foreach ($campaigns as $c):
          $pct = $c['goal_amount'] > 0 ? min(100, round($c['raised'] / $c['goal_amount'] * 100)) : 0;
        ?>
        <div class="card">
          <?php if ($c['image']): ?>
            <img src="<?= UPLOADS_URL ?>/campaigns/<?= h($c['image']) ?>" class="card__img" alt="<?= h($c['title']) ?>">
          <?php else: ?>
            <div class="card__img" style="background:linear-gradient(135deg,var(--blue-800),var(--blue-600));display:grid;place-items:center;">
              <i class="fa fa-hand-holding-heart" style="font-size:2.5rem;color:var(--gold-400)"></i>
            </div>
          <?php endif; ?>
          <div class="card__body">
            <h3 class="card__title"><?= h($c['title']) ?></h3>
            <?php if ($c['description']): ?>
              <p class="card__text"><?= h(mb_strimwidth($c['description'],0,100,'...')) ?></p>
            <?php endif; ?>
            <div class="mt-3">
              <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                <span class="text-sm fw-semibold">₵<?= number_format($c['raised']) ?> raised</span>
                <span class="text-sm text-muted"><?= $pct ?>%</span>
              </div>
              <div class="progress"><div class="progress__bar" style="width:<?= $pct ?>%"></div></div>
              <p class="text-xs text-muted mt-1">Goal: ₵<?= number_format($c['goal_amount']) ?></p>
            </div>
          </div>
          <div class="card__footer" style="justify-content:center;">
            <a href="#donate-form" onclick="document.getElementById('campaign_id').value='<?= $c['id'] ?>'" class="btn btn-primary">Donate to Campaign</a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- Donation Form -->
  <section class="section" style="background:var(--gray-50)" id="donate-form">
    <div class="container" style="max-width:640px;">
      <div class="section-header">
        <span class="tag">Give Now</span>
        <h2>Make Your Donation</h2>
      </div>

      <?php $s = get_flash('success'); if ($s): ?>
        <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($s['message']) ?></div>
      <?php endif; ?>
      <?php $e = get_flash('error'); if ($e): ?>
        <div class="alert alert-error"><i class="fa fa-circle-xmark"></i> <?= h($e['message']) ?></div>
      <?php endif; ?>

      <div class="card" style="padding:36px;">
        <form method="POST" action="<?= APP_URL ?>/donate/submit">
          <?= csrf_field() ?>
          <input type="hidden" id="campaign_id" name="campaign_id" value="">

          <?php if (!$user): ?>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
              <label class="form-label">Your Name</label>
              <input type="text" name="donor_name" class="form-control" required placeholder="Amina Yusuf">
            </div>
            <div class="form-group">
              <label class="form-label">Email Address</label>
              <input type="email" name="donor_email" class="form-control" required placeholder="you@example.com">
            </div>
          </div>
          <?php endif; ?>

          <!-- Amount presets -->
          <div class="form-group">
            <label class="form-label">Donation Amount (₵)</label>
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
              <?php foreach ([1000, 5000, 10000, 25000, 50000] as $amt): ?>
              <button type="button" class="btn btn-outline btn-sm amount-preset" data-amount="<?= $amt ?>">
                ₵<?= number_format($amt) ?>
              </button>
              <?php endforeach; ?>
            </div>
            <input type="number" id="amount" name="amount" class="form-control" required
                   min="100" placeholder="Enter amount" step="100">
          </div>

          <?php if (!empty($campaigns)): ?>
          <div class="form-group">
            <label class="form-label">Campaign (Optional)</label>
            <select name="campaign_id" id="campaign_id" class="form-control">
              <option value="">General Donation</option>
              <?php foreach ($campaigns as $c): ?>
                <option value="<?= $c['id'] ?>"><?= h($c['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>

          <div class="form-group">
            <label class="form-label">Note (Optional)</label>
            <textarea name="note" class="form-control" rows="2" placeholder="Anything you'd like to share..."></textarea>
          </div>

          <div style="background:var(--blue-100);border-radius:var(--radius-sm);padding:14px;margin-bottom:20px;font-size:.88rem;color:var(--blue-800);">
            <i class="fa fa-lock"></i> <strong>Secure Payment</strong> – Your payment details are protected. All transactions are encrypted.
          </div>

          <button type="submit" class="btn btn-primary btn-block btn-lg">
            <i class="fa fa-hand-holding-heart"></i> Donate Now
          </button>
        </form>
      </div>
    </div>
  </section>
</div>

<?php
include ROOT_PATH . '/components/footer.php';
?>
<script>
document.querySelectorAll('.amount-preset').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('amount').value = btn.dataset.amount;
    document.querySelectorAll('.amount-preset').forEach(b => b.classList.remove('btn-blue'));
    btn.classList.add('btn-blue');
  });
});
</script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
