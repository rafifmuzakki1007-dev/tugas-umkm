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
/* ================= PAGE WRAPPER (sama persis dengan Kelola Transaksi) ================ */
.page-wrapper {
    margin-top: 30px;
    padding-bottom: 40px;
}

/* ================= CARD & TABLE ================= */
.card {
    border-radius: 14px;
}
.card-header {
    padding: 18px 24px;
}
.table td, .table th {
    vertical-align: middle !important;
}
#menuTable img {
    border-radius: 8px;
    object-fit: cover;
}

/* ================= ALERT ================= */
.alert-custom {
    display:flex;
    align-items:center;
    gap:12px;
    border-radius:8px;
    padding:16px 20px;
    font-size:16px;
}

/* ================= BADGES & HIGHLIGHT ================= */
.badge-new { background:#0d6efd; color:#fff; padding:4px 8px; border-radius:6px; }
.badge-updated { background:#ffc107; color:#222; padding:4px 8px; border-radius:6px; }

.highlight-flash { animation: flashRow 1.2s ease-in-out 2; }
@keyframes flashRow {
    0% {background:transparent} 
    50% {background:rgba(13,110,253,.18)} 
    100% {background:transparent}
}

/* ================= MODAL RAPI PROFESSIONAL ================= */
.modal .modal-dialog {
    max-width: 760px;
}
.modal .modal-content {
    border-radius: 12px;
}
.modal .modal-header {
    padding: 18px 28px;
    border-bottom: none;
}
.modal .modal-title {
    font-weight: 500;
    font-size: 14px;
}
.modal .modal-body {
    padding: 22px 28px;
}
.modal .modal-footer {
    padding: 16px 28px;
    border-top: none;
}
.modal .form-label {
    font-weight: 500;
    margin-bottom: 6px;
}

.animated-alert {
    animation: scaleIn .35s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0.7);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}


/* ================= AUTO HIDE ALERT ================= */
.fade-out { transition: opacity .6s ease; opacity:0; }

/* Buttons clickable above highlight */
.editBtn, .deleteBtn { position: relative; z-index:20; cursor:pointer; }
</style>


<div class="page-wrapper">
<div class="container">

<!-- ===================== ALERT ===================== -->
<?php if (isset($_GET['alert'])): ?>
<script>
Swal.fire({
    icon: "success",
    title: "<?= $_GET['alert'] === 'add' ? 'Menu Ditambahkan!' : 'Menu Diperbarui!' ?>",
    text: "<?= $_GET['alert'] === 'add' ? 'Menu baru berhasil disimpan.' : 'Perubahan berhasil disimpan.' ?>",
    showConfirmButton: false,
    timer: 1500,
    timerProgressBar: true,
    position: "center",
    background: "#ffffff",
    iconColor: "#198754",
    customClass: { popup: "animated-alert" }
});

setTimeout(() => {
    history.replaceState(null, "", location.pathname + "?page=menu_admin");
}, 1600);
</script>
<?php endif; ?>


<!-- ===================== TITLE (dengan ICON seperti Kelola Transaksi) ====================== -->
<h2 class="fw-bold mb-4">
    <i class="bi bi-grid me-2"></i> Kelola Menu
</h2>


<!-- ===================== CARD MENU ====================== -->
<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="m-0">Daftar Menu</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle me-1"></i> Tambah Menu
        </button>
    </div>

    <div class="card-body">
        <table class="table table-hover align-middle" id="menuTable">
            <thead class="table-light">
                <tr>
                    <th style="width:8%">ID</th>
                    <th style="width:12%">Gambar</th>
                    <th>Nama Menu</th>
                    <th style="width:14%">Harga</th>
                    <th style="width:8%">Stok</th>
                    <th style="width:14%">Aksi</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach($menus as $row): ?>
            <tr data-id="<?= $row['id_menu'] ?>">
                <td><span class="badge bg-secondary"><?= $row['id_menu'] ?></span></td>

                <td><img src="assets/img/menu/<?= htmlspecialchars($row['gambar']); ?>" width="64" height="64"></td>

                <td>
                    <?= htmlspecialchars($row['nama_menu']); ?>
                    <span class="row-badge-placeholder"></span>
                </td>

                <td>Rp <?= number_format($row['harga'],0,',','.') ?></td>

                <td><?= $row['stok'] ?></td>

                <td>
                    <button class="btn btn-warning btn-sm editBtn"
                        data-id="<?= $row['id_menu'] ?>"
                        data-nama="<?= htmlspecialchars($row['nama_menu'], ENT_QUOTES) ?>"
                        data-harga="<?= $row['harga'] ?>"
                        data-stok="<?= $row['stok'] ?>">
                        <i class="bi bi-pencil-square"></i>
                    </button>

                    <button class="btn btn-danger btn-sm deleteBtn" data-id="<?= $row['id_menu'] ?>">
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


<!-- ===================== MODAL TAMBAH ===================== -->
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
                <input type="file" class="form-control" name="gambar" required>
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


<!-- ===================== MODAL EDIT ===================== -->
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
                <input type="file" name="gambar" class="form-control">
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


<!-- ===================== JAVASCRIPT ===================== -->
<script>
document.addEventListener("DOMContentLoaded", () => {

    const tbody = document.querySelector("#menuTable tbody");

    /* ===================== EDIT ===================== */
    tbody.addEventListener("click", (e) => {
        const btn = e.target.closest(".editBtn");
        if (!btn) return;

        document.getElementById("edit_id").value = btn.dataset.id;
        document.getElementById("edit_nama").value = btn.dataset.nama;
        document.getElementById("edit_harga").value = btn.dataset.harga;
        document.getElementById("edit_stok").value = btn.dataset.stok;

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    });

    /* ===================== DELETE (SweetAlert Confirm) ===================== */
    tbody.addEventListener("click", (e) => {
        const btn = e.target.closest(".deleteBtn");
        if (!btn) return;

        Swal.fire({
            title: "Hapus Menu?",
            text: "Data tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Hapus"
        }).then(res => {
            if (res.isConfirmed) {
                window.location.href = 
                    "app/controllers/MenuController.php?delete=" + encodeURIComponent(btn.dataset.id);
            }
        });
    });

    /* ===================== HIGHLIGHT BARU & EDIT ===================== */
    (function highlight() {
        const params = new URLSearchParams(window.location.search);
        const msg = params.get("msg");
        const newId = params.get("new_id");
        const updId = params.get("updated_id");

        function addBadge(tr, type) {
            const ph = tr.querySelector(".row-badge-placeholder");
            ph.innerHTML = `<span class="${type === 'new' ? 'badge-new' : 'badge-updated'}">
                                ${type === 'new' ? 'Baru' : 'Diedit'}
                            </span>`;
            tr.classList.add("highlight-flash");
        }

        function moveTop(tr) {
            tbody.insertBefore(tr, tbody.firstElementChild);
            tr.scrollIntoView({ behavior:'smooth', block:'center' });
        }

        if (msg === "added" && newId) {
            let tr = document.querySelector(`tr[data-id="${newId}"]`);
            if (tr) { addBadge(tr, "new"); moveTop(tr); }
        }

        if (msg === "updated" && updId) {
            let tr = document.querySelector(`tr[data-id="${updId}"]`);
            if (tr) { addBadge(tr, "updated"); moveTop(tr); }
        }

        // Clean URL
        if (msg || params.get("alert")) {
            history.replaceState(null, "", location.pathname + "?page=menu_admin");
        }
    })();

    /* ===================== ALERT AUTO HIDE ===================== */
    const alertBox = document.getElementById("pageAlert");
    if (alertBox) {
        const closeBtn = document.getElementById("closeAlertBtn");
        if (closeBtn) closeBtn.addEventListener("click", () => alertBox.remove());

        setTimeout(() => {
            alertBox.classList.add("fade-out");
            setTimeout(() => alertBox.remove(), 700);
        }, 3500);
    }

});

// ================ SUCCESS POPUP (AUTO CLOSE) =================
(function showSuccessPopup() {
    const params = new URLSearchParams(window.location.search);
    const alert = params.get("alert");

    if (!alert) return;

    let title = "";
    let text = "";

    if (alert === "add") {
        title = "Menu Ditambahkan!";
        text  = "Menu baru berhasil disimpan.";
    }
    if (alert === "edit") {
        title = "Menu Diperbarui!";
        text  = "Perubahan berhasil disimpan.";
    }

    if (alert === "add" || alert === "edit") {
        Swal.fire({
            icon: "success",
            title: title,
            text: text,
            showConfirmButton: false,
            timer: 1600,
            timerProgressBar: true,
            position: "center",
            background: "#ffffff",
            iconColor: "#198754",
            customClass: {
                popup: "animated-alert"
            }
        });

        // Hapus parameter alert dari URL setelah popup hilang
        setTimeout(() => {
            history.replaceState(null, "", location.pathname + "?page=menu_admin");
        }, 1700);
    }
})();


</script>
