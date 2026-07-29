<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$status_filter = $_GET['status'] ?? '';
$valid_statuses = ['available', 'claimed', 'completed', 'expired'];

$sql = "
  SELECT fl.*, u.name AS donor_name, u.organization AS donor_org
  FROM food_listings fl
  JOIN users u ON u.user_id = fl.donor_id
";
if (in_array($status_filter, $valid_statuses)) {
    $sql .= " WHERE fl.status = ?";
}
$sql .= " ORDER BY fl.created_at DESC";

$stmt = $conn->prepare($sql);
if (in_array($status_filter, $valid_statuses)) {
    $stmt->bind_param('s', $status_filter);
}
$stmt->execute();
$listings = $stmt->get_result();

$page_title = 'All Listings';
include __DIR__ . '/../includes/header.php';
?>

<div class="filter-bar">
  <form method="GET" action="<?= BASE_URL ?>/admin/listings.php">
    <select name="status" onchange="this.form.submit()">
      <option value="">All statuses</option>
      <?php foreach ($valid_statuses as $s): ?>
        <option value="<?= $s ?>" <?= $status_filter === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Food</th><th>Type</th><th>Quantity</th><th>Donor</th><th>Pickup location</th><th>Expiry</th><th>Status</th><th>Posted</th></tr>
    </thead>
    <tbody>
      <?php while ($row = $listings->fetch_assoc()): ?>
        <tr>
          <td><?= sanitize($row['food_name']) ?></td>
          <td><?= sanitize(ucfirst($row['food_type'])) ?></td>
          <td><?= sanitize($row['quantity'] . ' ' . $row['unit']) ?></td>
          <td><?= sanitize($row['donor_org'] ?: $row['donor_name']) ?></td>
          <td><?= sanitize($row['pickup_location']) ?></td>
          <td><?= sanitize(format_dt($row['expiry_time'])) ?></td>
          <td><span class="badge badge-<?= sanitize($row['status']) ?>"><?= sanitize($row['status']) ?></span></td>
          <td><?= sanitize(time_ago($row['created_at'])) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
