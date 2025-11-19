<?php
require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/../../../app/models/MenuModel.php';

$menuModel = new MenuModel($koneksi);
$menus = $menuModel->getAllMenu();

// --- Statistik stok ---
$totalMenu = count($menus);
$menuHabis = count(array_filter($menus, fn($m) => isset($m['stok']) && (int)$m['stok'] <= 0));
$menuHampir = count(array_filter($menus, fn($m) => isset($m['stok']) && (int)$m['stok'] > 0 && (int)$m['stok'] <= 3));

// Data stok rendah
$lowStockMenus = array_filter($menus, fn($m) => isset($m['stok']) && (int)$m['stok'] > 0 && (int)$m['stok'] <= 3);

// Data chart stok (nama + stok numeric)
$menuNames = array_map(fn($m) => $m['nama_menu'] ?? '-', $menus);
$menuStock = array_map(fn($m) => isset($m['stok']) ? (int)$m['stok'] : 0, $menus);

// --- Statistik Penjualan Realtime ---
$today = date("Y-m-d");

// Total Pendapatan Hari Ini
$qPendapatan = $koneksi->query("
    SELECT SUM(total_harga) as pendapatan 
    FROM transaksi 
    WHERE DATE(tgl_transaksi) = '$today'
");
$pendapatan = (float)($qPendapatan->fetch(PDO::FETCH_ASSOC)['pendapatan'] ?? 0);

// Jumlah transaksi hari ini
$qTransaksi = $koneksi->query("
    SELECT COUNT(*) as jumlah 
    FROM transaksi 
    WHERE DATE(tgl_transaksi) = '$today'
");
$jumlahTransaksi = (int)($qTransaksi->fetch(PDO::FETCH_ASSOC)['jumlah'] ?? 0);

// Menu Terlaris (overall)
$qBest = $koneksi->query("
    SELECT m.nama_menu, SUM(t.jumlah) as total_jual
    FROM transaksi t 
    JOIN menu m ON t.id_menu = m.id_menu
    GROUP BY m.nama_menu
    ORDER BY total_jual DESC
    LIMIT 1
");
$favoriteMenu = $qBest->fetch(PDO::FETCH_ASSOC)['nama_menu'] ?? "-";

// Grafik penjualan 7 hari terakhir (urut ascending)
$qChart = $koneksi->query("
    SELECT DATE(tgl_transaksi) as tanggal, SUM(total_harga) as total 
    FROM transaksi
    GROUP BY DATE(tgl_transaksi)
    ORDER BY tanggal DESC 
    LIMIT 7
");
$chartData = $qChart->fetchAll(PDO::FETCH_ASSOC);
$chartData = array_reverse($chartData);

// Grafik pendapatan 12 bulan terakhir (per bulan)
$qMonthly = $koneksi->query("
    SELECT DATE_FORMAT(tgl_transaksi, '%Y-%m') as ym, SUM(total_harga) as total 
    FROM transaksi
    GROUP BY ym
    ORDER BY ym DESC
    LIMIT 12
");
$monthlyRaw = $qMonthly->fetchAll(PDO::FETCH_ASSOC);
$monthlyRaw = array_reverse($monthlyRaw);
$monthlyLabels = array_map(fn($r) => $r['ym'], $monthlyRaw);
$monthlyTotals = array_map(fn($r) => (float)$r['total'], $monthlyRaw);

// --- AJAX endpoint untuk realtime refresh (dipanggil oleh JS) ---
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json');

    // ambil ulang realtime singkat (pendapatan hari ini, transaksi, best, chart7)
    $today = date("Y-m-d");
    $pendapatan = (float)($koneksi->query("SELECT SUM(total_harga) as pendapatan FROM transaksi WHERE DATE(tgl_transaksi) = '$today'")->fetch(PDO::FETCH_ASSOC)['pendapatan'] ?? 0);
    $jumlahTransaksi = (int)($koneksi->query("SELECT COUNT(*) as jumlah FROM transaksi WHERE DATE(tgl_transaksi) = '$today'")->fetch(PDO::FETCH_ASSOC)['jumlah'] ?? 0);
    $favoriteMenu = $koneksi->query("
        SELECT m.nama_menu, SUM(t.jumlah) as total_jual
        FROM transaksi t 
        JOIN menu m ON t.id_menu = m.id_menu
        GROUP BY m.nama_menu
        ORDER BY total_jual DESC
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC)['nama_menu'] ?? "-";
    $chartData = $koneksi->query("
        SELECT DATE(tgl_transaksi) as tanggal, SUM(total_harga) as total 
        FROM transaksi
        GROUP BY DATE(tgl_transaksi)
        ORDER BY tanggal DESC 
        LIMIT 7
    ")->fetchAll(PDO::FETCH_ASSOC);
    $chartData = array_reverse($chartData);

    // stock (ambil nama & stok realtime)
    $menusRealtime = $menuModel->getAllMenu();
    $menuNamesRealtime = array_map(fn($m) => $m['nama_menu'] ?? '-', $menusRealtime);
    $menuStockRealtime = array_map(fn($m) => isset($m['stok']) ? (int)$m['stok'] : 0, $menusRealtime);

    // monthly
    $monthlyRaw = $koneksi->query("
        SELECT DATE_FORMAT(tgl_transaksi, '%Y-%m') as ym, SUM(total_harga) as total 
        FROM transaksi
        GROUP BY ym
        ORDER BY ym DESC
        LIMIT 12
    ")->fetchAll(PDO::FETCH_ASSOC);
    $monthlyRaw = array_reverse($monthlyRaw);
    $monthlyLabels = array_map(fn($r) => $r['ym'], $monthlyRaw);
    $monthlyTotals = array_map(fn($r) => (float)$r['total'], $monthlyRaw);

    echo json_encode([
        'pendapatan' => $pendapatan,
        'jumlahTransaksi' => $jumlahTransaksi,
        'favoriteMenu' => $favoriteMenu,
        'chart7' => $chartData,
        'stock' => ['labels' => $menuNamesRealtime, 'data' => $menuStockRealtime],
        'monthly' => ['labels' => $monthlyLabels, 'data' => $monthlyTotals]
    ], JSON_NUMERIC_CHECK);
    exit;
}

?>
<!-- UI: DashboardKit-like layout -->
<style>
/* Card + grid styling (DashboardKit feel) */
.page-fade { padding: 18px 28px; }
.dk-grid { display:grid; grid-template-columns: repeat(3, 1fr); gap:18px; align-items:stretch; }
.dk-card { background:var(--card); border-radius:12px; padding:18px; box-shadow: 0 6px 24px rgba(20,30,40,0.04); }
.dk-stat { display:flex; gap:14px; align-items:center; }
.dk-stat .icon { width:52px; height:52px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; }
.text-muted-sm { color:rgba(0,0,0,0.45); font-size:13px; }

/* charts */
.chart-card { background:var(--card); border-radius:14px; padding:18px; box-shadow: 0 6px 24px rgba(0,0,0,0.06); }
.chart-wrapper { overflow-x:auto; }
.chart-inner { min-width: 720px; }

/* stock chart: if many items, use horizontal bars and adjust height */
.stock-wrapper { overflow:auto; }
.stock-canvas { width:100%; }

/* low stock list */
.low-stock-box { background:var(--card); border-radius:12px; padding:12px; box-shadow:0 6px 18px rgba(0,0,0,0.04); }

/* calendar mini */
.calendar { background:var(--card); border-radius:12px; padding:12px; width:260px; box-shadow:0 6px 18px rgba(0,0,0,0.04); }
.calendar .cal-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
.calendar table { width:100%; border-collapse:collapse; table-layout:fixed; text-align:center; }
.calendar td, .calendar th { padding:6px; font-size:13px; }
.calendar .today { background:#1d4ed8; color:#fff; border-radius:6px; }

/* responsive tweaks */
@media (max-width: 1100px) {
    .dk-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 720px) {
    .dk-grid { grid-template-columns: 1fr; }
}
</style>

<div class="page-fade">

    <h2 class="fw-bold mb-3"> <span style="font-size:18px"</span></h2>

    <div class="dk-header mb-4">
    <div class="dk-header-icon">
        <i class="bi bi-speedometer2"></i>
    </div>
    <div>
        <h2 class="dk-header-title">Dashboard Admin</h2>
        <div class="dk-header-sub">Ringkasan aktivitas & performa sistem secara realtime</div>
    </div>
</div>

<style>
.dk-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 22px;
    background: var(--card);
    padding: 14px 18px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.04);
}

.dk-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 14px;
    background: rgba(59,130,246,0.12);
    color: #2563eb;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size: 28px;
}

.dk-header-title {
    margin: 0;
    font-size: 26px;
    font-weight: 700;
}

.dk-header-sub {
    font-size: 14px;
    color: var(--subtext);
    margin-top: -2px;
}
</style>

    <!-- Top stat grid (3 columns) -->
    <div class="dk-grid mb-4">
        <div class="dk-card dk-stat">
            <div class="icon" style="background:rgba(16,185,129,0.12); color:#059669"><i class="bi bi-cash-stack fs-4"></i></div>
            <div>
                <div style="font-size:18px; font-weight:700" id="pendapatanText"><?= 'Rp '.number_format($pendapatan,0,',','.') ?></div>
                <div class="text-muted-sm">Pendapatan Hari Ini</div>
            </div>
        </div>

        <div class="dk-card dk-stat">
            <div class="icon" style="background:rgba(6,182,212,0.12); color:#0891b2"><i class="bi bi-receipt-cutoff fs-4"></i></div>
            <div>
                <div style="font-size:18px; font-weight:700" id="transaksiText"><?= $jumlahTransaksi ?></div>
                <div class="text-muted-sm">Transaksi Hari Ini</div>
            </div>
        </div>

        <div class="dk-card dk-stat">
            <div class="icon" style="background:rgba(59,130,246,0.12); color:#2563eb"><i class="bi bi-star-fill fs-4"></i></div>
            <div>
                <div style="font-size:18px; font-weight:700" id="favoriteText"><?= htmlspecialchars($favoriteMenu) ?></div>
                <div class="text-muted-sm">Menu Terlaris</div>
            </div>
        </div>
    </div>

    <!-- secondary stat grid -->
    <div class="dk-grid mb-4">
        <div class="dk-card">
            <div class="dk-stat">
                <div class="icon" style="background:rgba(99,102,241,0.08); color:#4f46e5"><i class="bi bi-grid-1x2 fs-4"></i></div>
                <div>
                    <div style="font-size:18px; font-weight:700"><?= $totalMenu ?></div>
                    <div class="text-muted-sm">Total Menu</div>
                </div>
            </div>
        </div>

        <div class="dk-card">
            <div class="dk-stat">
                <div class="icon" style="background:rgba(239,68,68,0.08); color:#dc2626"><i class="bi bi-x-octagon fs-4"></i></div>
                <div>
                    <div style="font-size:18px; font-weight:700"><?= $menuHabis ?></div>
                    <div class="text-muted-sm">Menu Habis</div>
                </div>
            </div>
        </div>

        <div class="dk-card">
            <div class="dk-stat">
                <div class="icon" style="background:rgba(245,158,11,0.08); color:#c2410c"><i class="bi bi-box-seam fs-4"></i></div>
                <div>
                    <div style="font-size:18px; font-weight:700"><?= $menuHampir ?></div>
                    <div class="text-muted-sm">Stok Rendah</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row gx-4">
        <div class="col-lg-8">
            <!-- Grafik Penjualan (7 Hari) -->
            <div class="chart-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">📈 Grafik Penjualan (7 Hari)</h5>
                    <small class="text-muted-sm">Realtime</small>
                </div>
                <div style="height:380px;">
                    <canvas id="salesChart" style="width:100%; height:100%;"></canvas>
                </div>
            </div>

            <!-- Pendapatan Bulanan (12 bulan) -->
            <div class="chart-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">💹 Pendapatan Bulanan (12 Bulan)</h5>
                    <small class="text-muted-sm">Terakhir 12 bulan</small>
                </div>
                <div style="height:300px;">
                    <canvas id="monthlyChart" style="width:100%; height:100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Statistik Stok Menu (horizontal, scrollable) -->
            <div class="chart-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">📊 Statistik Stok Menu</h5>
                    <small class="text-muted-sm">Realtime</small>
                </div>
                <div class="stock-wrapper" id="stockWrapper" style="max-height:360px; overflow:auto;">
                    <!-- canvas height akan diatur dinamis berdasarkan jumlah menu -->
                    <canvas id="stockChart" class="stock-canvas"></canvas>
                </div>
            </div>

            <!-- Kalender mini -->
            <div class="calendar mb-4">
                <div class="cal-head">
                    <button id="calPrev" class="btn btn-sm btn-outline-secondary">&lsaquo;</button>
                    <div id="calTitle" style="font-weight:700"></div>
                    <button id="calNext" class="btn btn-sm btn-outline-secondary">&rsaquo;</button>
                </div>
                <div id="calBody"></div>
            </div>

            <!-- List stok rendah -->
            <div class="low-stock-box">
                <h6 class="mb-2">⚠️ Menu Stok Rendah</h6>
                <?php if (empty($lowStockMenus)): ?>
                    <div class="text-muted-sm">Semua stok aman ✅</div>
                <?php else: ?>
                    <?php foreach ($lowStockMenus as $m): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div><b><?= htmlspecialchars($m['nama_menu']) ?></b></div>
                        <div class="text-muted-sm"><?= htmlspecialchars((int)$m['stok']) ?> pcs</div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- ChartJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* ---------- helper ---------- */
function formatRupiah(num){
    return 'Rp ' + Number(num).toLocaleString('id-ID', {maximumFractionDigits:0});
}

/* ---------- Sales 7 hari ---------- */
const salesRaw = <?= json_encode($chartData, JSON_NUMERIC_CHECK) ?> || [];
const salesLabels = salesRaw.map(r => r.tanggal);
const salesData = salesRaw.map(r => Number(r.total || 0));

const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: salesLabels,
        datasets: [{
            label: "Pendapatan",
            data: salesData,
            borderWidth: 3,
            tension: .35,
            fill: true,
            backgroundColor: 'rgba(59,130,246,0.10)',
            borderColor: 'rgba(59,130,246,1)',
            pointRadius: 5,
            pointBackgroundColor: '#fff',
            pointBorderWidth: 3
        }]
    },
    options: {
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: { label: (ctx) => formatRupiah(ctx.raw) }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: val => 'Rp ' + Number(val).toLocaleString('id-ID')
                }
            },
            x: { ticks: { autoSkip: true, maxRotation: 0, minRotation: 0 } }
        },
        responsive: true,
        maintainAspectRatio: false
    }
});

