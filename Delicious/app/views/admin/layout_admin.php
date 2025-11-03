<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel UMKM</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root{
    --bg: #f5f7fb;
    --card: #ffffff;
    --text: #1f2937;
    --subtext: #6b7280;
    --shadow: rgba(0,0,0,.08);
}
[data-theme="dark"]{
    --bg: #101827;
    --card: #1c2536;
    --text: #e5e7eb;
    --subtext: #9ca3af;
    --shadow: rgba(0,0,0,.5);
}
body {
    font-family: "Poppins", sans-serif;
    background: var(--bg);
    color: var(--text);
    transition:.3s;
}

/* Sidebar */
.sidebar {
    width: 240px;
    height: 100vh;
    position: fixed;
    background: #1b2a49;
    color: white;
    padding-top: 20px;
}
.sidebar a {
    color: #cbd5e1;
    display: block;
    padding: 12px 20px;
    text-decoration: none;
    font-weight: 500;
    transition:.2s;
}
.sidebar a:hover, .sidebar a.active {
    background: #243b6b;
    color: white;
}

.main-wrapper {
    margin-left: 240px;
}

/*** PAGE TRANSITION ***/
.page-fade {
    animation: pageFade .45s ease;
}
@keyframes pageFade {
    from{opacity:0; transform:translateY(12px);}
    to{opacity:1; transform:translateY(0);}
}

/* Content */
.content {
    padding: 25px;
}

/*** SIMPLE LOADER ***/
#simple-loader {
    position: fixed;
    inset: 0;
    background: var(--bg);
    display:flex;
    justify-content:center;
    align-items:center;
    z-index:20000;
}
.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
</head>

<body data-theme="light">

<?php include "sidebar_admin.php"; ?>

<div class="main-wrapper page-fade">

<?php include "topbar_admin.php"; ?>

<div class="content" id="page-content">
<?php 
if (isset($admin_content) && file_exists($admin_content)) {
    include $admin_content;
} else {
    echo "<h4>Halaman tidak ditemukan!</h4>";
}
?>
</div>
</div>

<div id="simple-loader" style="opacity:1;">
    <div class="spinner-border text-primary"></div>
</div>

<script>
document.addEventListener("DOMContentLoaded",()=>{
    setTimeout(()=>{
        document.getElementById("simple-loader").style.opacity="0";
        setTimeout(()=>document.getElementById("simple-loader").style.display="none",300);
    },400);
});

/*** DARK MODE TOGGLE ***/
function toggleTheme(){
    let current = document.body.getAttribute("data-theme");
    document.body.setAttribute("data-theme", current === "light" ? "dark":"light");
    localStorage.setItem("theme",document.body.getAttribute("data-theme"));
}
window.onload = ()=> {
    const saved = localStorage.getItem("theme");
    if(saved) document.body.setAttribute("data-theme",saved);
};
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
