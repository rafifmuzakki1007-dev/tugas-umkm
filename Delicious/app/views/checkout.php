<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/koneksi.php';

/*
  checkout.php — FINAL (Seblak Prasmanan + Minuman + Camilan)
  - Seblak Prasmanan tidak otomatis masuk sebagai item.
  - Semua topping di-cart dianggap bagian dari Seblak Prasmanan.
  - Item di tabel `menu` dikelompokkan:
      kategori = 'minuman' -> MINUMAN
      kategori = 'camilan' -> CAMILAN
      kategori = 'utama'   -> Seblak Prasmanan (hanya untuk ambil gambar & nama).
  - Ringkasan pesanan:
      1. Card Seblak Prasmanan (dengan gambar) + daftar topping.
      2. Daftar Minuman.
      3. Daftar Camilan.
  - Hanya ada 1 total besar di bagian bawah.
*/

$seblakToppings = [];   // topping seblak
$drinkItems     = [];   // menu kategori = minuman
$snackItems     = [];   // menu kategori = camilan

$seblakTotal = 0;
$drinkTotal  = 0;
$snackTotal  = 0;
$cartTotal   = 0;

// Ambil data Seblak Prasmanan untuk header (gambar + nama)
$baseSeblak = null;
try {
    $stmtm = $koneksi->prepare("SELECT id_menu, nama_menu, harga_dasar, gambar FROM menu WHERE id_menu = :id LIMIT 1");
    $stmtm->execute([':id' => 'MN001']);
    $baseSeblak = $stmtm->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $baseSeblak = null;
}

/**
 * Resolve ID cart menjadi data item.
 *
 * Mendukung pola:
 *  - "T123" -> topping.id_topping = 123
 *  - "M45"  -> menu.id_menu = 45
 *  - "S67"  -> menu.id_menu = 67
 *  - "topping-123" / "menu-45" / "snack-67"
 *  - numeric id -> coba topping dulu lalu menu
 *
 * Return:
 *  [
 *    'id'       => string,
 *    'name'     => string,
 *    'harga'    => int,
 *    'gambar'   => string,
 *    'jenis'    => 'topping'|'menu',
 *    'kategori' => 'topping'|'minuman'|'camilan'|'utama'|dll
 *  ]
 * atau null jika tidak ditemukan.
 */
function resolveCartKey(PDO $pdo, $key) {
    $k = trim((string)$key);
    if ($k === '') return null;

    // Helper: ambil topping
    $fetchTopping = function(PDO $pdo, $id) {
        $stmt = $pdo->prepare("
            SELECT id_topping AS id, nama_topping AS name, harga, gambar
            FROM topping
            WHERE id_topping = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            $r['jenis']    = 'topping';
            $r['kategori'] = 'topping';
            return $r;
        }
        return null;
    };

    // Helper: ambil menu
    $fetchMenu = function(PDO $pdo, $id) {
        $stmt = $pdo->prepare("
            SELECT id_menu AS id, nama_menu AS name, harga_dasar AS harga, gambar, kategori
            FROM menu
            WHERE id_menu = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            $r['jenis'] = 'menu';
            if (empty($r['kategori'])) $r['kategori'] = 'menu';
            return $r;
        }
        return null;
    };

    // prefix satu huruf: T / M / S
    $first = strtoupper(substr($k, 0, 1));
    if (in_array($first, ['T','M','S'], true)) {
        $raw = substr($k, 1);
        if ($first === 'T') {
            $r = $fetchTopping($pdo, $raw);
            if ($r) return $r;
        } else {
            $r = $fetchMenu($pdo, $raw);
            if ($r) return $r;
        }
    }

    // prefix dengan dash: topping-xxx / menu-xxx / snack-xxx
    if (strpos($k, '-') !== false) {
        list($pref, $raw) = explode('-', $k, 2);
        $pref = strtolower($pref);
        if ($pref === 'topping') {
            $r = $fetchTopping($pdo, $raw);
            if ($r) return $r;
        } else {
            $r = $fetchMenu($pdo, $raw);
            if ($r) return $r;
        }
    }

    // numeric murni: coba topping dulu, lalu menu
    if (ctype_digit($k)) {
        $r = $fetchTopping($pdo, $k);
        if ($r) return $r;
        $r = $fetchMenu($pdo, $k);
        if ($r) return $r;
    }

    // fallback terakhir
    $r = $fetchTopping($pdo, $k);
    if ($r) return $r;
    $r = $fetchMenu($pdo, $k);
    if ($r) return $r;

    return null;
}

