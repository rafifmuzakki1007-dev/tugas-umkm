<title>Menu | Seblak Say Cafe</title>

<?php  
if (session_status() === PHP_SESSION_NONE) session_start();

require_once 'config/koneksi.php';
require_once 'app/models/MenuModel.php';

$menuModel = new MenuModel($koneksi);

$menuMainQuery = $koneksi->query("SELECT * FROM menu WHERE kategori='utama' LIMIT 1");
$menuMain = $menuMainQuery->fetch(PDO::FETCH_ASSOC);

$toppingStmt = $koneksi->query("SELECT * FROM topping ORDER BY id_topping ASC");
$toppingsRaw = $toppingStmt->fetchAll(PDO::FETCH_ASSOC);

$available = [];
$sold = [];
foreach ($toppingsRaw as $t) {
    $isSold = isset($t['stok']) && intval($t['stok']) <= 0;
    if ($isSold) $sold[] = $t;
    else $available[] = $t;
}
$toppings = array_merge($available, $sold);

$drinkStmt = $koneksi->query("SELECT * FROM menu WHERE kategori='minuman' ORDER BY id_menu ASC");
$drinks = $drinkStmt->fetchAll(PDO::FETCH_ASSOC);

$snackStmt = $koneksi->query("SELECT * FROM menu WHERE kategori='camilan' ORDER BY id_menu ASC");
$snacks = $snackStmt->fetchAll(PDO::FETCH_ASSOC);

// init cart
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

