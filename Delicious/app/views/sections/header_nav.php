<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ✅ Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- ✅ AOS CSS -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

<header id="header" class="header fixed-top">

  <div class="topbar d-flex align-items-center bg-light py-1 border-bottom">
    <div class="container d-flex justify-content-between">
      <div class="contact-info small">
        <i class="bi bi-phone"></i> +62 8129-0966-363  
        <span class="ms-3"><i class="bi bi-clock"></i> Everyday: 10:00-22:00</span>
      </div>
      <a href="#" class="btn btn-warning rounded-pill px-4 py-1 fw-semibold shadow-sm">Book a table</a>
    </div>
  </div>

  <div class="bg-dark text-white shadow-sm">
    <div class="container d-flex align-items-center justify-content-between py-2">

      <a href="index.php" class="text-white text-decoration-none fw-bold fs-4">Seblak Sayy Cafe</a>

      <nav id="navmenu" class="navmenu">
        <ul class="d-flex gap-4 list-unstyled m-0">
          <li><a class="text-white text-decoration-none" href="index.php">Home</a></li>
          <li><a class="text-white text-decoration-none <?= ($_GET['page'] ?? '') === 'menu' ? 'fw-bold text-warning' : '' ?>" 
            href="index.php?page=menu">Menu</a>
          </li>
          <li><a class="text-warning text-decoration-none fw-semibold" href="index.php?page=dashboard">Login Admin</a></li>
        </ul>

        <i class="mobile-nav-toggle bi bi-list text-white fs-3 d-md-none"></i>
      </nav>

    </div>
  </div>
</header>

<!-- ✅ Custom CSS -->
<style>
.navmenu ul li a:hover { color:#ffb200 !important; transform: translateY(-2px); }
.mobile-nav-toggle { cursor:pointer; }
@media(max-width: 768px){
  .navmenu ul {
    flex-direction: column;
    background:#000;
    position: absolute;
    top:70px; right:10px; width:180px;
    padding:15px; border-radius:8px; display:none;
  }
  .navmenu ul.show { display:flex; }
  .navmenu ul li { margin:10px 0; }
}
</style>

<!-- ✅ Bootstrap JS & AOS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script> AOS.init({ once:true, duration:800 }); </script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.querySelector(".mobile-nav-toggle");
  const menu = document.querySelector("#navmenu ul");

  toggleBtn.addEventListener("click", () => menu.classList.toggle("show"));
});
</script>
