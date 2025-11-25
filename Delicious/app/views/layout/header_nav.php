<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">


<div id="pageLoader"></div>
<style>
  

#pageLoader {
  position: fixed;
  inset: 0;
  z-index: 999999;
  overflow: hidden;
  background: var(--background-color);
  transition: all 0.6s ease-out;
}

#pageLoader:before {
  content: "";
  position: fixed;
  top: calc(50% - 30px);
  left: calc(50% - 30px);
  border: 6px solid #ffffff;
  border-color: var(--accent-color) transparent var(--accent-color) transparent;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: animate-pageLoader 1.5s linear infinite;
}

@keyframes animate-pageLoader {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>

<header id="header" class="header fixed-top">
    </div>
  </div>

  <div class="bg-dark text-white shadow-sm">
    <div class="container d-flex align-items-center justify-content-between py-2">

    <img src="assets/img/logo1.png" alt="logo seblak say cafe" width="90px" height="100%">
    
      <nav id="navmenu" class="navmenu">
        <ul class="d-flex gap-4 list-unstyled m-0">

          <li>
            <a class="bi bi-house ms-4 d-none d-lg-flex align-items-center fs-2 <?= !isset($_GET['page']) ? 'fw-bold text-warning' : '' ?>" href="index.php"></a></li>
          <li>
            
        </ul>

        <i class="mobile-nav-toggle bi bi-list text-white fs-3 d-md-none"></i>
      </nav>

    </div>
  </div>
</header>

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
