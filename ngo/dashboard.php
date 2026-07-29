<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('ngo');

$type_filter = $_GET['type'] ?? '';
$valid_types = ['cooked', 'raw', 'packaged', 'bakery', 'other'];

$sql = "
  SELECT fl.*, u.name AS donor_name, u.organization AS donor_org, u.phone AS donor_phone
  FROM food_listings fl
  JOIN users u ON u.user_id = fl.donor_id
  WHERE fl.status = 'available' AND fl.expiry_time > NOW()
";
if (in_array($type_filter, $valid_types)) {
    $sql .= " AND fl.food_type = ?";
}
$sql .= " ORDER BY fl.expiry_time ASC";

$stmt = $conn->prepare($sql);
if (in_array($type_filter, $valid_types)) {
    $stmt->bind_param('s', $type_filter);
}
$stmt->execute();
$listings = $stmt->get_result();

$page_title = 'Browse Listings';
include __DIR__ . '/../includes/header.php';
?>

<div class="filter-bar">
  <form method="GET" action="<?= BASE_URL ?>/ngo/dashboard.php">
    <select name="type" onchange="this.form.submit()">
      <option value="">All food types</option>
      <?php foreach ($valid_types as $t): ?>
        <option value="<?= $t ?>" <?= $type_filter === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<?php if ($listings->num_rows === 0): ?>
  <div class="empty-state">
    <p>No surplus food is currently available<?= $type_filter ? ' in this category' : '' ?>. Check back soon.</p>
  </div>
<?php else: ?>
  <div class="listing-grid">
    <?php while ($row = $listings->fetch_assoc()): $urgency = urgency_status($row['expiry_time']); ?>
      <div class="listing-card">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
          <h3><?= sanitize($row['food_name']) ?></h3>
          <span class="badge badge-available">available</span>
        </div>
        <div class="listing-meta">
          <span><?= sanitize($row['quantity'] . ' ' . $row['unit']) ?></span>
          <span><?= sanitize(ucfirst($row['food_type'])) ?></span>
          <span><?= sanitize($row['pickup_location']) ?></span>
        </div>
        <p class="listing-donor">From <strong><?= sanitize($row['donor_org'] ?: $row['donor_name']) ?></strong></p>
        <?php if ($row['description']): ?><p class="listing-desc"><?= sanitize($row['description']) ?></p><?php endif; ?>

        <div class="urgency-wrap <?= $urgency['class'] ?>">
          <span class="urgency-label"><?= sanitize($urgency['label']) ?></span>
          <div class="urgency-track"><div class="urgency-fill"></div></div>
        </div>

        <div class="card-actions">
          <form method="POST" action="<?= BASE_URL ?>/ngo/claim.php">
            <input type="hidden" name="listing_id" value="<?= (int)$row['listing_id'] ?>">
            <button type="submit" class="btn btn-primary btn-sm">Claim This</button>
          </form>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
