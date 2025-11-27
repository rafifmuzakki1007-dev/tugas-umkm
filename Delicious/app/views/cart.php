<?php 
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
  echo "<div class='text-center py-5'>
          <h5>Keranjang masih kosong 🛒</h5>
          <button class='btn btn-warning mt-3' data-bs-dismiss='modal'>Tutup</button>
        </div>";
  return;
}

require_once __DIR__ . "/../../config/koneksi.php";

$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $koneksi->prepare("SELECT id_topping,nama_topping,harga,gambar FROM topping WHERE id_topping IN ($placeholders)");
foreach ($ids as $i=>$id) $stmt->bindValue($i+1,$id);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$map = [];
foreach ($rows as $r) $map[$r['id_topping']] = $r;

$grandTotal = 0;
?>
<div class="table-responsive">
<table class="table table-hover align-middle">
  <thead class="table-warning">
    <tr>
      <th>Menu</th>
      <th width="90px" class="text-center">Qty</th>
      <th>Harga</th>
      <th>Total</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>

<?php foreach ($_SESSION['cart'] as $id=>$qty): 
    if (!isset($map[$id])) continue;
    $t = $map[$id];

    $harga = floatval($t['harga']);
    $qtyInt = max(0,intval($qty));
    $total = $harga * $qtyInt;
    $grandTotal += $total;
?>
<tr>
  <td>
    <div class="d-flex align-items-center gap-3">
      <img src="assets/img/topping/<?= htmlspecialchars($t['gambar']); ?>" 
           style="width:60px;height:48px;object-fit:cover;border-radius:8px;">
      <div><strong><?= htmlspecialchars($t['nama_topping']); ?></strong></div>
    </div>
  </td>

  <td class="text-center"><?= $qtyInt ?></td>
  <td>Rp <?= number_format($harga,0,',','.') ?></td>
  <td><strong>Rp <?= number_format($total,0,',','.') ?></strong></td>

  <td>
    <button class="btn btn-danger btn-sm btn-delete-cart" data-id="<?= $id ?>">
      <i class="bi bi-trash"></i>
    </button>
  </td>
</tr>
<?php endforeach; ?>

<tr class="fw-bold bg-light">
  <td colspan="3" class="text-end">Grand Total</td>
  <td colspan="2">Rp <?= number_format($grandTotal,0,',','.') ?></td>
</tr>

  </tbody>
</table>
</div>

<div class="text-end mt-3">
  <button class="btn btn-outline-dark" data-bs-dismiss='modal'>Tambah Menu</button>

  <!-- FIX: langsung buka drawer tanpa reload -->
  <button type="button" onclick="window.openCheckout()" class="btn btn-warning fw-bold">Checkout</button>
</div>

<form id="removeForm" method="POST" action="index.php?page=menu" style="display:none;">
  <input type="hidden" name="remove_from_cart" id="removeInput">
</form>

<script>
document.querySelectorAll('.btn-delete-cart').forEach(btn=>{
  btn.addEventListener('click',()=>{
    const id = btn.dataset.id;
    if (!confirm("Hapus item ini?")) return;

    document.getElementById('removeInput').value = id;
    document.getElementById('removeForm').submit();
  });
});
</script>
