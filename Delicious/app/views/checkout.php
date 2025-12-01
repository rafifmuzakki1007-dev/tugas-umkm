<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/koneksi.php';

/*
  Perubahan penting:
  - Ambil data menu dasar (MN001) dan masukkan sebagai item pertama
    dengan harga 0 (seblak).
  - Perbaikan: deteksi ID yang tersimpan di $_SESSION['cart'] sehingga
    dapat menampilkan topping, minuman, dan camilan meskipun key
    memiliki prefix (mis. T123, M45, S67, or menu-45/snack-67).
  - Tidak merubah struktur HTML/JS asli—hanya memperbaiki logika backend.
*/

$cartItems = [];
$cartTotal = 0;

// --- ambil menu dasar (seblak) ---
$baseMenu = null;
try {
    $stmtm = $koneksi->prepare("SELECT id_menu, nama_menu, harga_dasar, gambar FROM menu WHERE id_menu = :id LIMIT 1");
    $stmtm->execute([':id' => 'MN001']);
    $baseMenu = $stmtm->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $baseMenu = null;
}

// jika ada menu dasar, tambahkan ke cartItems sebagai item base (harga 0)
if ($baseMenu) {
    $cartItems[] = [
        'is_base' => true,
        'id_menu' => $baseMenu['id_menu'],
        'nama_topping' => $baseMenu['nama_menu'],
        'gambar' => $baseMenu['gambar'],
        'qty' => 1,
        'harga' => 0,
        'subtotal' => 0
    ];
}

/**
 * Fetch cart item by stored session key.
 * Supports keys like:
 *  - "T123" -> topping id 123
 *  - "M45"  -> menu id 45 (minuman)
 *  - "S67"  -> snack id 67 (camilan)
 *  - "topping-123", "menu-45", "snack-67"
 *  - numeric ids => will try topping first, then menu
 *
 * Returns array with keys: id, name (nama_topping/nama_menu), harga, gambar, jenis ('topping'|'menu')
 * or null if not found.
 */
