<?php  
// order_success.php — FINAL PROFESSIONAL RECEIPT STYLE (dengan perbaikan path dan validasi)
require_once 'config/koneksi.php';

$id_transaksi = $_GET['id'] ?? null;

// Validasi ID
if (!$id_transaksi) {
    echo "<h3>ID transaksi tidak valid</h3>";
    exit;
}

// Ambil transaksi
$stmt = $koneksi->prepare("
    SELECT id_transaksi, tanggal, metode_bayar, total_harga, status, bukti_transfer, customer_name, customer_phone
    FROM transaksi WHERE id_transaksi = :id
");
$stmt->execute([':id' => $id_transaksi]);
$trx = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trx) { 
    echo "<h3>Transaksi tidak ditemukan</h3>"; 
    exit;
}

// Ambil detail topping
$stmt2 = $koneksi->prepare("
    SELECT d.qty, d.harga_satuan, t.nama_topping, t.gambar
    FROM detail_transaksi d
    JOIN topping t ON d.id_topping = t.id_topping
    WHERE d.id_transaksi = :id
");
$stmt2->execute([':id' => $id_transaksi]);
$details = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Ambil data menu dasar (seblak) untuk ditampilkan di struk
$baseMenu = null;
try {
    $stmMb = $koneksi->prepare("SELECT id_menu, nama_menu, gambar FROM menu WHERE id_menu = :id LIMIT 1");
    $stmMb->execute([':id' => 'MN001']);
    $baseMenu = $stmMb->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $baseMenu = null;
}

// Path bukti transfer
$buktiPath = null;
if (!empty($trx['bukti_transfer'])) {
    $file = 'assets/uploads/bukti/' . $trx['bukti_transfer'];
    if (file_exists($file)) {
        $buktiPath = $file;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pesanan Berhasil</title>

<style>
:root{
    --accent: #f4c542;
    --accent-dark:#e0ae24;
    --text-dark:#222;
    --text-muted:#777;
    --border:#e6e6e6;
    --glass-bg: rgba(255,255,255,0.80);
    --radius:16px;
    --shadow:0 14px 40px rgba(0,0,0,0.08);
    font-family: 'Inter','Poppins',sans-serif;
}

body{
    margin:0;
    padding:40px 12px;
    background:#fafafa;
    color:var(--text-dark);
}

/* wrapper */
.success-wrapper{
    max-width:560px;
    margin:0 auto;
}

/* card */
.receipt-card{
    background:var(--glass-bg);
    border-radius:var(--radius);
    padding:28px 26px;
    border:1px solid rgba(0,0,0,0.08);
    backdrop-filter: blur(12px);
    box-shadow:var(--shadow);
}

/* header */
.title-success{
    text-align:center;
    font-size:1.9rem;
    font-weight:800;
    color:var(--accent);
    margin-bottom:4px;
}

.subtitle{
    text-align:center;
    color:var(--text-muted);
    margin-bottom:28px;
}

/* section title */
.section-title{
    font-size:1.1rem;
    font-weight:700;
    margin-bottom:10px;
    color:#333;
}

/* transaction info */
.info-row{
    display:flex;
    justify-content:space-between;
    margin-bottom:6px;
    font-size:0.97rem;
}
.info-label{ color:var(--text-muted); }
.info-value{ font-weight:600; }

/* item */
.topping-box{
    border:1px solid var(--border);
    padding:12px;
    border-radius:12px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:12px;
    background:#fff;
}
.topping-left{
    display:flex; align-items:center; gap:12px;
}
.topping-left img{
    width:54px; height:54px; object-fit:cover; border-radius:8px;
}
.topping-name{
    font-weight:700; font-size:.95rem;
}
.qty-price{
    font-size:.85rem; color:var(--text-muted);
}

/* total */
.total-line{
    border-top:1px dashed var(--border);
    padding-top:10px;
    margin-top:10px;
    display:flex;
    justify-content:space-between;
    font-size:1rem;
}
.total-line b{
    font-size:1.1rem;
    color:var(--accent);
}

/* bukti transfer */
.bukti-box{
    text-align:center;
    margin:20px 0 10px 0;
}
.bukti-box img{
    max-width:240px;
    border-radius:12px;
    border:1px solid rgba(0,0,0,0.08);
    box-shadow:0 8px 25px rgba(0,0,0,0.06);
}

/* buttons */
.btn-row{
    margin-top:20px;
    text-align:center;
    display:flex;
    justify-content:center;
    gap:12px;
    flex-wrap:wrap;
}

.btn{
    padding:12px 18px;
    border-radius:10px;
    font-weight:700;
    text-decoration:none;
    font-size:0.95rem;
    cursor:pointer;
}

.btn-dark{
    background:#222;
    color:#fff;
}

.btn-warning{
    background:var(--accent);
    color:#222;
}

.btn-warning:hover{
    background:var(--accent-dark);
}
</style>

</head>
<body>

<div class="success-wrapper">
    <div class="receipt-card">

        <h2 class="title-success">Pesanan Berhasil</h2>
        <div class="subtitle">Terima kasih, pesanan Anda telah kami terima.</div>

        <!-- INFO TRANSAKSI -->
        <div class="section-title">Informasi Transaksi</div>

        <div class="info-row"><div class="info-label">No. Transaksi</div><div class="info-value"><?= htmlspecialchars($trx['id_transaksi']) ?></div></div>
        <div class="info-row"><div class="info-label">Tanggal</div><div class="info-value"><?= date('d M Y, H:i', strtotime($trx['tanggal'])) ?></div></div>
        <div class="info-row"><div class="info-label">Metode Bayar</div><div class="info-value"><?= ucfirst(htmlspecialchars($trx['metode_bayar'])) ?></div></div>
        <div class="info-row"><div class="info-label">Status</div><div class="info-value" style="color:var(--accent);"><?= ucfirst(htmlspecialchars($trx['status'])) ?></div></div>
        <div class="info-row"><div class="info-label">Nama Pembeli</div><div class="info-value"><?= htmlspecialchars($trx['customer_name']) ?></div></div>
        <div class="info-row"><div class="info-label">No. HP</div><div class="info-value"><?= htmlspecialchars($trx['customer_phone'] ?: '-') ?></div></div>

        <!-- BUkti -->
        <?php if ($buktiPath): ?>
        <div class="section-title" style="margin-top:20px;">Bukti Pembayaran</div>
        <div class="bukti-box">
            <img src="<?= htmlspecialchars($buktiPath) ?>" alt="Bukti Pembayaran">
        </div>
        <?php endif; ?>

        <!-- MENU DASAR SEBLAK -->
        <?php if ($baseMenu): ?>
            <div class="section-title" style="margin-top:20px;">Menu</div>
            <div class="topping-box">
                <div class="topping-left">
                    <!-- FIX GAMBAR → pakai folder menu -->
                    <img src="assets/img/menu/<?= htmlspecialchars($baseMenu['gambar']) ?>" alt="">
                    <div>
                        <div class="topping-name"><?= htmlspecialchars($baseMenu['nama_menu']) ?></div>

                        <!-- FIX: Hapus “1 × Rp 0” -->
                        <div class="qty-price"></div>
                    </div>
                </div>

                <!-- FIX: Hapus harga Rp0 -->
                <div style="font-weight:700;"></div>
            </div>
        <?php endif; ?>

        <!-- DETAIL TOPPING -->
        <div class="section-title" style="margin-top:20px;">Detail Pesanan</div>

        <?php foreach($details as $item): ?>
            <div class="topping-box">
                <div class="topping-left">
                    <img src="assets/img/topping/<?= htmlspecialchars($item['gambar']) ?>" alt="">
                    <div>
                        <div class="topping-name"><?= htmlspecialchars($item['nama_topping']) ?></div>
                        <div class="qty-price">
                            <?= intval($item['qty']) ?> × Rp <?= number_format($item['harga_satuan'],0,',','.') ?>
                        </div>
                    </div>
                </div>

                <div style="font-weight:700;">
                    Rp <?= number_format($item['qty'] * $item['harga_satuan'],0,',','.') ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="total-line">
            <div>Total</div>
            <b>Rp <?= number_format($trx['total_harga'], 0, ',', '.') ?></b>
        </div>

        <!-- BUTTON -->
        <div class="btn-row">
            <a href="index.php?page=menu" class="btn btn-dark">Kembali ke Menu</a>
            <button class="btn btn-warning" onclick="window.print()">Cetak Struk</button>
        </div>

    </div>
</div>

</body>
</html>
