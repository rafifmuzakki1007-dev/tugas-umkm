<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// koneksi dan model
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../app/models/MenuModel.php';

$menuModel = new MenuModel($koneksi);

// === CRUD ===
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $menuModel->deleteMenu($id);
    header("Location: menu_admin.php?status=delete_success");
    exit;
}

if (isset($_POST['add'])) {
    $filename = !empty($_FILES['gambar']['name']) ? basename($_FILES['gambar']['name']) : '';
    if ($filename) {
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../assets/img/menu/' . $filename);
        $_POST['gambar'] = $filename;
    }
    $menuModel->addMenu($_POST);
    header("Location: menu_admin.php?status=add_success");
    exit;
}

if (isset($_POST['update'])) {
    $filename = !empty($_FILES['gambar']['name']) ? basename($_FILES['gambar']['name']) : $_POST['gambar_lama'];
    if (!empty($_FILES['gambar']['name'])) {
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../assets/img/menu/' . $filename);
    }
    $_POST['gambar'] = $filename;
    $menuModel->updateMenu($_POST);
    header("Location: menu_admin.php?status=update_success");
    exit;
}

$menus = $menuModel->getAllMenu();

// --- Tambahkan data default jika kosong ---
if (empty($menus)) {
    $defaultData = [
        ['M001', 'Seblak Original', 20, 10000, 'seblak-ori.png'],
        ['M002', 'Seblak Kerupuk', 15, 12000, 'seblak-kerupuk.jpg'],
        ['M003', 'Seblak Bakso', 10, 15000, 'seblak-bakso.jpeg'],
        ['M004', 'Es Teh', 25, 5000, 'es-teh.jpg'],
        ['M005', 'Es Jeruk', 25, 6000, 'es-jeruk.jpeg'],
        ['M006', 'Air Mineral', 30, 4000, 'air-minum.jpg']
    ];

    foreach ($defaultData as $m) {
        $menuModel->addMenu([
            'id_menu' => $m[0],
            'nama_menu' => $m[1],
            'stok' => $m[2],
            'harga' => $m[3],
            'gambar' => $m[4]
        ]);
    }
    $menus = $menuModel->getAllMenu();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Kelola Menu</title>
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/main.css" rel="stylesheet">
</head>
<body class="index-page">

<header class="header d-flex align-items-center fixed-top bg-dark text-white p-3">
  <div class="container d-flex justify-content-between">
    <h3 class="m-0">Dashboard Admin - Seblak Say Café</h3>
    <li><a href="../index.php" target="_blank">Kembali ke Website</a></li>
  </div>
</header>

<main class="container" style="margin-top:100px;">
  <?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'add_success'): ?>
      <div class="alert alert-success">✅ Data menu berhasil ditambahkan!</div>
    <?php elseif ($_GET['status'] == 'update_success'): ?>
      <div class="alert alert-warning">✏️ Data menu berhasil diperbarui!</div>
    <?php elseif ($_GET['status'] == 'delete_success'): ?>
      <div class="alert alert-danger">🗑️ Data menu berhasil dihapus!</div>
    <?php endif; ?>
  <?php endif; ?>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kelola Menu</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Menu</button>
  </div>

  <div class="table-responsive shadow-sm">
    <table class="table table-striped table-hover text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nama Menu</th>
          <th>Stok</th>
          <th>Harga</th>
          <th>Gambar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($menus as $menu): ?>
          <tr>
            <td><?= htmlspecialchars($menu['id_menu']) ?></td>
            <td><?= htmlspecialchars($menu['nama_menu']) ?></td>
            <td><?= htmlspecialchars($menu['stok']) ?></td>
            <td>Rp <?= number_format($menu['harga'], 0, ',', '.') ?></td>
            <td><img src="../assets/img/menu/<?= htmlspecialchars($menu['gambar']) ?>" width="80" class="rounded"></td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $menu['id_menu'] ?>">Edit</button>
              <a href="menu_admin.php?delete=<?= $menu['id_menu'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
            </td>
          </tr>

          <!-- Modal Edit -->
          <div class="modal fade" id="modalEdit<?= $menu['id_menu'] ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                  <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="id_menu" value="<?= $menu['id_menu'] ?>">
                    <input type="hidden" name="gambar_lama" value="<?= $menu['gambar'] ?>">

                    <div class="mb-3">
                      <label>Nama Menu</label>
                      <input type="text" name="nama_menu" class="form-control" value="<?= $menu['nama_menu'] ?>" required>
                    </div>
                    <div class="mb-3">
                      <label>Stok</label>
                      <input type="number" name="stok" class="form-control" value="<?= $menu['stok'] ?>" required>
                    </div>
                    <div class="mb-3">
                      <label>Harga</label>
                      <input type="number" name="harga" class="form-control" value="<?= $menu['harga'] ?>" required>
                    </div>
                    <div class="mb-3">
                      <label>Ganti Gambar</label>
                      <input type="file" name="gambar" class="form-control">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-warning">Simpan</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Tambah Menu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>ID Menu</label>
            <input type="text" name="id_menu" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Nama Menu</label>
            <input type="text" name="nama_menu" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Gambar</label>
            <input type="file" name="gambar" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<footer class="footer bg-dark text-white text-center p-3 mt-5">
  © 2025 Seblak Say Café. All Rights Reserved.
</footer>

<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
