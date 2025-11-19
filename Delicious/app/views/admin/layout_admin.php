<?php 
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel UMKM</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
:root{
    --bg:#f5f7fb;
    --card:#fff;
    --text:#1f2937;
    --subtext:#6b7280;
}
[data-theme="dark"]{
    --bg:#101827;
    --card:#1c2536;
    --text:#e5e7eb;
    --subtext:#9ca3af;
}
body{
    font-family:"Poppins",sans-serif;
    background:var(--bg);
    color:var(--text);
    transition:.3s;
    overflow-x:hidden;
}

/* TOPBAR FIXED */
.fixed-topbar {
    position: fixed;
    top: 0;
    right: 0;
    left: 250px;
    height: 72px;
    background: var(--bg);
    z-index: 5000;
    display: flex;
    align-items: center;
    padding: 0 18px;
    transition: .25s;
}

body.sidebar-collapsed .fixed-topbar {
    left: 70px !important;
}

/* MAIN */
.main-wrapper{
    margin-left:250px;
    padding-top:86px !important;
    transition:.25s ease;
}

body.sidebar-collapsed .main-wrapper{
    margin-left:70px !important;
}

.content{ padding:25px; }

/* LOADER */
#simple-loader {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.25);
    backdrop-filter: blur(18px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 99999;
    transition: opacity .35s ease;
}
#simple-loader.hide {
    opacity: 0;
    pointer-events: none;
}
.dot-loader {
    display: flex;
    gap: 12px;
}
.dot-loader span {
    width: 18px;
    height: 18px;
    background: #4da3ff;
    border-radius: 50%;
    animation: bounce 0.6s infinite alternate;
}
.dot-loader span:nth-child(2) { animation-delay: 0.2s; }
.dot-loader span:nth-child(3) { animation-delay: 0.4s; }

@keyframes bounce {
    from { transform: translateY(0); opacity: .4; }
    to   { transform: translateY(-14px); opacity: 1; }
}
</style>

</head>

<body data-theme="light">

<?php include "sidebar_admin.php"; ?>

<!-- TOPBAR FIXED -->
<div class="fixed-topbar">
    <?php include "topbar_admin.php"; ?>
</div>

<div class="main-wrapper page-fade">

    <div class="content" id="page-content">
    <?php
    $adminPage = $_GET['page'] ?? 'dashboard';

    switch($adminPage){
        case 'menu_admin':        include __DIR__."/menu_admin.php"; break;
        case 'transaksi_admin':   include __DIR__."/transaksi_admin.php"; break;
        case 'profile_admin':     include __DIR__."/profile_admin.php"; break;
        default:                  include __DIR__."/dashboard.php"; break;
    }
    ?>
    </div>

</div>

<!-- LOADER -->
<div id="simple-loader">
    <div class="dot-loader">
        <span></span><span></span><span></span>
    </div>
</div>

<script>
// hide loader when finish
window.addEventListener("load", () => {
    const ld = document.getElementById("simple-loader");
    setTimeout(() => { ld.classList.add("hide"); }, 350);
});

// loader when clicking link
document.addEventListener("click", (e) => {
    const link = e.target.closest("a[href]");
    if (!link) return;
    if (link.id === "btnLogout") return;

    if (link.href.includes("page=")) {
        const ld = document.getElementById("simple-loader");
        ld.classList.remove("hide");
        ld.style.display = "flex";
    }
});

// theme
function toggleTheme(){
    const cur = document.body.getAttribute("data-theme");
    const next = cur === "light" ? "dark" : "light";
    document.body.setAttribute("data-theme", next);
    localStorage.setItem("theme", next);
}
window.onload = () => {
    const t = localStorage.getItem("theme");
    if(t) document.body.setAttribute("data-theme",t);
};

// sidebar collapse
document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("toggleSidebar");
    if (!toggle) return;

    toggle.addEventListener("click", () => {
        const sb = document.getElementById("adminSidebar");
        const btn = document.getElementById("toggleSidebar");

        sb.classList.toggle("collapsed");
        document.body.classList.toggle("sidebar-collapsed");

        btn.style.left = sb.classList.contains("collapsed") ? "80px" : "255px";
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