/* ---------- Monthly (12 bulan) ---------- */
const monthlyLabels = <?= json_encode($monthlyLabels) ?> || [];
const monthlyData = <?= json_encode($monthlyTotals, JSON_NUMERIC_CHECK) ?> || [];

// convert 'YYYY-MM' => 'Mon YYYY' for readability
function prettyMonth(ym){
    if(!ym) return ym;
    const [y,m] = ym.split('-').map(s=>parseInt(s,10));
    const d = new Date(y, m-1, 1);
    return d.toLocaleString('id-ID', { month: 'short', year: 'numeric' }); // e.g. "Nov 2025"
}
const monthlyLabelsPretty = monthlyLabels.map(prettyMonth);

const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: monthlyLabelsPretty,
        datasets: [{
            label: "Pendapatan Bulanan",
            data: monthlyData,
            backgroundColor: 'rgba(16,185,129,0.12)',
            borderColor: 'rgba(16,185,129,0.9)',
            borderWidth: 2,
            borderRadius: 6
        }]
    },
    options: {
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: (ctx) => formatRupiah(ctx.raw) } }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: val => 'Rp ' + Number(val).toLocaleString('id-ID') } },
            x: { ticks: { autoSkip: false, maxRotation: 0 } }
        },
        responsive: true,
        maintainAspectRatio: false
    }
});

