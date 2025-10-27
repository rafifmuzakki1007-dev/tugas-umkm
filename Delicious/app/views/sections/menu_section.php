<section id="menu" class="menu section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Menu</h2>
    <div><span>Check Our Tasty</span> <span class="description-title">Menu</span></div>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-5">
      <?php if (!empty($menuData)): ?>
        <?php foreach ($menuData as $menu): ?>
          <div class="col-lg-4 menu-item">
            <img src="assets/img/menu/<?= htmlspecialchars($menu['gambar']); ?>" class="menu-img img-fluid" alt="">
            <h4><?= htmlspecialchars($menu['nama_menu']); ?></h4>
            <p class="price">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></p>
            <p class="ingredients">Stok: <?= htmlspecialchars($menu['stok']); ?> pcs</p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center">Belum ada data menu.</p>
      <?php endif; ?>
    </div>
  </div>
</section>
