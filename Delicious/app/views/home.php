<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Seblak Say cafe</title>
  <meta name="description" content="">
  <meta name="keywords" content="">
  
  <!-- Favicons -->
  <link href="assets/img/logo1.png" rel="icon">
  <link href="assets/img/logo1.png" rel="apple-touch-icon">

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
  <link rel="stylesheet" href="assets/css/custom.css">

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

    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-end justify-content-md-between">
        <div class="contact-info d-flex align-items-center">
          <img src="assets/img/logo1.png" alt="" width="100px" height="100%" id="logo-img">
          <i class="bi bi-clock ms-4 d-none d-lg-flex align-items-center"><span>everyday: 10:00 AM - 22.00 PM</span></i>
    </div>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="#hero" class="active">Home</a></li>
            <li><a href="#about">Tentang</a></li>
            <li><a href="#menu">Menu</a></li>
            <li><a href="#chefs">Member</a></li>
            <li><a href="#testimonials">Testimoni</a></li>
            <li><a href="#contact">Lokasi</a></li>
            
        <a href="index.php?page=menu" class="cta-btn" id="order-btn">Order Online</a>
      
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
      

  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

        <div class="carousel-item active">
          <img src="assets/img/hero-carousel/bg-login.jpg" alt="">
          <div class="carousel-container">
            <h2><span>Seblak</span> Say Cafe</h2>
            <p>Tempat sederhana yang berbicara lewat rasa dan suasana — tanpa banyak kata.</p>
            
          </div>
        </div><!-- End Carousel Item -->  
      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section light-background">

      <div class="container">

        <div class="row gy-4">
          <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/about.jpg" class="img-fluid" alt="">
            <!-- <a href="https://www.tiktok.com/@seblakprasmanansaycafe/video/7404134925936381190?is_from_webapp=1&sender_device=pc" class="glightbox pulsating-play-btn"></a> -->
          </div>
          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="200">
            <h3>About Us <span class="description-title">Seblak</span> Say Cafe.</h3>
            <p class="fst-italic">
              Berdiri pada tahun 2022, Seblak Say Cafe menjadi salah satu tempat nyeblak paling digemari di warujayeng
              . Alasan mengapa seblak ini digemari :
            </p>
            <ul>
              <li><i class="bi bi-check2-all"></i> <span>Suasana yang Nyaman,Tenang dan Santai</span></li>
              <li><i class="bi bi-check2-all"></i> <span>Rasa Seblak Gurih</span></li>
              <li><i class="bi bi-check2-all"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate trideta storacalaperda mastiro dolore eu fugiat nulla pariatur.</span></li>
            </ul>
            <p>
              Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
              velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident
            </p>
          </div>
        </div>

      </div>

    </section><!-- /About Section -->

    <section id="menu" class="menu section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <!-- <h2>Menu</h2> -->
        <div><span>Varian</span> <span class="description-title">Menu</span> <span>Kami</span></div>
      </div><!-- End Section Title -->

      <div class="menu-card-wrapper">
<?php foreach ($menus as $menu):
  $stok = $menu['stok'];?>
<div class="menu-card <?= $sold?'sold-out':'' ?>">
  <div style="position:relative;">
    <img src="assets/img/menu/<?= $menu['gambar']; ?>">
    
  </div>
  <h5><?= $menu['nama_menu']; ?></h5>
</div>
<?php endforeach; ?>
</div>
      
    </section>
    

<!-- Chefs Section -->
<?php 
// Fallback jika DB kosong
$karyawans = $karyawans ?? [];

if (count($karyawans) == 0) {
  $karyawans = [
    ["foto" => "foto-1.png", "nama" => "Walter White", "jabatan" => ""],
    ["foto" => "foto-2.png", "nama" => "Sarah Jhonson", "jabatan" => ""],
    ["foto" => "foto-3.png", "nama" => "William Anderson", "jabatan" => ""],
    ["foto" => "foto-4.png", "nama" => "William Anderson", "jabatan" => ""],
    ["foto" => "foto-5.png", "nama" => "William Anderson", "jabatan" => ""],
    ["foto" => "foto-6.png", "nama" => "William Anderson", "jabatan" => ""],
    ["foto" => "foto-7.png", "nama" => "William Anderson", "jabatan" => ""],
  ];
}
?>

