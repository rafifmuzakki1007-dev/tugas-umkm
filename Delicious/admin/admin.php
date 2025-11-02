<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ambil halaman dari URL
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Path folder admin
$baseDir = __DIR__ . '/';

// Cek routing halaman admin
if ($page === 'menu_admin') {
    $file = $baseDir . 'menu_admin.php';
} elseif ($page === 'dashboard') {
    $file = $baseDir . 'dashboard.php';
} else {
    $file = null;
}

// Jika file ada → tampilkan
if ($file && file_exists($file)) {
    include $file;
} else {
    echo "<h3 style='text-align:center; margin-top:100px;'>Halaman tidak ditemukan!<br><small>($page)</small></h3>";
}
?>
