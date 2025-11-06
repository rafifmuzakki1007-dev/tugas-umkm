<title>Menu | Seblak Say cafe</title>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once 'config/koneksi.php';
require_once 'app/models/MenuModel.php';

$menuModel = new MenuModel($koneksi);
$menus = $menuModel->getAllMenu();

if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// ADD
if (isset($_POST['add_to_cart']) && isset($_POST['qty'])) {
    $id = $_POST['add_to_cart'];
    $qty = max(1, intval($_POST['qty']));
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
    echo "<script>
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Menu ditambahkan ke keranjang', timer: 1000, showConfirmButton: false });
    </script>";
}

// REMOVE
if (isset($_POST['remove_from_cart'])) {
    $id = $_POST['remove_from_cart'];
    unset($_SESSION['cart'][$id]);
}

include 'app/views/sections/header_nav.php'; 
?>

<style>.page-wrapper { margin-top:180px!important; }</style>
<link rel="stylesheet" href="assets/vendor/aos/aos.css">

<style>
/* CARD MENU HOVER ANIMATION */
.menu-card {
  width:230px;
  background:#fff;
  border-radius:14px;
  padding:18px;
  text-align:center;
  box-shadow:0 6px 18px rgba(0,0,0,.08);
  transition: .25s;
  opacity:0;
  animation:fadeUp .5s ease forwards;
}

.menu-card:hover{
  transform:translateY(-6px);
  box-shadow:0 12px 28px rgba(0,0,0,.18);
}

/* animasi zoom gambar waktu hover */
.menu-card img {
  width:140px;
  height:140px;
  object-fit:cover;
  border-radius:12px;
  margin-bottom:12px;
  transition: .25s ease;
}

.menu-card:hover img{
  transform: scale(1.08);
}

.menu-card img{
  transition: .25s ease;
}
.menu-card:hover img{
  transform: scale(1.1);
  filter: brightness(1.05);
}
.menu-card:hover{
  transform: translateY(-8px);
  transition: .25s ease;
}

