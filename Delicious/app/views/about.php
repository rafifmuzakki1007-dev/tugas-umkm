<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Seblak Say cafe</title>
  <meta name="description" content="">
  <meta name="keywords" content="">
  
  <!-- Favicons -->
  <link href="assets/img/logo-atas.jpg" rel="icon">
  <link href="assets/img/logo-atas.jpg" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Satisfy:wght@400&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Delicious
  * Template URL: https://bootstrapmade.com/delicious-free-restaurant-bootstrap-theme/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header fixed-top">
  <div class="topbar d-flex align-items-center" style="min-height: 60px; padding: 5px 0;">
    <div class="container d-flex align-items-center justify-content-between">

      <!-- Logo + Tombol Order Online (selalu kelihatan, bahkan di mobile) -->
      <div class="d-flex align-items-center gap-3">
        <a href="index.php">
          <img src="assets/img/logo1.png" alt="Logo" width="100px" id="logo-img">
        </a>

        <!-- Tombol Order Online selalu muncul (kiri hamburger) -->
        <a href="index.php?page=menu" class="order-btn d-none d-md-inline-block">
          Order Online
        </a>
        <a href="index.php?page=menu" class="order-btn-mobile d-md-none d-flex align-items-center">
          Order Online
        </a>
      </div>

      <!-- Navbar (menu dropdown hanya muncul di mobile) -->
      <nav id="navmenu" class="navmenu">
        <ul>
          <li>
            <a class="bi bi-house ms-4 d-none d-lg-flex align-items-center fs-2" href="index.php"></a>
          <li>
        </ul>
      </nav>

    </div>
  </div>
  <style>
    /* Tombol Order Online versi desktop/tablet */
.order-btn {
  color: var(--contrast-color);
  background: var(--accent-color);
  font-weight: 500;
  font-size: 14px;
  letter-spacing: 1px;
  padding: 8px 28px;
  border-radius: 50px;
  text-decoration: none;
  white-space: nowrap;
  transition: 0.3s;
}

.order-btn:hover{transform:scale(1.15);background:#ffcf3a;}

/* Tombol Order Online versi mobile (lebih kecil biar muat) */
.order-btn-mobile {
  color: var(--contrast-color);
  background: var(--accent-color);
  font-weight: 500;
  font-size: 13px;
  letter-spacing: 1px;
  padding: 7px 20px;
  border-radius: 50px;
  text-decoration: none;
  white-space: nowrap;
  transition: 0.3s;
}
.order-btn-mobile:hover{transform:scale(1.15);background:#ffcf3a;}

/* Biar di HP sangat kecil tetap rapi */
@media (max-width: 480px) {
  .order-btn-mobile {
    font-size: 12px;
    padding: 6px 16px;
  }
  .gap-3 {
    gap: 10px !important;
  }
}
  </style>
</header>

 <body class="index-page">


<!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

        <div class="carousel-item active">
          <img src="assets/img/hero-carousel/bg2.jpeg" alt="">
          <div class="carousel-container">
            <h2><span>About</span></h2>
          </div>
        </div><!-- End Carousel Item -->
    </section><!-- /Hero Section -->

    <!-- About Section -->
<section class="about section light-background py-5 py-md-7">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-xl-7 text-center">

        <!-- Judul -->
        <h3 class="display-5 fw-bold mb-4">
          <span class="description-title" style="color: #FEB64A;">Seblak</span> Say Cafe
        </h3>

        <!-- Paragraf -->
        <p class="lead fs-5 text-muted mx-auto" style="max-width: 700px; line-height: 1.8;">
          Berdiri sejak tahun 2020, Seblak Say Cafe menjadi salah satu tempat nyeblak yang digemari di Warujayeng. 
          Menawarkan seblak sebagai menu utama dengan cita rasa pedas dan gurih yang khas, 
          kami selalu berusaha memberikan pengalaman makan yang hangat, nyaman, dan bikin kangen untuk balik lagi.
        </p>

        <p class="lead fs-5 text-muted mx-auto" style="max-width: 700px; line-height: 1.8;">
            Seblak Say Cafe adalah tempat nikmatnya sensasi seblak kekinian yang diracik dengan cita rasa khas dan pilihan topping yang lengkap. 
            Kami hadir untuk menemani harimu dengan mangkuk seblak hangat, pedas, dan penuh varian yang bisa kamu sesuaikan sendiri.
            Di Seblak Say Cafe, setiap menu dibuat fresh dengan bahan berkualitas, bumbu racikan asli, serta tingkat kepedasan yang bisa kamu pilih sesuai selera. 
            Kami ingin memberikan pengalaman makan yang seru, nyaman, dan ramah bagi siapa pun—baik untuk nongkrong, makan cepat, ataupun pesan antar.
            Nikmati seblak dengan gaya baru, suasana cozy, dan pelayanan yang bikin kamu betah.
        </p>

        <!-- Optional: tambah garis pembatas elegan -->
        <div class="mt-5">
          <hr class="w-25 mx-auto" style="border-top: 3px solid #FEB64A; opacity: 1;">
        </div>

      </div>
    </div>
  </div>
</section>
<!-- /About Section -->

<?php
include 'app/views/layout/footer.php';
?>

    <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
    </body>
    </html>