<?php 
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
  echo "<div class='text-center py-5'>
          <h5>Keranjang masih kosong 🛒</h5>
          <button class='btn btn-warning mt-2' data-bs-dismiss='modal'>Tutup</button>
        </div>";
  return;
}

require_once __DIR__ . "/../../config/koneksi.php";
require_once __DIR__ . "/../../app/models/MenuModel.php";

$menuModel = new MenuModel($koneksi);
$grandTotal = 0;
?>

<table class="table table-hover">
  <thead class="table-warning">
    <tr>
      <th>Menu</th>
      <th width="70px">Qty</th>
      <th>Harga</th>
      <th>Total</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>

<?php foreach ($_SESSION['cart'] as $id => $qty):
  $menu = $menuModel->getMenuById($id);
  $total = $menu['harga'] * $qty;
  $grandTotal += $total;
?>
<tr>
  <td><?= $menu['nama_menu']; ?></td>
  <td class="text-center"><?= $qty; ?></td>
  <td>Rp <?= number_format($menu['harga'],0,',','.'); ?></td>
  <td>Rp <?= number_format($total,0,',','.'); ?></td>
  <td>
    <button class="btn btn-danger btn-sm btn-delete-cart" data-id="<?= $id; ?>">
      <i class="bi bi-trash"></i>
    </button>
  </td>
</tr>
<?php endforeach; ?>

<tr class="fw-bold bg-light">
  <td colspan="3" class="text-end">Grand Total</td>
  <td colspan="2">Rp <?= number_format($grandTotal,0,',','.'); ?></td>
</tr>
</tbody>
</table>

<div class="text-end">
  <button class="btn btn-outline-dark" data-bs-dismiss="modal">Tambah Menu</button>
  <a href="index.php?page=pesan_process" class="btn btn-warning fw-bold">Checkout</a>
</div>

<form id="removeForm" method="POST" style="display:none;">
  <input type="hidden" name="remove_from_cart" id="removeInput">
</form>

