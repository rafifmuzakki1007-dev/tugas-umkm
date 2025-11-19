<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>

<!-- ❌ SWEETALERT DIHAPUS DARI SINI (SUPAYA TIDAK DOUBLE LOAD) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">

<?php 
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$cartCount = array_sum($_SESSION['cart']);
?>

<!-- LOADER SCREEN -->
<div id="pageLoader" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:white; z-index:9999; display:flex; align-items:center; 
            justify-content:center;">
    <div class="spinner-border text-warning" style="width:55px;height:55px;"></div>
</div>

<header id="header" class="header fixed-top">
    </div>
  </div>

  <div class="bg-dark text-white shadow-sm">
    <div class="container d-flex align-items-center justify-content-between py-2">

    <img src="assets/img/logo1.png" alt="logo seblak say cafe" width="90px" height="100%">

      <?php if(isset($_SESSION['admin'])): ?>
      <form method="GET" action="index.php" class="d-none d-md-flex align-items-center" style="width:300px;">
        <input type="hidden" name="page" value="menu_admin">
        <div class="input-group input-group-sm">
          <input type="text" name="search" class="form-control" placeholder="Cari menu..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
          <button class="btn btn-warning"><i class="bi bi-search"></i></button>
        </div>
      </form>
      <?php endif; ?>

      <nav id="navmenu" class="navmenu">
        <ul class="d-flex gap-4 list-unstyled m-0">

          <li><a class="text-white text-decoration-none <?= !isset($_GET['page']) ? 'fw-bold text-warning' : '' ?>" href="index.php">Home</a></li>

          <li>
            <a onclick="showLoader()" class="text-white text-decoration-none <?= ($_GET['page'] ?? '') === 'menu' ? 'fw-bold text-warning' : '' ?>" 
               href="index.php?page=menu">
              Menu
            </a>
          </li>

          <?php if(isset($_SESSION['user']) && !isset($_SESSION['admin'])): ?>
          <li>
            <a class="text-white text-decoration-none <?= ($_GET['page'] ?? '') === 'riwayat' ? 'fw-bold text-warning' : '' ?>" href="index.php?page=riwayat">
              <i class="bi bi-clock-history"></i> Riwayat
            </a>
          </li>
          <?php endif; ?>

        </ul>

        <i class="mobile-nav-toggle bi bi-list text-white fs-3 d-md-none"></i>
      </nav>

    </div>
  </div>
</header>

<!-- FLOATING CART -->
<?php if(($_GET['page'] ?? '') === 'menu' && !isset($_SESSION['admin'])): ?>
<a onclick="openCart()" id="cartFloatBtn" class="cart-float-menu">
  <i class="bi bi-cart3"></i>
  <span class="cart-badge-menu"><?= $cartCount ?></span>
</a>

<style>
.cart-float-menu{
  position:fixed;
  top:120px;
  right:25px;
  height:60px;width:60px;
  background:#ffc107;color:#000;border-radius:50%;
  display:flex;justify-content:center;align-items:center;
  font-size:26px;cursor:pointer;z-index:9999;
  box-shadow:0 8px 18px rgba(0,0,0,.25);transition:.25s;
}
.cart-float-menu:hover{transform:scale(1.15);background:#ffcf3a;}
.cart-badge-menu{
  position:absolute;top:-5px;right:-5px;background:#e60023;color:#fff;
  font-size:11px;font-weight:bold;min-width:20px;height:20px;line-height:18px;
  border-radius:50%;text-align:center;border:2px solid #fff;
}
</style>
<?php endif; ?>

<script>
// Mobile Nav
document.addEventListener("DOMContentLoaded", () => {
  document.querySelector(".mobile-nav-toggle")
    ?.addEventListener("click", () => document.querySelector("#navmenu ul").classList.toggle("show"));
});

// Loader Function
function showLoader() {
  document.getElementById("pageLoader").style.display = "flex";
}

// Show loader when entering menu page (for refresh/redirect)
if (window.location.href.includes("page=menu")) {
  document.getElementById("pageLoader").style.display = "flex";
  setTimeout(() => {
    document.getElementById("pageLoader").style.display = "none";
  }, 600);
}
</script>
