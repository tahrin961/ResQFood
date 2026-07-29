<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('ngo');

$claim_id = (int)($_POST['claim_id'] ?? 0);
$ngo_id = $_SESSION['user_id'];

$stmt = $conn->prepare('SELECT listing_id FROM claims WHERE claim_id = ? AND ngo_id = ? AND status = "pending"');
$stmt->bind_param('ii', $claim_id, $ngo_id);
$stmt->execute();
$claim = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($claim) {
    $conn->begin_transaction();
    $s1 = $conn->prepare("UPDATE claims SET status = 'completed' WHERE claim_id = ?");
    $s1->bind_param('i', $claim_id);
    $s1->execute();
    $s1->close();

    $s2 = $conn->prepare("UPDATE food_listings SET status = 'completed' WHERE listing_id = ?");
    $s2->bind_param('i', $claim['listing_id']);
    $s2->execute();
    $s2->close();
    $conn->commit();

    set_flash('success', 'Marked as received. Thank you for closing the loop!');
} else {
    set_flash('error', 'Claim not found.');
}

redirect('/ngo/my_claims.php');
