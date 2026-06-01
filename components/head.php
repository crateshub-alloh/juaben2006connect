<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= h($metaDesc ?? 'Juaben2006 Connect – Connecting generations and building the future.') ?>">
  <title><?= h(($pageTitle ?? '') ? $pageTitle . ' – ' . APP_NAME : APP_NAME) ?></title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

  <!-- Icons (Font Awesome 6 free via CDN) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Styles -->
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
  <?php if (!empty($extraCss)) foreach ($extraCss as $css) echo "<link rel='stylesheet' href='{$css}'>"; ?>

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="<?= APP_URL ?>/assets/images/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= APP_URL ?>/assets/images/favicon-16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= APP_URL ?>/assets/images/apple-touch-icon.png">
</head>
<body>
