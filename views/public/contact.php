<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
$pageTitle = 'Contact Us';
include ROOT_PATH . '/components/head.php';
include ROOT_PATH . '/components/navbar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name    = sanitize_input($_POST['name']    ?? '');
    $email   = sanitize_input($_POST['email']   ?? '');
    $subject = sanitize_input($_POST['subject'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $subject && $message) {
        $html = "<h2>Contact Form Submission</h2>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <p><strong>Message:</strong><br>" . nl2br($message) . "</p>";
        send_mail(MAIL_FROM, "Contact: {$subject}", $html);
        flash('success', 'Message sent! We\'ll get back to you soon.', 'success');
    } else {
        flash('error', 'Please fill in all fields correctly.', 'error');
    }
    redirect('/contact');
}
?>
<div style="padding-top:var(--nav-h);">
  <section class="section-sm" style="background:linear-gradient(135deg,var(--blue-900),var(--blue-800));">
    <div class="container text-center">
      <h1 style="color:var(--white);">Contact Us</h1>
      <p style="color:rgba(255,255,255,.7);">We'd love to hear from you.</p>
    </div>
  </section>

  <section class="section">
    <div class="container" style="max-width:1000px;">
      <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:40px;align-items:start;">
        <!-- Info -->
        <div>
          <h2 style="margin-bottom:20px;">Get in Touch</h2>
          <div style="display:flex;flex-direction:column;gap:20px;">
            <div style="display:flex;gap:16px;align-items:flex-start;">
              <div style="width:44px;height:44px;border-radius:10px;background:var(--blue-100);display:grid;place-items:center;color:var(--blue-700);flex-shrink:0;"><i class="fa fa-envelope"></i></div>
              <div>
                <h4 style="font-size:.95rem;">Email</h4>
                <p class="text-muted text-sm">info@juaben2006connect.com</p>
              </div>
            </div>
            <div style="display:flex;gap:16px;align-items:flex-start;">
              <div style="width:44px;height:44px;border-radius:10px;background:var(--blue-100);display:grid;place-items:center;color:var(--blue-700);flex-shrink:0;"><i class="fa fa-phone"></i></div>
              <div>
                <h4 style="font-size:.95rem;">Phone</h4>
                <p class="text-muted text-sm">+233 249 538 818</p>
                <p class="text-muted text-sm">+233 200 005 452</p>
              </div>
            </div>
            <div style="display:flex;gap:16px;align-items:flex-start;">
              <div style="width:44px;height:44px;border-radius:10px;background:var(--blue-100);display:grid;place-items:center;color:var(--blue-700);flex-shrink:0;"><i class="fa fa-location-dot"></i></div>
              <div>
                <h4 style="font-size:.95rem;">Location</h4>
                <p class="text-muted text-sm">Ghana</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="card" style="padding:32px;">
          <?php $s = get_flash('success'); if ($s): ?>
            <div class="alert alert-success"><i class="fa fa-circle-check"></i> <?= h($s['message']) ?></div>
          <?php endif; ?>
          <?php $e = get_flash('error'); if ($e): ?>
            <div class="alert alert-error"><i class="fa fa-circle-xmark"></i> <?= h($e['message']) ?></div>
          <?php endif; ?>

          <form method="POST" action="<?= APP_URL ?>/contact">
            <?= csrf_field() ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
              <div class="form-group">
                <label class="form-label">Your Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Amina Yusuf">
              </div>
              <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="you@example.com">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Subject</label>
              <input type="text" name="subject" class="form-control" required placeholder="How can we help?">
            </div>
            <div class="form-group">
              <label class="form-label">Message</label>
              <textarea name="message" class="form-control" rows="5" required placeholder="Your message..."></textarea>
            </div>
            <button type="submit" class="btn btn-blue btn-block">
              <i class="fa fa-paper-plane"></i> Send Message
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<?php
include ROOT_PATH . '/components/footer.php';
include ROOT_PATH . '/components/scripts.php';
?>
