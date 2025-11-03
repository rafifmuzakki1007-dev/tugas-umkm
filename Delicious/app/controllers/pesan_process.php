<?php
// Pastikan path koneksi BENAR
require_once __DIR__ . '/../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_menu         = $_POST['id_menu'];
    $jumlah          = $_POST['jumlah'];
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $harga           = $_POST['harga'];

    $tgl_transaksi = date("Y-m-d");
    $total_harga   = $jumlah * $harga;

    // Ambil kode transaksi terakhir
    $stmtKode = $koneksi->query("SELECT MAX(id_transaksi) AS last FROM transaksi");
    $dataKode = $stmtKode->fetch(PDO::FETCH_ASSOC);

    $lastId = $dataKode && $dataKode['last'] ? intval(substr($dataKode['last'],2)) : 0;
    $newId  = "TR" . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

    // Insert transaksi
    $stmt = $koneksi->prepare("
        INSERT INTO transaksi (id_transaksi, tgl_transaksi, jenis_transaksi, jumlah, total_harga, id_menu)
        VALUES (:id, :tgl, :jenis, :jumlah, :total, :menu)
    ");
    $stmt->execute([
        ':id'     => $newId,
        ':tgl'    => $tgl_transaksi,
        ':jenis'  => $jenis_transaksi,
        ':jumlah' => $jumlah,
        ':total'  => $total_harga,
        ':menu'   => $id_menu
    ]);

    // Update stok
    $stmtStok = $koneksi->prepare("UPDATE menu SET stok = stok - :j WHERE id_menu = :id");
    $stmtStok->execute([ ':j' => $jumlah, ':id' => $id_menu ]);

    // Redirect dengan flag sukses
    header("Location: index.php?page=menu&status=success");
    exit;
}

// jika akses langsung
header("Location: index.php?page=menu");
exit;
