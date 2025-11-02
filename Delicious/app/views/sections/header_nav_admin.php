<header id="header" class="header fixed-top d-flex align-items-center">
  <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

    <a href="index.php?page=menu_admin" class="logo d-flex align-items-center">
      <h1 class="sitename">Seblak Say Café</h1>
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="index.php?page=menu_admin" class="<?= ($page === 'menu_admin') ? 'active' : '' ?>">Kelola Menu</a></li>
        <li><a href="index.php?page=home">Kembali ke Web</a></li>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

  </div>
</header>
