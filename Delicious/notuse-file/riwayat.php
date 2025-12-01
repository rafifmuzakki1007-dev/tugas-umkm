<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php?page=login");
  exit;
}

require_once __DIR__ . '/../../config/koneksi.php';

$id_user = $_SESSION['user_id'];

$query = $koneksi->prepare("
    SELECT t.*, m.nama_menu, m.gambar
    FROM transaksi t
    JOIN menu m ON t.id_menu = m.id_menu
    WHERE t.id_user = ?
    ORDER BY t.tgl_transaksi DESC
");
$query->execute([$id_user]);
$riwayat = $query->fetchAll(PDO::FETCH_ASSOC);

include 'sections/header_nav.php';
?>

<div class="container page-wrapper my-5">
  <h2 class="fw-bold mb-4"><i class="bi bi-clock-history"></i> Riwayat Pesanan Saya</h2>

  <?php if (!$riwayat): ?>
      <div class="alert alert-warning">Belum ada riwayat pesanan 📦</div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-warning">
        <tr>
          <th>Tanggal</th>
          <th>Menu</th>
          <th>Qty</th>
          <th>Total</th>
          <th>Pembayaran</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($riwayat as $row): ?>
        <tr>
          <td><?= date('d/m/Y', strtotime($row['tgl_transaksi'])); ?></td>
          <td><?= $row['nama_menu']; ?></td>
          <td><?= $row['jumlah']; ?></td>
          <td>Rp <?= number_format($row['total_harga']); ?></td>
          <td><?= $row['jenis_transaksi']; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
