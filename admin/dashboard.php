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

// Weekly food-saved trend: completed listings grouped by the week they were posted.
// Note: schema has no separate completed_at column, so created_at is used as a proxy.
$weeklyResult = $conn->query("
  SELECT
    YEARWEEK(created_at, 1) AS yw,
    MIN(DATE(created_at)) AS week_start,
    COUNT(*) AS listings_saved,
    SUM(quantity) AS quantity_saved
  FROM food_listings
  WHERE status = 'completed'
    AND created_at >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
  GROUP BY yw
  ORDER BY yw ASC
");

$weekLabels = [];
$weekListingCounts = [];
$weekQuantities = [];
while ($row = $weeklyResult->fetch_assoc()) {
    $weekLabels[] = date('M j', strtotime($row['week_start']));
    $weekListingCounts[] = (int)$row['listings_saved'];
    $weekQuantities[] = (float)$row['quantity_saved'];
}

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

<h2 style="font-size:18px;">Weekly food-saved trend</h2>
<div class="table-wrap" style="padding:16px;">
  <?php if (empty($weekLabels)): ?>
    <p style="color:#888;">No completed listings in the last 8 weeks yet.</p>
  <?php else: ?>
    <canvas id="weeklyTrendChart" height="90"></canvas>
  <?php endif; ?>
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

<?php if (!empty($weekLabels)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
  new Chart(document.getElementById('weeklyTrendChart'), {
    type: 'line',
    data: {
      labels: <?= json_encode($weekLabels) ?>,
      datasets: [
        {
          label: 'Listings saved',
          data: <?= json_encode($weekListingCounts) ?>,
          borderColor: '#2e7d32',
          backgroundColor: 'rgba(46,125,50,0.1)',
          tension: 0.3,
          fill: true
        },
        {
          label: 'Quantity saved',
          data: <?= json_encode($weekQuantities) ?>,
          borderColor: '#f57c00',
          backgroundColor: 'rgba(245,124,0,0.1)',
          tension: 0.3,
          fill: true,
          hidden: true
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom' } },
      scales: { y: { beginAtZero: true } }
    }
  });
</script>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>