<?php
session_start();

// Kalau sudah login admin → langsung dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php?page=dashboard");
    exit;
}

require_once 'config/koneksi.php';

$msg = "";

// Login proses
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // pakai plain text sesuai permintaan

    $q = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
    $q->execute([$username]);
    $user = $q->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['password'] === $password) {

        // Session admin
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['user'] = $user;
        $_SESSION['admin'] = true;
        $_SESSION['role'] = $user['role'] ?? 'admin';

        header("Location: index.php?page=dashboard");
        exit;
    } else {
        $msg = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Admin | UMKM Seblak</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
header, .topbar, .bg-dark, #header, nav, .navmenu { display: none !important; }

body{
    background: url('assets/img/bg-login.jpg') center/cover no-repeat;
    height:100vh;
    width:100vw;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family: "Poppins", sans-serif;
    margin:0;
    padding:0;
    overflow:hidden;
    position:relative;
}
body::before{
    content:"";
    position:absolute;
    inset:0;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(5px);
}

.login-box{
    position:absolute;
    top:50%; left:50%;
    transform:translate(-50%, -50%);
    width:360px;
    background: rgba(255,255,255,0.22);
    border:1px solid rgba(255,255,255,0.4);
    backdrop-filter: blur(14px);
    border-radius:18px;
    padding:32px;
    box-shadow: 0 20px 45px rgba(0,0,0,.50);
    z-index:10;
    opacity: 0;
    animation: fadeInZoom .6s ease forwards;
}
@keyframes fadeInZoom {
    to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
}

.btn-login{
    background: linear-gradient(90deg,#ff4b2b,#ff416c);
    border:none;
    padding:10px;
    font-weight:600;
    width:100%;
    border-radius:10px;
    color:white;
    transition:.25s;
}
.btn-login:hover{ box-shadow:0 10px 25px rgba(255,65,108,.45); }

.btn-loading{ pointer-events:none; opacity:.7; }
</style>
</head>

<body>

<div class="login-box">
    <div class="text-center text-white fw-bold fs-5 mb-3">Login Admin</div>

    <?php if($msg): ?>
        <div class="alert alert-danger py-2 text-center"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" id="loginForm">

        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>

        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <button class="btn-login" id="loginBtn">Login</button>
    </form>

    <div class="text-center text-white mt-2">
        Belum punya akun? <a href="register.php" class="text-warning">Register</a>
    </div>
</div>

<script>
// Button Loading Animation
document.getElementById("loginForm").addEventListener("submit", function() {
    let btn = document.getElementById("loginBtn");
    btn.classList.add("btn-loading");
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Loading...`;
});
</script>

</body>
</html>
