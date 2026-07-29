<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    if (current_role() === 'donor') redirect('/donor/dashboard.php');
    if (current_role() === 'ngo') redirect('/ngo/dashboard.php');
    if (current_role() === 'admin') redirect('/admin/dashboard.php');
}

$page_title = 'Home';
include __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <span class="eyebrow">Food rescue, Bangladesh</span>
  <h1>Surplus food shouldn't outlive the people who need it.</h1>
  <p class="lede">ResQFood connects restaurants, event planners, and households with surplus food to verified NGOs and community kitchens — before it spoils.</p>
  <div class="hero-actions">
    <a href="<?= BASE_URL ?>/register.php" class="btn btn-accent btn-lg">Start Rescuing Food</a>
    <a href="<?= BASE_URL ?>/login.php" class="btn btn-ghost btn-lg">I already have an account</a>
  </div>
</section>

<section class="how-it-works">
  <div class="step">
    <span class="step-mark">Post</span>
    <p>Donors list surplus food with quantity, type, and a pickup window.</p>
  </div>
  <div class="step">
    <span class="step-mark">Claim</span>
    <p>NGOs browse open listings and claim what they can collect in time.</p>
  </div>
  <div class="step">
    <span class="step-mark">Rescue</span>
    <p>Food reaches people before it expires, tracked from post to pickup.</p>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
