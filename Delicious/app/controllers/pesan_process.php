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

/* Normalisasi cart ke format ID => qty */
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
} else {
    $normalized = [];
    foreach ($_SESSION['cart'] as $k => $v) {
        $id = null;
        $qty = 0;

        if (is_array($v)) {
            $id = $v['id'] ?? $k;
            $qty = isset($v['qty']) ? (int)$v['qty'] : 1;
        } elseif (is_int($v) || ctype_digit((string)$v)) {
            $id = $k;
            $qty = (int)$v;
        } elseif (is_string($v)) {
            $id = $v;
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

if (count($_SESSION['cart']) === 0) {
    jexit(['ok' => false, 'message' => 'Keranjang kosong.']);
}

$nama   = trim($_POST['customer_name'] ?? '');
$phone  = trim($_POST['customer_phone'] ?? '');
$metode = trim($_POST['metode_bayar'] ?? 'tunai');
if ($metode !== 'tunai' && $metode !== 'transfer') $metode = 'tunai';
$level  = $_POST['level_pedas'] ?? 0;

if ($nama === '') {
    jexit(['ok' => false, 'message' => 'Nama wajib diisi.']);
}

/* handle optional upload bukti_transfer */
$bukti = null;
$uploadField = 'bukti_transfer';
if ($metode === 'transfer' && !empty($_FILES[$uploadField]['name'])) {
    $up = $_FILES[$uploadField];
    if ($up['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($up['name'], PATHINFO_EXTENSION);
        $bukti = 'bukti_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $dir = __DIR__ . '/../../assets/uploads/bukti';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        if (!is_writable($dir)) {
            logx("Upload dir not writable: $dir");
            jexit(['ok' => false, 'message' => 'Direktori upload tidak dapat ditulis.']);
        }
        if (!move_uploaded_file($up['tmp_name'], "$dir/$bukti")) {
            logx("Failed to move uploaded file: " . $up['name']);
            jexit(['ok' => false, 'message' => 'Gagal mengupload bukti transfer.']);
        }
    } else {
        logx("Upload error: " . $up['error']);
        jexit(['ok' => false, 'message' => 'Error upload bukti: ' . $up['error']]);
    }
}

/* Helper resolve item dari DB */
function resolve_item($pdo, $id) {
    $id = trim((string)$id);
    if ($id === '') return null;

    // Coba topping
    $stmt = $pdo->prepare("SELECT id_topping AS id, nama_topping AS name, harga, 'topping' AS type FROM topping WHERE id_topping = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($r) return $r;

    // Coba menu
    $stmt = $pdo->prepare("SELECT id_menu AS id, nama_menu AS name, harga_dasar AS harga, 'menu' AS type FROM menu WHERE id_menu = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($r) return $r;

    return null;
}

/* Resolve cart items and compute total */
$total = 0.0;
$items = []; // each: ['type'=>'topping'|'menu','id'=>'TP001','qty'=>n,'harga'=>float]

foreach ($_SESSION['cart'] as $id => $qty) {
    $qty = (int)$qty;
    if ($qty < 1) $qty = 1;

    $resolved = resolve_item($koneksi, $id);
    if (!$resolved) {
        jexit(['ok'=>false, 'message'=>"Item $id tidak ditemukan."]);
    }
    $harga    = (float)($resolved['harga'] ?? 0);
    $subtotal = $harga * $qty;
    $total   += $subtotal;

    $items[] = [
        'type'     => $resolved['type'],
        'id'       => $resolved['id'],
        'name'     => $resolved['name'],
        'qty'      => $qty,
        'harga'    => $harga,
        'subtotal' => $subtotal
    ];
}

/* Begin DB transaction */
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

    /* detect columns in detail_transaksi */
    $stmt = $koneksi->prepare("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'detail_transaksi' AND COLUMN_NAME = 'id_menu'");
    $stmt->execute();
    $has_id_menu = intval($stmt->fetch(PDO::FETCH_ASSOC)['cnt']) > 0;

    $stmt2 = $koneksi->prepare("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'detail_transaksi' AND COLUMN_NAME = 'id_topping'");
    $stmt2->execute();
    $has_id_topping = intval($stmt2->fetch(PDO::FETCH_ASSOC)['cnt']) > 0;

    if ($has_id_menu) {
        $det_stmt_menu = $koneksi->prepare("INSERT INTO detail_transaksi (id_transaksi, id_menu, qty, harga_satuan) VALUES (:id, :menu, :q, :h)");
    } 
    if ($has_id_topping) {
        $det_stmt_top = $koneksi->prepare("INSERT INTO detail_transaksi (id_transaksi, id_topping, qty, harga_satuan) VALUES (:id, :tp, :q, :h)");
    }
    if (!$has_id_menu && !$has_id_topping) {
        $det_stmt_generic = $koneksi->prepare("INSERT INTO detail_transaksi (id_transaksi, qty, harga_satuan) VALUES (:id, :q, :h)");
    }

    foreach ($items as $it) {
        if ($it['type'] === 'topping') {
            if ($has_id_topping) {
                $det_stmt_top->execute([
                    'id' => $id_trans,
                    'tp' => $it['id'],
                    'q'  => $it['qty'],
                    'h'  => $it['harga']
                ]);
            } elseif ($has_id_menu) {
                $det_stmt_menu->execute([
                    'id'   => $id_trans,
                    'menu' => $it['id'],
                    'q'    => $it['qty'],
                    'h'    => $it['harga']
                ]);
            } else {
                $det_stmt_generic->execute([
                    'id' => $id_trans,
                    'q'  => $it['qty'],
                    'h'  => $it['harga']
                ]);
            }
        } else { // menu item
            if ($has_id_menu) {
                $det_stmt_menu->execute([
                    'id'   => $id_trans,
                    'menu' => $it['id'],
                    'q'    => $it['qty'],
                    'h'    => $it['harga']
                ]);
            } elseif ($has_id_topping) {
                $det_stmt_top->execute([
                    'id' => $id_trans,
                    'tp' => $it['id'],
                    'q'  => $it['qty'],
                    'h'  => $it['harga']
                ]);
            } else {
                $det_stmt_generic->execute([
                    'id' => $id_trans,
                    'q'  => $it['qty'],
                    'h'  => $it['harga']
                ]);
            }
        }
    }

    $koneksi->commit();
    unset($_SESSION['cart']);

    jexit(['ok'=>true, 'id'=>$id_trans, 'redirect'=>"index.php?page=order_success&id=$id_trans"]);

} catch (Exception $e) {
    if ($koneksi->inTransaction()) $koneksi->rollBack();
    logx("Error processing order: " . $e->getMessage());
    jexit(['ok'=>false, 'message'=>'Terjadi kesalahan saat memproses pesanan.']);
}
