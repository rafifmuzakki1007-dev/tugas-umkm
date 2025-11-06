<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../../config/koneksi.php";
require_once __DIR__ . "/../../app/models/MenuModel.php";

$menuModel = new MenuModel($koneksi);

// Utility: nama file unik
function unique_name($orig) {
    $ext = pathinfo($orig, PATHINFO_EXTENSION);
    return date('YmdHis') . '_' . bin2hex(random_bytes(3)) . ($ext ? '.'.$ext : '');
}

// ===== CREATE =====
if (isset($_POST['add_menu'])) {
    $nama  = $_POST['nama_menu'];
    $harga = (int)$_POST['harga'];
    $stok  = (int)$_POST['stok'];

    $gambar = 'noimage.png';
    if (!empty($_FILES['gambar']['name'])) {
        $new = unique_name($_FILES['gambar']['name']);
        $destFs = __DIR__ . "/../../assets/img/menu/" . $new;
        @move_uploaded_file($_FILES['gambar']['tmp_name'], $destFs);
        $gambar = $new;
    }

    try {
        $menuModel->addMenu($nama, $harga, $stok, $gambar);
        header("Location: ../../index.php?page=menu_admin&msg=added");
    } catch (Throwable $e) {
        header("Location: ../../index.php?page=menu_admin&msg=error");
    }
    exit;
}

// ===== UPDATE =====
if (isset($_POST['update_menu'])) {
    $id    = $_POST['id_menu'];
    $nama  = $_POST['nama_menu'];
    $harga = (int)$_POST['harga'];
    $stok  = (int)$_POST['stok'];

    if (!empty($_FILES['gambar']['name'])) {
        $new = unique_name($_FILES['gambar']['name']);
        $destFs = __DIR__ . "/../../assets/img/menu/" . $new;
        @move_uploaded_file($_FILES['gambar']['tmp_name'], $destFs);
        $menuModel->updateMenu($id, $nama, $harga, $stok, $new);
    } else {
        $menuModel->updateMenuWithoutImage($id, $nama, $harga, $stok);
    }

    header("Location: ../../index.php?page=menu_admin&msg=updated");
    exit;
}

// ===== DELETE =====
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    if ($menuModel->menuUsed($id)) {
        // sudah dipakai transaksi → blok
        header("Location: ../../index.php?page=menu_admin&msg=used");
        exit;
    }

    $menuModel->deleteMenu($id);
    header("Location: ../../index.php?page=menu_admin&msg=deleted");
    exit;
}

// fallback
header("Location: ../../index.php?page=menu_admin");
exit;
