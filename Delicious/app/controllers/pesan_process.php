<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/koneksi.php';

// Kalau checkout dari cart (tanpa POST id_menu)
$isCartCheckout = isset($_SESSION['cart']) && empty($_POST['id_menu']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $isCartCheckout) {

    // Generate ID transaksi baru
    $stmtKode = $koneksi->query("SELECT MAX(id_transaksi) AS last FROM transaksi");
    $dataKode = $stmtKode->fetch(PDO::FETCH_ASSOC);
    $lastId   = $dataKode['last'] ? intval(substr($dataKode['last'], 2)) : 0;

    // fungsi generate id transaksi
    function newId($lastId){
        return "TR" . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
    }

    // CASE 1: PESAN DARI MODAL (langsung pesan 1 menu)
    if (isset($_POST['id_menu']) && $_POST['id_menu'] !== "") {

        $id_menu        = $_POST['id_menu'];
        $jumlah         = $_POST['jumlah'];
        $harga          = $_POST['harga'];
        $jenis_transaksi= $_POST['jenis_transaksi'];
        $total_harga    = $jumlah * $harga;

        $newId = newId($lastId);

        $stmt = $koneksi->prepare("
            INSERT INTO transaksi (id_transaksi, tgl_transaksi, jenis_transaksi, jumlah, total_harga, id_menu)
            VALUES (?, NOW(), ?, ?, ?, ?)
        ");
        $stmt->execute([$newId, $jenis_transaksi, $jumlah, $total_harga, $id_menu]);

        // Kurangi stok
        $stmtStok = $koneksi->prepare("UPDATE menu SET stok = stok - ? WHERE id_menu = ?");
        $stmtStok->execute([$jumlah, $id_menu]);

    } 
    // CASE 2: CHECKOUT DARI CART
    elseif ($isCartCheckout) {

        foreach ($_SESSION['cart'] as $id => $qty) {
            
            $harga = $koneksi->query("SELECT harga FROM menu WHERE id_menu='$id'")
                             ->fetch(PDO::FETCH_COLUMN);

            $total = $harga * $qty;

            $lastId++;
            $newId = newId($lastId);

            $stmt = $koneksi->prepare("
                INSERT INTO transaksi (id_transaksi, tgl_transaksi, jenis_transaksi, jumlah, total_harga, id_menu)
                VALUES (?, NOW(), 'Tunai', ?, ?, ?)
            ");
            $stmt->execute([$newId, $qty, $total, $id]);

            $stmtStok = $koneksi->prepare("UPDATE menu SET stok = stok - ? WHERE id_menu = ?");
            $stmtStok->execute([$qty, $id]);
        }

        unset($_SESSION['cart']); // kosongi keranjang
    }

    header("Location: ../../index.php?page=menu&status=success");
    exit;
}
header("Location: ../../index.php?page=menu");
exit;
