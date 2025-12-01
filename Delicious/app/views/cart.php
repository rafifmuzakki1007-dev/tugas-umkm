<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/koneksi.php';

/*
  CART VIEWER — FINAL FIX
  - Handles semua format cart lama & baru
  - Hydrate data dari DB ketika kurang data
  - Memastikan gambar dan jenis item aman
  - Output rapi & stabil
*/

$items = [];
$grandTotal = 0;

/********************************************
 * Helper DB loader
 ********************************************/
function fetch_menu_or_topping($koneksi, $id) {

    // Topping
    $q = $koneksi->prepare("
        SELECT 
            id_topping AS id, 
            nama_topping AS nama, 
            harga, 
            gambar, 
            'topping' AS jenis
        FROM topping 
        WHERE id_topping = :id LIMIT 1
    ");
    $q->execute([':id'=>$id]);
    if ($r = $q->fetch(PDO::FETCH_ASSOC)) return $r;

    // Menu
    $q2 = $koneksi->prepare("
        SELECT 
            id_menu AS id, 
            nama_menu AS nama, 
            harga_dasar AS harga, 
            gambar, 
            kategori AS jenis
        FROM menu 
        WHERE id_menu = :id LIMIT 1
    ");
    $q2->execute([':id'=>$id]);
    if ($r2 = $q2->fetch(PDO::FETCH_ASSOC)) return $r2;

    return null;
}

/********************************************
 * Build cart items array
 ********************************************/
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $key => $val) {

        /* CASE 1 — simple: id => qty */
        if ((is_string($key) || is_int($key)) && (is_int($val) || ctype_digit(strval($val)))) {

            $id = $key;
            $qty = intval($val);

            $row = fetch_menu_or_topping($koneksi, preg_replace('/^[A-Za-z]+/', '', $id));
            if (!$row) continue;

            $row['id'] = $id;
            $row['qty'] = max(1, $qty);
            $row['harga'] = intval($row['harga'] ?? 0);
            $row['subtotal'] = $row['qty'] * $row['harga'];

            $items[] = $row;
            $grandTotal += $row['subtotal'];
            continue;
        }

        /* CASE 2 — numeric index, value is string (id) */
        if (is_int($key) && is_string($val)) {

            $id = $val;
            $row = fetch_menu_or_topping($koneksi, preg_replace('/^[A-Za-z]+/', '', $id));
            if (!$row) continue;

            $row['id'] = $id;
            $row['qty'] = 1;
            $row['harga'] = intval($row['harga'] ?? 0);
            $row['subtotal'] = $row['harga'];

            $items[] = $row;
            $grandTotal += $row['subtotal'];
            continue;
        }

        /* CASE 3 — full array item */
        if (is_array($val)) {

            $id = $val['id'] ?? null;
            if (!$id) continue;

            $lookupId = preg_replace('/^[A-Za-z]+/', '', $id);

            $nama = $val['nama'] ?? null;
            $harga = isset($val['harga']) ? intval($val['harga']) : null;
            $gambar = $val['gambar'] ?? null;
            $jenis = $val['jenis'] ?? null;
            $qty = isset($val['qty']) ? intval($val['qty']) : 1;

            // Hydrate jika kurang data
            if (!$nama || $harga === null || !$gambar || !$jenis) {
                $db = fetch_menu_or_topping($koneksi, $lookupId);
                if ($db) {
                    $nama = $nama ?: $db['nama'];
                    $harga = $harga ?? intval($db['harga']);
                    $gambar = $gambar ?: $db['gambar'];
                    $jenis = $jenis ?: $db['jenis'];
                }
            }

            if (!$nama) continue;

            $qty = max(1, intval($qty));
            $subtotal = $qty * intval($harga);

            $items[] = [
                'id'=>$id,
                'nama'=>$nama,
                'harga'=>$harga,
                'gambar'=>$gambar ?? '',
                'jenis'=>$jenis ?? 'menu',
                'qty'=>$qty,
                'subtotal'=>$subtotal
            ];
            $grandTotal += $subtotal;
        }
    }
}
?>

<!-- CART HTML VIEW -->
<style>
.cart-root { font-family: system-ui, Arial; color:#222; }
.cart-img { width:64px; height:64px; border-radius:12px; object-fit:cover; }
.btn-delete-cart{ background:#fff; border:1px solid #ccc; padding:5px 10px; border-radius:8px; cursor:pointer; }
.btn-delete-cart:hover{ background:#ffe9e9; color:#b00; }
.cart-empty{ text-align:center; padding:25px; color:#999; }
.grand-total{ font-weight:900; color:#d43f3f; }
</style>

<div class="cart-root">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Menu</th>
                <th width="60">Qty</th>
                <th width="120">Harga</th>
                <th width="120">Total</th>
                <th width="80">Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php if (empty($items)): ?>
            <tr><td colspan="5" class="cart-empty">Keranjang kosong</td></tr>
        <?php else: ?>
            <?php foreach ($items as $it): 

                // Gambar
                $fileMenu = 'assets/img/menu/' . $it['gambar'];
                $fileTopping = 'assets/img/topping/' . $it['gambar'];

                if ($it['jenis'] === 'topping') {
                    $img = $fileTopping;
                } else {
                    $img = $fileMenu;
                }

                if (!file_exists(__DIR__ . '/../../' . $img)) {
                    $img = 'assets/img/menu/no-image.png';
                }
            ?>
            <tr>
                <td>
                    <div style="display:flex;gap:12px;align-items:center;">
                        <img src="<?= $img ?>" class="cart-img">
                        <div>
                            <strong><?= htmlspecialchars($it['nama']) ?></strong>
                            <?php if ($it['jenis'] !== 'topping'): ?>
                                <div style="font-size:12px;color:#777;">Kategori: <?= htmlspecialchars($it['jenis']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>

                <td><strong><?= $it['qty'] ?></strong></td>

                <td>Rp <?= number_format($it['harga'],0,',','.') ?></td>

                <td><strong>Rp <?= number_format($it['subtotal'],0,',','.') ?></strong></td>

                <td>
                    <button class="btn-delete-cart" data-id="<?= htmlspecialchars($it['id']) ?>">
                        Hapus
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="text-align:right;margin-top:10px;">
        <div>Grand Total</div>
        <div class="grand-total">Rp <?= number_format($grandTotal,0,',','.') ?></div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:15px;">
        <a href="index.php?page=menu" class="btn btn-outline-secondary">Tambah Menu</a>
        <button class="btn btn-warning" onclick="openCheckout()">Checkout</button>
    </div>
</div>

<script>
// PRG DELETE FIX
document.querySelectorAll('.btn-delete-cart').forEach(btn=>{
    btn.onclick = () => {
        if (!confirm('Hapus item dari keranjang?')) return;

        const f = document.createElement('form');
        f.method = 'POST';
        f.innerHTML = `<input type="hidden" name="remove_from_cart" value="${btn.dataset.id}">`;
        document.body.appendChild(f);
        f.submit();
    };
});
</script>
