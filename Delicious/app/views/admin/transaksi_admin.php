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

<style>
.container { margin-top: 10px; }

.table {
    border-collapse: separate;
    border-spacing: 0 10px;
}
.table thead th {
    background: #f8f9fc !important;
    color: black;
    font-weight: 700;
    font-size: 15px;
    padding: 14px 18px;
    border: none !important;
}
.table tbody tr {
    background: #ffffff;
    box-shadow: 0 3px 12px rgba(0,0,0,0.07);
    border-radius: 12px;
}
.table tbody tr:hover {
    transform: translateY(-2px);
    transition: 0.15s ease;
    box-shadow: 0 5px 16px rgba(0,0,0,0.12);
}
.table tbody tr td {
    padding: 16px 18px;
    font-size: 15px;
    border: none !important;
}
.table .btn {
    padding: 5px 10px;
    border-radius: 10px;
    font-size: 13px;
}
.swal2-popup { animation: swal2-show .24s ease-out; }
@keyframes swal2-show {
    from { transform: scale(0.96); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}
</style>

<div class="container py-4">

  <h3 class="fw-bold mb-4"><i class="bi bi-receipt me-2"></i> Daftar Transaksi</h3>

  <div class="card shadow-sm border-0">
    <div class="card-body">

      <table class="table align-middle" id="transaksiTable">
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
// ========================= FIX ANTI-FREEZE =========================
function cleanupSweetAlert() {
    document.querySelectorAll(".swal2-container").forEach(el => el.remove());
    document.querySelectorAll(".swal2-backdrop").forEach(el => el.remove());
    document.body.classList.remove("swal2-shown", "swal2-height-auto");
    document.documentElement.classList.remove("swal2-shown", "swal2-height-auto");
    document.body.style.overflow = "auto";
    document.documentElement.style.overflow = "auto";

    // ARIA-HIDDEN killer (penyebab utama freeze)
    document.querySelectorAll("[aria-hidden]").forEach(el => el.removeAttribute("aria-hidden"));
}

// ketika halaman dibuka, bersihkan residu swal (jaga2)
cleanupSweetAlert();

// ========================= DELETE SweetAlert =========================
document.querySelectorAll(".deleteTransaksi").forEach(btn => {
  btn.onclick = () => {

    cleanupSweetAlert(); // bersihkan sebelum swal muncul

    Swal.fire({
      title: "Hapus transaksi?",
      text: "Data yang dihapus tidak bisa dikembalikan!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Hapus",
      allowOutsideClick: false
    }).then((res) => {

      cleanupSweetAlert(); // bersihkan setelah swal

      if (res.isConfirmed) {
        window.location = "index.php?page=transaksi_admin&delete=" + btn.dataset.id;
      }
    });
  };
});

// ========================= ALERT SUKSES =========================
if (new URLSearchParams(window.location.search).get("transaksi") === "deleted") {
  Swal.fire({
    icon: "success",
    title: "Transaksi berhasil dihapus!",
    timer: 1500,
    showConfirmButton: false
  }).then(() => cleanupSweetAlert());
}
</script>
