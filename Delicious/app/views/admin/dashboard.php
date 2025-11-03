<?php
require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/../../../app/models/MenuModel.php';

$menuModel = new MenuModel($koneksi);
$menus = $menuModel->getAllMenu();

// Statistik
$totalMenu = count($menus);
$menuHabis = count(array_filter($menus, fn($m) => $m['stok'] <= 0));
$menuHampir = count(array_filter($menus, fn($m) => $m['stok'] > 0 && $m['stok'] <= 3));

// Data chart
$menuNames = array_column($menus, 'nama_menu');
$menuStock = array_column($menus, 'stok');
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
</style>

<div class="page-fade">

<h3 class="fw-bold mb-4">Dashboard Admin 👋</h3>

<div class="row g-4 mb-4">

    <!-- Total Menu -->
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

    <!-- Menu Habis -->
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

    <!-- Stok Rendah -->
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

<h5 class="fw-bold mb-3 mt-4">Statistik Stok Menu 📊</h5>

<div class="card p-4 shadow-sm rounded-4" style="background:var(--card); min-height:420px;">
    <canvas id="stockChart" style="width:100%; height:300px;"></canvas>
</div>

</div>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('stockChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($menuNames) ?>,
        datasets: [{
            label: 'Stok Menu',
            data: <?= json_encode($menuStock) ?>,
            borderWidth: 2,
            borderColor: 'rgba(54,162,235,1)',
            backgroundColor: 'rgba(54,162,235,0.5)',
            hoverBackgroundColor: 'rgba(0,180,255,0.8)',
            borderRadius: 12
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                labels: { 
                    boxWidth: 12,
                    color: getComputedStyle(document.body).getPropertyValue('--text-color')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 5,
                    color: getComputedStyle(document.body).getPropertyValue('--text-color')
                }
            },
            x: {
                ticks: {
                    color: getComputedStyle(document.body).getPropertyValue('--text-color')
                }
            }
        }
    }
});
</script>
