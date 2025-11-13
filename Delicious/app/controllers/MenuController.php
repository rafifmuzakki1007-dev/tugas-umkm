<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once _DIR_ . "/../../config/koneksi.php";
require_once _DIR_ . "/../../app/models/MenuModel.php";

$menuModel = new MenuModel($koneksi);

// Utility untuk nama file unik
function unique_name($orig) {
    $ext = pathinfo($orig, PATHINFO_EXTENSION);
    return date('YmdHis') . '_' . bin2hex(random_bytes(3)) . ($ext ? '.'.$ext : '');
}

/* ================== CREATE ================== */
if (isset($_POST['add_menu'])) {
    $nama  = trim($_POST['nama_menu']);
    $harga = (int)$_POST['harga'];
    $stok  = (int)$_POST['stok'];

    $gambar = 'noimage.png';
    if (!empty($_FILES['gambar']['name'])) {
        $new = unique_name($_FILES['gambar']['name']);
        $dest = _DIR_ . "/../../assets/img/menu/" . $new;
        @move_uploaded_file($_FILES['gambar']['tmp_name'], $dest);
        $gambar = $new;
    }

    try {
        // simpan
        $menuModel->addMenu($nama, $harga, $stok, $gambar);

        // ambil id terbaru (format MNxxx) — fallback aman jika query gagal
        $query = "SELECT id_menu FROM menu ORDER BY CAST(SUBSTRING(id_menu,3) AS UNSIGNED) DESC LIMIT 1";
        $res = $koneksi->query($query);
        $new_id = '';
        if ($res && $res->num_rows) {
            $row = $res->fetch_assoc();
            $new_id = $row['id_menu'] ?? '';
        }

        // redirect dengan param new_id hanya jika ada
        $loc = "../../index.php?page=menu_admin&msg=added";
        if (!empty($new_id)) $loc .= "&new_id=" . urlencode($new_id);
        header("Location: " . $loc);
    } catch (Throwable $e) {
        error_log('Error add menu: ' . $e->getMessage());
        header("Location: ../../index.php?page=menu_admin&msg=error");
    }
    exit;
}

/* ================== UPDATE ================== */
if (isset($_POST['update_menu'])) {
    $id    = $_POST['id_menu'];
    $nama  = trim($_POST['nama_menu']);
    $harga = (int)$_POST['harga'];
    $stok  = (int)$_POST['stok'];

    try {
        if (!empty($_FILES['gambar']['name'])) {
            $new = unique_name($_FILES['gambar']['name']);
            $dest = _DIR_ . "/../../assets/img/menu/" . $new;
            @move_uploaded_file($_FILES['gambar']['tmp_name'], $dest);
            $menuModel->updateMenu($id, $nama, $harga, $stok, $new);
        } else {
            $menuModel->updateMenuWithoutImage($id, $nama, $harga, $stok);
        }

        header("Location: ../../index.php?page=menu_admin&msg=updated&updated_id=" . urlencode($id));
    } catch (Throwable $e) {
        error_log('Error update menu: ' . $e->getMessage());
        header("Location: ../../index.php?page=menu_admin&msg=error");
    }
    exit;
}

/* ================== DELETE ================== */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    try {
        if ($menuModel->menuUsed($id)) {
            header("Location: ../../index.php?page=menu_admin&msg=used");
            exit;
        }
        $menuModel->deleteMenu($id);
        header("Location: ../../index.php?page=menu_admin&msg=deleted");
    } catch (Throwable $e) {
        header("Location: ../../index.php?page=menu_admin&msg=error");
    }
    exit;
}

header("Location: ../../index.php?page=menu_admin");
exit;