// Build cart berdasarkan $_SESSION['cart']
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $val) {

        $id  = null;
        $qty = 1;

        // Pola sederhana: 'ID' => 3
        if ((is_string($key) || is_int($key)) && (is_int($val) || ctype_digit(strval($val)))) {
            $id  = $key;
            $qty = (int)$val;

        // Pola array: ['id' => 'ID', 'qty' => 3]
        } elseif (is_array($val)) {
            $id  = $val['id']  ?? $key;
            $qty = isset($val['qty']) ? (int)$val['qty'] : 1;
        }

        if (!$id || $qty < 1) continue;

        $resolved = resolveCartKey($koneksi, $id);
        if (!$resolved) continue;

        $harga    = (int)$resolved['harga'];
        $subtotal = $harga * $qty;

        // TOTAL GLOBAL
        $cartTotal += $subtotal;

        // Mapping struktur umum
        $item = [
            'id'       => $resolved['id'],
            'nama'     => $resolved['name'],
            'gambar'   => $resolved['gambar'],
            'harga'    => $harga,
            'qty'      => $qty,
            'subtotal' => $subtotal,
            'kategori' => $resolved['kategori'],
            'jenis'    => $resolved['jenis'],
        ];

        // Kelompokkan
        if ($resolved['jenis'] === 'topping') {
            $seblakToppings[] = $item;
            $seblakTotal     += $subtotal;

        } elseif ($resolved['jenis'] === 'menu') {
            $kat = strtolower($resolved['kategori']);

            // Seblak Prasmanan (kategori 'utama') diabaikan dari list item
            if ($kat === 'utama') {
                continue;
            } elseif ($kat === 'minuman') {
                $drinkItems[] = $item;
                $drinkTotal  += $subtotal;
            } elseif ($kat === 'camilan') {
                $snackItems[] = $item;
                $snackTotal  += $subtotal;
            }
        }
    }
}
?>
<style>
:root{
  --accent:#f4c542;
  --muted:#7a7a7a;
  --drawer-w:420px;
}

/* ROOT */
.checkout-root{
  position:fixed; inset:0;
  z-index:15000;
  pointer-events:none;
}

/* OVERLAY */
.checkout-overlay{
  position:absolute; inset:0;
  background:rgba(0,0,0,.45);
  backdrop-filter:blur(4px);
  opacity:0;
  transition:.25s;
  pointer-events:none;
  z-index:15010;
}
.checkout-overlay.visible{
  opacity:1;
  pointer-events:auto;
}

/* DRAWER */
.checkout-drawer{
  position:absolute; right:0; top:0;
  height:100%; width:var(--drawer-w);
  transform:translateX(110%);
  transition:.35s ease;
  display:flex; flex-direction:column;
  background:rgba(255,255,255,.98);
  backdrop-filter:blur(6px);
  border-left:1px solid rgba(0,0,0,0.03);
  box-shadow:0 0 45px rgba(0,0,0,.18);
  pointer-events:auto;
  overflow:hidden;
  z-index:15100;
}
.checkout-drawer.open{ transform:translateX(0); }

@media(max-width:720px){
  .checkout-drawer{
    width:100%; left:0; right:0;
    bottom:0; top:auto;
    height:82vh; border-radius:16px 16px 0 0;
    transform:translateY(110%);
  }
  .checkout-drawer.open{ transform:translateY(0); }
}

