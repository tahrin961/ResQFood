<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('donor');

$listing_id = (int)($_GET['id'] ?? 0);
$donor_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM food_listings WHERE listing_id = ? AND donor_id = ? AND status = 'available'");
$stmt->bind_param('ii', $listing_id, $donor_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    set_flash('success', 'Listing removed.');
} else {
    set_flash('error', 'Could not remove that listing (it may already be claimed).');
}
$stmt->close();

redirect('/donor/dashboard.php');