/* ======= HANDLE POST (PRG) ======= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD to cart (topping flow)
    

/* ======= HANDLE POST (PRG) ======= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD to cart (topping/menu unified)
    if (isset($_POST['add_to_cart'])) {
        $rawId = trim($_POST['add_to_cart']);
        $qty = (int)($_POST['qty'] ?? 1);
        if ($qty < 1) $qty = 1;

        // decide lookup id and type (topping if prefixed letter(s))
        $isTopping = false;
        if (preg_match('/^[A-Za-z]/', $rawId)) {
            $isTopping = true;
            $lookupId = preg_replace('/^[A-Za-z]+/', '', $rawId);
        } else {
            $lookupId = $rawId;
        }

        $item = null;
        try {
            if ($isTopping) {
                $stmt = $koneksi->prepare("SELECT id_topping, nama_topping, harga, gambar FROM topping WHERE id_topping = :id");
                $stmt->execute([':id' => $lookupId]);
                $t = $stmt->fetch();
                if ($t) {
                    $item = [
                        'id' => $rawId,
                        'nama' => $t['nama_topping'],
                        'harga' => isset($t['harga']) ? (int)$t['harga'] : 0,
                        'qty' => $qty,
                        'gambar' => $t['gambar'] ?? ''
                    ];
                }
            }

            if (!$item) {
                $stmt = $koneksi->prepare("SELECT id_menu, nama_menu, harga_dasar, gambar FROM menu WHERE id_menu = :id");
                $stmt->execute([':id' => $lookupId]);
                $mRow = $stmt->fetch();
                if ($mRow) {
                    $item = [
                        'id' => $rawId,
                        'nama' => $mRow['nama_menu'],
                        'harga' => isset($mRow['harga_dasar']) ? (int)$mRow['harga_dasar'] : 0,
                        'qty' => $qty,
                        'gambar' => $mRow['gambar'] ?? ''
                    ];
                }
            }
        } catch (PDOException $e) {
            // log or set flash error (non-fatal)
            $_SESSION['flash_add_failed'] = "DB error when adding: " . $e->getMessage();
            $item = null;
        }

        if ($item) {
            if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];
            if (isset($_SESSION['cart'][$item['id']])) {
                $_SESSION['cart'][$item['id']]['qty'] += $item['qty'];
            } else {
                $_SESSION['cart'][$item['id']] = $item;
            }
            $_SESSION['flash_add'] = true;
        } else {
            // optional: set flash to inform item not found
            if (!isset($_SESSION['flash_add_failed'])) $_SESSION['flash_add_failed'] = 'Item tidak ditemukan.';
        }
    }

    // REMOVE from cart
    if (isset($_POST['remove_from_cart'])) {
        $id = trim($_POST['remove_from_cart']);
        if ($id !== '' && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            $_SESSION['flash_remove'] = true;
        }
    }

    // PRG redirect to avoid resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}


        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    // REMOVE from cart (topping flow)
    if (isset($_POST['remove_from_cart'])) {
        $id = trim($_POST['remove_from_cart']);
        if ($id !== '' && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            $_SESSION['flash_remove'] = true;
        }
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

// include header
include 'app/views/sections/header_nav.php';
?>

<!-- LOCAL STYLES -->
<style>
:root{
  --gold:#ffca28;
  --accent:#e6a400;
  --dark:#222;
  --muted:#6b6b6b;
  --card-shadow: 0 18px 48px rgba(12,12,12,.08);
  --white:#ffffff;
  --glass: rgba(255,255,255,0.75);
  --glass-strong: rgba(255,255,255,0.88);
  --glass-border: rgba(255,255,255,0.22);
  --tab-indicator: rgba(230,164,0,0.12);
}

/* PAGE */
.page-wrapper{
  margin-top:40px !important;
  padding-bottom:100px;
  background:linear-gradient(180deg,#fff 0%, #fbfaf9 100%);
}

.hero {
    width: 100%;
    margin-top: 78px;
    margin-bottom: auto;
    background-image: url('assets/img/hero/hero-banner.png');
    background-size: contain !important;
    background-repeat: no-repeat !important;
    background-position: center center !important;
    background-color: #111;
    height: 390px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: visible;
}

.hero-img{
  width:100%;
  height:auto;
  display:block;
  object-fit: cover;
  -o-object-fit: cover;
  max-height: 720px;
}

.hero::after{
  content:'';
  position:absolute;
  inset:0;
  background: linear-gradient(180deg, rgba(0,0,0,0.26) 0%, rgba(0,0,0,0.06) 45%, rgba(255,255,255,0.00) 100%);
  pointer-events:none;
  z-index:1;
}

.hero-inner{
  max-width:1180px;
  margin:0 auto;
  padding:6px 20px;
  display:flex;
  gap:20px;
  align-items:center;
  justify-content:center;
  flex-direction:column;
  text-align:center;
  z-index:2;
  position:relative;
  transform: translateY(12px);
  opacity:0;
  animation: heroFadeIn .9s cubic-bezier(.2,.9,.2,1) .18s forwards;
}
@keyframes heroFadeIn {
  to { transform: translateY(0); opacity:1; }
}

.hero-floating{
  position:absolute;
  inset:auto;
  z-index:3;
  pointer-events:none;
  width:100%;
  height:100%;
  top:36px;
  left:0;
  overflow:visible;
}
.floating-item{
  position:absolute;
  will-change: transform, opacity;
  filter: drop-shadow(0 18px 40px rgba(0,0,0,.28));
  opacity:0;
  transform: translateY(28px) scale(.94);
  animation: floatIn .9s cubic-bezier(.2,.9,.2,1) forwards;
}
@keyframes floatIn {
  to { opacity:1; transform: translateY(0) scale(1); }
}
@keyframes bob {
  0% { transform: translateY(0) rotate(-1deg); }
  50% { transform: translateY(-8px) rotate(1deg); }
  100% { transform: translateY(0) rotate(-1deg); }
}
.floating-bob { animation: bob 6s ease-in-out infinite; transform-origin:center; }

.tabs {
  display:flex;
  gap:18px;
  justify-content:center;
  margin:26px 0 12px;
  align-items:center;
  position:relative;
  z-index:5;
}
.tab-btn{
  padding:10px 18px;
  background:transparent;
  border-radius:30px;
  font-weight:800;
  border:2px solid transparent;
  cursor:pointer;
  transition: transform .22s ease, color .18s ease;
  color:var(--muted);
  font-size:16px;
  position:relative;
  z-index:6;
}
.tab-btn:hover{ transform: translateY(-3px); }
.tab-btn.active{
  color:#111;
  transform: translateY(-6px);
}

.tab-indicator{
  position:absolute;
  height:52px;
  width:140px;
  background: linear-gradient(180deg, rgba(255,204,77,0.14), rgba(255,164,0,0.08));
  border-radius:28px;
  left:0;
  top:0;
  transform: translateY(6px);
  transition: left .32s cubic-bezier(.2,.9,.2,1), width .32s, transform .28s;
  filter: blur(.6px);
  z-index:4;
  pointer-events:none;
  box-shadow: 0 18px 42px rgba(230,164,0,0.08);
  backdrop-filter: blur(6px);
  opacity:0.98;
}

/* main section title */
.section-big-title{
  font-size:34px;
  font-weight:900;
  text-align:center;
  margin:14px 0 22px;
  color:var(--dark);
}

/* layout split */
.menu-layout{
  display:flex;
  gap:36px;
  align-items:flex-start;
  margin-bottom:40px;
}

/* glassmorphism seblak card */
.menu-left{ flex:0.95; }
.seblak-card{
  background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,255,255,0.85));
  border-radius:20px;
  padding:20px;
  box-shadow:var(--card-shadow);
  border: 1px solid var(--glass-border);
  backdrop-filter: blur(6px);
}
.seblak-img{
  width:100%;
  max-height:420px;
  object-fit:cover;
  border-radius:14px;
  display:block;
  margin-bottom:16px;
}
.seblak-title{ font-size:28px; font-weight:900; color:var(--dark); margin:0 0 8px; }
.seblak-lead{ color:var(--muted); margin:0 0 12px; }
.base-price{ color:#d43f3f; font-weight:800; margin-bottom:12px; }

/* RIGHT (topping panel) */
.menu-right{
  flex:1.15;
  background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(255,255,255,0.92));
  padding:22px;
  border-radius:16px;
  box-shadow:var(--card-shadow);
  height:620px;
  overflow-y:auto;
  border: 1px solid var(--glass-border);
  backdrop-filter: blur(6px);
}

