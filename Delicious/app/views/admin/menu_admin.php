<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';

$stmt = $koneksi->prepare("SELECT * FROM menu ORDER BY id_menu ASC");
$stmt->execute();
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>

/* ============================================================
   BASE PAGE LAYOUT
============================================================ */
.page-wrapper { 
    margin-top: 30px; 
    padding-bottom: 40px; 
}
.card { border-radius: 14px; }
.card-header { padding: 18px 24px; }


/* ============================================================
   CRUD TABLE — PREMIUM CLEAN DASHBOARD STYLE
============================================================ */

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

#menuTable tbody tr {
    background: #ffffff;
    box-shadow: 0 3px 12px rgba(0,0,0,0.07);
    border-radius: 12px;
}

#menuTable tbody tr td {
    padding: 16px 18px;
    font-size: 14px;
    vertical-align: middle !important;
    border: none !important;
}

/* Gambar menu seragam */
#menuTable img {
    width: 70px;
    height: 55px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e6e6e6;
}

/* ID badge */
#menuTable .badge {
    padding: 6px 10px;
    font-size: 12px;
}

/* Nama menu lebih tegas */
#menuTable td:nth-child(3) {
    font-weight: 600;
    color: #2a2f3c;
}

/* Harga */
#menuTable td:nth-child(4) {
    font-weight: 700;
    color: black;
}

/* Stok */
#menuTable td:nth-child(5) {
    font-weight: 600;
    color: #1f2937;
}

/* Aksi Buttons */
#menuTable .btn {
    padding: 5px 10px;
  /*  border-radius: ; */
    font-size: 13px;
}

/* Hover row efek */
#menuTable tbody tr:hover {
    transform: translateY(-2px);
    transition: 0.15s ease;
    box-shadow: 0 5px 16px rgba(0,0,0,0.12);
}

/* ============================================================
   BADGES: Baru & Diedit
============================================================ */
.badge-new {
    background: #0d6efd;
    color: #fff;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
}

.badge-updated {
    background: #ff9f43;
    color: #fff !important;   /* ← FIX WARNA PUTIH */
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
}

.highlight-flash {
    animation: flashRow 1.2s ease-in-out 2;
}

@keyframes flashRow {
    0% { background: transparent; }
    50% { background: rgba(13,110,253,0.12); }
    100% { background: transparent; }
}


/* ============================================================
   MODAL STYLE (dirapikan agar senada tabel)
============================================================ */

.modal .modal-dialog { max-width: 760px; }
.modal .modal-content { border-radius: 14px; }
.modal .modal-header { 
    padding: 18px 28px; 
    border-bottom: none; 
}
.modal .modal-title { 
    font-weight: 700; 
    font-size: 16px; 
}
.modal .modal-body { padding: 22px 28px; }
.modal .modal-footer { 
    border-top: none; 
    padding: 16px 28px; 
}

/* bulk preview img */
#bulkTable .preview-img {
    width:56px;
    height:56px;
    border-radius:8px;
    object-fit:cover;
    display:none;
}

/* left icon tweak */
.modal-header .bi-collection { 
    transform: translateY(-1px); 
}