/* ---------- Stock (horizontal bars) ---------- */
let stockLabels = <?= json_encode($menuNames) ?> || [];
let stockData = <?= json_encode($menuStock, JSON_NUMERIC_CHECK) ?> || [];

const stockCanvas = document.getElementById('stockChart');
const stockCtx = stockCanvas.getContext('2d');

// set canvas height dynamically based on number of items for horizontal bars
function adjustStockCanvasHeight(labels){
    const perItemHeight = 36; // px per item
    const base = 180; // minimum height
    const height = Math.max(base, labels.length * perItemHeight);
    stockCanvas.height = height;
    stockCanvas.style.height = height + 'px';
}
adjustStockCanvasHeight(stockLabels);

const stockChart = new Chart(stockCtx, {
    type: 'bar',
    data: {
        labels: stockLabels,
        datasets: [{
            label: 'Stok Menu',
            data: stockData,
            borderWidth: 1,
            backgroundColor: 'rgba(59,130,246,0.7)',
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y', // horizontal bars (best for many items)
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { stepSize: 1 } },
            y: { ticks: { autoSkip: false } }
        },
        responsive: true,
        maintainAspectRatio: false
    }
});

/* ---------- Simple Calendar (pure JS, client-side) ---------- */
(function(){
    const now = new Date();
    let viewYear = now.getFullYear();
    let viewMonth = now.getMonth(); // 0-index

    const calTitle = document.getElementById('calTitle');
    const calBody = document.getElementById('calBody');
    function renderCalendar(y,m){
        calTitle.innerText = new Date(y,m,1).toLocaleString('id-ID', { month:'long', year:'numeric' });
        const firstDay = new Date(y,m,1).getDay(); // 0 Sun .. 6 Sat
        const daysInMonth = new Date(y, m+1, 0).getDate();

        // create table
        let html = '<table><thead><tr>';
        const days = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
        for(const d of days) html += '<th>'+d+'</th>';
        html += '</tr></thead><tbody><tr>';

        // pad empty cells (firstDay: convert Sunday(0) to 0)
        let cur = 0;
        for(let i=0;i<firstDay;i++){ html += '<td></td>'; cur++; }

        for(let d=1; d<=daysInMonth; d++){
            const isToday = (y===now.getFullYear() && m===now.getMonth() && d===now.getDate());
            html += isToday ? `<td class="today">${d}</td>` : `<td>${d}</td>`;
            cur++;
            if(cur%7===0 && d!==daysInMonth) html += '</tr><tr>';
        }
        // pad end
        while(cur%7!==0){ html += '<td></td>'; cur++; }
        html += '</tr></tbody></table>';
        calBody.innerHTML = html;
    }
    document.getElementById('calPrev').addEventListener('click', ()=>{
        viewMonth--; if(viewMonth<0){ viewMonth=11; viewYear--; } renderCalendar(viewYear, viewMonth);
    });
    document.getElementById('calNext').addEventListener('click', ()=>{
        viewMonth++; if(viewMonth>11){ viewMonth=0; viewYear++; } renderCalendar(viewYear, viewMonth);
    });
    renderCalendar(viewYear, viewMonth);
})();

