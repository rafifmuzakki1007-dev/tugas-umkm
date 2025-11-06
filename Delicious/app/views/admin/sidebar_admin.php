<div class="sidebar d-flex flex-column">

    <!-- Branding -->
    <div class="sidebar-header px-3 mb-4 d-flex align-items-center gap-2">
        <span class="fs-4">🍽️</span>
        <h4 class="text-white fw-bold m-0">Admin Say Cafe</h4>
    </div>

    <!-- Navigation -->
    <div class="menu-title px-3">Navigation</div>

    <a href="index.php?page=dashboard"
       class="sidebar-item <?= (($_GET['page'] ?? '') == 'dashboard') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <div class="menu-title px-3 mt-3">Menu & Transaksi</div>

    <a href="index.php?page=menu_admin"
       class="sidebar-item <?= (($_GET['page'] ?? '') == 'menu_admin') ? 'active' : '' ?>">
        <i class="bi bi-list-ul me-2"></i> Kelola Menu
    </a>

    <!-- Kelola Transaksi -->
    <a href="index.php?page=transaksi_admin"
        class="sidebar-item <?= (($_GET['page'] ?? '') == 'transaksi_admin') ? 'active' : '' ?>">
      <i class="bi bi-receipt me-2"></i> Kelola Transaksi
    </a>



    <div class="menu-title px-3 mt-3">Admin</div>

    <a href="index.php?page=profile_admin"
       class="sidebar-item <?= (($_GET['page'] ?? '') == 'profile_admin') ? 'active' : '' ?>">
        <i class="bi bi-person me-2"></i> Profil Admin
    </a>

    <!-- Spacer -->
    <div class="flex-grow-1"></div>

    <a href="index.php?page=home" class="sidebar-item">
        <i class="bi bi-house-door me-2"></i> Kembali ke Website
    </a>

    <!-- Logout -->
    <a href="#" class="sidebar-item logout-btn text-danger fw-semibold">
        <i class="bi bi-box-arrow-right me-2 text-danger"></i> Logout
    </a>


</div>

<style>
.sidebar {
  width: 250px;
  height: 100vh;
  background: #1e2a38;
  position: fixed;
  top: 0;
  left: 0;
  padding: 20px 0;
  color: #cbd5e1;
  transition: 0.3s ease-in-out;
  box-shadow:
    0 0 6px rgba(0,0,0,0.15),
    6px 0 15px rgba(0,0,0,0.25),
    0 15px 30px rgba(0,0,0,0.10);
  z-index: 1050;
  border-right: 1px solid rgba(255,255,255,0.05);
  z-index: 2000 !important;
}
.sidebar-header {
  font-weight: 700; color: #fff; padding-bottom: 5px;
}
.menu-title {
  font-size: 12px; font-weight: 600; text-transform: uppercase;
  letter-spacing: .5px; color: #8fa3bb; margin-bottom: 6px;
}
.sidebar-item {
  display: flex; align-items: center;
  padding: 10px 18px; margin: 4px 10px;
  color: #cbd5e1; border-radius: 8px;
  font-size: 14px; gap: 10px;
  transition: .2s; text-decoration: none;
}
.sidebar-item:hover {
  background: #283b52; color: #fff;
}
.sidebar-item.active {
  background: #30445e; color: #fff; font-weight: 600;
  box-shadow: inset 0 0 6px rgba(0,0,0,0.25);
}
.sidebar-item i { font-size: 18px; }
.sidebar-item.logout { color: #f87171; }
.sidebar-item.logout:hover {
  background: rgba(248,113,113,.15); color: #fca5a5;
}
</style>
