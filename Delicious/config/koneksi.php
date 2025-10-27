<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "umkm1";

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
} else {
    //echo "Koneksi berhasil ke database '$db'";
}
?>
