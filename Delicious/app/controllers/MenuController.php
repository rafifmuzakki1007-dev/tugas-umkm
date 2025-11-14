<?php
session_start();
require_once __DIR__ . '/../../config/koneksi.php';

/* ================= GENERATE ID MENU MN001, MN002 ================= */
function generateMenuId($db) {
    $q = $db->query("SELECT id_menu FROM menu ORDER BY id_menu DESC LIMIT 1");
    $last = $q->fetch(PDO::FETCH_ASSOC);
    if (!$last) return "MN001";

    $num = intval(substr($last['id_menu'], 2)) + 1;
    return "MN" . str_pad($num, 3, '0', STR_PAD_LEFT);
}

/* ================= TAMBAH MENU ================= */
if (isset($_POST['add_menu'])) {

    $id_menu = generateMenuId($koneksi);
    $nama = $_POST['nama_menu'];
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $filename = time() . "_" . $gambar;
    move_uploaded_file($tmp, __DIR__ . "/../../assets/img/menu/" . $filename);

    $stmt = $koneksi->prepare("
        INSERT INTO menu (id_menu, nama_menu, harga, stok, gambar)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$id_menu, $nama, $harga, $stok, $filename]);

    header("Location: ../../index.php?page=menu_admin&msg=added&alert=add&new_id=$id_menu");
    exit;
}

/* ================= UPDATE MENU ================= */
if (isset($_POST['update_menu'])) {

    $id_menu = $_POST['id_menu'];
    $nama = $_POST['nama_menu'];
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];

    if (!empty($_FILES['gambar']['name'])) {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $filename = time() . "_" . $gambar;
        move_uploaded_file($tmp, __DIR__ . "/../../assets/img/menu/" . $filename);

        $stmt = $koneksi->prepare("
            UPDATE menu SET nama_menu=?, harga=?, stok=?, gambar=? WHERE id_menu=?
        ");
        $stmt->execute([$nama, $harga, $stok, $filename, $id_menu]);

    } else {
        $stmt = $koneksi->prepare("
            UPDATE menu SET nama_menu=?, harga=?, stok=? WHERE id_menu=?
        ");
        $stmt->execute([$nama, $harga, $stok, $id_menu]);
    }

    header("Location: ../../index.php?page=menu_admin&msg=updated&alert=edit&updated_id=$id_menu");
    exit;
}

/* ================= DELETE MENU ================= */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $koneksi->prepare("DELETE FROM menu WHERE id_menu=?");
    $stmt->execute([$id]);

    header("Location: ../../index.php?page=menu_admin&alert=delete&msg=deleted");
    exit;
}

header("Location: ../../index.php?page=menu_admin");
exit;
