<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../../login.php");
    exit;
}

require_once _DIR_ . '/../../../config/koneksi.php';
require_once _DIR_ . '/../../../app/models/MenuModel.php';

$menuModel = new MenuModel($koneksi);
$menus = $menuModel->getAllMenu();

// Urut MN001 -> MN999
usort($menus, function($a, $b) {
    return intval(substr($a['id_menu'], 2)) <=> intval(substr($b['id_menu'], 2));
});
?>

<style>
/* styling badge */
.badge-new {
  background: #0d6efd; color: #fff; margin-left:8px;
  font-weight:600; padding:4px 8px; border-radius:8px; font-size:12px;
}
.badge-updated {
  background: #ffc107; color:#222; margin-left:8px;
  font-weight:700; padding:4px 8px; border-radius:8px; font-size:12px;
}

/* flash animation */
tr.highlight-flash { animation: flashRow 1.2s ease-in-out 2; }
@keyframes flashRow {
  0%,100% { background-color: transparent; }
  50% { background-color: rgba(13,110,253,0.06); }
}

/* minor: keep modal above sidebar (tidy) */
.modal { z-index: 2000 !important; }
.modal-backdrop { z-index: 1500 !important; }
.modal-dialog-centered { margin-top: 72px !important; }
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
      <table class="table table-hover align-middle" id="menuTable">
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
          <tr data-id="<?= htmlspecialchars($row['id_menu']); ?>">
            <td><span class="badge bg-secondary"><?= $row['id_menu']; ?></span></td>
            <td><img src="assets/img/menu/<?= $row['gambar']; ?>" width="60" height="60" class="rounded" style="object-fit:cover"></td>
            <td class="name-col">
              <?= htmlspecialchars($row['nama_menu']); ?>
              <span class="row-badge-placeholder"></span>
            </td>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form action="app/controllers/MenuController.php" method="POST" enctype="multipart/form-data" id="formTambah">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah Menu</h5>
          <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"></button>
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
          <button type="submit" name="add_menu" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form action="app/controllers/MenuController.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-warning">
          <h5 class="modal-title text-dark"><i class="bi bi-pencil-square"></i> Edit Menu</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
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
          <button type="submit" name="update_menu" class="btn btn-warning">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.querySelector("#menuTable tbody");

  // event delegation untuk tombol Edit/Delete agar tetap bekerja walau row dipindah
  tbody.addEventListener("click", (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;

    // Edit
    if (btn.classList.contains("editBtn")) {
      const id = btn.dataset.id;
      const nama = btn.dataset.nama;
      const harga = btn.dataset.harga;
      const stok = btn.dataset.stok;

      document.getElementById("edit_id").value = id;
      document.getElementById("edit_nama").value = nama;
      document.getElementById("edit_harga").value = harga;
      document.getElementById("edit_stok").value = stok;
      new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    // Delete
    if (btn.classList.contains("deleteBtn")) {
      const id = btn.dataset.id;
      Swal.fire({
        title: "Hapus Menu?",
        text: "Data tidak bisa dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Hapus"
      }).then(res => {
        if (res.isConfirmed) {
          window.location.href = "app/controllers/MenuController.php?delete=" + encodeURIComponent(id);
        }
      });
    }
  });

  // highlight logic (move row to top temporarily + badge)
  (function handleHighlight() {
    const params = new URLSearchParams(window.location.search);
    const newId = params.get("new_id");
    const updatedId = params.get("updated_id");
    const msg = params.get("msg");

    function addBadgeToRow(tr, type) {
      if (!tr) return;
      const placeholder = tr.querySelector(".row-badge-placeholder");
      if (!placeholder) return;
      placeholder.innerHTML = '';
      const s = document.createElement("span");
      s.className = (type === "new") ? "badge-new" : "badge-updated";
      s.textContent = (type === "new") ? "Baru" : "Diedit";
      placeholder.appendChild(s);
      tr.classList.add("highlight-flash");
    }

    function moveRowToTop(tr) {
      if (!tr) return;
      // insert at top of tbody
      tbody.insertBefore(tr, tbody.firstElementChild);
      // scroll into view a little (optional): keep user context
      tr.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // try new_id first
    if (msg === "added") {
      let row = null;
      if (newId) {
        row = document.querySelector(#menuTable tbody tr[data-id="${CSS.escape(newId)}"]);
      }
      // fallback: last row (most likely newly added if server appended at end)
      if (!row) {
        const rows = tbody.querySelectorAll("tr");
        if (rows.length) row = rows[rows.length - 1];
      }
      if (row) {
        addBadgeToRow(row, "new");
        moveRowToTop(row);
      }
      // remove params so refresh won't repeat highlight
      params.delete('new_id'); params.delete('msg');
      history.replaceState(null, '', location.pathname + (params.toString() ? '?'+params.toString() : ''));
    }

    if (msg === "updated") {
      let row = null;
      if (updatedId) {
        row = document.querySelector(#menuTable tbody tr[data-id="${CSS.escape(updatedId)}"]);
      }
      // fallback: try first row
      if (!row) {
        const rows = tbody.querySelectorAll("tr");
        if (rows.length) row = rows[0];
      }
      if (row) {
        addBadgeToRow(row, "updated");
        moveRowToTop(row);
      }
      params.delete('updated_id'); params.delete('msg');
      history.replaceState(null, '', location.pathname + (params.toString() ? '?'+params.toString() : ''));
    }
  })();
});
</script>