<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

function sanitize($value) {
    return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header('Location: ' . BASE_URL . $path);
    exit;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_role() {
    return $_SESSION['role'] ?? null;
}

function require_login() {
    if (!is_logged_in()) {
        redirect('/login.php');
    }
}

function require_role($role) {
    require_login();
    if (current_role() !== $role) {
        redirect('/login.php');
    }
}

function set_flash($type, $message) {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes() {
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

// Highlights the active sidebar link.
function active($path) {
    return (strpos($_SERVER['SCRIPT_NAME'], $path) !== false) ? 'active' : '';
}

/**
 * Works out how urgent a listing is based on time left before expiry.
 * Drives the colour-coded urgency bar shown on every listing card.
 */
function urgency_status($expiry_time) {
    $now = new DateTime();
    $expiry = new DateTime($expiry_time);
    $diff_hours = ($expiry->getTimestamp() - $now->getTimestamp()) / 3600;

    if ($diff_hours <= 0) {
        return ['label' => 'Expired', 'class' => 'urgency-expired', 'percent' => 100];
    } elseif ($diff_hours <= 3) {
        return ['label' => 'Urgent · ' . round($diff_hours, 1) . 'h left', 'class' => 'urgency-high', 'percent' => 85];
    } elseif ($diff_hours <= 12) {
        return ['label' => round($diff_hours, 1) . 'h left', 'class' => 'urgency-medium', 'percent' => 55];
    } else {
        $days = round($diff_hours / 24, 1);
        return ['label' => $days . ' day(s) left', 'class' => 'urgency-low', 'percent' => 20];
    }
}

function time_ago($datetime) {
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->getTimestamp() - $past->getTimestamp();

    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    return floor($diff / 86400) . 'd ago';
}

function format_dt($datetime) {
    return date('d M, h:i A', strtotime($datetime));
}
