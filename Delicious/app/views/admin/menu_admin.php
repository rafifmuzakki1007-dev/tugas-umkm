<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/../../../app/models/MenuModel.php';

$menuModel = new MenuModel($koneksi);
$menus = $menuModel->getAllMenu();

// Urut MN001 -> MN999
usort($menus, function($a, $b) {
    return intval(substr($a['id_menu'], 2)) <=> intval(substr($b['id_menu'], 2));
});
?>

<style>
/* ====== PERAPIHAN MODAL TANPA MENGUBAH STRUKTUR ====== */
/* pastikan modal selalu di atas sidebar */
.modal { z-index: 2000 !important; }
.modal-backdrop { z-index: 1500 !important; }

/* center horizontal + beri jarak dari atas agar tidak nabrak header/sidebar  */
.modal-dialog {
  margin-left: auto !important;
  margin-right: auto !important;
}
.modal-dialog-centered {
  margin-top: 72px !important;       /* jarak nyaman dari top */
}

/* kecilkan padding agar rapi */
.modal-header { padding: 14px 18px; }
.modal-body .form-control { padding: 10px; font-size: 15px; }
.modal-footer { padding: 12px 18px; }

/* hilangkan padding kanan bawaan bootstrap saat modal open (biar layout nggak geser) */
body.modal-open { padding-right: 0 !important; }
</style>

<div class="container py-4">
  <h2 class="fw-bold mb-4"><i class="bi bi-grid"></i> Kelola Menu</h2>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
      <h5 class="m-0">Daftar Menu</h5>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle"></i> Tambah Menu
      </button>
    </div>

    <div class="card-body">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID Menu</th>
            <th>Gambar</th>
            <th>Nama Menu</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($menus as $row): ?>
          <tr>
            <td><span class="badge bg-secondary"><?= $row['id_menu']; ?></span></td>
            <td><img src="assets/img/menu/<?= $row['gambar']; ?>" width="60" height="60" class="rounded" style="object-fit:cover"></td>
            <td><?= $row['nama_menu']; ?></td>
            <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
            <td><?= $row['stok']; ?></td>
            <td>
              <button class="btn btn-warning btn-sm editBtn"
                data-id="<?= $row['id_menu']; ?>"
                data-nama="<?= htmlspecialchars($row['nama_menu'], ENT_QUOTES); ?>"
                data-harga="<?= $row['harga']; ?>"
                data-stok="<?= $row['stok']; ?>">
                <i class="bi bi-pencil-square"></i>
              </button>

              <button class="btn btn-danger btn-sm deleteBtn" data-id="<?= $row['id_menu']; ?>">
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

<!-- =============== MODAL TAMBAH =============== -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form action="app/controllers/MenuController.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah Menu</h5>
          <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nama Menu</label>
              <input type="text" name="nama_menu" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Harga</label>
              <input type="number" name="harga" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Stok</label>
              <input type="number" name="stok" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Gambar Menu</label>
              <input type="file" name="gambar" class="form-control" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="add_menu" onclick="sessionStorage.setItem('flash','added')" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- =============== MODAL EDIT =============== -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form action="app/controllers/MenuController.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-warning">
          <h5 class="modal-title text-dark"><i class="bi bi-pencil-square"></i> Edit Menu</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit_id" name="id_menu">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nama Menu</label>
              <input type="text" id="edit_nama" name="nama_menu" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Harga</label>
              <input type="number" id="edit_harga" name="harga" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Stok</label>
              <input type="number" id="edit_stok" name="stok" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Ganti Gambar (opsional)</label>
              <input type="file" name="gambar" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
          <button type="submit" onclick="sessionStorage.setItem('flash','updated')" name="update_menu" class="btn btn-warning">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// === Edit Modal ===
document.querySelectorAll(".editBtn").forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById("edit_id").value    = btn.dataset.id;
    document.getElementById("edit_nama").value  = btn.dataset.nama;
    document.getElementById("edit_harga").value = btn.dataset.harga;
    document.getElementById("edit_stok").value  = btn.dataset.stok;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
  });
});

// === Hapus Menu ===
document.querySelectorAll(".deleteBtn").forEach(btn => {
  btn.addEventListener('click', () => {
    Swal.fire({
      title: "Hapus Menu?",
      text: "Data tidak bisa dikembalikan!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Hapus"
    }).then((res) => {
      if (res.isConfirmed) {
        window.location.href = "app/controllers/MenuController.php?delete=" + btn.dataset.id;
      }
    });
  });
});

// === Flash for add/update ===
document.addEventListener("DOMContentLoaded", () => {
  const flash = sessionStorage.getItem("flash");
  if (flash === "added") Swal.fire({icon:"success",title:"Menu berhasil ditambahkan",timer:1500,showConfirmButton:false});
  if (flash === "updated") Swal.fire({icon:"success",title:"Menu berhasil diubah",timer:1500,showConfirmButton:false});
  sessionStorage.removeItem("flash");

  const params = new URLSearchParams(window.location.search);
  const msg = params.get('msg');

  if(msg==="deleted") Swal.fire({icon:"success",title:"Menu berhasil dihapus",timer:1500,showConfirmButton:false});
  if(msg==="used") Swal.fire({icon:"warning",title:"Menu tidak bisa dihapus",text:"Data menu dipakai transaksi"});
  if(msg==="error") Swal.fire({icon:"error",title:"Terjadi kesalahan"});

  if(msg){
    params.delete('msg');
    history.replaceState(null,'',location.pathname + (params.toString() ? '?'+params : ''));
  }
});
</script>
