<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('ngo');

$ngo_id = $_SESSION['user_id'];

$stmt = $conn->prepare('
  SELECT c.claim_id, c.status AS claim_status, c.claimed_at,
         fl.food_name, fl.quantity, fl.unit, fl.pickup_location, fl.food_type,
         u.name AS donor_name, u.organization AS donor_org, u.phone AS donor_phone
  FROM claims c
  JOIN food_listings fl ON fl.listing_id = c.listing_id
  JOIN users u ON u.user_id = fl.donor_id
  WHERE c.ngo_id = ?
  ORDER BY c.claimed_at DESC
');
$stmt->bind_param('i', $ngo_id);
$stmt->execute();
$claims = $stmt->get_result();

$page_title = 'My Claims';
include __DIR__ . '/../includes/header.php';
?>

<?php if ($claims->num_rows === 0): ?>
  <div class="empty-state">
    <p>You haven't claimed any listings yet.</p>
    <a href="<?= BASE_URL ?>/ngo/dashboard.php" class="btn btn-accent">Browse Listings</a>
  </div>
<?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Food</th><th>Quantity</th><th>Pickup location</th><th>Donor</th><th>Contact</th><th>Claimed</th><th>Status</th><th></th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $claims->fetch_assoc()): ?>
          <tr>
            <td><?= sanitize($row['food_name']) ?></td>
            <td><?= sanitize($row['quantity'] . ' ' . $row['unit']) ?></td>
            <td><?= sanitize($row['pickup_location']) ?></td>
            <td><?= sanitize($row['donor_org'] ?: $row['donor_name']) ?></td>
            <td><?= sanitize($row['donor_phone'] ?: '—') ?></td>
            <td><?= sanitize(time_ago($row['claimed_at'])) ?></td>
            <td><span class="badge badge-<?= $row['claim_status'] === 'completed' ? 'completed' : 'claimed' ?>"><?= sanitize($row['claim_status']) ?></span></td>
            <td>
              <?php if ($row['claim_status'] === 'pending'): ?>
                <form method="POST" action="<?= BASE_URL ?>/ngo/complete_claim.php">
                  <input type="hidden" name="claim_id" value="<?= (int)$row['claim_id'] ?>">
                  <button type="submit" class="btn btn-ghost btn-sm">Mark Received</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