/* Popup SweetAlert smooth */
.swal2-popup { animation: swal2-show .24s ease-out; }
@keyframes swal2-show {
    from { transform: scale(0.96); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

</style>

<div class="page-wrapper">
  <div class="container">

    <h2 class="fw-bold mb-4">
      <i class="bi bi-grid me-2"></i> Kelola Menu
    </h2>

    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="m-0">Daftar Menu</h5>
        <div>
          <!-- Tambah Banyak jadi biru (btn-primary). Tambah Menu dihapus dari header -->
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBulk">
            <i class="bi bi-collection me-1"></i> Tambah Menu
          </button>
        </div>
      </div>

      <div class="card-body">
        <table class="table table-hover align-middle" id="menuTable">
          <thead class="table-light">
            <tr>
              <th style="width:8%">ID Menu</th>
              <th style="width:12%">Gambar</th>
              <th>Nama Menu</th>
              <th style="width:14%">Harga</th>
              <th style="width:8%">Stok</th>
              <th style="width:14%">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($menus as $row): ?>
            <tr data-id="<?= htmlspecialchars($row['id_menu']); ?>">
              <td><span class="badge bg-secondary"><?= htmlspecialchars($row['id_menu']); ?></span></td>
              <td><img src="assets/img/menu/<?= htmlspecialchars($row['gambar']); ?>" width="64" height="64"></td>
              <td><?= htmlspecialchars($row['nama_menu']); ?> <span class="row-badge-placeholder"></span></td>
              <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
              <td><?= htmlspecialchars($row['stok']); ?></td>
              <td>
                <button type="button" class="btn btn-warning btn-sm editBtn"
                  data-id="<?= htmlspecialchars($row['id_menu']); ?>"
                  data-nama="<?= htmlspecialchars($row['nama_menu'], ENT_QUOTES); ?>"
                  data-harga="<?= htmlspecialchars($row['harga']); ?>"
                  data-stok="<?= htmlspecialchars($row['stok']); ?>">
                  <i class="bi bi-pencil-square"></i>
                </button>

                <button type="button" class="btn btn-danger btn-sm deleteBtn" data-id="<?= htmlspecialchars($row['id_menu']); ?>">
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
</div>

<!-- MODAL TAMBAH (tetap ada, tidak dihapus; bisa digunakan jika butuh) -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form action="app/controllers/MenuController.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Tambah Menu</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label">Nama Menu</label>
              <input type="text" class="form-control" name="nama_menu" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Harga</label>
              <input type="number" class="form-control" name="harga" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Stok</label>
              <input type="number" class="form-control" name="stok" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Gambar</label>
              <input type="file" class="form-control" name="gambar" required accept="image/*">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="add_menu" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form action="app/controllers/MenuController.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-warning">
          <h5 class="modal-title text-dark"><i class="bi bi-pencil-square me-2"></i> Edit Menu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="edit_id" name="id_menu">
          <div class="row g-4">
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
              <label class="form-label">Ganti Gambar (Opsional)</label>
              <input type="file" name="gambar" class="form-control" accept="image/*">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="update_menu" class="btn btn-warning">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL BULK -->
<div class="modal fade" id="modalBulk" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <form action="app/controllers/MenuController.php" method="POST" enctype="multipart/form-data">

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-collection me-2"></i> Tambah Banyak Menu</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-bordered align-middle" id="bulkTable">
              <thead class="table-light">
                <tr>
                  <th style="width:30%">Nama Menu</th>
                  <th style="width:15%">Harga</th>
                  <th style="width:15%">Stok</th>
                  <th style="width:25%">Gambar</th>
                  <th style="width:15%">Aksi</th>
                </tr>
              </thead>

              <tbody id="bulkBody">
                <tr>
                  <td><input type="text" name="nama_menu[]" class="form-control" required></td>
                  <td><input type="number" name="harga[]" class="form-control" required min="0"></td>
                  <td><input type="number" name="stok[]" class="form-control" required min="0"></td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <input type="file" name="gambar[]" class="form-control imgInput" accept="image/*" required>
                      <img src="" class="preview-img">
                    </div>
                  </td>
                  <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                      <button type="button" class="btn btn-info btn-sm cloneRow"><i class="bi bi-files"></i></button>
                      <button type="button" class="btn btn-danger btn-sm removeRow"><i class="bi bi-x-lg"></i></button>
                    </div>
                  </td>
                </tr>
              </tbody>

            </table>
          </div>

          <button type="button" id="addRow" class="btn btn-outline-primary mt-1">
            <i class="bi bi-plus-lg"></i> Tambah Baris
          </button>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="add_menu_bulk" class="btn btn-primary">Simpan Semua</button>
        </div>

      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

  // Hilangkan aria-hidden yg membuat tombol tidak bisa diklik
  document.querySelectorAll("[aria-hidden='true']").forEach(el => {
      el.removeAttribute("aria-hidden");
  });

    /* ============================================================
       FIX: Tambah fungsi cleanup agar tidak error
    ============================================================ */
    function cleanupBootstrapModalArtifacts() {
        document.body.classList.remove("modal-open");
        document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
    }

    function cleanupSweetAlertArtifacts() {
        document.querySelectorAll('.swal2-container').forEach(el => el.remove());
        document.querySelectorAll('.swal2-backdrop').forEach(el => el.remove());
        document.body.classList.remove("swal2-shown", "swal2-height-auto");
        document.body.style.overflow = "auto";
    }

    /* SAFE INIT */
    cleanupBootstrapModalArtifacts();
    cleanupSweetAlertArtifacts();

    const tbody = document.querySelector("#menuTable tbody");

    /* ============================================================
       EDIT BUTTON — ADDED HANDLER (fix tombol edit tidak bisa diklik)
       - Bersihkan overlay / aria-hidden sebelum membuka modal
       - Isi form lalu show modal menggunakan Bootstrap's API
    ============================================================ */
    tbody.addEventListener("click", (e) => {
        const edit = e.target.closest(".editBtn");
        if (edit) {
            // cleanup any leftover overlays or aria-hidden attributes
            cleanupSweetAlertArtifacts();
            cleanupBootstrapModalArtifacts();
            document.querySelectorAll("[aria-hidden]").forEach(el => el.removeAttribute("aria-hidden"));
            document.querySelectorAll(".page-fade").forEach(el => el.classList.remove("page-fade"));
            document.body.style.pointerEvents = "auto";

            // fill form
            document.getElementById("edit_id").value = edit.dataset.id;
            document.getElementById("edit_nama").value = edit.dataset.nama;
            document.getElementById("edit_harga").value = edit.dataset.harga;
            document.getElementById("edit_stok").value = edit.dataset.stok;

            // show modal (Bootstrap)
            const modalEl = document.getElementById('modalEdit');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            return; // stop here so delete handler won't run for same click
        }

        const btn = e.target.closest(".deleteBtn");
        if (!btn) return;

        Swal.fire({
            title: "Hapus Menu?",
            text: "Data tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            allowOutsideClick: false
        }).then((res) => {
            cleanupBootstrapModalArtifacts();
            cleanupSweetAlertArtifacts();

            if (res.isConfirmed) {
                window.location.href =
                  "app/controllers/MenuController.php?delete=" +
                  encodeURIComponent(btn.dataset.id);
            }
        });
    });


    /* ============================================================
       MOVE NEW / UPDATED ROW TO TOP
    ============================================================ */
    const params = new URLSearchParams(window.location.search);
    const msg = params.get("msg");
    const newId = params.get("new_id");
    const updId = params.get("updated_id");
    const newIdsParam = params.get("new_ids");

    function moveRowToTop(id, isUpdated = false) {
        const tr = document.querySelector(`tr[data-id="${id}"]`);
        if (!tr) return;

        const badge = tr.querySelector(".row-badge-placeholder");
        badge.innerHTML = isUpdated
            ? '<span class="badge-updated">Diedit</span>'
            : '<span class="badge-new">Baru</span>';

        tr.classList.add("highlight-flash");
        tbody.prepend(tr);
    }

    if (msg === "added" && newId) moveRowToTop(newId);
    if (msg === "added" && newIdsParam) {
        newIdsParam.split(",").forEach(id => moveRowToTop(id));
    }
    if (msg === "updated" && updId) moveRowToTop(updId, true);


    /* ============================================================
       AUTO CLOSE SUCCESS ALERT
    ============================================================ */
    const alertType = params.get("alert");

    if (alertType) {

        let title = "", text = "";

        if (alertType === "add") {
            title = "Menu Ditambahkan!";
            text = "Menu baru berhasil ditambahkan.";
        } else if (alertType === "edit") {
            title = "Menu Diperbarui!";
            text = "Perubahan berhasil disimpan.";
        } else if (alertType === "delete") {
            title = "Menu Dihapus!";
            text = "Penghapusan berhasil.";
        }

        Swal.fire({
            icon: "success",
            title,
            text,
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            cleanupSweetAlertArtifacts();
            history.replaceState(null, "", location.pathname + "?page=menu_admin");
        });
    }


    /* ============================================================
       BULK ADD
    ============================================================ */
    const bulkBody = document.getElementById("bulkBody");
    const addRowBtn = document.getElementById("addRow");

    addRowBtn.addEventListener("click", () => {
        const row = bulkBody.firstElementChild.cloneNode(true);
        row.querySelectorAll("input").forEach(inp => inp.value = "");
        row.querySelector(".preview-img").style.display = "none";
        bulkBody.appendChild(row);
    });

    bulkBody.addEventListener("click", (e) => {
        if (e.target.closest(".removeRow")) {
            if (bulkBody.children.length > 1) e.target.closest("tr").remove();
        }

        if (e.target.closest(".cloneRow")) {
            const tr = e.target.closest("tr");
            const clone = tr.cloneNode(true);
            clone.querySelectorAll("input").forEach(inp => inp.value = "");
            clone.querySelector(".preview-img").style.display = "none";
            bulkBody.appendChild(clone);
        }
    });

    bulkBody.addEventListener("change", (e) => {
        if (!e.target.classList.contains("imgInput")) return;
        const img = e.target.closest("td").querySelector(".preview-img");
        const file = e.target.files[0];
        img.src = file ? URL.createObjectURL(file) : "";
        img.style.display = file ? "block" : "none";
    });

});
</script>
