<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../app/models/MenuModel.php';

$menuModel = new MenuModel($koneksi);

// ===== HANDLE DELETE =====
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $menuModel->deleteMenu($id);
    header("Location: index.php?page=menu_admin");
    exit;
}

// ===== HANDLE UPDATE =====
if (isset($_POST['update'])) {
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $uploadDir = __DIR__ . '/../../assets/img/menu/';
        $filename = basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $filename);
        $_POST['gambar'] = $filename;
    }
    $menuModel->updateMenu($_POST);
    header("Location: index.php?page=menu_admin");
    exit;
}

// ===== HANDLE ADD =====
if (isset($_POST['add'])) {
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $uploadDir = __DIR__ . '/../../assets/img/menu/';
        $filename = basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $filename);
        $_POST['gambar'] = $filename;
    }
    $menuModel->addMenu($_POST);
    header("Location: index.php?page=menu_admin");
    exit;
}

// Ambil semua menu
$menus = $menuModel->getAllMenu();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Menu - Seblak Say Café</title>

<!-- Template Delicious CSS -->
<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="assets/vendor/aos/aos.css" rel="stylesheet">
<link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
<link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
<link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="index-page">

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">
  <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
    <a href="index.php?page=menu_admin" class="logo d-flex align-items-center">
      <h1 class="sitename">Seblak Say Café</h1>
    </a>
    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="index.php?page=menu_admin" class="active">Kelola Menu</a></li>
        <li><a href="index.php?page=home">Kembali ke Web</a></li>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>
  </div>
</header>

<main id="main" class="main" style="margin-top:100px;">

<!-- ======= Menu Section ======= -->
<section id="menu" class="menu section-bg" style="min-height:80vh;">
  <div class="container" data-aos="fade-up">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Kelola Menu</h2>
      <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Menu</button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle text-center" data-aos="fade-up">
        <thead class="table-dark">
          <tr>
            <th>ID Menu</th>
            <th>Nama Menu</th>
            <th>Stok</th>
            <th>Harga</th>
            <th>Gambar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($menus)): ?>
            <?php foreach ($menus as $menu): ?>
            <tr>
              <td><?= htmlspecialchars($menu['id_menu']); ?></td>
              <td><?= htmlspecialchars($menu['nama_menu']); ?></td>
              <td><?= htmlspecialchars($menu['stok']); ?></td>
              <td>Rp <?= number_format($menu['harga'],0,',','.'); ?></td>
              <td><img src="assets/img/menu/<?= htmlspecialchars($menu['gambar']); ?>" width="80" class="rounded shadow-sm"></td>
              <td>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $menu['id_menu']; ?>">Edit</button>
                <a href="index.php?page=menu_admin&delete=<?= $menu['id_menu']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin hapus menu ini?')">Delete</a>
              </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit<?= $menu['id_menu']; ?>" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header bg-warning text-dark">
                      <h5 class="modal-title">Edit Menu</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="id_menu" value="<?= $menu['id_menu']; ?>">
                      <div class="mb-3">
                        <label>Nama Menu</label>
                        <input type="text" name="nama_menu" class="form-control" value="<?= $menu['nama_menu']; ?>" required>
                      </div>
                      <div class="mb-3">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" value="<?= $menu['stok']; ?>" required>
                      </div>
                      <div class="mb-3">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control" value="<?= $menu['harga']; ?>" required>
                      </div>
                      <div class="mb-3">
                        <label>Gambar</label>
                        <input type="file" name="gambar" class="form-control">
                        <small>Biarkan kosong jika tidak ingin mengganti gambar</small>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" name="update" class="btn btn-warning">Simpan Perubahan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6">Belum ada data menu</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
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
                    <button type="submit" name="add" class="btn btn-primary">Tambah Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

</main>

<!-- ======= Footer ======= -->
<footer id="footer" class="footer dark-background">
  <div class="container text-center">
    <p>© 2025 <strong class="sitename">Seblak Say Café</strong>. All Rights Reserved</p>
  </div>
</footer>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
  // Aktifkan AOS animasi
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true
  });
</script>
</body>
</html>
