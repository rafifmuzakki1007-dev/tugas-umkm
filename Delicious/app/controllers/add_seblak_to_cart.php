<?php
session_start();
require_once __DIR__ . '/../../config/koneksi.php';

header('Content-Type: application/json');

if (!isset($_POST['topping'])) {
    echo json_encode(['success' => false]);
    exit;
}

$toppingJson = $_POST['topping'];
$selectedTopping = json_decode($toppingJson, true);

if (!is_array($selectedTopping)) {
    echo json_encode(['success' => false]);
    exit;
}

// Data seblak utama (bisa diambil dari DB juga)
$seblak = [
    'menu_id'    => 'MN001',                    // sesuaikan dengan ID seblak kamu
    'menu_nama'  => 'Seblak Prasmanan'
];

// Tambah sebagai 1 pesanan baru
$_SESSION['cart'][] = [
    'menu_id'    => $seblak['menu_id'],
    'menu_nama'  => $seblak['menu_nama'],
    'harga_menu' => $seblak['harga_menu'],
    'topping'    => $selectedTopping
];

echo json_encode(['success' => true]);