function resolveCartKey($pdo, $key) {
    $k = trim((string)$key);
    if ($k === '') return null;

    // prefixed single-letter like T/M/S
    $first = strtoupper(substr($k,0,1));
    if (in_array($first, ['T','M','S'])) {
        $raw = substr($k,1);
        // numeric id expected
        if ($first === 'T') {
            $stmt = $pdo->prepare("SELECT id_topping AS id, nama_topping AS name, harga, gambar FROM topping WHERE id_topping = :id LIMIT 1");
            $stmt->execute([':id' => $raw]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) { $r['jenis']='topping'; return $r; }
        } else {
            // M or S -> menu table
            $stmt = $pdo->prepare("SELECT id_menu AS id, nama_menu AS name, harga_dasar AS harga, gambar FROM menu WHERE id_menu = :id LIMIT 1");
            $stmt->execute([':id' => $raw]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) { $r['jenis']='menu'; return $r; }
        }
    }

    // prefixed like "menu-123" or "snack-45" or "topping-12"
    if (strpos($k,'-') !== false) {
        list($pref,$raw) = explode('-', $k, 2);
        $pref = strtolower($pref);
        if ($pref === 'topping') {
            $stmt = $pdo->prepare("SELECT id_topping AS id, nama_topping AS name, harga, gambar FROM topping WHERE id_topping = :id LIMIT 1");
            $stmt->execute([':id' => $raw]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) { $r['jenis']='topping'; return $r; }
        } else { // menu/snack/menu etc -> menu table
            $stmt = $pdo->prepare("SELECT id_menu AS id, nama_menu AS name, harga_dasar AS harga, gambar FROM menu WHERE id_menu = :id LIMIT 1");
            $stmt->execute([':id' => $raw]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) { $r['jenis']='menu'; return $r; }
        }
    }

    // fallback: numeric id -> try topping first, then menu
    if (ctype_digit($k)) {
        $stmt = $pdo->prepare("SELECT id_topping AS id, nama_topping AS name, harga, gambar FROM topping WHERE id_topping = :id LIMIT 1");
        $stmt->execute([':id' => $k]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r) { $r['jenis']='topping'; return $r; }

        $stmt = $pdo->prepare("SELECT id_menu AS id, nama_menu AS name, harga_dasar AS harga, gambar FROM menu WHERE id_menu = :id LIMIT 1");
        $stmt->execute([':id' => $k]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r) { $r['jenis']='menu'; return $r; }
    }

    // if nothing matched, try to search both tables by id-like string (last resort)
    $stmt = $pdo->prepare("SELECT id_topping AS id, nama_topping AS name, harga, gambar FROM topping WHERE id_topping = :id LIMIT 1");
    $stmt->execute([':id' => $k]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($r) { $r['jenis']='topping'; return $r; }
    $stmt = $pdo->prepare("SELECT id_menu AS id, nama_menu AS name, harga_dasar AS harga, gambar FROM menu WHERE id_menu = :id LIMIT 1");
    $stmt->execute([':id' => $k]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($r) { $r['jenis']='menu'; return $r; }

    return null;
}

if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $tid => $q) {
        $resolved = resolveCartKey($koneksi, $tid);
        if (!$resolved) continue;
        $resolved['qty'] = intval($q);
        $resolved['subtotal'] = $resolved['qty'] * floatval($resolved['harga']);
        // map to original keys expected by template (nama_topping used previously)
        $cartItems[] = [
            'id' => $resolved['id'],
            'is_base' => false,
            'nama_topping' => $resolved['name'],
            'gambar' => $resolved['gambar'],
            'qty' => $resolved['qty'],
            'harga' => $resolved['harga'],
            'subtotal' => $resolved['subtotal']
        ];
        $cartTotal += $resolved['subtotal'];
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
  border-radius:8px; box-shadow:0 8px 40px rgba(0,0,0,.6);
}

.btn-primary{padding:10px 18px;font-weight:800;background:var(--accent);border:0;border-radius:10px;cursor:pointer;}
.btn-ghost{color:#d08c00;font-weight:700;}
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

        <!-- GANTI: gunakan nama sesuai kolom DB -->
        <input type="hidden" name="metode_bayar" id="metodeInput" value="tunai">

        <!-- KIRIM JUGA total_harga sebagai integer (backend mungkin juga menghitung sendiri, tapi ini aman) -->
        <input type="hidden" name="total_harga" id="totalHargaInput" value="<?= intval($cartTotal) ?>">

        <h4>Ringkasan Pesanan</h4>

        <?php if (empty($cartItems)): ?>
          <div class="drawer-sub">Keranjang kosong.</div>
        <?php else: ?>

          <ul style="list-style:none;padding:0;margin:0 0 12px 0">

          <?php
          $firstTopping = true;
          foreach ($cartItems as $it):

              if (!empty($it['is_base'])) {
                  $imgPath = 'assets/img/menu/' . htmlspecialchars($it['gambar']);
              } else {
                  $imgPath = 'assets/img/topping/' . htmlspecialchars($it['gambar']);
              }
          ?>

            <li style="display:flex;justify-content:space-between;gap:12px;padding:10px;border-radius:10px;background:#fff;border:1px solid rgba(0,0,0,0.03);margin-bottom:10px;align-items:center">
              <div style="display:flex;gap:10px;align-items:center">

                <img src="<?= $imgPath ?>" style="width:64px;height:64px;object-fit:cover;border-radius:10px">

                <div>
                  <div style="font-weight:800">

                    <?php if (!empty($it['is_base'])): ?>
                        <?= htmlspecialchars($it['nama_topping']) ?>
                    <?php else: ?>
                        <?php if ($firstTopping): ?>
                          Seblak +
                        <?php endif; ?>
                        <?= htmlspecialchars($it['nama_topping']) ?>
                    <?php endif; ?>

                  </div>

                  <?php if (empty($it['is_base'])): ?>
                  <div style="color:var(--muted)">
                    <?= intval($it['qty']) ?> × Rp <?= number_format($it['harga'],0,',','.') ?>
                  </div>
                  <?php endif; ?>
                </div>
              </div>

              <?php if (empty($it['is_base'])): ?>
              <div style="font-weight:800">
                Rp <?= number_format($it['subtotal'],0,',','.') ?>
              </div>
              <?php else: ?>
              <div style="font-weight:800"></div>
              <?php endif; ?>
            </li>

          <?php
            if (empty($it['is_base'])) $firstTopping = false;
          endforeach;
          ?>

          </ul>

        <?php endif; ?>

        <div style="display:flex;justify-content:space-between;align-items:center;font-weight:800;margin-top:6px;padding-top:8px;border-top:1px dashed rgba(0,0,0,0.04)">
          <div class="muted">Total Pesanan</div>
          <div>Rp <?= number_format($cartTotal,0,',','.') ?></div>
        </div>

        <h4 style="margin-top:12px;margin-bottom:6px">Metode Pembayaran</h4>
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;margin-bottom:8px">
          <button type="button" class="pay-chip active" data-metode="tunai" aria-pressed="true">💵 Tunai</button>
          <!-- NOTE: data-metode non_tunai -> JS akan set metode_bayar = 'transfer' -->
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
            <!-- GANTI name menjadi bukti_transfer sesuai nama kolom/konvensi backend -->
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
  const totalHargaInput = document.getElementById('totalHargaInput');

  chips.forEach(c=> c.addEventListener('click', function(){
    chips.forEach(x=>x.classList.remove('active'));
    this.classList.add('active');
    const m = this.dataset.metode;
    if (m === 'non_tunai') {
      panelTunai.style.display = 'none';
      panelNon.style.display = '';
      // KIRIM value 'transfer' karena enum di DB adalah 'transfer' untuk non-tunai
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

    // update hidden total (safety)
    const totalElem = document.getElementById('totalHargaInput');
    if (totalElem) {
        // ensure integer
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