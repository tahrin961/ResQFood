<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('donor');

$errors = [];
$old = ['food_name' => '', 'food_type' => 'cooked', 'quantity' => '', 'unit' => 'kg', 'pickup_location' => '', 'expiry_time' => '', 'description' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $_) {
        $old[$key] = $_POST[$key] ?? '';
    }

    $food_name = trim($old['food_name']);
    $quantity = $old['quantity'];
    $pickup_location = trim($old['pickup_location']);
    $expiry_time = $old['expiry_time'];
    $description = trim($old['description']);
    $food_type = in_array($old['food_type'], ['cooked', 'raw', 'packaged', 'bakery', 'other']) ? $old['food_type'] : 'other';
    $unit = in_array($old['unit'], ['kg', 'liters', 'pieces', 'plates', 'packets']) ? $old['unit'] : 'kg';

    if ($food_name === '') $errors[] = 'Food name is required.';
    if (!is_numeric($quantity) || $quantity <= 0) $errors[] = 'Quantity must be a positive number.';
    if ($pickup_location === '') $errors[] = 'Pickup location is required.';
    if ($expiry_time === '') {
        $errors[] = 'Expiry / best-before time is required.';
    } elseif (strtotime($expiry_time) <= time()) {
        $errors[] = 'Expiry time must be in the future.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare('INSERT INTO food_listings (donor_id, food_name, food_type, quantity, unit, pickup_location, expiry_time, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('issdssss', $_SESSION['user_id'], $food_name, $food_type, $quantity, $unit, $pickup_location, $expiry_time, $description);
        if ($stmt->execute()) {
            set_flash('success', 'Listing posted. NGOs can now see and claim it.');
            redirect('/donor/dashboard.php');
        } else {
            $errors[] = 'Something went wrong. Please try again.';
        }
        $stmt->close();
    }
}

$page_title = 'Post Surplus Food';
include __DIR__ . '/../includes/header.php';
?>

<?php foreach ($errors as $err): ?>
  <div class="alert alert-error"><?= sanitize($err) ?></div>
<?php endforeach; ?>

<form method="POST" action="<?= BASE_URL ?>/donor/add_listing.php" class="form-card">
  <div class="form-group">
    <label for="food_name">Food name</label>
    <input type="text" id="food_name" name="food_name" value="<?= sanitize($old['food_name']) ?>" placeholder="e.g. Leftover biryani trays" required>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="food_type">Food type</label>
      <select id="food_type" name="food_type">
        <?php foreach (['cooked' => 'Cooked meal', 'raw' => 'Raw ingredients', 'packaged' => 'Packaged / sealed', 'bakery' => 'Bakery', 'other' => 'Other'] as $val => $label): ?>
          <option value="<?= $val ?>" <?= $old['food_type'] === $val ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="quantity">Quantity</label>
      <input type="number" id="quantity" name="quantity" step="0.1" min="0.1" value="<?= sanitize($old['quantity']) ?>" required>
    </div>
    <div class="form-group">
      <label for="unit">Unit</label>
      <select id="unit" name="unit">
        <?php foreach (['kg', 'liters', 'pieces', 'plates', 'packets'] as $u): ?>
          <option value="<?= $u ?>" <?= $old['unit'] === $u ? 'selected' : '' ?>><?= $u ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="form-group">
    <label for="pickup_location">Pickup location</label>
    <input type="text" id="pickup_location" name="pickup_location" value="<?= sanitize($old['pickup_location']) ?>" placeholder="Address or landmark" required>
  </div>

  <div class="form-group">
    <label for="expiry_time">Best before / must be picked up by</label>
    <input type="datetime-local" id="expiry_time" name="expiry_time" value="<?= sanitize($old['expiry_time']) ?>" required>
    <p class="hint">This drives the urgency indicator NGOs see — be realistic about how long the food stays safe to eat.</p>
  </div>

  <div class="form-group">
    <label for="description">Notes (optional)</label>
    <textarea id="description" name="description" placeholder="Packaging, storage, any allergens..."><?= sanitize($old['description']) ?></textarea>
  </div>

  <button type="submit" class="btn btn-accent btn-lg">Post Listing</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
