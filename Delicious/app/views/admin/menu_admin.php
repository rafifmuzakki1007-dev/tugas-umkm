<?php
// === CONNECT DB & MODEL ===
$basePath = dirname(__DIR__, 3);
require_once $basePath . '/config/koneksi.php';
require_once $basePath . '/app/models/MenuModel.php';

$menuModel = new MenuModel($koneksi);

// === ADD MENU ===
if (isset($_POST['add'])) {
    $filename = "";
    if (!empty($_FILES['gambar']['name'])) {
        $uploadDir = $basePath . '/assets/img/menu/';
        $filename = time() . "-" . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $filename);
    }
    $_POST['gambar'] = $filename;
    $menuModel->addMenu($_POST);
    echo "<script>Swal.fire('Berhasil','Menu berhasil ditambahkan','success')</script>";
}

// === UPDATE MENU ===
if (isset($_POST['update'])) {
    if (!empty($_FILES['gambar']['name'])) {
        $uploadDir = $basePath . '/assets/img/menu/';
        $filename = time() . "-" . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $filename);
        $_POST['gambar'] = $filename;
    }
    $menuModel->updateMenu($_POST);
    echo "<script>Swal.fire('Berhasil','Menu berhasil diperbarui','success')</script>";
}

// === DELETE MENU ===
if (isset($_GET['delete'])) {
    $menuModel->deleteMenu($_GET['delete']);
    echo "<script>Swal.fire('Dihapus','Menu berhasil dihapus','success')</script>";
}

// === GET DATA ===
$menus = $menuModel->getAllMenu();
?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">
            Kelola Menu 🍽️
        </h3>
        <button class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle"></i> Tambah Menu
        </button>
    </div>

    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0 dashboard-table">
                <thead class="table-dark">
                    <tr>
                        <th width="8%">ID</th>
                        <th width="10%">Gambar</th>
                        <th>Nama Menu</th>
                        <th width="10%">Stok</th>
                        <th width="20%">Harga</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($menus as $menu): ?>
                    <tr class="menu-row">
                        <td><b><?= $menu['id_menu']; ?></b></td>

                        <td>
                            <img src="assets/img/menu/<?= $menu['gambar']; ?>" class="rounded-3 shadow-sm" style="width:50px;height:50px;object-fit:cover;">
                        </td>

                        <td class="fw-semibold"><?= $menu['nama_menu']; ?></td>

                        <td>
                            <?php if ($menu['stok'] == 0): ?>
                                <span class="badge bg-danger px-3">Habis</span>
                            <?php elseif ($menu['stok'] <= 3): ?>
                                <span class="badge bg-warning px-3"><?= $menu['stok']; ?></span>
                            <?php else: ?>
                                <span class="badge bg-success px-3"><?= $menu['stok']; ?></span>
                            <?php endif; ?>
                        </td>

                        <td class="fw-semibold text-primary">
                            Rp <?= number_format($menu['harga'],0,',','.'); ?>
                        </td>

                        <td>
                            <button class="btn btn-warning btn-sm me-1 edit-btn" data-bs-toggle="modal" data-bs-target="#edit<?= $menu['id_menu']; ?>">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $menu['id_menu']; ?>">
                                <i class="bi bi-trash"></i>
                            </button>

                        </td>
                    </tr>

                    <!-- MODAL EDIT -->
                    <div class="modal fade" id="edit<?= $menu['id_menu']; ?>">
                        <div class="modal-dialog">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="modal-content shadow-lg border-0">
                                    <div class="modal-header bg-warning">
                                        <h5>Edit Menu</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">

                                        <input type="hidden" name="id_menu" value="<?= $menu['id_menu']; ?>">

                                        <label>Nama Menu</label>
                                        <input type="text" name="nama_menu" class="form-control mb-2" value="<?= $menu['nama_menu']; ?>" required>

                                        <label>Stok</label>
                                        <input type="number" name="stok" class="form-control mb-2" value="<?= $menu['stok']; ?>" required>

                                        <label>Harga</label>
                                        <input type="number" name="harga" class="form-control mb-2" value="<?= $menu['harga']; ?>" required>

                                        <label>Gambar</label>
                                        <input type="file" name="gambar" class="form-control mb-2">
                                        <small class="text-muted">Kosongkan jika tidak diganti</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="update" class="btn btn-warning">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5>Tambah Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <label>ID Menu</label>
                    <input type="text" name="id_menu" class="form-control mb-2" required>

                    <label>Nama Menu</label>
                    <input type="text" name="nama_menu" class="form-control mb-2" required>

                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control mb-2" required>

                    <label>Harga</label>
                    <input type="number" name="harga" class="form-control mb-2" required>

                    <label>Gambar</label>
                    <input type="file" name="gambar" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add" class="btn btn-primary">Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- SWEETALERT DELETE -->
<script>
document.addEventListener("click", function (e) {
    let btn = e.target.closest(".delete-btn");
    if (!btn) return;

    const id = btn.getAttribute("data-id");

    Swal.fire({
        title: "Yakin Hapus?",
        text: "Menu yang dihapus tidak bisa dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "index.php?page=menu_admin&delete=" + id;
        }
    });
});
</script>


<style>
.dashboard-table thead th {
    font-size: 14px;
    text-transform: uppercase;
}
.menu-row { transition: .15s; }
.menu-row:hover { background:#f8f9fa!important; transform:scale(1.005); }
.table td { vertical-align: middle; }
</style>
