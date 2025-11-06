<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/koneksi.php';
require_once 'app/models/MenuModel.php';
require_once 'app/models/KaryawanModel.php';

$menuModel = new MenuModel($koneksi);
$karyawanModel = new KaryawanModel($koneksi);

// pastikan selalu array (anti Warning chefs)
$karyawans = $karyawanModel->getAllKaryawan() ?? [];

$page = isset($_GET['page']) ? strtolower($_GET['page']) : 'home';

/* ---------------- ADMIN PAGE LIST ---------------- */
$admin_pages = ['dashboard', 'menu_admin', 'transaksi_admin', 'profile_admin'];

/* ---------------- LOGOUT ---------------- */
if ($page === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php?page=home");
    exit;
}

/* ---------------- ADMIN ROUTING ---------------- */
if (in_array($page, $admin_pages)){

    if (!isset($_SESSION['admin_logged_in'])) {
        header("Location: login.php");
        exit;
    }

    // Lewat layout admin agar design tetap konsisten
    include "app/views/admin/layout_admin.php";
    exit;
}

/* ---------------- USER ROUTES ---------------- */

//Halaman sukses setelah pesan
if ($page === 'order_success') {
    include "app/views/order_success.php";
    exit;
}

//Halaman menu
if ($page === 'menu') {
    $menus = $menuModel->getAllMenu();
    include "app/views/menu.php";
    exit;
}

//Proses pesanan
if ($page === 'pesan_process') {
    include "app/controllers/pesan_process.php";
    exit;
}

//Cart
if ($page === 'cart') {
    include "app/views/cart.php";
    exit;
}

//Riwayat Pesanan User
if ($page === 'riwayat') {
    include "app/views/riwayat.php";
    exit;
}

//Login
if ($page === 'login') {
    if (file_exists("login.php")) {
        include "login.php";
    } else {
        echo "<h3>Halaman login tidak ditemukan.</h3>";
    }
    exit;
}

/* 
---------------- DEFAULT → HOME ----------------
Jika user klik "Chefs" di navbar → sebenarnya hanya scroll (#chefs)
Jadi tetap load home.php
*/
$menus = $menuModel->getAllMenu();
include "app/views/home.php";
