<header id="header" class="header fixed-top">
  <div class="topbar d-flex align-items-center">
    ...
  </div>
  <div class="branding d-flex align-items-cente">
    <div class="container position-relative d-flex align-items-center justify-content-between">
      <a href="index.php?page=home" class="logo d-flex align-items-center">
        <h1 class="sitename">Seblak Sayy Ah</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php?page=home" class="<?= ($page === 'home') ? 'active' : '' ?>">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="index.php?page=menu_admin" class="<?= ($page === 'menu_admin') ? 'active' : '' ?>">Menu</a></li>
          <li><a href="#specials">Specials</a></li>
          <li><a href="#events">Events</a></li>
          <li><a href="#chefs">Chefs</a></li>
          <li><a href="#gallery">Gallery</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </div>
</header>
