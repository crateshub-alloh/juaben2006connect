<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_login();
$user         = auth();
$msgModel     = new Message();
$notifModel   = new Notification();
$unreadMsgs   = $msgModel->unreadCount((int)$user['id']);
$unreadNotifs = $notifModel->unreadCount((int)$user['id']);
$notifications = $notifModel->forUser((int)$user['id'], 8);

// Determine active nav item
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function nav_active(string $path): string {
    global $uri;
    return str_contains($uri, $path) ? 'active' : '';
}

$roleId = (int)$user['role_id'];
$avatarUrl = !empty($user['avatar'])
    ? UPLOADS_URL . '/avatars/' . $user['avatar']
    : APP_URL . '/assets/images/avatar-placeholder.png';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($pageTitle ?? 'Dashboard') ?> – <?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/dashboard.css">
</head>
<body>
<div class="dashboard-layout">

  <!-- Sidebar overlay (mobile) -->
  <div class="sidebar-overlay"></div>

  <!-- Sidebar -->
  <aside class="sidebar" aria-label="Dashboard navigation">
    <div class="sidebar__logo">
      <a href="<?= APP_URL ?>/" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
        <img src="<?= APP_URL ?>/assets/images/njosa-logo.png" alt="NJOSA Logo" style="width:38px;height:38px;object-fit:contain;flex-shrink:0;">
        <h2 style="margin:0;font-size:.9rem;line-height:1.25;">Juaben<span style="color:var(--gold-400)">2006</span><br>Connect</h2>
      </a>
    </div>

    <nav class="sidebar__nav">
      <!-- Member links (all roles) -->
      <p class="sidebar__section-label">My Account</p>
      <a href="<?= APP_URL ?>/member/dashboard"  class="<?= nav_active('member/dashboard') ?>">
        <i class="fa fa-gauge nav-icon"></i> Dashboard
      </a>
      <a href="<?= APP_URL ?>/member/profile" class="<?= nav_active('member/profile') ?>">
        <i class="fa fa-user nav-icon"></i> My Profile
      </a>
      <a href="<?= APP_URL ?>/member/events" class="<?= nav_active('member/events') ?>">
        <i class="fa fa-calendar nav-icon"></i> Events
      </a>
      <a href="<?= APP_URL ?>/member/payments" class="<?= nav_active('member/payments') ?>">
        <i class="fa fa-credit-card nav-icon"></i> Payments
      </a>
      <a href="<?= APP_URL ?>/member/messages" class="<?= nav_active('member/messages') ?>">
        <i class="fa fa-envelope nav-icon"></i> Messages
        <?php if ($unreadMsgs > 0): ?>
          <span class="badge"><?= $unreadMsgs ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= APP_URL ?>/member/donations" class="<?= nav_active('member/donations') ?>">
        <i class="fa fa-hand-holding-heart nav-icon"></i> Donations
      </a>

      <?php if (in_array($roleId, [ROLE_EXECUTIVE, ROLE_ADMIN])): ?>
        <p class="sidebar__section-label">Executive</p>
        <a href="<?= APP_URL ?>/executive/dashboard" class="<?= nav_active('executive/dashboard') ?>">
          <i class="fa fa-chart-line nav-icon"></i> Overview
        </a>
        <a href="<?= APP_URL ?>/executive/members" class="<?= nav_active('executive/members') ?>">
          <i class="fa fa-users nav-icon"></i> Members
        </a>
        <a href="<?= APP_URL ?>/executive/events" class="<?= nav_active('executive/events') ?>">
          <i class="fa fa-calendar-days nav-icon"></i> Manage Events
        </a>
        <a href="<?= APP_URL ?>/executive/projects" class="<?= nav_active('executive/projects') ?>">
          <i class="fa fa-diagram-project nav-icon"></i> Projects
        </a>
        <a href="<?= APP_URL ?>/executive/gallery" class="<?= nav_active('executive/gallery') ?>">
          <i class="fa fa-images nav-icon"></i> Gallery
        </a>
        <a href="<?= APP_URL ?>/executive/broadcast" class="<?= nav_active('executive/broadcast') ?>">
          <i class="fa fa-bullhorn nav-icon"></i> Broadcast
        </a>
      <?php endif; ?>

      <?php if (in_array($roleId, [ROLE_FINANCIAL, ROLE_ADMIN])): ?>
        <p class="sidebar__section-label">Finance</p>
        <a href="<?= APP_URL ?>/financial/dashboard" class="<?= nav_active('financial/dashboard') ?>">
          <i class="fa fa-coins nav-icon"></i> Finance Overview
        </a>
        <a href="<?= APP_URL ?>/financial/donations" class="<?= nav_active('financial/donations') ?>">
          <i class="fa fa-hand-holding-dollar nav-icon"></i> Donations
        </a>
        <a href="<?= APP_URL ?>/financial/payments" class="<?= nav_active('financial/payments') ?>">
          <i class="fa fa-money-bill nav-icon"></i> Payments
        </a>
        <a href="<?= APP_URL ?>/financial/reports" class="<?= nav_active('financial/reports') ?>">
          <i class="fa fa-file-invoice nav-icon"></i> Reports
        </a>
      <?php endif; ?>

      <?php if ($roleId === ROLE_ADMIN): ?>
        <p class="sidebar__section-label">Administration</p>
        <a href="<?= APP_URL ?>/admin/dashboard" class="<?= nav_active('admin/dashboard') ?>">
          <i class="fa fa-shield nav-icon"></i> Admin Panel
        </a>
        <a href="<?= APP_URL ?>/admin/settings" class="<?= nav_active('admin/settings') ?>">
          <i class="fa fa-gear nav-icon"></i> Settings
        </a>
        <a href="<?= APP_URL ?>/admin/testimonials" class="<?= nav_active('admin/testimonials') ?>">
          <i class="fa fa-quote-left nav-icon"></i> Testimonials
        </a>
        <a href="<?= APP_URL ?>/admin/audit" class="<?= nav_active('admin/audit') ?>">
          <i class="fa fa-scroll nav-icon"></i> Audit Log
        </a>
      <?php endif; ?>

      <p class="sidebar__section-label">General</p>
      <a href="<?= APP_URL ?>/member/settings" class="<?= nav_active('member/settings') ?>">
        <i class="fa fa-sliders nav-icon"></i> Settings
      </a>
      <a href="<?= APP_URL ?>/logout" onclick="return confirm('Log out?')">
        <i class="fa fa-right-from-bracket nav-icon"></i> Log Out
      </a>
    </nav>

    <div class="sidebar__user">
      <img src="<?= h($avatarUrl) ?>" alt="<?= h($user['first_name']) ?>" class="sidebar__avatar">
      <div class="sidebar__user-info">
        <p class="sidebar__user-name"><?= h($user['first_name'] . ' ' . $user['last_name']) ?></p>
        <p class="sidebar__user-role"><?= h($user['role_name']) ?></p>
      </div>
      <a href="<?= APP_URL ?>/logout" class="sidebar__logout" title="Log out" onclick="return confirm('Log out?')">
        <i class="fa fa-sign-out"></i>
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar__left">
        <button class="sidebar-toggle" aria-label="Toggle sidebar">
          <i class="fa fa-bars"></i>
        </button>
        <h1 class="topbar__title"><?= h($pageTitle ?? 'Dashboard') ?></h1>
      </div>
      <div class="topbar__right">
        <button id="darkModeToggle" class="topbar__icon-btn" aria-label="Toggle dark mode">
          <i class="fa fa-circle-half-stroke"></i>
        </button>

        <!-- Notifications -->
        <div style="position:relative;">
          <button id="notifBtn" class="topbar__icon-btn" aria-label="Notifications">
            <i class="fa fa-bell"></i>
            <?php if ($unreadNotifs > 0): ?>
              <span class="topbar__badge"><?= $unreadNotifs ?></span>
            <?php endif; ?>
          </button>
          <div id="notifDrop" class="notif-drop">
            <div class="notif-drop__header">
              <span class="notif-drop__title">Notifications</span>
              <a href="<?= APP_URL ?>/member/notifications?mark_all=1" style="font-size:.8rem;color:var(--blue-600)">Mark all read</a>
            </div>
            <?php if (empty($notifications)): ?>
              <p style="padding:20px;text-align:center;color:var(--gray-500);font-size:.88rem;">No notifications</p>
            <?php else: foreach ($notifications as $n): ?>
              <div class="notif-item <?= !$n['read_at'] ? 'unread' : '' ?>">
                <p class="notif-item__title"><?= h($n['title']) ?></p>
                <?php if ($n['body']): ?><p class="notif-item__body"><?= h($n['body']) ?></p><?php endif; ?>
                <p class="notif-item__time"><?= date('M j, g:i a', strtotime($n['created_at'])) ?></p>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </div>

        <!-- User -->
        <a href="<?= APP_URL ?>/member/profile" class="topbar__user">
          <img src="<?= h($avatarUrl) ?>" alt="<?= h($user['first_name']) ?>">
          <span class="topbar__user-name"><?= h($user['first_name']) ?></span>
        </a>
      </div>
    </header>

    <!-- Page body injected here -->
    <main class="page-content">
