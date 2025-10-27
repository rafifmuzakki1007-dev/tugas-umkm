<section id="chefs" class="chefs section">

  <div class="container section-title" data-aos="fade-up">
    <h2>Karyawan</h2>
    <div><span>Our</span> <span class="description-title">Professional Staff</span></div>
  </div>

  <div class="container">
    <div class="row gy-4">
      <?php if (!empty($karyawanData)): ?>
        <?php 
          $delay = 100; 
          foreach ($karyawanData as $k): 
        ?>
          <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="<?= $delay; ?>">
            <div class="chef-member">
              <div class="member-img">
                <img src="assets/img/chefs/<?= !empty($k['foto']) ? htmlspecialchars($k['foto']) : 'chefs-1.jpg'; ?>" class="img-fluid" alt="">
                <div class="social">
                  <a href="#"><i class="bi bi-twitter-x"></i></a>
                  <a href="#"><i class="bi bi-facebook"></i></a>
                  <a href="#"><i class="bi bi-instagram"></i></a>
                  <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
              <div class="member-info">
                <h4><?= htmlspecialchars($k['nama_karyawan'] ?? ''); ?></h4>
                <span><?= htmlspecialchars($k['jabatan'] ?? ''); ?></span>
              </div>
            </div>
          </div>
        <?php 
          $delay += 100; 
          endforeach; 
        ?>
      <?php else: ?>
        <div class="col-lg-12">
          <p class="text-center">Belum ada data karyawan.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

</section>
