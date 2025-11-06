<?php
if (!isset($_SESSION)) session_start();

// pastikan user login admin
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
    --bg:#f5f7fb;--card:#fff;--text:#1f2937;--subtext:#6b7280;--shadow:rgba(0,0,0,.08);
}
[data-theme="dark"]{
    --bg:#101827;--card:#1c2536;--text:#e5e7eb;--subtext:#9ca3af;--shadow:rgba(0,0,0,.5);
}
body{ font-family:"Poppins",sans-serif; background:var(--bg); color:var(--text); transition:.3s;}

.main-wrapper{margin-left:240px;}
.page-fade{animation:fade .4s ease;}
@keyframes fade{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);} }

.content{padding:25px;}

#simple-loader{
  position:fixed; inset:0; background:var(--bg);
  display:flex; justify-content:center; align-items:center;
  z-index:5000; transition:.3s;
}
.spinner-border{width:3rem;height:3rem}
/* Fix: loader tidak blok sidebar */
#simple-loader.hide {
    opacity:0;
    pointer-events:none;
}
</style>
</head>

<body data-theme="light">

<?php include "sidebar_admin.php"; ?>

<div class="main-wrapper page-fade">

<?php include "topbar_admin.php"; ?>

<div class="content" id="page-content">

<?php
/* Routing admin pages */
$adminPage = $_GET['page'] ?? 'dashboard';

switch($adminPage){

    case 'menu_admin':
        include __DIR__ . "/menu_admin.php";
        break;

    case 'transaksi_admin':
        include __DIR__ . "/transaksi_admin.php";
        break;

    case 'profile_admin':
        include __DIR__ . "/profile_admin.php";
        break;

    default:
        include __DIR__ . "/dashboard.php";
        break;
}
?>

</div>
</div>

<div id="simple-loader"><div class="spinner-border text-primary"></div></div>

<script>
/* Loader: auto hide after load */
document.addEventListener("DOMContentLoaded",()=>{
    const loader=document.getElementById("simple-loader");
    setTimeout(()=>{
        loader.classList.add("hide");
        setTimeout(()=> loader.remove(),300);
    },400);
});

/* Loader on sidebar navigation click */
document.addEventListener("click",(e)=>{
  const link=e.target.closest('a[href]');
  if(!link) return;

  if(link.href.includes("page=")){ 
      const loader=document.getElementById("simple-loader");
      if(loader){
        loader.style.display="flex";
        loader.classList.remove("hide");
      }
  }
});

/* Theme Toggle */
function toggleTheme(){
    let cur=document.body.getAttribute("data-theme");
    document.body.setAttribute("data-theme",cur=="light"?"dark":"light");
    localStorage.setItem("theme",document.body.getAttribute("data-theme"));
}
window.onload=()=>{
    const t=localStorage.getItem("theme");
    if(t) document.body.setAttribute("data-theme",t);
};

/* ✅ SweetAlert Logout (fix click) */
document.addEventListener("DOMContentLoaded", () => {
    const logoutButtons = document.querySelectorAll(".logout-btn");

    logoutButtons.forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: "Yakin ingin Log out?",
                text: "Kamu akan keluar dari dashboard admin",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, Logout",
                cancelButtonText: "Batal"
            }).then((res) => {
                if (res.isConfirmed) {
                    window.location.href = "index.php?page=logout";
                }
            });
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
