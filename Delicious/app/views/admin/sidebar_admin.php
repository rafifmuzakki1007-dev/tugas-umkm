<div class="sidebar d-flex flex-column">

    <!-- Branding -->
    <div class="sidebar-header px-3 mb-4 d-flex align-items-center gap-2">
        <span class="fs-4">🍽️</span>
        <h4 class="text-white fw-bold m-0">UMKM Admin</h4>
    </div>

    <!-- Menu Links -->
    <a href="index.php?page=dashboard" 
       class="sidebar-item <?= (($_GET['page'] ?? '') == 'dashboard') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <a href="index.php?page=menu_admin" 
       class="sidebar-item <?= (($_GET['page'] ?? '') == 'menu_admin') ? 'active' : '' ?>">
        <i class="bi bi-list-ul me-2"></i> Kelola Menu
    </a>

    <a href="index.php?page=admin_profile" 
       class="sidebar-item <?= (($_GET['page'] ?? '') == 'admin_profile') ? 'active' : '' ?>">
        <i class="bi bi-person-circle me-2"></i> Profil Admin
    </a>

    <!-- Spacer -->
    <div class="flex-grow-1"></div>

    <!-- Logout -->
    <a href="index.php?page=logout" class="sidebar-item logout">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>

</div>

<style>
/* Sidebar Base */
.sidebar {
    width: 240px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    background: var(--sidebar-bg, #1b2a49);
    padding-top: 20px;
    color: white;
    transition: 0.3s;
    z-index: 1020;
}

/* Sidebar Header */
.sidebar-header {
    border-bottom: 1px solid rgba(255,255,255,0.15);
    padding-bottom: 15px;
}

/* Menu Item */
.sidebar-item {
    color: var(--sidebar-text, #cbd5e1);
    padding: 12px 24px;
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    transition: .2s;
    font-size: 15px;
}

/* Hover & Active */
.sidebar-item:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}

.sidebar-item.active {
    background: rgba(255,255,255,0.15);
    border-left: 4px solid #4f9cff;
    color: #fff;
}

.sidebar-item.logout {
    color: #ff6060 !important;
}

.sidebar-item.logout:hover {
    background: rgba(255,50,50,0.15);
}
</style>
