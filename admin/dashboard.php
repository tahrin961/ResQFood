<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$userStats = $conn->query("SELECT
    SUM(role = 'donor') AS donors,
    SUM(role = 'ngo') AS ngos
  FROM users")->fetch_assoc();

$listingStats = $conn->query("SELECT
    COUNT(*) AS total,
    SUM(status = 'available') AS available,
    SUM(status = 'claimed') AS claimed,
    SUM(status = 'completed') AS completed
  FROM food_listings")->fetch_assoc();

$claimCount = $conn->query("SELECT COUNT(*) AS c FROM claims")->fetch_assoc()['c'];

$recent = $conn->query("
  SELECT fl.food_name, fl.quantity, fl.unit, fl.status, fl.created_at, u.name AS donor_name, u.organization AS donor_org
  FROM food_listings fl
  JOIN users u ON u.user_id = fl.donor_id
  ORDER BY fl.created_at DESC
  LIMIT 8
");

$page_title = 'Overview';
include __DIR__ . '/../includes/header.php';
?>

<div class="stat-row">
  <div class="stat-card"><span class="num"><?= (int)$userStats['donors'] ?></span><span class="label">Donors</span></div>
  <div class="stat-card"><span class="num"><?= (int)$userStats['ngos'] ?></span><span class="label">NGOs</span></div>
  <div class="stat-card"><span class="num"><?= (int)$listingStats['total'] ?></span><span class="label">Listings posted</span></div>
  <div class="stat-card"><span class="num"><?= (int)$listingStats['available'] ?></span><span class="label">Available now</span></div>
  <div class="stat-card"><span class="num"><?= (int)$listingStats['completed'] ?></span><span class="label">Meals rescued</span></div>
  <div class="stat-card"><span class="num"><?= (int)$claimCount ?></span><span class="label">Total claims</span></div>
</div>

<h2 style="font-size:18px;">Recent activity</h2>
<div class="table-wrap">
  <table>
    <thead><tr><th>Food</th><th>Quantity</th><th>Donor</th><th>Status</th><th>Posted</th></tr></thead>
    <tbody>
      <?php while ($row = $recent->fetch_assoc()): ?>
        <tr>
          <td><?= sanitize($row['food_name']) ?></td>
          <td><?= sanitize($row['quantity'] . ' ' . $row['unit']) ?></td>
          <td><?= sanitize($row['donor_org'] ?: $row['donor_name']) ?></td>
          <td><span class="badge badge-<?= sanitize($row['status']) ?>"><?= sanitize($row['status']) ?></span></td>
          <td><?= sanitize(time_ago($row['created_at'])) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
