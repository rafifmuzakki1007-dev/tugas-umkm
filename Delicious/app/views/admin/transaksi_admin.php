<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config/koneksi.php';

// Ambil data transaksi + menu
$query = $koneksi->query("
    SELECT t.*, m.nama_menu 
    FROM transaksi t
    JOIN menu m ON t.id_menu = m.id_menu
    ORDER BY t.tgl_transaksi ASC
");
$transaksi = $query->fetchAll(PDO::FETCH_ASSOC);

// Proses Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $del = $koneksi->prepare("DELETE FROM transaksi WHERE id_transaksi = ?");
    $del->execute([$id]);
    header("Location: index.php?page=transaksi_admin&transaksi=deleted");
    exit;
}
?>

<div class="container py-4">
  <h3 class="fw-bold mb-3"><i class="bi bi-receipt"></i> Daftar Transaksi</h3>

  <div class="card shadow-sm border-0">
    <div class="card-body">

      <table class="table table-hover">
        <thead class="table-light">
          <tr>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Menu</th>
            <th>Jumlah</th>
            <th>Total</th>
            <th>Pembayaran</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($transaksi as $row): ?>
          <tr>
            <td><span class="badge bg-dark"><?= $row['id_transaksi'] ?></span></td>

            <!-- Format tanggal realtime -->
            <td><?= date("d M Y H:i:s", strtotime($row['tgl_transaksi'])) ?></td>

            <td><?= $row['nama_menu'] ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
            <td><?= $row['jenis_transaksi'] ?></td>

            <td>
              <button class="btn btn-danger btn-sm deleteTransaksi" data-id="<?= $row['id_transaksi'] ?>">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

    </div>
  </div>
</div>

<script>
// SweetAlert delete
document.querySelectorAll(".deleteTransaksi").forEach(btn => {
  btn.onclick = () => {
    Swal.fire({
      title: "Hapus transaksi?",
      text: "Data yang dihapus tidak bisa dikembalikan!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Hapus",
    }).then((res) => {
      if (res.isConfirmed) {
        window.location = "index.php?page=transaksi_admin&delete=" + btn.dataset.id;
      }
    });
  };
});

// Alert sukses hapus
if (new URLSearchParams(window.location.search).get("transaksi") === "deleted") {
  Swal.fire({
    icon: "success",
    title: "Transaksi berhasil dihapus!",
    timer: 1500,
    showConfirmButton: false
  });
}
</script>
