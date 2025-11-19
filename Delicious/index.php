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

// agar tidak warning
$karyawans = $karyawanModel->getAllKaryawan() ?? [];

$page = isset($_GET['page']) ? strtolower($_GET['page']) : 'home';

/* ==================================================
   FIX LOGOUT — versi B (aman & stabil)
   URL: index.php?do_logout=1
================================================== */
if (isset($_GET['do_logout'])) {

    // Hapus semua data sesi
    session_unset();

    // Hancurkan sesi
    session_destroy();

    // Hentikan cookie session agar benar-benar logout
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    header("Location: index.php?page=home");
    exit;
}

/* ==================================================
   ADMIN PAGE LIST
================================================== */
$admin_pages = ['dashboard', 'menu_admin', 'transaksi_admin', 'profile_admin'];

/* ==================================================
   ADMIN ROUTING
================================================== */
if (in_array($page, $admin_pages)) {

    // pastikan sudah login admin
    if (!isset($_SESSION['admin_logged_in'])) {
        header("Location: login.php");
        exit;
    }

    include "app/views/admin/layout_admin.php";
    exit;
}

/* ==================================================
   USER ROUTES
================================================== */

if ($page === 'order_success') {
    include "app/views/order_success.php";
    exit;
}

if ($page === 'menu') {
    $menus = $menuModel->getAllMenu();
    include "app/views/menu.php";
    exit;
}

if ($page === 'pesan_process') {
    include "app/controllers/pesan_process.php";
    exit;
}

if ($page === 'cart') {
    include "app/views/cart.php";
    exit;
}

if ($page === 'riwayat') {
    include "app/views/riwayat.php";
    exit;
}

if ($page === 'login') {
    include file_exists("login.php") ? "login.php" : "Halaman login tidak ditemukan.";
    exit;
}

/* ==================================================
   DEFAULT HOME
================================================== */
$menus = $menuModel->getAllMenu();
include "app/views/home.php";
