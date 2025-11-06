<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['ok' => false]);
    exit;
}

if (isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

// Hitung ulang
$total = array_sum($_SESSION['cart'] ?? []);

echo json_encode(['ok' => true, 'count' => $total]);
