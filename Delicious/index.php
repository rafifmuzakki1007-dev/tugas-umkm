<?php
// index.php — FINAL CLEAN FIX

ob_start();
if (session_status() === PHP_SESSION_NONE)
    session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/koneksi.php';
require_once 'app/models/MenuModel.php';
require_once 'app/models/KaryawanModel.php';

$menuModel = new MenuModel($koneksi);
$karyawanModel = new KaryawanModel($koneksi);
$karyawans = $karyawanModel->getAllKaryawan() ?? [];

$page = isset($_GET['page']) ? strtolower($_GET['page']) : 'home';

/* =====================================================
   DETEKSI AJAX
===================================================== */
$isAjax = false;
if (
    (isset($_POST['from_ajax']) && $_POST['from_ajax'] == '1')
    || (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
) {
    $isAjax = true;
}

/* =====================================================
   HAPUS BACKDROP
===================================================== */
if (!$isAjax) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.modal-backdrop').forEach(x => x.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
        });
    </script>";
}

/* =====================================================
   pesan_process (AJAX ONLY)
===================================================== */
if ($page === 'pesan_process') {
    if (!empty($_POST['from_ajax']) || $isAjax) {
        include 'app/controllers/pesan_process.php';
        exit;
    }
}

/* =====================================================
   LOGOUT
===================================================== */
if (isset($_GET['do_logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php?page=home");
    exit;
}

/* =====================================================
   ADMIN PAGES
===================================================== */
$admin_pages = ['dashboard','menu_admin','transaksi_admin','profile_admin'];

if (in_array($page, $admin_pages)) {
    if (!isset($_SESSION['admin_logged_in'])) {
        header("Location: login.php");
        exit;
    }
    include "app/views/admin/layout_admin.php";
    exit;
}

/* =====================================================
   PUBLIC ROUTING
===================================================== */
switch ($page) {

    case 'order_success':
        include "app/views/order_success.php";
        include "app/views/checkout.php";
        break;

    case 'menu':
        $menus = $menuModel->getAllMenu();
        include "app/views/menu.php";
        include "app/views/checkout.php";
        break;

    case 'cart':
        include "app/views/cart.php";
        include "app/views/checkout.php";
        break;

    case 'riwayat':
        include "app/views/riwayat.php";
        include "app/views/checkout.php";
        break;

    case 'login':
        if (file_exists("login.php")) include "login.php";
        else echo "Halaman login tidak ditemukan.";
        include "app/views/checkout.php";
        break;

    case 'checkout':
        $menus = $menuModel->getAllMenu();
        include "app/views/menu.php";
        include "app/views/checkout.php";
        break;

    case 'home':
    default:
        $menus = $menuModel->getAllMenu();
        include "app/views/home.php";
        include "app/views/checkout.php";
        break;
}
?>
