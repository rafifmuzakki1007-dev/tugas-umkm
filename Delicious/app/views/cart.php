<?php  
// cart.php — FINAL SYNC (ID => QTY) + EMPTY STATE FIX + TRASH ICON PRESISI
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/koneksi.php';

/*
  FORMAT CART:
  $_SESSION['cart'] = [
      'TP001' => 2,
      'MN001' => 1
  ];
*/

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
} else {
    $normalized = [];
    foreach ($_SESSION['cart'] as $k => $v) {
        $id  = null;
        $qty = 0;

        if (is_array($v)) {
            $id  = $v['id'] ?? $k;
            $qty = isset($v['qty']) ? (int)$v['qty'] : 1;
        } elseif (is_int($v) || ctype_digit((string)$v)) {
            $id  = $k;
            $qty = (int)$v;
        } elseif (is_string($v)) {
            $id  = $v;
            $qty = 1;
        }

        if ($id) {
            if ($qty < 1) $qty = 1;
            if (!isset($normalized[$id])) $normalized[$id] = 0;
            $normalized[$id] += $qty;
        }
    }
    $_SESSION['cart'] = $normalized;
}

function fetch_item_from_db($pdo, $id) {
    // topping
    $stmt = $pdo->prepare("
        SELECT id_topping AS id, nama_topping AS nama, harga, gambar, 'topping' AS jenis
        FROM topping WHERE id_topping = :id LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) return $r;

    // menu (minuman, camilan, dll)
    $stmt = $pdo->prepare("
        SELECT id_menu AS id, nama_menu AS nama, harga_dasar AS harga, gambar, kategori AS jenis
        FROM menu WHERE id_menu = :id LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) return $r;

    return null;
}

$items = [];
$grandTotal = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    $qty = (int)$qty;
    if ($qty < 1) $qty = 1;

    $row = fetch_item_from_db($koneksi, $id);
    if (!$row) continue;

    $harga    = (int)($row['harga'] ?? 0);
    $subtotal = $harga * $qty;

    $items[] = [
        'id'       => $row['id'],
        'nama'     => $row['nama'],
        'jenis'    => $row['jenis'],
        'harga'    => $harga,
        'qty'      => $qty,
        'subtotal' => $subtotal,
        'gambar'   => $row['gambar'] ?? ''
    ];
    $grandTotal += $subtotal;
}
?>
<style>
.cart-root{
  max-width: 650px;          /* biar nggak terlalu lebar */
  margin: 0 auto;            /* center di dalam modal */
  font-family: 'Inter', system-ui, sans-serif;
  color:#222;
}

/* EMPTY STATE */
.empty-box{
  width:100%;
  padding:60px 10px;
  text-align:center;
  font-size:1.1rem;
  color:#555;
}
.empty-box i{
  font-size:2.4rem;
  color:#b5b5b5;
  margin-bottom:12px;
}
.btn-empty-close{
  margin-top:20px;
  background:#ffca28;
  border:none;
  padding:10px 28px;
  font-weight:600;
  border-radius:10px;
  cursor:pointer;
}

/* NORMAL TABLE */
.cart-img{
  width:60px;
  height:60px;
  border-radius:14px;
  object-fit:cover;
}
.cart-row-wrapper{
  display:flex;
  align-items:center;
  justify-content:center;
  height:58px;
}

/* ICON TRASH PRESISI */
.btn-delete-cart{
  width:36px;
  height:36px;
  display:flex;
  align-items:center;
  justify-content:center;
  border:none;
  background:#ffecec;
  color:#d33535;
  border-radius:10px;
  transition:0.2s;
}
.btn-delete-cart:hover{
  background:#ffcccc;
  transform:scale(1.08);
}
.btn-delete-cart i{
  font-size:17px;
}

/* FOOTER */
.cart-grand-label{
  font-size:0.9rem;
  color:#555;
}
.cart-grand-value{
  font-size:1.1rem;
  font-weight:900;
  color:#d43f3f;
}
.cart-footer-actions{
  display:flex;
  justify-content:flex-end;
  gap:10px;
  margin-top:12px;
}
.cart-footer-actions .btn{
  border-radius:999px;
  padding:7px 16px;
  font-weight:600;
}

/* Mobile */
@media (max-width: 576px){
  .cart-root{ max-width:100%; }
  .cart-table thead{ display:none; }
  .cart-table tbody tr{
    display:block;
    padding:10px 0;
    border-bottom:1px solid rgba(0,0,0,0.05);
  }
  .cart-table td{
    display:block;
    width:100%;
    text-align:left !important;
  }
  .cart-table td:nth-child(5){
    text-align:right !important;
  }
}
</style>

<div class="cart-root">

<?php if (empty($items)): ?>

  <!-- EMPTY CART -->
  <div class="empty-box">
    <i class="bi bi-cart"></i><br>
    Keranjang masih kosong
    <br>
    <!-- data-bs-dismiss="modal" supaya selalu nutup modal bootstrap -->
    <button type="button" class="btn-empty-close" data-bs-dismiss="modal">
      Tutup
    </button>
  </div>

<?php else: ?>

  <table class="table align-middle cart-table mb-2">
    <thead>
      <tr>
        <th>Menu</th>
        <th class="text-center" width="60">Qty</th>
        <th class="text-center" width="120">Harga</th>
        <th class="text-center" width="120">Total</th>
        <th class="text-center" width="80">Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $it):
        $fileMenu    = 'assets/img/menu/' . ($it['gambar'] ?? '');
        $fileTopping = 'assets/img/topping/' . ($it['gambar'] ?? '');
        $isTopping   = ($it['jenis'] ?? '') === 'topping';
        $img         = $isTopping ? $fileTopping : $fileMenu;
        if (!file_exists(__DIR__ . '/../../' . $img)) $img = 'assets/img/menu/no-image.png';
    ?>
      <tr>
        <td>
          <div style="display:flex;gap:12px;align-items:center;">
            <img src="<?= $img ?>" class="cart-img" alt="">
            <div>
              <div style="font-weight:700;font-size:0.97rem;">
                <?= htmlspecialchars($it['nama']) ?>
              </div>
              <div style="font-size:0.8rem;color:#777;">
                <?= $isTopping ? 'Topping' : 'Kategori: ' . htmlspecialchars($it['jenis']) ?>
              </div>
            </div>
          </div>
        </td>

        <td class="text-center"><strong><?= $it['qty'] ?></strong>x</td>
        <td class="text-center">Rp <?= number_format($it['harga'],0,',','.') ?></td>
        <td class="text-center"><strong>Rp <?= number_format($it['subtotal'],0,',','.') ?></strong></td>

        <td>
          <div class="cart-row-wrapper">
            <button type="button" class="btn-delete-cart" data-id="<?= htmlspecialchars($it['id']) ?>">
              <i class="bi bi-trash-fill"></i>
            </button>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
    <div class="cart-grand-label">Grand Total</div>
    <div class="cart-grand-value">Rp <?= number_format($grandTotal,0,',','.') ?></div>
  </div>

  <div class="cart-footer-actions">
    <a href="index.php?page=menu" class="btn btn-outline-secondary btn-sm">Tambah Menu</a>
    <button class="btn btn-warning btn-sm" onclick="openCheckout()">Checkout</button>
  </div>

<?php endif; ?>

</div>
