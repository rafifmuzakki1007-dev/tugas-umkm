    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Menu | Seblak Say cafe</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    
    <!-- Favicons -->
    <link href="assets/img/logo-atas.jpg" rel="icon">
    <link href="assets/img/logo-atas.jpg" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Satisfy:wght@400&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- =======================================================
    * Template Name: Delicious
    * Template URL: https://bootstrapmade.com/delicious-free-restaurant-bootstrap-theme/
    * Updated: Aug 07 2024 with Bootstrap v5.3.3
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
    </head>

    <?php
    if (session_status() === PHP_SESSION_NONE) session_start();
    require_once 'config/koneksi.php';

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    // Handle tambah ke keranjang
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        $id = $_POST['add_to_cart'];
        $qty = max(1, (int)($_POST['qty'] ?? 1));
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
        $_SESSION['flash'] = 'Item ditambahkan ke keranjang!';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Ambil semua menu + kelompokkan berdasarkan kategori
    $stmt = $koneksi->query("SELECT * FROM menu");
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $grouped = [];
    foreach ($all as $m) {
        $kat = $m['kategori'] ?? 'lainnya';
        // Ubah nama kategori biar rapi
        $namaKat = match(strtolower($kat)) {
            'menu utama' => 'Seblak',
            'minuman' => 'Minuman',
            'camilan' => 'Camilan',
            default => ucwords($kat)
        };
        $grouped[$namaKat][] = $m;
    }

    // include 'app/views/sections/header_nav.php';
    ?>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <div class="container">

        <?php foreach ($grouped as $kategori => $menus): ?>
            <h2 class="cat-title"><?= $kategori ?></h2>
            
            <div class="menu-grid">
                <?php foreach ($menus as $m): 
                    $isSeblak = strtolower($m['kategori'] ?? '') === 'menu utama';
                ?>
                    <div class="menu-item position-relative"
                                onclick="<?= $isSeblak 
                                ? "openSeblakModal('{$m['id_menu']}', '".addslashes($m['nama_menu'])."')" 
                                : "addToCart('{$m['id_menu']}', '".addslashes($m['nama_menu'])."', ".($m['harga']??0).")" ?>">

                        <?php if (stripos($m['nama_menu'], 'baru') !== false): ?>
                            <div class="badge-new">BARU</div>
                        <?php endif; ?>

                        <?php 
                        $path = __DIR__ . '/../../assets/img/menu/' . $m['gambar'];
                            if (!empty($m['gambar']) && file_exists($path)): ?>
                                <img src="assets/img/menu/<?= htmlspecialchars($m['gambar']) ?>" 
                                    class="card-img-top rounded" 
                                    alt="<?= htmlspecialchars($m['nama_menu']) ?>"
                                    style="height:240px; object-fit:cover;">
                        <?php else: ?>
                                <div style="height:240px;background:#f8f8f8;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:1.3rem;border-radius:0.5rem;">
                                    Tidak ada gambar 
                                </div> 
                        <?php endif; ?>


                        <div class="menu-name"><?= htmlspecialchars($m['nama_menu']) ?></div>
                        <div class="menu-price">
                            <?= $m['harga'] ? 'Rp '.number_format($m['harga']) : 'Pilih Topping' ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

    </div>

        <!-- Modal Topping Seblak -->
    <div class="modal fade" id="seblakModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark border-0">
                    <h4 class="modal-title fw-bold" id="seblakTitle">Pilih Topping</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="seblakBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-warning" style="width:4rem;height:4rem;"></div>
                    </div>
                </div>

                <!-- TOMBOL KERANJANG TETAP DI SINI (di file utama) -->
                <div class="modal-footer border-0 bg-light rounded-bottom-4">
                    <div class="w-100 text-center">
                        <button id="btnTambahKeKeranjang" class="btn btn-success btn-lg px-5 py-3 shadow" disabled>
                            <i class="bi bi-cart-plus-fill me-2"></i>
                            <span id="btnText">Tambah ke Keranjang</span>
                            <span id="btnCount" class="ms-2"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Keranjang -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold">Keranjang Belanja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cartBody">Memuat...</div>
            </div>
        </div>
    </div>

    </div>

    <!-- modal menu selain seblak -->
    <div class="modal fade" id="qtyModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title" id="qtyTitle">Pilih Jumlah</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="add_to_cart" id="qtyMenuId">
                    
                    <div class="modal-body text-center">
                        <h6 class="mb-3 text-muted" id="qtyMenuName">Nama Menu</h6>
                        
                        <div class="input-group justify-content-center">
                            <button type="button" class="btn btn-outline-secondary" onclick="updatePlainQty(-1)">-</button>
                            <input type="number" name="qty" id="plainQtyInput" 
                                    class="form-control text-center fw-bold" 
                                    value="1" min="1" style="max-width:80px;"
                                    onchange="if(this.value < 1) this.value=1;">
                            <button type="button" class="btn btn-outline-secondary" onclick="updatePlainQty(1)">+</button>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="bi bi-cart-plus-fill me-2"></i> Tambah ke Keranjang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tombol Keranjang -->
    <div class="cart-float">
        <button onclick="openCart()" class="position-relative shadow-lg">
            <i class="bi bi-cart3"></i>
            <span class="cart-badge"><?= array_sum($_SESSION['cart'] ?? []) ?></span>
        </button>
    </div>  

    <!-- Preloader -->
    <div id="preloader"></div>


    <!-- JAVA SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

    const preloader = document.querySelector('#preloader');
    if (preloader) {
        window.addEventListener('load', () => {
        preloader.remove();
        });
    }

    // BARU: Kontrol QTY di Modal Menu Biasa
    function updatePlainQty(change) {
        const input = document.getElementById('plainQtyInput');
        let qty = parseInt(input.value) || 1;
        
        qty += change;
        if (qty < 1) qty = 1;

        input.value = qty;
    }

    // BARU: Fungsi untuk membuka modal QTY menu biasa
    let currentQtyModal; // Variabel global untuk modal

    function openQtyModal(id, name) {
        // Set data di Modal
        document.getElementById('qtyTitle').textContent = `Pesan ${name}`;
        document.getElementById('qtyMenuName').textContent = name;
        document.getElementById('qtyMenuId').value = id;
        document.getElementById('plainQtyInput').value = 1; // Reset QTY
        
        // Tampilkan Modal
        if (!currentQtyModal) {
            currentQtyModal = new bootstrap.Modal(document.getElementById('qtyModal'));
        }
        currentQtyModal.show();
    }

    // buka chart
    function openCart() {
        fetch('app/views/cart.php')
            .then(r => r.text())
            .then(html => {
                document.getElementById('cartBody').innerHTML = html;
                new bootstrap.Modal('#cartModal').show();
            });
    }

    // Flash message
    <?php if (isset($_SESSION['flash'])): ?>
        Swal.fire({icon:'success', title:'Berhasil!', text:'<?= $_SESSION['flash'] ?>', timer:1500, showConfirmButton:false});
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    </script>


    <script>
    // Data stok dari file topping (dipindah ke sini biar pasti jalan)
    function updateQty(id, change) {
        const input   = document.getElementById('qty-' + id);
        const hidden  = document.getElementById('hidden-qty-' + id);
        const btn     = document.getElementById('btn-add-' + id);

        if (!input) return; // safety

        let qty = parseInt(input.value)     || 0;
        qty += change;

        const max = (window.toppingStokData && window.toppingStokData[id] !== null) ? window.toppingStokData[id] : 9999;
        if (qty > max) qty = max;
        if (qty < 0) qty = 0;

        input.value = qty;
        hidden.value = qty;
        btn.disabled = (qty === 0);

        if (qty > 0) {
            btn.innerHTML = `<i class="bi bi-cart-check"></i> Tambah ${qty} ke Keranjang`;
            btn.classList.remove('btn-warning'); btn.classList.add('btn-success');
        } else {
            btn.innerHTML = `<i class="bi bi-cart-plus"></i> Tambah ke Keranjang`;
            btn.classList.remove('btn-success'); btn.classList.add('btn-warning');
        }
    }

    function addToCart(id, name, price) {
        openQtyModal(id, name);
    }

    // ============================
    // SYSTEM BARU TOPPING + AJAX
    // ============================

    // Array topping yang dipilih
    let selectedToppings = [];

    // Tambah/Kurang topping tombol + dan -
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("btnPlus")) {
            let id = e.target.dataset.id;
            let harga = parseInt(e.target.dataset.harga);
            updateTopping(id, harga, +1);
        }

        if (e.target.classList.contains("btnMinus")) {
            let id = e.target.dataset.id;
            let harga = parseInt(e.target.dataset.harga);
            updateTopping(id, harga, -1);
        }
    });

    // update topping dalam array
    function updateTopping(id, harga, delta) {
        let exist = selectedToppings.find(t => t.id == id);

        if (!exist && delta === 1) {
            selectedToppings.push({ id, qty: 1, harga });
        } else if (exist) {
            exist.qty += delta;
            if (exist.qty <= 0) {
                selectedToppings = selectedToppings.filter(t => t.id != id);
            }
        }

        updateToppingCount();
        activateAddCartButton();
    }

    // Update jumlah topping di tombol
    function updateToppingCount() {
        const total = selectedToppings.reduce((a,b) => a + b.qty, 0);
        document.getElementById("btnCount").innerHTML = total ? `(${total})` : "";
    }

    // Aktifkan tombol setelah pilih topping
    function activateAddCartButton() {
        document.getElementById("btnTambahKeKeranjang").disabled = false;
    }

    // Kirim ke keranjang
    document.getElementById("btnTambahKeKeranjang").addEventListener("click", function () {

        const menuId = this.dataset.menu;
        const payload = {
            id_menu: menuId,
            qty: 1,
            topping: selectedToppings
        };

        fetch("cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === "success") {
                alert("Berhasil masuk keranjang!");
                selectedToppings = [];
                updateToppingCount();
                bootstrap.Modal.getInstance(document.getElementById("seblakModal")).hide();
            } else {
                alert("Gagal: " + res.message);
            }
        });
    });


    // buka modal seblak
    function openSeblakModal(menuId, name) {
        document.getElementById("btnTambahKeKeranjang").dataset.menu = menuId;

        fetch("app/views/ajax/seblak_toppings.php?id_menu=" + menuId)
            .then(r => r.text())
            .then(html => {
                document.getElementById("seblakBody").innerHTML = html;
                selectedToppings = []; // reset
                updateToppingCount();
                new bootstrap.Modal(document.getElementById("seblakModal")).show();
            });
    }
    </script>


    <!-- include footer.php -->
    <?php include 'app/views/sections/footer.php'; ?>

    <style>
    :root { --gold:#ffca28; --red:#d43f3f; --dark:#222; }
    body { background:#fff; font-family:'Helvetica Neue',Arial,sans-serif; }
    .container { max-width:1380px; padding:0 20px; }
        
    /* Judul Kategori */
    .cat-title {
        font-size: 2.8rem; font-weight:900; text-align:center; 
        margin: 80px 0 50px; color:var(--dark); position:relative;
    }
    .cat-title::after {
        content:''; width:120px; height:6px; background:var(--gold); 
        position:absolute; bottom:-15px; left:50%; transform:translateX(-50%); border-radius:3px;
    }

    /* Grid Menu */
    .menu-grid {
        display:grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 50px 30px;
        padding: 20px 0;
    }
    .menu-item {
        text-align:center; cursor:pointer; transition:all .3s ease;
        padding:20px; border-radius:16px;
    }
    .menu-item:hover { 
        transform: translateY(-12px) scale(1.04); 
        background:#fff; box-shadow:0 20px 40px rgba(0,0,0,.1);
    }
    .menu-item img {
        width:100%; height:240px; object-fit:contain; 
        filter:drop-shadow(0 15px 25px rgba(0,0,0,.15));
        background:transparent;
    }
    .menu-name {
        font-size:1.4rem; font-weight:800; margin:16px 0 6px; color:var(--dark);
    }
    .menu-price {
        font-size:1.45rem; font-weight:900; color:var(--red);
    }

    /* Badge BARU */
    .badge-new {
        position:absolute; top:10px; left:50%; transform:translateX(-50%);
        background:#ff3b30; color:white; padding:6px 18px; border-radius:20px;
        font-size:0.8rem; font-weight:bold; z-index:10;
    }

    /* Tombol Keranjang */
    .cart-float {
        position:fixed; bottom:30px; right:30px; z-index:9999;
    }
    .cart-float button {
        width:72px; height:72px; border-radius:50%; background:var(--gold);
        border:none; font-size:2.2rem; color:#111; box-shadow:0 12px 35px rgba(0,0,0,.3);
    }
    .cart-badge {
        position:absolute; top:-10px; right:-10px; background:#d32f2f;
        color:white; font-size:0.85rem; min-width:28px; height:28px;
        border-radius:50%; display:flex; align-items:center; justify-content:center;
    }
    </style>