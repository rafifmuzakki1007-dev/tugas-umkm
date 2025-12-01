<?php   
require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/../../../app/models/ToppingModel.php';

$id_menu = $_GET['id_menu'] ?? null;

$toppingModel = new ToppingModel($koneksi);
$toppings = $toppingModel->getForSeblak();
?>

<div class="container py-3">
    <div class="text-center mb-4">
        <h4 class="fw-bold">Pilih Topping Seblak Prasmanan</h4>
        <p class="text-muted">Atur jumlah topping sesuai selera kamu</p>
    </div>

        <div class="row g-4">
            <?php foreach ($toppings as $t): ?>
<div class="d-flex justify-content-between align-items-center mb-2 px-1 py-2 border-bottom">
    
    <div>
        <strong><?= $t['nama_topping'] ?></strong><br>
        
        <small>Rp <?= number_format($t['harga'], 0, ',', '.') ?></small>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button 
            class="btn btn-sm btn-warning btnMinus"
            data-id="<?= $t['id_topping'] ?>"
            data-harga="<?= $t['harga'] ?>"
        >-</button>

        <span id="qty-<?= $t['id_topping'] ?>">0</span>

        <button 
            class="btn btn-sm btn-warning btnPlus"
            data-id="<?= $t['id_topping'] ?>"
            data-harga="<?= $t['harga'] ?>"
            data-stok="<?= $t['stok'] ?>"
        >+</button>
    </div>

</div>
<?php endforeach; ?>

        </div>
        </div>

<script>
// Tidak butuh logika apapun di file topping
// Semua event plus/minus dan pengiriman topping ditangani oleh JS di file utama (modal)

document.addEventListener("click", function(e) {
    // Kurangi qty di UI
    if (e.target.classList.contains("btnMinus")) {
        let id = e.target.dataset.id;
        let domQty = document.getElementById("qty-" + id);
        let current = parseInt(domQty.textContent) || 0;
        if (current > 0) domQty.textContent = current - 1;
    }

    // Tambah qty di UI
    if (e.target.classList.contains("btnPlus")) {
        let id = e.target.dataset.id;
        let domQty = document.getElementById("qty-" + id);
        let current = parseInt(domQty.textContent) || 0;
        domQty.textContent = current + 1;
    }
});
</script>


<style>
    .topping-card {
        position: relative;
        background: #fff;
        border-radius: 15px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 15px;
        transition: 0.2s ease;
    }

    .topping-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }

    .topping-habis {
        opacity: 0.4;
    }

    .topping-habis .qty-btn {
        pointer-events: none;
    }

    .topping-info {
        flex: 1;
    }

    .topping-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
    }

    .topping-price {
        color: #777;
        font-size: 0.9rem;
        margin: 0;
    }

    .topping-counter {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .qty-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 2px solid #ffc107;
        background: #fff3cd;
        color: #b57f00;
        font-size: 20px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
        cursor: pointer;
    }

    .qty-btn:hover {
        background: #ffe08a;
    }

    .qty-display {
        width: 45px;
        height: 36px;
        text-align: center;
        font-size: 1.2rem;
        border: 1.4px solid #ddd;
        border-radius: 6px;
        background: #fff;
    }

    .topping-image-wrapper {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .topping-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .topping-image.placeholder {
        background: #eee;
    }

    .sticky-footer {
        position: sticky;
        bottom: 0;
        background: #fff;
        padding: 15px;
        border-top: 2px solid #f1f1f1;
        z-index: 50;
        margin-top: 30px;
    }
</style>
