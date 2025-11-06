<?php 
require_once 'config/koneksi.php';

$id_transaksi = $_GET['id'] ?? null;

$stmt = $koneksi->prepare("
  SELECT t.*, m.nama_menu, m.harga 
  FROM transaksi t
  JOIN menu m ON t.id_menu = m.id_menu
  WHERE id_transaksi = :id
");
$stmt->execute([':id' => $id_transaksi]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$data){
  echo "<h3>Transaksi tidak ditemukan</h3>";
  exit;
}

include 'app/views/sections/header_nav.php';
?>

<div class="container py-5" style="margin-top:150px;">
  <div class="text-center">
    <h2 class="fw-bold text-success mb-3">Pesanan Berhasil!</h2>
    <p>Terima kasih telah memesan 🙌</p>
    
    <div class="card shadow p-4 mx-auto" style="max-width:400px;">
      <h5><b>No. Transaksi:</b> <?= $data['id_transaksi'] ?></h5>
      <p><b>Menu:</b> <?= $data['nama_menu'] ?></p>
      <p><b>Jumlah:</b> <?= $data['jumlah'] ?></p>
      <p><b>Total:</b> Rp <?= number_format($data['total_harga'],0,',','.') ?></p>
      <p><b>Tanggal:</b> <?= $data['tgl_transaksi'] ?></p>
    </div>

    <div class="mt-4">
      <a href="index.php?page=menu" class="btn btn-dark mt-2">Kembali ke Menu</a>

      <a 
        href="https://wa.me/6281290966363?text=Halo%2C%20saya%20baru%20memesan:%0A%0A-%20Menu:%20<?= urlencode($data['nama_menu']) ?>%0A-%20Jumlah:%20<?= $data['jumlah'] ?>%0A-%20Total:%20Rp%20<?= number_format($data['total_harga'],0,',','.') ?>%0A%0AMohon%20diproses%20ya%20😊" 
        class="btn btn-success mt-2" 
        target="_blank">
        Kirim ke WhatsApp
      </a>

      <button onclick="window.print()" class="btn btn-warning mt-2">Cetak Struk 🧾</button>
    </div>
  </div>
</div>
