<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$eventModel   = new Event();
$projectModel = new Project();
$donationModel= new Donation();
$userModel    = new User();
$galleryModel = new Gallery();

$upcomingEvents  = $eventModel->upcoming(3);
$featuredProjects= $projectModel->featured(3);
$totalMembers    = $userModel->totalCount();
$totalEvents     = $eventModel->totalCount();
$fundsRaised     = $donationModel->totalRaised();
$completedProjects = $projectModel->completedCount();
$recentImages    = $galleryModel->recentImages(8);

$testimonialModel = new Testimonial();
$testimonials     = $testimonialModel->featured(6);
// Recent members preview
$recentMembers = $userModel->all(1, 6, ['is_active' => 1, 'role_id' => ROLE_MEMBER])['data'];

$pageTitle = 'Home';
include ROOT_PATH . '/components/head.php';
include ROOT_PATH . '/components/navbar.php';
?>

<!-- ────────────────────────────────────────────────────────────
     HERO
──────────────────────────────────────────────────────────── -->
<section class="hero" aria-label="Hero">
  <div class="container">
    <div class="hero__content animate-fade-up">
      <div class="hero__badge">
        <span class="hero__badge-dot"></span>
        NJOSA Year Group 2006 · Koforidua, Ghana
      </div>
      <h1>One Class.<br><span>One Community. Forever.</span></h1>
      <p>The official platform for New Juaben Old Student Association (NJOSA) Year Group 2006 — stay connected with your classmates, support shared projects, and celebrate how far we've come.</p>
      <div class="hero__actions">
        <a href="<?= APP_URL ?>/register" class="btn btn-primary btn-lg">
          <i class="fa fa-user-plus"></i> Join YG 2006
        </a>
        <a href="<?= APP_URL ?>/donate" class="btn btn-secondary btn-lg">
          <i class="fa fa-hand-holding-heart"></i> Donate Now
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ── Stats Bar ─────────────────────────────────────────── -->
<div class="hero-stats-bar">
  <div class="container">
    <div class="hero-stats-bar__inner">
      <div class="hero-stats-bar__item">
        <span class="hero-stats-bar__num" data-counter data-target="<?= $totalMembers ?>" data-suffix="+">0</span>
        <span class="hero-stats-bar__label">Members</span>
      </div>
      <div class="hero-stats-bar__divider"></div>
      <div class="hero-stats-bar__item">
        <span class="hero-stats-bar__num" data-counter data-target="<?= $totalEvents ?>" data-suffix="">0</span>
        <span class="hero-stats-bar__label">Events Held</span>
      </div>
      <div class="hero-stats-bar__divider"></div>
      <div class="hero-stats-bar__item">
        <span class="hero-stats-bar__num" data-counter data-target="<?= (int)($fundsRaised/1000) ?>" data-suffix="K+">0</span>
        <span class="hero-stats-bar__label">Funds Raised (₵)</span>
      </div>
      <div class="hero-stats-bar__divider"></div>
      <div class="hero-stats-bar__item">
        <span class="hero-stats-bar__num" data-counter data-target="<?= $completedProjects ?>" data-suffix="">0</span>
        <span class="hero-stats-bar__label">Projects Completed</span>
      </div>
    </div>
  </div>
</div>

<!-- ────────────────────────────────────────────────────────────
     UPCOMING EVENTS
──────────────────────────────────────────────────────────── -->
<section class="section" id="events">
  <div class="container">
    <div class="section-header">
      <span class="tag">Upcoming Events</span>
      <h2>Don't Miss What's Next</h2>
      <p>Register for upcoming Year Group 2006 reunions, hangouts, and networking events.</p>
    </div>

    <?php if (empty($upcomingEvents)): ?>
      <p class="text-center text-muted">No upcoming events right now. Check back soon!</p>
    <?php else: ?>
    <div class="events-grid">
      <?php foreach ($upcomingEvents as $ev): ?>
      <article class="card event-card fade-in">
        <?php if ($ev['image']): ?>
          <img src="<?= UPLOADS_URL ?>/events/<?= h($ev['image']) ?>" alt="<?= h($ev['title']) ?>" class="card__img" loading="lazy">
        <?php else: ?>
          <div class="card__img" style="background:linear-gradient(135deg,var(--blue-800),var(--blue-600));display:grid;place-items:center;">
            <i class="fa fa-calendar-days" style="font-size:2.5rem;color:var(--gold-400)"></i>
          </div>
        <?php endif; ?>
        <div class="card__body">
          <div class="card__meta">
            <span class="event-badge badge-<?= h($ev['status']) ?>">
              <?= ucfirst(h($ev['status'])) ?>
            </span>
            <span><i class="fa fa-calendar"></i> <?= date('M j, Y', strtotime($ev['starts_at'])) ?></span>
          </div>
          <h3 class="card__title"><?= h($ev['title']) ?></h3>
          <?php if ($ev['location']): ?>
          <p class="card__text text-sm"><i class="fa fa-location-dot"></i> <?= h($ev['location']) ?></p>
          <?php endif; ?>
        </div>
        <div class="card__footer">
          <span class="text-sm text-muted"><i class="fa fa-users"></i> <?= $ev['registrations'] ?> registered</span>
          <a href="<?= APP_URL ?>/events/<?= h($ev['slug']) ?>" class="btn btn-blue btn-sm">Register</a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="text-center mt-5">
      <a href="<?= APP_URL ?>/events" class="btn btn-outline">View All Events <i class="fa fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- ────────────────────────────────────────────────────────────
     FEATURED PROJECTS