<section id="chefs" class="chefs section-bg">
  <div class="container">

    <!-- Title sesuai template -->
    <div class="section-header text-center mb-5" data-aos="fade-up">
      <span class="subtitle-chef">CHEFS</span>
      <h2 class="chef-title">Our <span>Professional</span> Chefs</h2>
    </div>

    <div class="row gy-4">

      <?php 
      $i = 1;
      foreach ($karyawans as $chef): 
        $foto = $chef['foto'] ?? "chefs-$i.jpg"; 
        $nama = $chef['nama'] ?? ($chef['nama_karyawan'] ?? 'Nama Tidak Ada');
        $jabatan = $chef['jabatan'] ?? ($chef['posisi'] ?? 'Chef');
      ?>

      <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="<?= $i*100 ?>">
        <div class="chef-member">

          <div class="member-img">
            <img src="assets/img/chefs/<?= $foto ?>" class="img-fluid" style="height:350px; width:100%; object-fit:cover;" alt="">
            <div class="social">
              <a href="#"><i class="bi bi-twitter"></i></a>
              <a href="#"><i class="bi bi-facebook"></i></a>
              <a href="#"><i class="bi bi-instagram"></i></a>
              <a href="#"><i class="bi bi-linkedin"></i></a>
            </div>
          </div>

          <div class="member-info">
            <h4><?= $nama ?></h4>
            <span><?= ucfirst($jabatan) ?></span>
          </div>

        </div>
      </div>

      <?php 
      $i++;
      endforeach; 
      ?>

    </div>

  </div>
</section>


    <!-- Testimonials Section -->
     
    <section id="testimonials" class="testimonials section dark-background">

      <img src="assets/img/testimonials-bg.jpg" class="testimonials-bg" alt="">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              }
            }
          </script>
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-1.jpg" class="testimonial-img" alt="">
                <h3>Saul Goodman</h3>
                <h4>Ceo &amp; Founder</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Proin iaculis purus consequat sem cure digni ssim donec porttitora entum suscipit rhoncus. Accusantium quam, ultricies eget id, aliquam eget nibh et. Maecen aliquam, risus at semper.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-2.jpg" class="testimonial-img" alt="">
                <h3>Sara Wilsson</h3>
                <h4>Designer</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Export tempor illum tamen malis malis eram quae irure esse labore quem cillum quid cillum eram malis quorum velit fore eram velit sunt aliqua noster fugiat irure amet legam anim culpa.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-3.jpg" class="testimonial-img" alt="">
                <h3>Jena Karlis</h3>
                <h4>Store Owner</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Enim nisi quem export duis labore cillum quae magna enim sint quorum nulla quem veniam duis minim tempor labore quem eram duis noster aute amet eram fore quis sint minim.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-4.jpg" class="testimonial-img" alt="">
                <h3>Matt Brandon</h3>
                <h4>Freelancer</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Fugiat enim eram quae cillum dolore dolor amet nulla culpa multos export minim fugiat minim velit minim dolor enim duis veniam ipsum anim magna sunt elit fore quem dolore labore illum veniam.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-5.jpg" class="testimonial-img" alt="">
                <h3>John Larson</h3>
                <h4>Entrepreneur</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Quis quorum aliqua sint quem legam fore sunt eram irure aliqua veniam tempor noster veniam enim culpa labore duis sunt culpa nulla illum cillum fugiat legam esse veniam culpa fore nisi cillum quid.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Testimonials Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <!-- <h2>Contact</h2> -->
        <div><span>Kunjungi </span><span class="description-title">Seblak Say Cafe</div>
      </div><!-- End Section Title -->

      <div class="mb-5">
       <iframe style="width: 100%; height: 400px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.318951464519!2d112.01129887481696!3d-7.648810892367321!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e784f0048ac8ce5%3A0x99f1db18d32fb436!2sSeblak%20SAY%20Cafe!5e0!3m2!1sid!2sid!4v1762855183435!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div><!-- End Google Maps -->

      <div class="container" data-aos="fade">

        <div class="row gy-5 gx-lg-5">

          <div class="col-lg-4">

            <div class="info">
              <h3>Get in touch</h3>
              <p>Et id eius voluptates atque nihil voluptatem enim in tempore minima sit ad mollitia commodi minus.</p>

              <div class="info-item d-flex">
                <i class="bi bi-geo-alt flex-shrink-0"></i>
                <div>
                  <h4>Location:</h4>
                  <p>Jl. Warujayeng - Kediri, RT.02/RW.06, Krajan Selatan, Kampungbaru, Kec. Tanjunganom, Kabupaten Nganjuk, Jawa Timur 64482
