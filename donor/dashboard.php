<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('donor');

$donor_id = $_SESSION['user_id'];

$stats = $conn->prepare('SELECT
    COUNT(*) AS total,
    SUM(status = "available") AS available,
    SUM(status = "claimed") AS claimed,
    SUM(status = "completed") AS completed
  FROM food_listings WHERE donor_id = ?');
$stats->bind_param('i', $donor_id);
$stats->execute();
$stat_row = $stats->get_result()->fetch_assoc();
$stats->close();

$stmt = $conn->prepare('
  SELECT fl.*, u.name AS ngo_name, u.organization AS ngo_org, c.status AS claim_status
  FROM food_listings fl
  LEFT JOIN claims c ON c.listing_id = fl.listing_id
  LEFT JOIN users u ON u.user_id = c.ngo_id
  WHERE fl.donor_id = ?
  ORDER BY fl.created_at DESC
');
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$listings = $stmt->get_result();

$page_title = 'My Listings';
include __DIR__ . '/../includes/header.php';
?>

<div class="stat-row">
  <div class="stat-card"><span class="num"><?= (int)$stat_row['total'] ?></span><span class="label">Total posted</span></div>
  <div class="stat-card"><span class="num"><?= (int)$stat_row['available'] ?></span><span class="label">Available now</span></div>
  <div class="stat-card"><span class="num"><?= (int)$stat_row['claimed'] ?></span><span class="label">Claimed, pending pickup</span></div>
  <div class="stat-card"><span class="num"><?= (int)$stat_row['completed'] ?></span><span class="label">Rescued</span></div>
</div>

<?php if ($listings->num_rows === 0): ?>
  <div class="empty-state">
    <p>You haven't posted any surplus food yet.</p>
    <a href="<?= BASE_URL ?>/donor/add_listing.php" class="btn btn-accent">Post Surplus Food</a>
  </div>
<?php else: ?>
  <div class="listing-grid">
    <?php while ($row = $listings->fetch_assoc()):
      $urgency = urgency_status($row['expiry_time']);
      $isExpiredButUnclaimed = $urgency['class'] === 'urgency-expired' && $row['status'] === 'available';
      $displayStatus = $isExpiredButUnclaimed ? 'expired' : $row['status'];
    ?>
      <div class="listing-card">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
          <h3><?= sanitize($row['food_name']) ?></h3>
          <span class="badge badge-<?= sanitize($displayStatus) ?>"><?= sanitize($displayStatus) ?></span>
        </div>
        <div class="listing-meta">
          <span><?= sanitize($row['quantity'] . ' ' . $row['unit']) ?></span>
          <span><?= sanitize(ucfirst($row['food_type'])) ?></span>
          <span><?= sanitize($row['pickup_location']) ?></span>
        </div>
        <?php if ($row['description']): ?><p class="listing-desc"><?= sanitize($row['description']) ?></p><?php endif; ?>

        <?php if ($row['status'] === 'available' && !$isExpiredButUnclaimed): ?>
          <div class="urgency-wrap <?= $urgency['class'] ?>">
            <span class="urgency-label"><?= sanitize($urgency['label']) ?></span>
            <div class="urgency-track"><div class="urgency-fill"></div></div>
          </div>
        <?php elseif ($row['ngo_name']): ?>
          <p class="listing-donor">Claimed by <strong><?= sanitize($row['ngo_org'] ?: $row['ngo_name']) ?></strong> · <?= $row['claim_status'] === 'completed' ? 'picked up' : 'awaiting pickup' ?></p>
        <?php endif; ?>

        <div class="card-actions">
          <?php if ($row['status'] === 'available'): ?>
            <a href="<?= BASE_URL ?>/donor/edit_listing.php?id=<?= (int)$row['listing_id'] ?>" class="btn btn-ghost btn-sm">Edit</a>
            <a href="<?= BASE_URL ?>/donor/delete_listing.php?id=<?= (int)$row['listing_id'] ?>" class="btn btn-danger btn-sm" data-confirm="Remove this listing?">Delete</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
