<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'config/koneksi.php';
require_once 'app/models/MenuModel.php';

$menuModel = new MenuModel($koneksi);

// Ambil page dari URL, default 'home'
$page = $_GET['page'] ?? 'home';

// Daftar halaman admin yang butuh login
$adminPages = ['dashboard', 'menu_admin'];

// Jika halaman termasuk admin, cek login
if (in_array($page, $adminPages)) {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php"); 
        exit;
    }
}

/*************** ADMIN ROUTES ***************/
if ($page === 'dashboard') {
    $admin_content = 'app/views/admin/dashboard.php';
    include 'app/views/admin/layout_admin.php';
    exit;
}

if ($page === 'menu_admin') {
    $admin_content = 'app/views/admin/menu_admin.php';
    include 'app/views/admin/layout_admin.php';
    exit;
}

// LOGOUT admin
if ($page === 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}

/*************** USER ROUTES ***************/
if ($page === 'menu') {
    $menus = $menuModel->getAllMenu();
    include 'app/views/menu.php';
    exit;
}

/*************** FORM SUBMIT ORDER ***************/
if ($page === 'pesan_process') {
    include 'app/controllers/pesan_process.php';
    exit;
}

/*************** DEFAULT HOME (FRONTEND) ***************/
$menus = $menuModel->getAllMenu();
include 'app/views/home.php';