──────────────────────────────────────────────────────────── -->
<section class="section" style="background:var(--gray-50)" id="projects">
  <div class="container">
    <div class="section-header">
      <span class="tag">Featured Projects</span>
      <h2>Projects We're Building Together</h2>
      <p>The Year Group 2006 giving back — see the projects your contributions are powering.</p>
    </div>

    <?php if (empty($featuredProjects)): ?>
      <p class="text-center text-muted">No featured projects yet.</p>
    <?php else: ?>
    <div class="events-grid">
      <?php foreach ($featuredProjects as $proj): ?>
      <?php
        $progress = $proj['goal_amount'] > 0
          ? min(100, round($proj['raised_amount'] / $proj['goal_amount'] * 100))
          : 0;
      ?>
      <article class="card fade-in">
        <?php if ($proj['image']): ?>
          <img src="<?= UPLOADS_URL ?>/projects/<?= h($proj['image']) ?>" alt="<?= h($proj['title']) ?>" class="card__img" loading="lazy">
        <?php else: ?>
          <div class="card__img" style="background:linear-gradient(135deg,var(--blue-900),var(--blue-700));display:grid;place-items:center;">
            <i class="fa fa-diagram-project" style="font-size:2.5rem;color:var(--gold-400)"></i>
          </div>
        <?php endif; ?>
        <div class="card__body">
          <div class="card__meta">
            <span class="status status-<?= h($proj['status']) ?>"><?= ucfirst(str_replace('_',' ',h($proj['status']))) ?></span>
          </div>
          <h3 class="card__title"><?= h($proj['title']) ?></h3>
          <?php if ($proj['description']): ?>
            <p class="card__text"><?= h(mb_strimwidth($proj['description'], 0, 100, '...')) ?></p>
          <?php endif; ?>
          <div class="mt-3">
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
              <span class="text-sm fw-semibold">₵<?= number_format($proj['raised_amount']) ?> raised</span>
              <span class="text-sm text-muted"><?= $progress ?>%</span>
            </div>
            <div class="progress">
              <div class="progress__bar" data-width="<?= $progress ?>" style="width:<?= $progress ?>%"></div>
            </div>
            <p class="text-xs text-muted mt-1">Goal: ₵<?= number_format($proj['goal_amount']) ?></p>
          </div>
        </div>
        <div class="card__footer">
          <a href="<?= APP_URL ?>/projects/<?= h($proj['slug']) ?>" class="btn btn-outline btn-sm">Learn More</a>
          <a href="<?= APP_URL ?>/donate" class="btn btn-primary btn-sm">Support</a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="text-center mt-5">
      <a href="<?= APP_URL ?>/projects" class="btn btn-outline">All Projects <i class="fa fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- ────────────────────────────────────────────────────────────
     TESTIMONIALS
──────────────────────────────────────────────────────────── -->
<?php if (!empty($recentMembers)): ?>
<section class="section" id="members-preview">
  <div class="container">
    <div class="section-header">
      <span class="tag">Members</span>
      <h2>Meet Some of Our Members</h2>
      <p>Registered Year Group 2006 members — connect and network.</p>
    </div>

    <div class="events-grid">
      <?php foreach ($recentMembers as $m): ?>
      <article class="card member-card fade-in">
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
          <?php if ($m['occupation']): ?><p class="text-sm"><?= h($m['occupation']) ?><?= $m['employer'] ? ' at ' . h($m['employer']) : '' ?></p><?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
      <a href="<?= APP_URL ?>/oldstudents" class="btn btn-outline">View All Members <i class="fa fa-arrow-right"></i></a>
    </div>
  </div>
