<?php
session_start();
require_once 'config/koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php?page=dashboard");
    exit;
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $q = $koneksi->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $q->execute([$username,$password]);
    $user = $q->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user'] = $user;
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

/* ===== BACKGROUND ===== */
body{
    background: url('assets/img/bg-login.jpg') center/cover no-repeat;
    height:100vh;
    margin:0;
    padding:0;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family: "Poppins", sans-serif;
    overflow:hidden;
    position:relative;
}

body::before{
    content:"";
    position:absolute;
    inset:0;
    background: rgba(0,0,0,0.50);
    backdrop-filter: blur(5px);
    z-index:1;
}

/* ===== CARD ===== */
.login-box{
    position:relative;
    width:360px;
    background: rgba(255,255,255,0.20);
    border:1px solid rgba(255,255,255,0.45);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-radius:18px;
    padding:32px 28px;
    box-shadow: 0 20px 45px rgba(0,0,0,.50);
    animation: zoom .5s ease-out;
    z-index:5;
}

@keyframes zoom{
    from{transform: scale(.85); opacity:0;}
    to{transform: scale(1); opacity:1;}
}

.login-title{
    font-weight:700;
    font-size:22px;
    text-align:center;
    margin-bottom:18px;
    color:#fff;
}

/* ===== INPUTS ===== */
.input-group-text{
    background:rgba(255,255,255,.8);
    border:none;
    border-radius:12px 0 0 12px;
}

.form-control{
    border:none;
    border-radius:0 12px 12px 0;
    background:rgba(255,255,255,.8);
}

.form-control:focus{
    background:white;
}

/* ===== BUTTON ===== */
.btn-login{
    background: linear-gradient(90deg,#ff4b2b,#ff416c);
    border:none;
    border-radius:12px;
    padding:10px;
    font-weight:600;
    width:100%;
    color:white;
    transition:.25s;
}

.btn-login:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(255,65,108,.45);
}

/* ===== EXTRA ===== */
.reg{
    text-align:center;
    margin-top:14px;
    font-size:14px;
    color:#fff;
}

.reg a{
    text-decoration:none;
    font-weight:600;
    color:#ffbaba;
}
.reg a:hover{ text-decoration:underline; }

.alert-custom{
    background:rgba(255,147,147,.75);
    border:1px solid #e02a2a;
    border-radius:10px;
    color:#fff;
    font-size:14px;
    padding:8px;
    text-align:center;
    margin-bottom:10px;
}

</style>
</head>

<body>

<div class="login-box">

    <div class="login-title">Login Admin</div>

    <?php if($msg): ?>
        <div class="alert-custom"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>

        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <button class="btn-login">Login</button>
    </form>

    <div class="reg">
        Belum punya akun? <a href="register.php">Register</a>
    </div>
</div>

</body>
</html>