27 mnt</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex">
                <i class="bi bi-envelope flex-shrink-0"></i>
                <div>
                  <h4>Email:</h4>
                  <p>info@example.com</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex">
                <i class="bi bi-phone flex-shrink-0"></i>
                <div>
                  <h4>Call:</h4>
                  <p>+1 5589 55488 55</p>
                </div>
              </div><!-- End Info Item -->

            </div>

          </div>

          <div class="col-lg-8">
            <form action="forms/contact.php" method="post" role="form" class="php-email-form">
              <div class="row">
                <div class="col-md-6 form-group">
                  <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required="">
                </div>
                <div class="col-md-6 form-group mt-3 mt-md-0">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required="">
                </div>
              </div>
              <div class="form-group mt-3">
                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required="">
              </div>
              <div class="form-group mt-3">
                <textarea class="form-control" name="message" placeholder="Message" required=""></textarea>
              </div>
              <div class="my-3">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Your message has been sent. Thank you!</div>
              </div>
              <div class="text-center"><button type="submit">Send Message</button></div>
            </form>
          </div><!-- End Contact Form -->

        </div>

      </div>

    </section><!-- /Contact Section -->



    <footer id="footer" class="footer dark-background">

    <div class="container">
      <div class="row gy-3">
        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-geo-alt icon"></i>
          <div class="address">
            <h4>Address</h4>
             <p>Jl. Warujayeng - Kediri, RT.02/RW.06, Krajan Selatan, Kampungbaru, Kec. Tanjunganom, Kabupaten Nganjuk, Jawa Timur 64482
</p>
            <p></p>
          </div>

        </div>

        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-telephone icon"></i>
          <div>
            <h4>Contact</h4>
            <p>
              <strong>Phone:</strong> <span>+1 5589 55488 55</span><br>
              <strong>Email:</strong> <span>info@example.com</span><br>
            </p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-clock icon"></i>
          <div>
            <h4>Opening Hours</h4>
            <p>
              <strong>Mon-Sat:</strong> <span>11AM - 23PM</span><br>
              <strong>Sunday</strong>: <span>Closed</span>
            </p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <h4>Follow Us</h4>
          <div class="social-links d-flex">
            <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Seblak Say Cafe</strong> <span>All Rights Reserved</span></p>
     Designed by 
      <a href="#" onclick="window.location='index.php?page=dashboard'" style="opacity:0.2; color:#ffca28; text-decoration:none;"
        onmouseover="this.style.opacity=1" 
        onmouseout="this.style.opacity=0.2">
      Nothing
      </a>
      </div>
    </div>

  </footer>

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
<style>
/* CARD MENU HOVER ANIMATION */
.menu-card {
  width:230px;
  background:#fff;
  border-radius:14px;
  padding:18px;
  text-align:center;
  box-shadow:0 6px 18px rgba(0,0,0,.08);
  transition: .25s;
  opacity:0;
  animation:fadeUp .5s ease forwards;
}

.menu-card:hover{
  transform:translateY(-6px);
  box-shadow:0 12px 28px rgba(0,0,0,.18);
}

/* animasi zoom gambar waktu hover */
.menu-card img {
  width:140px;
  height:140px;
  object-fit:cover;
  border-radius:12px;
  margin-bottom:12px;
  transition: .25s ease;
}

.menu-card:hover img{
  transform: scale(1.08);
}

.menu-card img{
  transition: .25s ease;
}
.menu-card:hover img{
  transform: scale(1.1);
  filter: brightness(1.05);
}
.menu-card:hover{
  transform: translateY(-8px);
  transition: .25s ease;
}

.menu-card-wrapper { display:flex; flex-wrap:wrap; gap:25px; justify-content:center; }
.menu-card { width:230px; background:#fff; border-radius:14px; padding:18px; text-align:center; box-shadow:0 6px 18px rgba(0,0,0,.08); transition:.25s; opacity:0; animation:fadeUp .5s ease forwards; }
.menu-card:hover{ transform:translateY(-6px); box-shadow:0 10px 24px rgba(0,0,0,.12); }
.menu-card img{ width:140px; height:140px; object-fit:cover; border-radius:12px; margin-bottom:12px; transition:.25s;}
.menu-card h5{font-size:1.05rem; font-weight:600;}
.menu-card .stok{font-size:.9rem; color:#666;}
.menu-card .harga{color:#e6a400; font-weight:700;}
.btn-cart, .btn-pesan{ margin-top:10px; width:100%; padding:10px; border-radius:8px; border:none; font-weight:600; transition:.2s;}
.btn-cart{ background:#fff; border:2px solid #ffca28; }
.btn-cart:hover{ background:#ffca28; }
.btn-pesan{ background:#ffc107; color:#fff; }
.btn-pesan:hover{ background:#ffca2c; }
.menu-card.sold-out img { filter: grayscale(100%) brightness(65%); }
.label-soldout { position:absolute; top:8px; left:8px; background:rgba(200,0,0,.95); color:#fff; font-weight:700; font-size:.8rem; padding:5px 10px; border-radius:6px;}
.disabled-btn{ background:#ccc !important; color:#555 !important; cursor:not-allowed; }
@keyframes fadeUp { from{opacity:0; transform:translateY(20px);} to{opacity:1; transform:translateY(0);} }
</style>
</body>
</html>