</section>
<?php endif; ?>
<section class="section testimonials-section">
  <div class="container">
    <div class="section-header">
      <span class="tag">Testimonials</span>
      <h2>Voices From the Year Group 2006</h2>
    </div>

    <?php if (!empty($testimonials)): ?>
    <div class="testimonials-slider" aria-label="Alumni testimonials">
      <div class="testimonials-track">
        <?php foreach ($testimonials as $t): ?>
        <div class="testimonial-card">
          <div class="testimonial-card__quote">"</div>
          <p class="testimonial-card__body"><?= h($t['message']) ?></p>
          <div class="testimonial-card__author">
            <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--blue-600),var(--blue-400));display:grid;place-items:center;color:white;font-weight:700;font-family:var(--font-heading);">
              <?= h(mb_substr($t['name'], 0, 1)) ?>
            </div>
            <div>
              <p class="testimonial-card__name"><?= h($t['name']) ?></p>
              <?php if ($t['nickname']): ?>
                <p style="font-size:.8rem;color:var(--gray-500);margin:0 0 2px;">"<?= h($t['nickname']) ?>"</p>
              <?php endif; ?>
              <?php if ($t['class_name']): ?>
                <p class="testimonial-card__year"><?= h($t['class_name']) ?> Class · NJOSA 2006</p>
              <?php else: ?>
                <p class="testimonial-card__year">NJOSA Year Group 2006</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="slider-controls">
      <button class="slider-btn slider-prev" aria-label="Previous testimonial"><i class="fa fa-chevron-left"></i></button>
      <div class="slider-dots" aria-label="Slide indicators">
        <?php foreach ($testimonials as $i => $t): ?>
        <button class="slider-dot <?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
      </div>
      <button class="slider-btn slider-next" aria-label="Next testimonial"><i class="fa fa-chevron-right"></i></button>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ────────────────────────────────────────────────────────────
     GALLERY PREVIEW
──────────────────────────────────────────────────────────── -->
<?php if (!empty($recentImages)): ?>
<section class="section" style="background:var(--gray-100)" id="gallery-preview">
  <div class="container">
    <div class="section-header">
      <span class="tag">Gallery</span>
      <h2>Memories & Moments</h2>
      <p>Snapshots from our events, reunions, and community gatherings.</p>
    </div>

    <div class="gallery-masonry" id="galleryGrid">
      <?php foreach ($recentImages as $img): ?>
      <div class="gallery-item">
        <img src="<?= UPLOADS_URL ?>/gallery/<?= h($img['filename']) ?>"
             data-full="<?= UPLOADS_URL ?>/gallery/<?= h($img['filename']) ?>"
             alt="<?= h($img['caption'] ?? 'Gallery image') ?>"
             loading="lazy">
        <div class="gallery-item__overlay"><i class="fa fa-expand"></i></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
      <a href="<?= APP_URL ?>/gallery" class="btn btn-blue">View Full Gallery <i class="fa fa-images"></i></a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Lightbox -->
<div id="lightbox" class="lightbox" aria-modal="true" role="dialog" aria-label="Image preview">
  <button class="lightbox-close" aria-label="Close"><i class="fa fa-xmark"></i></button>
  <button class="lightbox-nav lightbox-prev" aria-label="Previous image"><i class="fa fa-chevron-left"></i></button>
  <img src="" alt="Preview">
  <button class="lightbox-nav lightbox-next" aria-label="Next image"><i class="fa fa-chevron-right"></i></button>
</div>

<!-- ────────────────────────────────────────────────────────────
     DONATION CTA
──────────────────────────────────────────────────────────── -->
<section class="donate-cta" id="donate" aria-label="Donation call to action">
  <div class="container">
    <h2>Give Back as a Class</h2>
    <p>The New Juaben Old Student Association – Year Group 2006 giving back together — fund scholarships, infrastructure, and programmes that leave a lasting legacy.</p>
    <a href="<?= APP_URL ?>/donate" class="btn btn-primary btn-lg">
      <i class="fa fa-hand-holding-heart"></i> Donate Today
    </a>
  </div>
</section>

<?php
include ROOT_PATH . '/components/footer.php';
include ROOT_PATH . '/components/scripts.php';
?>
