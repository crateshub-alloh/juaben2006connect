<?php $user = auth(); ?>
<nav class="navbar" role="navigation" aria-label="Main navigation">
  <div class="container">
    <a href="<?= APP_URL ?>/" class="navbar__logo" aria-label="Juaben2006 Connect home">
      <img src="<?= APP_URL ?>/assets/images/njosa-logo.png" alt="NJOSA Logo" class="navbar__logo-img">
      <span>Juaben<span style="color:var(--gold-600)">2006</span>Connect</span>
    </a>

    <button class="hamburger" aria-label="Toggle navigation" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

    <ul class="nav-links" role="list">
      <li><a href="<?= APP_URL ?>/" class="<?= ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') ? 'active' : '' ?>">Home</a></li>
      <li><a href="<?= APP_URL ?>/about">About</a></li>
      <li><a href="<?= APP_URL ?>/events">Events</a></li>
      <li><a href="<?= APP_URL ?>/projects">Projects</a></li>
      <li><a href="<?= APP_URL ?>/gallery">Gallery</a></li>
      <li><a href="<?= APP_URL ?>/donate">Donate</a></li>
      <li><a href="<?= APP_URL ?>/contact">Contact</a></li>
      <?php if ($user): ?>
        <li><a href="<?= APP_URL ?>/member/dashboard" class="btn-nav">Dashboard</a></li>
      <?php else: ?>
        <li><a href="<?= APP_URL ?>/login">Login</a></li>
        <li><a href="<?= APP_URL ?>/register" class="btn-nav">Join YG 2006</a></li>
      <?php endif; ?>
      <li>
        <button id="darkModeToggle" aria-label="Toggle dark mode" style="background:none;border:none;cursor:pointer;padding:8px;color:var(--gray-700);font-size:1.1rem;">
          <i class="fa-solid fa-circle-half-stroke"></i>
        </button>
      </li>
    </ul>
  </div>
</nav>
