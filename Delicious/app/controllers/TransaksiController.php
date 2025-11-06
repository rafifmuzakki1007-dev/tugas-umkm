<?php
require_once "../../config/koneksi.php";

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $koneksi->prepare("DELETE FROM transaksi WHERE id_transaksi = ?");
    $stmt->execute([$id]);

    header("Location: ../../index.php?page=transaksi_admin&status=deleted");
    exit;
}
