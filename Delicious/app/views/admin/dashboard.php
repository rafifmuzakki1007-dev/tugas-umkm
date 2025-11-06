<?php
require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/../../../app/models/MenuModel.php';

$menuModel = new MenuModel($koneksi);
$menus = $menuModel->getAllMenu();

// Statistik stok
$totalMenu = count($menus);
$menuHabis = count(array_filter($menus, fn($m) => $m['stok'] <= 0));
$menuHampir = count(array_filter($menus, fn($m) => $m['stok'] > 0 && $m['stok'] <= 3));

// Data stok rendah
$lowStockMenus = array_filter($menus, fn($m) => $m['stok'] > 0 && $m['stok'] <= 3);

// Data chart stok
$menuNames = array_column($menus, 'nama_menu');
$menuStock = array_column($menus, 'stok');


// Statistik Penjualan Realtime
$today = date("Y-m-d");

// Total Pendapatan Hari Ini
$qPendapatan = $koneksi->query("
    SELECT SUM(total_harga) as pendapatan 
    FROM transaksi 
    WHERE DATE(tgl_transaksi) = '$today'
");
$pendapatan = $qPendapatan->fetch(PDO::FETCH_ASSOC)['pendapatan'] ?? 0;

// Jumlah transaksi hari ini
$qTransaksi = $koneksi->query("
    SELECT COUNT(*) as jumlah 
    FROM transaksi 
    WHERE DATE(tgl_transaksi) = '$today'
");
$jumlahTransaksi = $qTransaksi->fetch(PDO::FETCH_ASSOC)['jumlah'] ?? 0;

// Menu Terlaris
$qBest = $koneksi->query("
    SELECT m.nama_menu, SUM(t.jumlah) as total_jual
    FROM transaksi t 
    JOIN menu m ON t.id_menu = m.id_menu
    GROUP BY m.nama_menu
    ORDER BY total_jual DESC
    LIMIT 1
");
$favoriteMenu = $qBest->fetch(PDO::FETCH_ASSOC)['nama_menu'] ?? "-";

// Grafik penjualan 7 hari terakhir
$qChart = $koneksi->query("
    SELECT DATE(tgl_transaksi) as tanggal, SUM(total_harga) as total 
    FROM transaksi
    GROUP BY DATE(tgl_transaksi)
    ORDER BY tanggal DESC 
    LIMIT 7
");
$chartData = $qChart->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.stat-card {
    transition: all .25s ease;
    cursor: pointer;
}
.stat-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 8px 24px rgba(0,0,0,0.10);
}
.low-stock-box {
    background: var(--card);
    border-radius: 14px;
    padding: 18px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    transition: .2s;
}
.low-stock-box:hover { transform: scale(1.01); }
.low-stock-item { padding: 8px 0; border-bottom: 1px solid rgba(0,0,0,0.06); }
.low-stock-item:last-child { border-bottom: none; }
.badge-warning-soft { background: rgba(255,193,7,.15); color: #e5a800; font-weight: 600; }
</style>


<div class="page-fade">

<h3 class="fw-bold mb-4">Dashboard Admin 👋</h3>

<!-- STATISTIK PENJUALAN REALTIME -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card p-4 shadow-sm rounded-4" style="background:var(--card)">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-stack fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold">Rp <?= number_format($pendapatan,0,',','.') ?></h4>
                    <small>Pendapatan Hari Ini</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card p-4 shadow-sm rounded-4" style="background:var(--card)">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-3 bg-info bg-opacity-10 text-info">
                    <i class="bi bi-receipt-cutoff fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold"><?= $jumlahTransaksi ?></h4>
                    <small>Transaksi Hari Ini</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card p-4 shadow-sm rounded-4" style="background:var(--card)">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-star-fill fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold"><?= $favoriteMenu ?></h4>
                    <small>Menu Terlaris</small>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- STATISTIK STOK -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card p-4 shadow-sm rounded-4" style="background:var(--card)">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-grid-1x2 fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold"><?= $totalMenu ?></h4>
                    <small>Total Menu</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card p-4 shadow-sm rounded-4" style="background:var(--card)">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-3 bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-x-octagon fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold"><?= $menuHabis ?></h4>
                    <small>Menu Habis</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card p-4 shadow-sm rounded-4" style="background:var(--card)">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-box-seam fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold"><?= $menuHampir ?></h4>
                    <small>Stok Rendah</small>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Grafik Penjualan -->
<h5 class="fw-bold mb-3 mt-4">📈 Grafik Penjualan (7 Hari)</h5>
<div class="card p-4 shadow-sm rounded-4 mb-4" style="background:var(--card); min-height:420px;">
    <canvas id="salesChart" style="width:100%; height:300px;"></canvas>
</div>


<!-- Grafik Stok Menu -->
<h5 class="fw-bold mb-3 mt-4">📊 Statistik Stok Menu</h5>
<div class="card p-4 shadow-sm rounded-4 mb-4" style="background:var(--card); min-height:420px;">
    <canvas id="stockChart" style="width:100%; height:300px;"></canvas>
</div>


<!-- List Stok Rendah -->
<h5 class="fw-bold mb-3 mt-4">⚠️ Menu Stok Rendah</h5>
<div class="low-stock-box">
    <?php if (empty($lowStockMenus)): ?>
        <div class="text-muted">Semua stok aman ✅</div>
    <?php else: ?>
        <?php foreach ($lowStockMenus as $m): ?>
        <div class="low-stock-item d-flex justify-content-between align-items-center">
            <span><b><?= $m['nama_menu'] ?></b></span>
            <span class="badge badge-warning-soft"><?= $m['stok'] ?> pcs</span>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


</div>


<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// --- Chart Penjualan ---
const salesLabels = <?= json_encode(array_column($chartData, 'tanggal')); ?>;
const salesData = <?= json_encode(array_column($chartData, 'total')); ?>;

new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: salesLabels.reverse(),
        datasets: [{
            label: "Pendapatan",
            data: salesData.reverse(),
            borderWidth: 3,
            tension: .4
        }]
    },
    options: {
        plugins:{ legend:{ display:false }},
        scales:{ y:{ beginAtZero:true } }
    }
});


// --- Chart Stok ---
new Chart(document.getElementById('stockChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($menuNames) ?>,
        datasets: [{
            label: 'Stok Menu',
            data: <?= json_encode($menuStock) ?>,
            borderWidth: 2,
            borderColor: 'rgba(54,162,235,1)',
            backgroundColor: 'rgba(54,162,235,0.5)',
            borderRadius: 12
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>
