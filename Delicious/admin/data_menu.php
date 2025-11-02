<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../app/models/MenuModel.php';

// ambil data menu
$model = new MenuModel($pdo);
$menus = $model->getAllMenu();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Menu - Admin</title>
  <link rel="stylesheet" href="dash-admin/dist/assets/css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

  <div class="container mt-4">
    <h2 class="mb-4 text-center">📋 Data Menu Seblak</h2>

    <div class="mb-3">
      <a href="../index.php" class="btn btn-secondary">← Kembali ke Website</a>
      <a href="#" class="btn btn-primary">+ Tambah Menu</a>
    </div>

    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Nama Menu</th>
          <th>Deskripsi</th>
          <th>Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($menus)) : ?>
          <?php foreach ($menus as $i => $menu) : ?>
            <tr>
              <td><?= $i + 1; ?></td>
              <td><?= htmlspecialchars($menu['nama_menu']); ?></td>
              <td><?= htmlspecialchars($menu['deskripsi']); ?></td>
              <td>Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></td>
              <td>
                <a href="edit_menu.php?id=<?= $menu['id_menu']; ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="hapus_menu.php?id=<?= $menu['id_menu']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus menu ini?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr><td colspan="5" class="text-center">Belum ada data menu.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
