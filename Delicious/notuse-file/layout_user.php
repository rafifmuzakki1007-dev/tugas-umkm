<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Seblak Say Cafe</title>

  <!-- SITE ICON -->
  <link href="assets/img/logo-seblak-sementara.png" rel="icon">

  <!-- CSS VENDOR -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- MAIN CSS -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
/* CARD MENU LEBIH KECIL & RAPI */
.menu-card {
    border-radius: 14px;
    padding: 18px;
    transition: 0.25s ease;
}

.menu-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

/* Ukuran gambar menu lebih kecil */
.menu-card img {
    height: 120px;
    width: 120px;
    object-fit: cover;
    border-radius: 10px;
}

/* Title menu */
.menu-card h5 {
    font-size: 1.05rem;
    margin-top: 10px;
}

/* Harga */
.menu-card h6 {
    font-size: 1rem;
    margin-bottom: 8px;
}

/* Button kecil */
.menu-card .btn {
    padding: 6px 10px;
    font-size: 0.9rem;
    border-radius: 8px;
}
</style>

</head>

<body class="index-page">

<?php include 'app/views/sections/header_nav.php'; ?>

<main style="margin-top: 100px;">
  <?php include $content; ?>
</main>

<?php include 'app/views/sections/footer.php'; ?>

<!-- JS -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/js/main.js"></script>

</body>
</html>
