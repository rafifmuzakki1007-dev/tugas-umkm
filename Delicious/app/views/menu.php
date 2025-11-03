<?php include 'app/views/sections/header_nav.php'; ?>

<link href="assets/vendor/aos/aos.css" rel="stylesheet">

<style>
.page-wrapper { margin-top: 170px; }

.menu-card-wrapper { 
  display:flex; flex-wrap:wrap; gap:25px; justify-content:center; 
}

.menu-card{
  width:230px; background:#fff; border-radius:14px; padding:18px; text-align:center;
  box-shadow:0 6px 18px rgba(0,0,0,.08); transition:.25s; opacity:0; animation:fadeUp .5s ease forwards;
}
.menu-card:hover{ transform:translateY(-6px); box-shadow:0 10px 24px rgba(0,0,0,.12); }

.menu-card img{
  width:140px; height:140px; object-fit:cover; border-radius:12px; margin-bottom:12px; transition:.25s;
}
.menu-card:hover img{ transform:scale(1.05); }

.menu-card h5{ font-size:1.05rem; font-weight:600; margin-top:8px; }
.menu-card .stok{ font-size:.9rem; color:#666; margin-bottom:5px; }
.menu-card .harga{ color:#e6a400; font-weight:700; font-size:1.05rem; }

.menu-card button{
  margin-top:12px; width:100%; padding:10px; border-radius:8px;
  background:#ffc107; border:none; font-weight:600; transition:.2s;
}
.menu-card button:hover{ background:#ffca2c; }

@keyframes fadeUp{ from{opacity:0; transform:translateY(15px);} to{opacity:1; transform:translateY(0);} }
</style>

<div class="page-wrapper">
<div class="container my-5" data-aos="fade-up">

  <div class="text-center mb-5">
    <h2 class="fw-bold">Menu Kami</h2>
    <p class="text-muted m-0">Pilih makanan favoritmu 🍽️</p>
  </div>

<!-- ✅ Modal Pesan -->
<div class="modal fade" id="modalPesan" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3 border-0 rounded-4 shadow-lg">

      <h5 class="text-center mb-3 fw-bold">Pesan Menu</h5>

      <form action="index.php?page=pesan_process" method="POST">

        <input type="hidden" id="id_menu" name="id_menu">
        <input type="hidden" id="harga_menu" name="harga">

        <div class="mb-2">
          <label class="fw-semibold">Nama Menu</label>
          <input type="text" id="nama_menu" class="form-control" readonly>
        </div>

        <div class="mb-2">
          <label class="fw-semibold">Jumlah</label>
          <input type="number" id="jumlah" name="jumlah" min="1" value="1" class="form-control" required>
        </div>

        <div class="mb-2">
          <label class="fw-semibold">Total Harga</label>
          <input type="text" id="total_harga" class="form-control" readonly>
        </div>

        <div class="mb-2">
          <label class="fw-semibold">Jenis Transaksi</label>
          <select name="jenis_transaksi" class="form-control" required>
            <option value="Tunai">Tunai</option>
            <option value="Transfer">Transfer</option>
          </select>
        </div>

        <button type="submit" class="btn btn-warning w-100 fw-bold mt-3">
          Konfirmasi Pesanan ✅
        </button>

      </form>

    </div>
  </div>
</div>
<!-- ✅ End Modal -->

<!-- ✅ Daftar Menu -->
<div class="menu-card-wrapper">
<?php foreach ($menus as $menu): ?>
  <div class="menu-card" data-aos="zoom-in" data-aos-duration="700">
    <img src="assets/img/menu/<?= $menu['gambar']; ?>" alt="<?= $menu['nama_menu']; ?>">
    <h5><?= $menu['nama_menu']; ?></h5>
    <div class="stok">Stok: <?= $menu['stok']; ?></div>
    <div class="harga">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></div>

    <button 
      class="btn btn-warning fw-bold btnPesan"
      data-id="<?= $menu['id_menu']; ?>"
      data-nama="<?= $menu['nama_menu']; ?>"
      data-harga="<?= $menu['harga']; ?>"
    >Pesan Sekarang</button>
  </div>
<?php endforeach; ?>
</div>

</div>
</div>

<!-- ✅ Library -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script>AOS.init({ once:true, duration:800 });</script>

<!-- ✅ Modal Logic -->
<script>
document.querySelectorAll(".btnPesan").forEach(btn => {
  btn.addEventListener("click", function(){

    const id    = this.dataset.id;
    const nama  = this.dataset.nama;
    const harga = parseInt(this.dataset.harga);

    document.getElementById("id_menu").value = id;
    document.getElementById("nama_menu").value = nama;
    document.getElementById("harga_menu").value = harga;

    const jumlahInput = document.getElementById("jumlah");
    const totalInput  = document.getElementById("total_harga");

    function formatRupiah(x) {
      return "Rp " + new Intl.NumberFormat("id-ID").format(x);
    }

    function hitung() {
      const j = parseInt(jumlahInput.value);
      totalInput.value = formatRupiah(j * harga);
    }

    jumlahInput.value = 1;
    hitung();
    jumlahInput.oninput = hitung;

    new bootstrap.Modal(document.getElementById('modalPesan')).show();
  });
});
</script>

<!-- ✅ SweetAlert Success Redirect -->
<?php if(isset($_GET['status']) && $_GET['status']=="success"): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'success',
    title: 'Pesanan Berhasil!',
    text: 'Terima kasih sudah memesan 😊',
    confirmButtonColor: '#ffc107'
})
</script>
<?php endif; ?>
