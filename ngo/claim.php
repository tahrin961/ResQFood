<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('ngo');

$listing_id = (int)($_POST['listing_id'] ?? 0);
$ngo_id = $_SESSION['user_id'];

// Guard against two NGOs claiming the same listing at once: the UPDATE
// only succeeds if the listing is still 'available' at the moment it runs.
$stmt = $conn->prepare("UPDATE food_listings SET status = 'claimed' WHERE listing_id = ? AND status = 'available' AND expiry_time > NOW()");
$stmt->bind_param('i', $listing_id);
$stmt->execute();
$claimed = $stmt->affected_rows > 0;
$stmt->close();

if ($claimed) {
    $stmt = $conn->prepare('INSERT INTO claims (listing_id, ngo_id) VALUES (?, ?)');
    $stmt->bind_param('ii', $listing_id, $ngo_id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Listing claimed. Coordinate pickup with the donor.');
} else {
    set_flash('error', 'This listing was just claimed or has expired.');
}

redirect('/ngo/dashboard.php');