/* ---------- Realtime polling (AJAX) ---------- */
const ajaxUrl = (() => {
    // relative url: keep same index.php?page=dashboard param
    const params = new URLSearchParams(window.location.search);
    params.set('page','dashboard');
    params.set('ajax','1');
    return window.location.pathname + '?' + params.toString();
})();

async function refreshRealtime(){
    try {
        const res = await fetch(ajaxUrl);
        if (!res.ok) throw new Error('Network');
        const data = await res.json();

        // update stat cards
        document.getElementById('pendapatanText').innerText = formatRupiah(Number(data.pendapatan || 0));
        document.getElementById('transaksiText').innerText = (data.jumlahTransaksi || 0);
        document.getElementById('favoriteText').innerText = (data.favoriteMenu || '-');

        // update sales 7 days
        if (Array.isArray(data.chart7)) {
            const chart7 = data.chart7.map(r => ({ t: r.tanggal, v: Number(r.total||0) }));
            salesChart.data.labels = chart7.map(r => r.t);
            salesChart.data.datasets[0].data = chart7.map(r => r.v);
            salesChart.update();
        }

        // update monthly
        if (data.monthly && Array.isArray(data.monthly.labels)) {
            const labelsPretty = data.monthly.labels.map(y => {
                const [yy,mm] = y.split('-').map(Number);
                const d = new Date(yy, mm-1, 1);
                return d.toLocaleString('id-ID', { month:'short', year:'numeric' });
            });
            monthlyChart.data.labels = labelsPretty;
            monthlyChart.data.datasets[0].data = data.monthly.data;
            monthlyChart.update();
        }

        // update stock (recreate heights if counts changed)
        if (data.stock && Array.isArray(data.stock.labels)) {
            const newLabels = data.stock.labels;
            const newData = data.stock.data.map(x => Number(x || 0));
            // adjust canvas height
            (function(){
                const perItemHeight = 36;
                const base = 180;
                const h = Math.max(base, newLabels.length * perItemHeight);
                stockCanvas.height = h;
                stockCanvas.style.height = h + 'px';
            })();
            stockChart.data.labels = newLabels;
            stockChart.data.datasets[0].data = newData;
            stockChart.update();
        }

    } catch (err) {
        console.warn('Realtime refresh failed', err);
    } finally {
        // schedule next
        setTimeout(refreshRealtime, 30000); // 30s
    }
}

// start polling after short delay
setTimeout(refreshRealtime, 2000);

</script>
