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
          Order Now
        </a>
        <a href="index.php?page=menu" class="order-btn-mobile d-md-none d-flex align-items-center">
          Order Now
        </a>
      </div>

      <!-- Navbar (menu dropdown hanya muncul di mobile) -->
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="#about">Tentang</a></li>
          <li><a href="#menu">Menu</a></li>
          <li><a href="#contact">Lokasi</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
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

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

        
        <div class="carousel-item active">
          <img src="assets/img/hero-carousel/bg-login.jpg" alt="">
          <div class="carousel-container">
            <h2><span>Seblak</span> Say Cafe </h2>
            <p>Enjoy the spicy and savory taste only here</p>
          </div>
        </div><!-- End Carousel Item -->

    </div>
 
    </section><!-- /Hero Section -->



    <!-- About Section -->
    <section id="about" class="about section light-background">  

      <div class="container">

        <!-- Ganti yang ini saja (satu baris) -->
<div class="row gy-4 flex-column-reverse flex-md-row">

 <!-- Gambar jadi di bawah teks (hanya di mobile) -->
  <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="100">
    <img src="assets/img/hero-carousel/bg-hero.png" class="img-fluid" alt="Seblak Say Cafe" style="border: 2px solid #333;">
  </div>
  
  <!-- Teks dulu di mobile, gambar sesudahnya -->
  <div class="col-lg-6 content align-self-center text-center" data-aos="fade-up" data-aos-delay="200">
    <h3 class="fs-2">
     About Us
    </h3>
    <p class="fst-italic fs-5 text-muted mb-4" style="line-height: 1.8;">
          Berdiri sejak tahun 2020, Seblak Say Cafe menjadi salah satu tempat nyeblak yang digemari di Warujayeng. 
          Menawarkan seblak sebagai menu utama dengan cita rasa pedas dan gurih khas yang selalu bikin kangen.
    </p>

    <a href="index.php?page=about" class="btn mt-3 px-4 py-2" >More About
      <style>
        .btn {
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

.btn:hover{transform:scale(1.15);background:#ffcf3a;}

      </style>
      
    </a>
  </div>
</div>
      </div>

      <style>
        @media (max-width: 991.98px) {
  .about img {
    border-radius: 12px;
    margin-top: 2rem;
    width: 100%;
    object-fit: cover;
  }
}
      </style>

    </section><!-- /About Section -->


    <!-- Why Us Section -->
    <section id="why-us" class="why-us section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">

        <div><span>Why choose</span> <span class="description-title">Seblak Say Cafe</span></div>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card-item">
              
              <h4 class="stretched-link">Kenikmatan yang berbeda</h4>
              <p>Dengan bumbu racikan sendiri. Rasa pedas-gurihnya beda, nggak ada duanya! </p>
            </div>
          </div><!-- Card Item -->

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card-item">
              
              <h4 class="stretched-link">Rumah Semua Kalangan</h4>
              <p>Disini kami menyediakan tempat yang pastinya nyaman buat kalian, Jadi jangan lupa mampir yaa!</p>
            </div>
          </div><!-- Card Item -->

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card-item">
              
              <h4 class="stretched-link">Online Deliverry</h4>
              <p>Mager keluar? Ngga masalah.. klik "order online" sekarang maka pesananmu akan sampai kerumah</p>
            </div>
          </div><!-- Card Item -->

        </div>

      </div>

    </section><!-- /Why Us Section -->




    <!-- menu section -->
    <section id="menu" class="about section">
  <div class="container">

  <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <!-- <h2>Menu</h2> -->
        <div><span>Food</span> <span class="description-title">and</span> <span>Drink</span></div>
      </div><!-- End Section Title -->

    <!-- Ganti yang ini saja (satu baris) -->
<div class="row gy-4 flex-column-reverse flex-md-row">

      <!-- 1. Teks dulu (akan muncul atas di mobile) -->
      <div class="col-lg-6 content align-self-center text-center" 
           data-aos="fade-up" data-aos-delay="200">
        <h3 class="fs-2"> 
          Our Favorite <span class="description-title">Menus</span>
        </h3>
        <p class="fst-italic fs-5 text-muted mb-4" style="line-height: 1.8; color:white;">
          Seblak kami hadir dengan berbagai tingkat kepedasan dan topping yang bisa kamu pilih sesuka hati.
          Dari yang creamy, gurih, hingga super pedas—semua tersedia untuk memanjakan lidahmu.
        </p>

        <a href="index.php?page=menu" class="btn mt-3 px-4 py-2">
          View Menu
        <style>
          .btn {
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

          .btn:hover{transform:scale(1.15);background:#ffcf3a;}
       
       </style>
        </a>
      </div>
      
      <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="assets/img/gallery/seblak-3.png" class="glightbox" data-gallery="images-gallery">
                <img src="assets/img/gallery/seblak-3.png" alt="" class="img-fluid" style="border: 5px solid #c68252ff;;">
              </a>
            </div>
          </div><!-- End Gallery Item -->
          <div class="col-lg-3 col-md-4">
            <div class="gallery-item">
              <a href="assets/img/gallery/seblak-4.png" class="glightbox" data-gallery="images-gallery">
                <img src="assets/img/gallery/seblak-4.png" alt="" class="img-fluid" style="border: 5px solid #8B4513;;">
              </a>
            </div>
          </div><!-- End Gallery Item -->

    </div>
    <!-- /.row -->
  </div>
  <style>
    @media (max-width: 991.98px) {
  #menu img {
    margin-top: 2.5rem;
    border-radius: 12px;
  }
}
  </style>
</section>
    

    <!-- Contact Section -->
    <section id="contact" class="about section py-6">
      <!-- Section Title -->
      <div class="container section-title" style="padding: 10px;" data-aos="fade-up">
        <!-- <h2>Contact</h2> -->
        <div><span>Kunjungi </span><span class="description-title">Seblak Say Cafe</div>
      </div><!-- End Section Title -->

      <div class="mb-5">
        <center>
          <iframe style="width: 80%; height: 400px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.318951464519!2d112.01129887481696!3d-7.648810892367321!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e784f0048ac8ce5%3A0x99f1db18d32fb436!2sSeblak%20SAY%20Cafe!5e0!3m2!1sid!2sid!4v1762855183435!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </center>
      </div><!-- End Google Maps -->
      </section><!-- /Contact Section --> 

      <?php
      include 'app/views/sections/footer.php';
      ?>


  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

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

<!-- Floating Admin Button -->
<a href="index.php?page=dashboard" class="admin-float-btn">
  <i class="bi bi-shield-lock"></i>
</a>


</body>
</html> 