.menu-card-wrapper { display:flex; flex-wrap:wrap; gap:25px; justify-content:center; }
.menu-card { width:230px; background:#fff; border-radius:14px; padding:18px; text-align:center; box-shadow:0 6px 18px rgba(0,0,0,.08); transition:.25s; opacity:0; animation:fadeUp .5s ease forwards; }
.menu-card:hover{ transform:translateY(-6px); box-shadow:0 10px 24px rgba(0,0,0,.12); }
.menu-card img{ width:140px; height:140px; object-fit:cover; border-radius:12px; margin-bottom:12px; transition:.25s;}
.menu-card h5{font-size:1.05rem; font-weight:600;}
.menu-card .stok{font-size:.9rem; color:#666;}
.menu-card .harga{color:#e6a400; font-weight:700;}
.btn-cart, .btn-pesan{ margin-top:10px; width:100%; padding:10px; border-radius:8px; border:none; font-weight:600; transition:.2s;}
.btn-cart{ background:#fff; border:2px solid #ffca28; }
.btn-cart:hover{ background:#ffca28; }
.btn-pesan{ background:#ffc107; color:#fff; }
.btn-pesan:hover{ background:#ffca2c; }
.menu-card.sold-out img { filter: grayscale(100%) brightness(65%); }
.label-soldout { position:absolute; top:8px; left:8px; background:rgba(200,0,0,.95); color:#fff; font-weight:700; font-size:.8rem; padding:5px 10px; border-radius:6px;}
.disabled-btn{ background:#ccc !important; color:#555 !important; cursor:not-allowed; }
@keyframes fadeUp { from{opacity:0; transform:translateY(20px);} to{opacity:1; transform:translateY(0);} }
</style>

<div class="page-wrapper">
<div class="container my-5" data-aos="fade-up">

<div class="text-center mb-5">
  <h2 class="fw-bold">Menu Kami</h2>
  <p class="text-muted">Pilih menu favoritmu 🍽️</p>
</div>

<div class="modal fade" id="cartModal"><div class="modal-dialog modal-lg"><div class="modal-content">
  <div class="modal-header bg-warning"><h5 class="fw-bold"><i class="bi bi-cart"></i> Keranjang</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body" id="cartContent"><div class="text-center py-5">Memuat...</div></div>
</div></div></div>

<div class="modal fade" id="modalPesan"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
<form id="formPesan" action="app/controllers/pesan_process.php" method="POST">
<div class="modal-header bg-warning"><h5 class="fw-bold"><i class="bi bi-bag-check"></i> Pesan Menu</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<input type="hidden" id="id_menu" name="id_menu">
<input type="hidden" id="harga_hidden" name="harga">
<div class="mb-2"><label>Nama Menu</label><input id="nama_menu" class="form-control" readonly></div>
<div class="mb-2"><label>Harga</label><input id="harga_menu" class="form-control" readonly></div>
<div class="mb-2"><label>Jumlah</label><input type="number" id="jumlah" name="jumlah" class="form-control" min="1" value="1"></div>
<div class="mb-2"><label>Total</label><input id="total_harga" class="form-control" readonly></div>
<div class="mb-2"><label>Metode Pembayaran</label>
<select name="jenis_transaksi" class="form-control" required>
<option value="Tunai">Tunai</option>
<option value="Transfer">Transfer</option>
</select></div>
</div>
<div class="modal-footer"><button class="btn btn-warning fw-bold">Pesan Sekarang</button></div>
</form></div></div></div>

<div class="menu-card-wrapper">
<?php foreach ($menus as $menu):
  $stok = $menu['stok']; $sold = $stok <= 0; ?>
<div class="menu-card <?= $sold?'sold-out':'' ?>">
  <div style="position:relative;">
    <img src="assets/img/menu/<?= $menu['gambar']; ?>">
    <?php if ($sold): ?><span class="label-soldout">Habis</span><?php endif; ?>
  </div>
  <h5><?= $menu['nama_menu']; ?></h5>
  <div class="stok">Stok: <?= $stok ?></div>
  <div class="harga">Rp <?= number_format($menu['harga']); ?></div>

  <?php if(!$sold): ?>
    <button class="btn-cart" onclick="addToCartPrompt('<?= $menu['id_menu']; ?>')"><i class="bi bi-cart-plus"></i> Tambah ke Keranjang</button>
    <button class="btn-pesan btnPesan" data-id="<?= $menu['id_menu']; ?>" data-nama="<?= $menu['nama_menu']; ?>" data-harga="<?= $menu['harga']; ?>">Pesan Langsung</button>
  <?php else: ?>
    <button class="btn-cart disabled-btn">Stok Habis</button>
    <button class="btn-pesan disabled-btn">Tidak Tersedia</button>
  <?php endif; ?>
</div>
<?php endforeach; ?>
</div>

</div></div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script>AOS.init();</script>

<script>
function openCart(){ 
  fetch("app/views/cart.php")
  .then(r=>r.text())
  .then(h=>{
    document.getElementById("cartContent").innerHTML=h;
    new bootstrap.Modal(document.getElementById('cartModal')).show();
  });
}

function addToCartPrompt(id){
Swal.fire({title:'Berapa banyak?',input:'number',inputValue:1,inputAttributes:{min:1},showCancelButton:true,confirmButtonText:'Tambah'})
.then(r=>{
    if(!r.isConfirmed)return;
    let f=document.createElement('form');
    f.method='POST';
    f.innerHTML=`<input type="hidden" name="add_to_cart" value="${id}"><input type="hidden" name="qty" value="${r.value}">`;
    document.body.appendChild(f);
    f.submit();
});
}

document.querySelectorAll(".btnPesan").forEach(btn=>{
btn.onclick=function(){
let id=this.dataset.id,n=this.dataset.nama,h=parseInt(this.dataset.harga);
document.getElementById("id_menu").value=id;
document.getElementById("nama_menu").value=n;
document.getElementById("harga_hidden").value=h;
document.getElementById("harga_menu").value="Rp "+h.toLocaleString("id-ID");
let j=document.getElementById("jumlah"),t=document.getElementById("total_harga");
function calc(){t.value="Rp "+(j.value*h).toLocaleString("id-ID");}
j.value=1;calc();j.oninput=calc;
new bootstrap.Modal(document.getElementById('modalPesan')).show();
};
});
</script>

<!-- FIX DELETE CART TOMBOL -->
<script>
document.addEventListener("click", e => {
  const btn = e.target.closest(".btn-delete-cart");
  if (!btn) return;

  const id = btn.dataset.id;

  Swal.fire({
    title: "Hapus item?",
    text: "Item akan dihapus dari keranjang",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Ya, hapus"
  }).then(res => {
    if (!res.isConfirmed) return;

    let form = document.createElement("form");
    form.method = "POST";
    form.innerHTML = `<input type="hidden" name="remove_from_cart" value="${id}">`;
    document.body.appendChild(form);
    form.submit();
  });
});

// Variabel penanda apakah user menekan tombol submit atau tidak
let isSubmitting = false;

// Konfirmasi sebelum submit pesan langsung
document.getElementById("formPesan").addEventListener("submit", function(e){
    e.preventDefault();
    isSubmitting = true; // user benar klik tombol submit

    Swal.fire({
        title: "Konfirmasi Pesanan",
        text: "Apakah pesanan sudah benar?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#ffc107",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ya, Pesan!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit(); // lanjut submit
        } else {
            isSubmitting = false; // user batal
        }
    });
});

// Deteksi modal ditutup — jangan munculkan alert
document.getElementById('modalPesan').addEventListener('hidden.bs.modal', function () {
    isSubmitting = false;
});

// Cek status success dari URL untuk alert berhasil
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get("status") === "success") {
    Swal.fire({
        icon: "success",
        title: "Pesanan Berhasil!",
        text: "Pesananmu sudah diterima, silakan tunggu 😊",
        confirmButtonColor: "#ffc107"
    });
}

</script>
