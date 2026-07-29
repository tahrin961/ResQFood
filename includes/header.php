<?php
$page_title = $page_title ?? 'ResQFood';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= sanitize($page_title) ?> · ResQFood</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<?php if (is_logged_in()): ?>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand">
      <span class="brand-mark">R</span>
      <span class="brand-name">ResQFood</span>
    </div>
    <nav class="side-nav">
      <?php if (current_role() === 'donor'): ?>
        <a href="<?= BASE_URL ?>/donor/dashboard.php" class="<?= active('donor/dashboard.php') ?>">My Listings</a>
        <a href="<?= BASE_URL ?>/donor/add_listing.php" class="<?= active('donor/add_listing.php') ?>">Post Surplus Food</a>
      <?php elseif (current_role() === 'ngo'): ?>
        <a href="<?= BASE_URL ?>/ngo/dashboard.php" class="<?= active('ngo/dashboard.php') ?>">Browse Listings</a>
        <a href="<?= BASE_URL ?>/ngo/my_claims.php" class="<?= active('ngo/my_claims.php') ?>">My Claims</a>
      <?php elseif (current_role() === 'admin'): ?>
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="<?= active('admin/dashboard.php') ?>">Overview</a>
        <a href="<?= BASE_URL ?>/admin/listings.php" class="<?= active('admin/listings.php') ?>">All Listings</a>
        <a href="<?= BASE_URL ?>/admin/users.php" class="<?= active('admin/users.php') ?>">All Users</a>
      <?php endif; ?>
    </nav>
    <a href="<?= BASE_URL ?>/logout.php" class="logout-link">Log out</a>
  </aside>
  <div class="main-area">
    <header class="topbar">
      <h1><?= sanitize($page_title) ?></h1>
      <span class="who"><?= sanitize($_SESSION['name']) ?> <span class="role-tag"><?= sanitize(ucfirst(current_role())) ?></span></span>
    </header>
    <main class="content">
      <?php foreach (get_flashes() as $f): ?>
        <div class="alert alert-<?= sanitize($f['type']) ?>"><?= sanitize($f['message']) ?></div>
      <?php endforeach; ?>
<?php else: ?>
<nav class="public-nav">
  <a href="<?= BASE_URL ?>/index.php" class="brand">
    <span class="brand-mark">R</span><span class="brand-name">ResQFood</span>
  </a>
  <div class="public-nav-links">
    <a href="<?= BASE_URL ?>/login.php">Log in</a>
    <a href="<?= BASE_URL ?>/register.php" class="btn btn-accent">Get Started</a>
  </div>
</nav>
<main class="public-main">
  <?php foreach (get_flashes() as $f): ?>
    <div class="alert alert-<?= sanitize($f['type']) ?>"><?= sanitize($f['message']) ?></div>
  <?php endforeach; ?>
<?php endif; ?>