/* DAFTAR TOPPING heading */
.menu-right .section-title{
  font-size:20px;
  font-weight:800;
  text-align:center;
  color:var(--dark);
  margin-bottom:18px;
}

/* TOPPING GRID - card styling */
.topping-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(150px,1fr));
  gap:16px;
}
.topping-card{
  position:relative;
  background: rgba(255,255,255,0.98);
  border-radius:14px;
  padding:12px;
  text-align:center;
  box-shadow: 0 12px 34px rgba(13,13,13,.06);
  transition: transform .18s ease, box-shadow .18s ease, opacity .18s ease;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  border: 1px solid rgba(0,0,0,0.03);
  transform: translateY(8px);
  opacity:0;
  animation: cardEnter .56s cubic-bezier(.2,.9,.2,1) forwards;
}
@keyframes cardEnter {
  to { transform: translateY(0); opacity:1; }
}
.topping-card:hover{
  transform: translateY(-10px);
  box-shadow: 0 28px 60px rgba(12,12,12,.12);
}
.topping-img{
  width:100%;
  aspect-ratio: 16/9;
  object-fit:cover;
  border-radius:10px;
  margin-bottom:10px;
}
.topping-name{ font-weight:800; font-size:1rem; color:#333; margin-bottom:6px; }
.topping-price{ color:#d43f3f; font-weight:800; margin-bottom:10px; }

/* add button */
.btn-add{
  width:100%;
  padding:9px 10px;
  border-radius:10px;
  border:2px solid var(--gold);
  background:var(--white);
  color:var(--gold);
  font-weight:800;
  transition:all .12s ease;
  box-shadow:0 8px 18px rgba(0,0,0,.06);
}
.btn-add:hover{ background:var(--gold); color:#111; transform:translateY(-2px); }

/* sold badge */
.sold-badge{
  display:inline-block;
  position:absolute;
  left:12px;
  top:12px;
  background:#c43b3b;
  color:#fff;
  padding:6px 10px;
  border-radius:10px;
  font-weight:800;
  font-size:.82rem;
  box-shadow:0 8px 18px rgba(0,0,0,.12);
}
.topping-card.sold { opacity:0.9; pointer-events:none; }
.topping-card.sold .topping-img { filter:grayscale(100%) brightness(.86); }
.topping-card.sold .btn-add { display:none; }

/* menu section box */
.menu-section-box{
  background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(255,255,255,0.94));
  padding:28px;
  border-radius:18px;
  box-shadow:var(--card-shadow);
  margin-top:28px;
  border:1px solid var(--glass-border);
  backdrop-filter: blur(6px);
}

.menu-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
  gap:24px;
  align-items:start;
}

/* item card */
.menu-item-card{
  background: rgba(255,255,255,0.99);
  border-radius:16px;
  padding:12px;
  text-align:center;
  box-shadow:0 18px 44px rgba(12,12,12,.06);
  transition: transform .18s ease, box-shadow .18s ease;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  min-height:250px;
  transform: translateY(10px);
  opacity:0;
  animation: cardEnterSmall .56s cubic-bezier(.2,.9,.2,1) forwards;
}
@keyframes cardEnterSmall { to { transform: translateY(0); opacity:1; } }
.menu-item-card:hover{
  transform:translateY(-10px);
  box-shadow:0 32px 72px rgba(12,12,12,.12);
}
.menu-item-img{
  width:100%;
  aspect-ratio:4/3;
  object-fit:cover;
  border-radius:12px;
  margin-bottom:12px;
}
.menu-item-name{ font-weight:900; font-size:1.05rem; margin-bottom:8px; color:var(--dark); }
.menu-item-price{ color:#d43f3f; font-weight:800; margin-bottom:14px; }

/* button Pesan Sekarang */
.btn-order-now{
  display:inline-block;
  width:100%;
  padding:12px 14px;
  border-radius:12px;
  background:linear-gradient(180deg,var(--gold),var(--accent));
  color:#111;
  font-weight:900;
  border:none;
  cursor:pointer;
  text-align:center;
  text-decoration:none;
  box-shadow:0 18px 40px rgba(250,180,10,.12);
  margin-top:auto;
}
.btn-order-now:hover{ opacity:0.98; transform:translateY(-3px); }

/* hidden */
.hidden { display:none !important; opacity:0; transform: translateY(6px); }

/* responsive adjustments */
@media(max-width:1100px){
  .menu-layout{ flex-direction:column; }
  .menu-right{ height:auto; max-height:520px; }
  .seblak-img{ max-height:260px; object-fit:cover; }
  .menu-grid{ grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); }
  .menu-item-img{ aspect-ratio: 4/3; }
  .tab-indicator{ display:none; }
  .hero { margin-top:70px; min-height:300px; }
  .hero-floating{ top:26px; }
}

/* modal qty polish */
.modal-content.custom-qty { border-radius:12px; overflow:hidden; }
.modal-header.custom-qty { background:var(--gold); border-bottom:none; }
#qtyModal .modal-body{ padding:18px 22px; text-align:center; }
#qtyItemName{ font-weight:800; font-size:1.05rem; margin-bottom:6px; color:var(--dark); }
#qtyItemPrice{ color:var(--muted); display:block; margin-bottom:10px; }
.qty-control{ display:flex; align-items:center; justify-content:center; gap:12px; margin:12px 0; }
.qty-control button{ width:44px; height:44px; border-radius:8px; border:1px solid #ddd; font-weight:800; background:#fff; cursor:pointer; box-shadow:0 6px 14px rgba(0,0,0,.06); }
.qty-display{ min-width:56px; text-align:center; font-weight:800; font-size:1.05rem; }
#qtySubtotal{ font-weight:800; color:var(--dark); }
#qtyForm button{ border-radius:10px; }

/* subtle entrance for whole page */
@keyframes pageFadeIn { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
.container.page-wrapper { animation: pageFadeIn .6s ease both; }

/* scrollbar polish */
.menu-right::-webkit-scrollbar, .menu-section-box::-webkit-scrollbar { width:12px; }
.menu-right::-webkit-scrollbar-thumb, .menu-section-box::-webkit-scrollbar-thumb { background:#efefef; border-radius:8px; border:3px solid #fff; }

/* === McD-style layout overrides: show all categories stacked and simplify topping layout === */
.tabs{ display:none !important; }
.tab-indicator{ display:none !important; }

/* Make layout vertical so left and right stack */
.menu-layout{ flex-direction: column; gap: 28px; }
.menu-left{ flex: unset; width:100% !important; }
.menu-right{ flex: unset; width:100% !important; margin-top: 24px; box-shadow:none; background:transparent; border:none; padding:0; }

/* Topping cards simplified to clean grid (no heavy cards) */
.topping-grid{ grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap:18px; }
.topping-card{ background: transparent; box-shadow: none; border-radius:12px; padding:10px; display:flex; flex-direction:column; align-items:center; gap:8px; border:1px solid rgba(0,0,0,0.04); }
.topping-card img{ width:100%; height:95px; object-fit:cover; border-radius:10px; }
.topping-card .topping-name{ font-weight:700; color:var(--dark); text-align:center; }
.topping-card .topping-price{ color:#d43f3f; font-weight:800; margin-top:4px; }

/* Menu item cards larger and simple */
.menu-grid{ grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:36px; }
.menu-item-card{ background: transparent; box-shadow:none; border-radius:12px; padding:8px; text-align:center; }
.menu-item-card img{ width:160px; height:140px; object-fit:contain; margin:0 auto 12px; }

/* Center the main title like McD */
.section-big-title{ font-size:44px; font-weight:900; text-align:center; margin:28px 0 24px; color:var(--dark); }

/* Ensure buttons are visible and simple */
.btn-order-now{ background:transparent; border:2px solid var(--gold); color:var(--gold); padding:8px 12px; border-radius:8px; font-weight:700; text-decoration:none; }

/* Small screens: maintain single column */
@media (max-width: 900px){
  .menu-grid, .topping-grid{ grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
  .topping-card img{ height:86px; }
  .section-big-title{ font-size:32px; }
}

/* === Seblak Hero Redesign === */
.seblak-card{
  max-width:900px !important;
  margin:0 auto 40px auto !important;
  padding:0 !important;
  background:transparent !important;
  box-shadow:none !important;
  border:none !important;
}
.seblak-img{
  width:100% !important;
  height:320px !important;
  object-fit:cover !important;
  border-radius:22px !important;
  display:block;
}
.seblak-title, .seblak-lead{
  padding-left:8px;
}

/* === Remove background card on Minuman & Camilan sections === */
#tab-minuman .menu-section-box, 
#tab-camilan .menu-section-box{
  background:transparent !important;
  box-shadow:none !important;
  border:none !important;
  padding:0 !important;
}

/* hover animation for product cards */
.menu-item-card:hover img{
  transform:scale(1.06);
  transition:0.25s ease;
}
.menu-item-card img{
  transition:0.25s ease;
}

/* Fix Seblak Card */
.seblak-card{
  background: #ffffff !important;
  padding:20px !important;
  border-radius:20px !important;
  box-shadow:0 4px 20px rgba(0,0,0,0.08) !important;
  max-width:900px !important;
  margin:0 auto 40px auto !important;
}
.seblak-img{
  width:100% !important;
  height:300px !important;
  object-fit:cover !important;
  border-radius:18px !important;
}

/* Remove background card from Minuman & Camilan */
#tab-minuman, #tab-camilan{
  background:transparent !important;
  box-shadow:none !important;
  border:none !important;
}

/* Topping Grid 4 columns */
.topping-grid{
  display:grid !important;
  grid-template-columns: repeat(4,1fr) !important;
  gap:18px !important;
}

/* Force show panels */
#tab-makanan, #tab-minuman, #tab-camilan{
  display:block !important;
  opacity:1 !important;
  visibility:visible !important;
}

/* --- HERO SEBLAK PREMIUM --- */
.seblak-card{
  max-width:560px !important;
  margin:0 auto 50px auto !important;
  padding:22px !important;
  background:white !important;
  border-radius:22px !important;
  box-shadow:0 6px 22px rgba(0,0,0,0.06) !important;
}
.seblak-img{
  width:100% !important;
  height:270px !important;
  object-fit:cover !important;
  border-radius:20px !important;
}

/* --- TOPPING GRID SMALLER --- */
.topping-grid{
  display:grid !important;
  grid-template-columns:repeat(4,1fr) !important;
  gap:12px !important;
}
.topping-grid img{
  height:100px !important;
  object-fit:cover !important;
  border-radius:12px !important;
}
.topping-grid .menu-item-card{
  padding:12px !important;
}

/* --- MINUMAN & CAMILAN CARD STYLE --- */
.menu-item-card{
  background:white !important;
  padding:18px !important;
  border-radius:20px !important;
  box-shadow:0 4px 14px rgba(0,0,0,0.06) !important;
}

/* --- HOVER BUTTON ANIMATION YELLOW --- */
.menu-item-card button{
  transition:0.25s ease !important;
}
.menu-item-card button:hover{
  background:#ffcc00 !important;
  border-color:#ffcc00 !important;
  color:black !important;
}

/* FORCE SHOW PANELS */
#tab-makanan, #tab-minuman, #tab-camilan{
  display:block !important;
  opacity:1 !important;
  visibility:visible !important;
}
</style>

<?php
$heroPathServer = __DIR__ . '/../../assets/img/hero/hero-banner.png';
$heroPathServerJpg = __DIR__ . '/../../assets/img/hero/hero-banner.jpg';
$heroUrlPng = 'assets/img/hero/hero-banner.png';
$heroUrlJpg = 'assets/img/hero/hero-banner.jpg';

$heroImgUrl = '';
if (file_exists($heroPathServer)) {
    $heroImgUrl = $heroUrlPng;
} elseif (file_exists($heroPathServerJpg)) {
    $heroImgUrl = $heroUrlJpg;
}
?>

<!-- HERO -->
<section class="hero" role="banner" aria-hidden="false">
  <?php if(!empty($heroImgUrl)): ?>
    <img src="<?php echo $heroImgUrl; ?>" alt="Hero banner" class="hero-img" />
  <?php else: ?>
    <div style="width:100%;height:360px;background:linear-gradient(135deg, #fff6e5 0%, #fff 100%);"></div>
  <?php endif; ?>

  <div class="hero-floating" aria-hidden="true">
    <img class="floating-item floating-bob" src="assets/img/menu/es-teh.jpeg" style="width:120px; left:6%; top:38%; animation-delay:.28s;" alt="">
    <img class="floating-item floating-bob" src="assets/img/menu/es-jeruk.jpeg" style="width:160px; left:46%; top:14%; animation-delay:.44s;" alt="">
    <img class="floating-item floating-bob" src="assets/img/menu/kopi.jpeg" style="width:120px; right:8%; top:24%; animation-delay:.6s;" alt="">
    <img class="floating-item" src="assets/img/menu/kentang-goreng.jpeg" style="width:140px; left:34%; bottom:14%; animation-delay:.72s;" alt="">
    <img class="floating-item" src="assets/img/menu/tahu-krispi.jpeg" style="width:110px; right:18%; bottom:10%; animation-delay:.86s;" alt="">
  </div>

  <div class="hero-inner" aria-hidden="true"></div>
</section>

<div class="container page-wrapper">
  <div style="position:relative;">
    <div class="tabs" role="tablist" aria-label="Kategori Menu">
      <button class="tab-btn active" data-tab="makanan" aria-selected="true" role="tab">Makanan</button>
      <button class="tab-btn" data-tab="minuman" aria-selected="false" role="tab">Minuman</button>
      <button class="tab-btn" data-tab="camilan" aria-selected="false" role="tab">Camilan</button>
    </div>
    <div class="tab-indicator" id="tabIndicator" aria-hidden="true"></div>
  </div>

  <div id="tab-makanan" class="tab-panel fade-in" role="tabpanel">

    <div class="section-big-title">Menu</div>

    <div class="menu-layout">

      <!-- LEFT -->
      <div class="menu-left">
        <div class="seblak-card">
          <?php if(!empty($menuMain['gambar'])): ?>
            <img src="assets/img/menu/<?php echo htmlspecialchars($menuMain['gambar']); ?>" class="seblak-img" alt="<?php echo htmlspecialchars($menuMain['nama_menu']); ?>">
          <?php else: ?>
            <div class="seblak-img" style="background:#eee;display:flex;align-items:center;justify-content:center;color:#888;">No Image</div>
          <?php endif; ?>

          <div class="seblak-title"><?php echo htmlspecialchars($menuMain['nama_menu'] ?? 'Seblak Prasmanan'); ?></div>
          <p class="seblak-lead"><?php echo htmlspecialchars($menuMain['deskripsi'] ?? 'Seblak prasmanan — pilih topping sesuai selera.'); ?></p>

          <!-- Pilih Topping removed as requested -->

        </div>
      </div>

      <!-- RIGHT -->
      <div class="menu-right" aria-live="polite" aria-label="Daftar Topping">
        <div class="section-title">Daftar Topping</div>

        <div class="topping-grid">
        <?php foreach($toppings as $index => $t):
            $sold = isset($t['stok']) && intval($t['stok']) <= 0;
            $class = $sold ? "topping-card sold" : "topping-card";
            $delay = 0.06 * ($index % 8);
        ?>
          <div class="<?php echo $class; ?>" style="animation-delay: <?php echo $delay; ?>s;">
            <?php if($sold): ?>
              <span class="sold-badge">HABIS</span>
            <?php endif; ?>

            <img src="assets/img/topping/<?php echo htmlspecialchars($t['gambar']); ?>" class="topping-img" alt="<?php echo htmlspecialchars($t['nama_topping']); ?>">
            <div class="topping-name"><?php echo htmlspecialchars($t['nama_topping']); ?></div>
            <div class="topping-price">Rp <?php echo number_format($t['harga']); ?></div>

            <?php if($sold): ?>
              <div style="color:#999;font-weight:700;">Stok habis</div>
            <?php else: ?>
              <button class="btn-add openQtyModal"
                data-id="<?php echo htmlspecialchars('T'.$t['id_topping']); ?>"
                data-name="<?php echo htmlspecialchars($t['nama_topping']); ?>"
                data-price="<?php echo htmlspecialchars($t['harga']); ?>">
                + Tambah
              </button>
            <?php endif; ?>

          </div>
        <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>

  <div id="tab-minuman" class="tab-panel fade-in" role="tabpanel">
    <div class="menu-section-box fade-in">
      <div class="section-big-title">Minuman</div>

      <div class="menu-grid">
        <?php foreach($drinks as $d): ?>
          <div class="menu-item-card">
            <img src="assets/img/menu/<?php echo htmlspecialchars($d['gambar']); ?>" class="menu-item-img" alt="<?php echo htmlspecialchars($d['nama_menu']); ?>">
            <div>
              <div class="menu-item-name"><?php echo htmlspecialchars($d['nama_menu']); ?></div>
              <div class="menu-item-price">Rp <?php echo number_format($d['harga_dasar']); ?></div>
            </div>

            <!-- Convert to + Tambah that opens qty modal and adds to cart -->
            <button class="btn-add openQtyModal"
              data-id="<?php echo htmlspecialchars('M'.$d['id_menu']); ?>"
              data-name="<?php echo htmlspecialchars($d['nama_menu']); ?>"
              data-price="<?php echo htmlspecialchars($d['harga_dasar']); ?>">
              + Tambah
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div id="tab-camilan" class="tab-panel fade-in" role="tabpanel">
    <div class="menu-section-box fade-in">
      <div class="section-big-title">Camilan</div>

      <div class="menu-grid">
        <?php foreach($snacks as $s): ?>
          <div class="menu-item-card">
            <img src="assets/img/menu/<?php echo htmlspecialchars($s['gambar']); ?>" class="menu-item-img" alt="<?php echo htmlspecialchars($s['nama_menu']); ?>">
            <div>
              <div class="menu-item-name"><?php echo htmlspecialchars($s['nama_menu']); ?></div>
              <div class="menu-item-price">Rp <?php echo number_format($s['harga_dasar']); ?></div>
            </div>

            <button class="btn-add openQtyModal"
              data-id="<?php echo htmlspecialchars('S'.$s['id_menu']); ?>"
              data-name="<?php echo htmlspecialchars($s['nama_menu']); ?>"
              data-price="<?php echo htmlspecialchars($s['harga_dasar']); ?>">
              + Tambah
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div>

<!-- QUANTITY CONFIRMATION MODAL -->
<div class="modal fade" id="qtyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content custom-qty">
      <div class="modal-header custom-qty">
        <h5 class="modal-title fw-bold"><i class="bi bi-basket3"></i> Tambah Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <div id="qtyItemName" class="fw-bold mb-1"></div>
        <div class="text-muted" style="margin-bottom:8px;">Harga: <span id="qtyItemPrice">Rp 0</span></div>

        <div class="qty-control">
          <button type="button" id="qtyMinus">-</button>
          <div class="qty-display" id="qtyDisplay">1</div>
          <button type="button" id="qtyPlus">+</button>
        </div>

        <div class="mb-2">Subtotal: <span id="qtySubtotal" class="fw-bold">Rp 0</span></div>

        <!-- Form submit to add to cart (PRG) -->
        <form id="qtyForm" method="POST">
          <input type="hidden" name="add_to_cart" id="qtyFormId" value="">
          <input type="hidden" name="qty" id="qtyFormQty" value="1">
          <button type="submit" class="btn btn-warning w-100 fw-bold">Tambah ke Keranjang</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- CART MODAL -->
<div class="modal fade" id="cartModalDialog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title fw-bold"><i class="bi bi-cart3"></i> Keranjang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="cartModalContent">
        <div class="text-center py-4">Memuat...</div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../notuse-file/footer.php'; ?>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function(){
  const tabs = Array.from(document.querySelectorAll('.tab-btn'));
  const panels = {
    makanan: document.getElementById('tab-makanan'),
    minuman: document.getElementById('tab-minuman'),
    camilan: document.getElementById('tab-camilan')
  };
  const indicator = document.getElementById('tabIndicator');

  function updateIndicator(activeBtn){
    if(!activeBtn || !indicator) return;
    const rect = activeBtn.getBoundingClientRect();
    const parentRect = activeBtn.parentElement.getBoundingClientRect();
    const left = rect.left - parentRect.left + activeBtn.parentElement.scrollLeft;
    const width = rect.width;
    const indicatorLeft = Math.max(left - 10, 6);
    const indicatorWidth = Math.max(width + 20, 120);
    indicator.style.left = indicatorLeft + 'px';
    indicator.style.width = indicatorWidth + 'px';
    indicator.style.transform = 'translateY(6px)';
    indicator.style.opacity = '1';
  }

  function showTab(name){
    Object.values(panels).forEach(p => {
      p.classList.add('hidden');
      p.classList.remove('fade-in');
    });
    tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected','false'); });

    const panel = panels[name];
    if(panel){
      panel.classList.remove('hidden');
      void panel.offsetWidth;
      panel.classList.add('fade-in');
      setTimeout(()=> {
        const r = panel.getBoundingClientRect();
        if (r.top < 0 || r.bottom > window.innerHeight) {
          panel.scrollIntoView({behavior:'smooth', block: 'start'});
        }
      }, 60);
    }
    const btn = document.querySelector('.tab-btn[data-tab="'+name+'"]');
    if(btn){ btn.classList.add('active'); btn.setAttribute('aria-selected','true'); updateIndicator(btn); }
  }

  tabs.forEach(btn => {
    btn.addEventListener('click', function(){
      const tab = this.dataset.tab;
      showTab(tab);
    });
  });

  setTimeout(()=> {
    const active = document.querySelector('.tab-btn.active');
    if(active) updateIndicator(active);
  }, 260);

  window.addEventListener('resize', ()=> {
    const active = document.querySelector('.tab-btn.active');
    if(active) updateIndicator(active);
  });

  showTab('makanan');

  // Quantity modal logic (works for toppings, drinks, snacks now)
  const qtyModalEl = document.getElementById('qtyModal');
  const qtyItemName = document.getElementById('qtyItemName');
  const qtyItemPrice = document.getElementById('qtyItemPrice');
  const qtyDisplay = document.getElementById('qtyDisplay');
  const qtySubtotal = document.getElementById('qtySubtotal');
  const qtyFormId = document.getElementById('qtyFormId');
  const qtyFormQty = document.getElementById('qtyFormQty');
  const qtyPlus = document.getElementById('qtyPlus');
  const qtyMinus = document.getElementById('qtyMinus');
  let currentPrice = 0;
  let currentQty = 1;

  let bsQtyModal = new bootstrap.Modal(qtyModalEl, {backdrop:'static', keyboard:true});

  document.querySelectorAll('.openQtyModal').forEach(btn => {
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const id = this.dataset.id;
      const name = this.dataset.name || 'Item';
      const price = parseFloat(this.dataset.price) || 0;

      currentQty = 1;
      currentPrice = price;

      qtyItemName.textContent = name;
      qtyItemPrice.textContent = formatRp(price);
      qtyDisplay.textContent = currentQty;
      qtyFormQty.value = currentQty;
      qtyFormId.value = id;
      qtySubtotal.textContent = formatRp(price * currentQty);

      bsQtyModal.show();
    });
  });

  qtyPlus && qtyPlus.addEventListener('click', () => {
    currentQty++;
    updateQtyUI();
  });
  qtyMinus && qtyMinus.addEventListener('click', () => {
    if (currentQty > 1) currentQty--;
    updateQtyUI();
  });

  function updateQtyUI(){
    qtyDisplay.textContent = currentQty;
    qtyFormQty.value = currentQty;
    qtySubtotal.textContent = formatRp(currentQty * currentPrice);
    qtyDisplay.style.transform = 'scale(1.06)';
    setTimeout(()=> qtyDisplay.style.transform = 'scale(1)', 160);
  }

  function formatRp(n){
    const num = Number(n) || 0;
    return 'Rp ' + num.toLocaleString('id-ID');
  }

  // Open cart: fetch modal content then show bootstrap modal
  window.openCart = function(){
    const target = document.getElementById('cartModalContent');
    target.innerHTML = '<div class="text-center py-4">Memuat...</div>';
    fetch('app/views/cart.php')
      .then(r => r.text())
      .then(html => {
        target.innerHTML = html;
        var cartModal = new bootstrap.Modal(document.getElementById('cartModalDialog'));
        cartModal.show();

        target.querySelectorAll('.btn-delete-cart').forEach(btn => {
          btn.addEventListener('click', function(){
            const id = this.dataset.id;
            Swal.fire({
              title:'Hapus item?',
              icon:'warning',
              showCancelButton:true,
              confirmButtonColor:'#d33',
              confirmButtonText:'Ya, hapus'
            }).then(res => {
              if(!res.isConfirmed) return;
              const f = document.createElement('form');
              f.method = 'POST';
              f.style.display = 'none';
              f.innerHTML = '<input type="hidden" name="remove_from_cart" value="'+id+'">';
              document.body.appendChild(f);
              f.submit();
            });
          });
        });

      })
      .catch(err => {
        console.error(err);
        Swal.fire({icon:'error', title:'Gagal memuat keranjang'});
      });
  };

  // Flash messages from server (PRG)
  <?php if(isset($_SESSION['flash_add'])): unset($_SESSION['flash_add']); ?>
  Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Item ditambahkan ke keranjang', timer: 1000, showConfirmButton: false });
  <?php endif; ?>

  <?php if(isset($_SESSION['flash_remove'])): unset($_SESSION['flash_remove']); ?>
  Swal.fire({ icon: 'success', title: 'Dihapus', text: 'Item dihapus dari keranjang', timer: 900, showConfirmButton: false });
  <?php endif; ?>

  // Small parallax for hero
  const hero = document.querySelector('.hero');
  if(hero){
    window.addEventListener('scroll', ()=> {
      const offset = window.scrollY;
      hero.style.transform = 'translateY(' + Math.min(offset * 0.02, 18) + 'px)';
      hero.style.backgroundPosition = 'center ' + (50 - offset * 0.006) + '%';
    });
  }

  document.querySelectorAll('.hero-floating .floating-item').forEach((el, i) => {
    const delay = (i * 120);
    el.style.animationDelay = (delay/1000) + 's';
    if(!el.classList.contains('floating-bob')) {
      el.classList.add('floating-bob');
    }
  });

})();
</script>
