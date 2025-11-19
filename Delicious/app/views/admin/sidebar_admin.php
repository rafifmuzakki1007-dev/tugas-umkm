<div class="sidebar d-flex flex-column" id="adminSidebar">

    <!-- Branding -->
    <div class="sidebar-header px-3 mb-4 d-flex align-items-center justify-content-center">
      <img src="assets/img/logo1.png"
         alt="Logo"
         class="logo-img">
    </div>

    <!-- Navigation -->
    <div class="menu-title px-3 item-text">Navigation</div>

    <a href="index.php?page=dashboard"
       class="sidebar-item <?= (($_GET['page'] ?? '') == 'dashboard') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> <span class="item-text">Dashboard</span>
    </a>

    <div class="menu-title px-3 mt-3 item-text">Menu & Transaksi</div>

    <a href="index.php?page=menu_admin"
       class="sidebar-item <?= (($_GET['page'] ?? '') == 'menu_admin') ? 'active' : '' ?>">
        <i class="bi bi-list-ul"></i> <span class="item-text">Kelola Menu</span>
    </a>

    <a href="index.php?page=transaksi_admin"
       class="sidebar-item <?= (($_GET['page'] ?? '') == 'transaksi_admin') ? 'active' : '' ?>">
        <i class="bi bi-receipt"></i> <span class="item-text">Kelola Transaksi</span>
    </a>

    <div class="menu-title px-3 mt-3 item-text">Admin</div>

    <a href="index.php?page=profile_admin"
       class="sidebar-item <?= (($_GET['page'] ?? '') == 'profile_admin') ? 'active' : '' ?>">
        <i class="bi bi-person"></i> <span class="item-text">Profil Admin</span>
    </a>

    <div class="flex-grow-1"></div>

    <a href="index.php?page=home" class="sidebar-item">
        <i class="bi bi-house-door"></i> <span class="item-text">Kembali ke Website</span>
    </a>

    <!-- Logout -->
    <a href="#" id="btnLogout" class="sidebar-item text-danger fw-semibold">
        <i class="bi bi-box-arrow-right text-danger"></i>
        <span class="item-text text-danger">Logout</span>
    </a>

</div>

<!-- TOGGLE ARROW  -->
<button id="toggleSidebarArrow" class="toggle-arrow">
    <i class="bi bi-chevron-left" id="arrowIcon"></i>
</button>

<style>
/* SIDEBAR */
.sidebar{
  width:250px;
  height:100vh;
  background:#1e2a38;
  position:fixed;
  top:0;
  left:0;
  padding:20px 0;
  color:#cbd5e1;
  transition:.3s ease;
  box-shadow:0 0 6px rgba(0,0,0,0.15), 6px 0 15px rgba(0,0,0,0.25);
  border-right:1px solid rgba(255,255,255,0.05);
  z-index:5000 !important;
}

.sidebar.collapsed{
  width:70px;
}

.sidebar-header {
  font-weight:700;
  color:#fff;
}

.menu-title{
  font-size:12px;
  font-weight:600;
  text-transform:uppercase;
  color:#8fa3bb;
  margin-bottom:6px;
}

.sidebar-item{
  display:flex;
  align-items:center;
  padding:10px 18px;
  margin:4px 10px;
  border-radius:8px;
  color:#cbd5e1;
  font-size:14px;
  gap:12px;
  text-decoration:none;
  transition:.2s;
}

.sidebar-item:hover{
  background:#283b52;
  color:#fff;
}

.sidebar-item.active{
  background:#30445e;
  color:#fff;
  font-weight:600;
}

.sidebar.collapsed .item-text,
.sidebar.collapsed .menu-title{
  display:none !important;
}

.sidebar.collapsed .sidebar-item{
  justify-content:center;
}

/* Logout button */
#btnLogout{
  z-index:9999 !important;
  pointer-events:auto !important;
}

/* Logo styling */
.logo-img {
    height: 110px;
    width: auto;
    object-fit: contain;
    display: block;
    margin: 0 auto;
    filter: brightness(1.1);
    transition: 0.25s ease;
}

.sidebar.collapsed .logo-img {
    height: 44px !important;
}

/* === ARROW TOGGLE BUTTON (model phpMyAdmin) === */
.toggle-arrow{
  position:fixed;
  top:120px;
  left:250px;
  width:22px;
  height:40px;
  background:#1e2a38d0;    /* sedikit transparan */
  border:none;
  border-radius:0 6px 6px 0;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  color:white;
  z-index:6001;
  transition:.28s ease;
}

.toggle-arrow i{
  font-size:14px;
}

.sidebar.collapsed + .toggle-arrow{
  left:70px;
}

.toggle-arrow:hover{
  background:#253445f0;
}

#toggleSidebarArrow {
    opacity: 0;
    pointer-events: none;
    transition: opacity .3s;
}
body.loaded #toggleSidebarArrow {
    opacity: 1;
    pointer-events: auto;
}

</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const sb = document.getElementById("adminSidebar");
    const arrow = document.getElementById("arrowIcon");
    const toggleBtn = document.getElementById("toggleSidebarArrow");

    const collapsed = localStorage.getItem("sidebarCollapsed");

    if (collapsed === "true") {
        sb.classList.add("collapsed");
        document.body.classList.add("sidebar-collapsed");

        // update arrow direction
        arrow.classList.remove("bi-chevron-left");
        arrow.classList.add("bi-chevron-right");

        // posisi toggle ikut sidebar
        toggleBtn.style.left = "70px";
    } else {
        toggleBtn.style.left = "250px";
    }
});

// === ON CLICK: toggle sidebar + save to localStorage ===
document.getElementById("toggleSidebarArrow").addEventListener("click", () => {
    const sb = document.getElementById("adminSidebar");
    const arrow = document.getElementById("arrowIcon");
    const toggleBtn = document.getElementById("toggleSidebarArrow");

    sb.classList.toggle("collapsed");
    const isCollapsed = sb.classList.contains("collapsed");

    document.body.classList.toggle("sidebar-collapsed");

    // simpan state
    localStorage.setItem("sidebarCollapsed", isCollapsed);

    // arah panah
    arrow.classList.toggle("bi-chevron-left");
    arrow.classList.toggle("bi-chevron-right");

    // posisi toggle
    toggleBtn.style.left = isCollapsed ? "70px" : "250px";
});

// === LOGOUT ===
document.getElementById("btnLogout").addEventListener("click", function(e){
    e.preventDefault();

    Swal.fire({
        icon:"warning",
        title:"Yakin ingin logout?",
        text:"Anda akan keluar dari dashboard admin.",
        showCancelButton:true,
        confirmButtonColor:"#d33",
        cancelButtonColor:"#6c757d",
        confirmButtonText:"Ya, Logout",
        cancelButtonText:"Batal"
    }).then((res)=>{
        if(res.isConfirmed){
            window.location.href="index.php?do_logout=1";
        }
    });
});

// Setelah halaman selesai → toggle boleh muncul
window.addEventListener("load", () => {
    document.body.classList.add("loaded");
});

</script>