/* HEADER + BODY + FOOTER */
.drawer-header{padding:18px;display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid rgba(0,0,0,.06);}
.drawer-title{font-weight:800;font-size:20px;}
.drawer-sub{color:var(--muted);font-size:13px;margin-top:4px;}
.drawer-body{padding:14px;overflow:auto;flex:1;}
.drawer-footer{padding:12px 14px;border-top:1px solid rgba(0,0,0,.05);background:#fff;display:flex;justify-content:space-between;align-items:center;}

.field{margin-top:12px;display:flex;flex-direction:column;gap:6px;}
.input{
  padding:11px 12px;border-radius:10px;
  border:1px solid rgba(0,0,0,.1);
  background:#fff;font-size:14px;
  pointer-events:auto !important;
}
.input.invalid { border-color:#e03e3e; }
.input.valid { border-color:#2fb66a; }

.pay-chip{
  padding:10px 12px;border-radius:10px;
  border:1px solid rgba(0,0,0,.08);
  cursor:pointer;font-weight:700;background:#fff;
}
.pay-chip.active{
  background:#fff7dd;border-color:#f4c542;
  box-shadow:0 0 10px rgba(244,197,66,.18);
}

.qris-box{
  width:230px;height:230px;margin:auto;
  border:1px dashed rgba(0,0,0,.12);
  border-radius:14px;background:#fff;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;
}
.qris-box img{max-width:90%;max-height:90%;object-fit:contain;}

#qrisModal{
  position:fixed; inset:0;
  display:none; align-items:center; justify-content:center;
  background:rgba(0,0,0,0.6); z-index:16000;
}
#qrisModal img{
  max-width:92%;max-height:92%;
  border-radius:8px; box-shadow:0 0 8px rgba(0,0,0,.6);
}

.btn-primary{padding:10px 18px;font-weight:800;background:var(--accent);border:0;border-radius:10px;cursor:pointer;}
.btn-ghost{color:#d08c00;font-weight:700;}

/* section style (GoFood-like) */
.section-label{
  margin-top:10px;
  margin-bottom:4px;
  font-weight:700;
  font-size:14px;
}
</style>

<div class="checkout-root" id="checkoutRoot" aria-hidden="true">
  <div class="checkout-overlay" id="checkoutOverlay" tabindex="-1"></div>

  <aside class="checkout-drawer" id="checkoutDrawer" role="dialog" aria-modal="true">

    <!-- HEADER -->
    <div class="drawer-header">
      <div>
        <div id="checkoutTitle" class="drawer-title">Checkout</div>
        <div class="drawer-sub">Pilih metode pembayaran & isi data</div>
      </div>

      <div style="display:flex;align-items:center;gap:12px;">
        <div style="font-weight:700;color:var(--muted)">Total</div>
        <div style="font-weight:900">Rp <?= number_format($cartTotal,0,',','.') ?></div>
        <button id="closeCheckoutBtn" style="background:none;border:0;font-size:22px;cursor:pointer" aria-label="Tutup checkout">✕</button>
      </div>
    </div>

    <!-- BODY -->
    <div class="drawer-body">
      <form id="checkoutForm" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="from_ajax" value="1">
        <input type="hidden" name="metode_bayar" id="metodeInput" value="tunai">
        <input type="hidden" name="total_harga" id="totalHargaInput" value="<?= (int)$cartTotal ?>">

        <h4>Ringkasan Pesanan</h4>

        <?php if (empty($seblakToppings) && empty($drinkItems) && empty($snackItems)): ?>
          <div class="drawer-sub">Keranjang kosong.</div>
        <?php else: ?>

          <!-- SEBLAK PRASMANAN + TOPPING -->
          <?php if (!empty($seblakToppings)): ?>
            <div class="section-label">Seblak Prasmanan</div>

            <?php if ($baseSeblak): ?>
              <div style="display:flex;gap:10px;align-items:center;margin-bottom:6px;padding:10px;border-radius:10px;background:#fff;border:1px solid rgba(0,0,0,0.03);">
                <img src="assets/img/menu/<?= htmlspecialchars($baseSeblak['gambar']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:10px">
                <div>
                  <div style="font-weight:800;"><?= htmlspecialchars($baseSeblak['nama_menu']) ?></div>
                  <div style="color:var(--muted);font-size:13px;">Harga mengikuti total topping yang dipilih</div>
                </div>
              </div>
            <?php endif; ?>

            <div class="drawer-sub" style="margin-bottom:4px;">Topping yang dipilih:</div>
            <ul style="list-style:none;padding:0;margin:4px 0 6px 0">
              <?php foreach ($seblakToppings as $it): ?>
                <li style="display:flex;justify-content:space-between;gap:12px;padding:9px;border-radius:10px;background:#fff;border:1px solid rgba(0,0,0,0.03);margin-bottom:6px;align-items:center">
                  <div style="display:flex;gap:10px;align-items:center">
                    <img src="assets/img/topping/<?= htmlspecialchars($it['gambar']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:10px">
                    <div>
                      <div style="font-weight:800;"><?= htmlspecialchars($it['nama']) ?></div>
                      <div style="color:var(--muted);font-size:13px;">
                        <?= (int)$it['qty'] ?> × Rp <?= number_format($it['harga'],0,',','.') ?>
                      </div>
                    </div>
                  </div>
                  <div style="font-weight:800;font-size:14px;">
                    Rp <?= number_format($it['subtotal'],0,',','.') ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <!-- MINUMAN -->
          <?php if (!empty($drinkItems)): ?>
            <div class="section-label" style="margin-top:8px;">Minuman</div>
            <ul style="list-style:none;padding:0;margin:4px 0 6px 0">
              <?php foreach ($drinkItems as $it): ?>
                <li style="display:flex;justify-content:space-between;gap:12px;padding:9px;border-radius:10px;background:#fff;border:1px solid rgba(0,0,0,0.03);margin-bottom:6px;align-items:center">
                  <div style="display:flex;gap:10px;align-items:center">
                    <img src="assets/img/menu/<?= htmlspecialchars($it['gambar']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:10px">
                    <div>
                      <div style="font-weight:800;"><?= htmlspecialchars($it['nama']) ?></div>
                      <div style="color:var(--muted);font-size:13px;">
                        <?= (int)$it['qty'] ?> × Rp <?= number_format($it['harga'],0,',','.') ?>
                      </div>
                    </div>
                  </div>
                  <div style="font-weight:800;font-size:14px;">
                    Rp <?= number_format($it['subtotal'],0,',','.') ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <!-- CAMILAN -->
          <?php if (!empty($snackItems)): ?>
            <div class="section-label" style="margin-top:8px;">Camilan</div>
            <ul style="list-style:none;padding:0;margin:4px 0 6px 0">
              <?php foreach ($snackItems as $it): ?>
                <li style="display:flex;justify-content:space-between;gap:12px;padding:9px;border-radius:10px;background:#fff;border:1px solid rgba(0,0,0,0.03);margin-bottom:6px;align-items:center">
                  <div style="display:flex;gap:10px;align-items:center">
                    <img src="assets/img/menu/<?= htmlspecialchars($it['gambar']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:10px">
                    <div>
                      <div style="font-weight:800;"><?= htmlspecialchars($it['nama']) ?></div>
                      <div style="color:var(--muted);font-size:13px;">
                        <?= (int)$it['qty'] ?> × Rp <?= number_format($it['harga'],0,',','.') ?>
                      </div>
                    </div>
                  </div>
                  <div style="font-weight:800;font-size:14px;">
                    Rp <?= number_format($it['subtotal'],0,',','.') ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

        <?php endif; ?>

        <!-- TOTAL AKHIR -->
        <div style="display:flex;justify-content:space-between;align-items:center;font-weight:800;margin-top:10px;padding-top:10px;border-top:1px dashed rgba(0,0,0,0.08)">
          <div class="muted">Total Pesanan</div>
          <div>Rp <?= number_format($cartTotal,0,',','.') ?></div>
        </div>

        <h4 style="margin-top:12px;margin-bottom:6px">Metode Pembayaran</h4>
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;margin-bottom:8px">
          <button type="button" class="pay-chip active" data-metode="tunai" aria-pressed="true">💵 Tunai</button>
          <button type="button" class="pay-chip" data-metode="non_tunai" aria-pressed="false">🏦 Non Tunai (QRIS)</button>
        </div>

        <div id="panelTunai">
          <div class="drawer-sub">Pembayaran langsung di tempat.</div>
        </div>

        <div id="panelNonTunai" style="display:none;margin-top:12px;">
          <div style="font-weight:700;margin-bottom:6px">Scan QRIS</div>
          <div class="qris-box" id="qrisBox">
            <img src="<?= file_exists(__DIR__ . '/../../assets/img/qris.jpg') ? 'assets/img/qris.jpg' : 'assets/img/qris.png' ?>">
          </div>
          <div class="field">
            <label class="drawer-sub">Upload Bukti (opsional)</label>
            <input class="input" type="file" name="bukti_transfer" id="bukti_non_tunai" accept="image/*">
          </div>
          <div id="previewNonTunai" style="margin-top:10px;display:none;"></div>
        </div>

        <h4 style="margin-top:14px">Data Pembeli</h4>

        <div class="field">
          <label>Nama Lengkap (wajib)</label>
          <input class="input" type="text" id="customer_name" name="customer_name" placeholder="Nama lengkap..." required autocomplete="name">
          <small id="nameHint" style="color:#e03e3e;display:none;margin-top:6px">Nama wajib diisi.</small>
        </div>

        <div class="field">
          <label>Nomor HP (opsional)</label>
          <input class="input" type="tel" id="customer_phone" name="customer_phone" placeholder="08xxxxxxxxxx" autocomplete="tel">
          <small id="phoneHint" style="color:#e03e3e;display:none;margin-top:6px">Format nomor tidak valid.</small>
        </div>

      </form>
    </div>

    <!-- FOOTER -->
    <div class="drawer-footer">
      <a href="index.php?page=menu" class="btn-ghost">Kembali</a>
      <div style="display:flex;align-items:center;gap:12px">
        <div style="font-weight:900">Rp <?= number_format($cartTotal,0,',','.') ?></div>
        <button id="submitCheckout" class="btn-primary">Bayar Sekarang</button>
      </div>
    </div>

  </aside>
</div>

<!-- QRIS modal -->
<div id="qrisModal">
  <img src="<?= file_exists(__DIR__ . '/../../assets/img/qris.jpg') ? 'assets/img/qris.jpg' : 'assets/img/qris.png' ?>">
</div>

<script>
(function(){
  const root = document.getElementById('checkoutRoot');
  const overlay = document.getElementById('checkoutOverlay');
  const drawer = document.getElementById('checkoutDrawer');
  const closeBtn = document.getElementById('closeCheckoutBtn');

  function removeAnyModalBackdrops() {
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');
    if (document.body.style.overflow === 'hidden') document.body.style.overflow = '';
  }

  window.openCheckout = function(){
    removeAnyModalBackdrops();
    document.querySelectorAll('.modal').forEach(m => {
      m.style.display = 'none';
      m.classList.remove('show');
    });

    if (!root || !overlay || !drawer) return;
    root.style.pointerEvents = 'auto';
    overlay.classList.add('visible');
    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden','false');
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
      document.getElementById('customer_name')?.focus();
      drawer.querySelector('.drawer-body')?.scrollTo({ top: 0, behavior: 'instant' });
    }, 60);
  };

  window.closeCheckout = function(){
    if (!root || !overlay || !drawer) return;
    overlay.classList.remove('visible');
    drawer.classList.remove('open');
    drawer.setAttribute('aria-hidden','true');
    setTimeout(()=>{ root.style.pointerEvents = 'none'; }, 300);
    document.body.style.overflow = '';
    removeAnyModalBackdrops();
  };

  overlay.addEventListener && overlay.addEventListener('click', window.closeCheckout);
  closeBtn && closeBtn.addEventListener && closeBtn.addEventListener('click', window.closeCheckout);

  const chips = document.querySelectorAll('.pay-chip');
  const panelTunai = document.getElementById('panelTunai');
  const panelNon = document.getElementById('panelNonTunai');
  const metodeInput = document.getElementById('metodeInput');

  chips.forEach(c=> c.addEventListener('click', function(){
    chips.forEach(x=>x.classList.remove('active'));
    this.classList.add('active');
    const m = this.dataset.metode;
    if (m === 'non_tunai') {
      panelTunai.style.display = 'none';
      panelNon.style.display = '';
      metodeInput.value = 'transfer';
    } else {
      panelNon.style.display = 'none';
      panelTunai.style.display = '';
      metodeInput.value = 'tunai';
    }
  }));

  const qrisBox = document.getElementById('qrisBox');
  const qrisModal = document.getElementById('qrisModal');
  if (qrisBox && qrisModal) {
    qrisBox.addEventListener('click', ()=> { qrisModal.style.display = 'flex'; });
    qrisModal.addEventListener('click', (e)=> { if (e.target === qrisModal) qrisModal.style.display = 'none'; });
  }

  document.getElementById('bukti_non_tunai')?.addEventListener('change', function(){
    const box = document.getElementById('previewNonTunai');
    if (!this.files.length){ box.style.display='none'; box.innerHTML=''; return; }
    const url = URL.createObjectURL(this.files[0]);
    box.innerHTML = `<img src="${url}" style="max-width:160px;border-radius:10px;border:1px solid #ccc">`;
    box.style.display='block';
  });

})();
</script>

<script>
// =======================
// Submit Checkout (AJAX)
// =======================
document.getElementById('submitCheckout').addEventListener('click', function (e) {
    e.preventDefault();

    const name = document.getElementById('customer_name');
    if (!name.value.trim()) {
        name.classList.add("invalid");
        name.focus();
        return;
    }

    const totalElem = document.getElementById('totalHargaInput');
    if (totalElem) {
        totalElem.value = parseInt(totalElem.value) || 0;
    }

    const form = document.getElementById('checkoutForm');
    const formData = new FormData(form);

    fetch("index.php?page=pesan_process", {
        method: "POST",
        body: formData,
    })
    .then(r => r.json())
    .then(json => {
        if (json && json.ok) {
            if (json.redirect) {
                window.location.href = json.redirect;
            } else {
                window.location.href = "index.php?page=order_success&id=" + encodeURIComponent(json.id || '');
            }
        } else {
            const msg = (json && json.message) ? json.message : "Gagal memproses pesanan.";
            alert(msg);
        }
    })
    .catch(err => {
        console.error("Checkout error:", err);
        alert("Terjadi kesalahan saat memproses pesanan.");
    });

});
</script>
