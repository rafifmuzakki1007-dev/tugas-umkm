<?php  
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../config/koneksi.php';

function jexit($arr) {
    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}

function logx($msg) {
    file_put_contents(__DIR__ . '/pesan_process.log',
        date('Y-m-d H:i:s') . " - $msg\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jexit(['ok' => false, 'message' => 'Invalid method']);
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    jexit(['ok' => false, 'message' => 'Keranjang kosong.']);
}

$nama  = trim($_POST['customer_name'] ?? '');
$phone = trim($_POST['customer_phone'] ?? '');
$metode = trim($_POST['metode'] ?? 'tunai');
$level  = $_POST['level_pedas'] ?? 0;

if ($nama === '') {
    jexit(['ok' => false, 'message' => 'Nama wajib diisi.']);
}

$bukti = null;
if (!empty($_FILES['bukti_non_tunai']['name'])) {
    $up = $_FILES['bukti_non_tunai'];
    if ($up['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($up['name'], PATHINFO_EXTENSION);
        $bukti = 'bukti_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $dir = __DIR__ . '/../../assets/uploads/bukti';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        move_uploaded_file($up['tmp_name'], $dir . '/' . $bukti);
    }
}

$total = 0;
$items = [];

foreach ($_SESSION['cart'] as $idTopping => $qty) {
    $qty = max(1, intval($qty));

    $stmt = $koneksi->prepare("SELECT harga FROM topping WHERE id_topping = :id");
    $stmt->execute(['id' => $idTopping]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        jexit(['ok' => false, 'message' => "Topping $idTopping tidak ditemukan"]);
    }

    $harga = floatval($row['harga']);
    $subtotal = $harga * $qty;

    $total += $subtotal;

    $items[] = [
        'id_topping' => $idTopping,
        'qty' => $qty,
        'harga' => $harga
    ];
}

try {
    $koneksi->beginTransaction();

    $id_trans = "TR-" . date("ymdHis") . "-" . rand(100,999);

    $ins = $koneksi->prepare("
        INSERT INTO transaksi 
        (id_transaksi, tanggal, metode_bayar, level_pedas, total_harga, bukti_transfer, status, customer_name, customer_phone)
        VALUES (:id, NOW(), :m, :l, :t, :b, 'pending', :n, :p)
    ");
    $ins->execute([
        'id' => $id_trans,
        'm'  => $metode,
        'l'  => $level,
        't'  => $total,
        'b'  => $bukti,
        'n'  => $nama,
        'p'  => $phone
    ]);

    $det = $koneksi->prepare("
        INSERT INTO detail_transaksi (id_transaksi, id_topping, qty, harga_satuan)
        VALUES (:id, :tp, :q, :h)
    ");

    foreach ($items as $it) {
        $det->execute([
            'id' => $id_trans,
            'tp' => $it['id_topping'],
            'q'  => $it['qty'],
            'h'  => $it['harga']
        ]);
    }

    $koneksi->commit();

    unset($_SESSION['cart']);

    jexit([
        'ok' => true,
        'id' => $id_trans,
        'redirect' => "index.php?page=order_success&id=$id_trans"
    ]);

} catch (Exception $e) {
    if ($koneksi->inTransaction()) $koneksi->rollBack();
    jexit(['ok' => false, 'message' => "Gagal memproses pesanan"]);
}
