<?php
session_start();
require_once 'config/koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php?page=dashboard");
    exit;
}

$msg = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password']; // simpan password asli

    $check = $koneksi->prepare("SELECT * FROM users WHERE username=?");
    $check->execute([$username]);

    if ($check->rowCount() > 0) {
        $msg = "Username sudah digunakan!";
    } else {
        $query = $koneksi->prepare("INSERT INTO users (nama, username, password) VALUES (?,?,?)");
        $query->execute([$nama, $username, $password]);
        $success = "Akun berhasil dibuat! Silakan login.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Register Admin | UMKM Seblak</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body{
    background: url('assets/img/bg-login.jpg') center/cover no-repeat;
    height:100vh; display:flex; justify-content:center; align-items:center;
    font-family: "Poppins", sans-serif; position:relative;
    margin:0; padding:0; overflow:hidden;
}
body::before{
    content:""; position:absolute; inset:0; z-index:1;
    background:rgba(0,0,0,0.55); backdrop-filter:blur(5px);
}

.register-box{
    position:relative; z-index:2;
    width:360px; padding:32px 28px;
    border-radius:18px; 
    backdrop-filter:blur(15px);
    background:rgba(255,255,255,0.20);
    border:1px solid rgba(255,255,255,0.25);
    box-shadow:0 18px 38px rgba(0,0,0,.45);
    animation:show .4s ease-out;
}

@keyframes show{
    from{transform:translateY(25px); opacity:0;}
    to{transform:translateY(0); opacity:1;}
}

.register-title{
    font-size:22px; font-weight:700; color:#fff;
    text-align:center; margin-bottom:18px;
}

.input-group-text, .form-control{
    background:rgba(255,255,255,.88);
    border:none; 
}

.form-control:focus{background:white;}
.btn-register{
    width:100%; border:none; padding:11px;
    background:linear-gradient(90deg,#ff4b2b,#ff416c);
    border-radius:12px; font-weight:600; color:white;
}

.reg{ text-align:center; margin-top:12px; color:white; font-size:14px;}
.reg a{ color:#ffd2d2; font-weight:600; text-decoration:none; }
.reg a:hover{ text-decoration:underline; }

.password-wrapper{ position:relative; }
.password-eye{
    position:absolute; right:13px; top:50%;
    transform:translateY(-50%); cursor:pointer;
    font-size:18px; opacity:.8;
}
.password-eye:hover{opacity:1}

/* ✅ Prevent screen flicker */
.swal2-container { backdrop-filter:none !important; }
.swal2-popup{ z-index:9999; }
</style>
</head>

<body>

<div class="register-box">
    <div class="register-title">Buat Akun Admin</div>

    <form method="POST">
        <div class="mb-2 input-group">
            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
            <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
        </div>

        <div class="mb-2 input-group">
            <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>

        <div class="mb-3 password-wrapper">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" id="passwordInput" name="password" class="form-control" placeholder="Password" required>
            </div>
            <i id="eyeIcon" class="bi bi-eye-slash password-eye" onclick="togglePassword()"></i>
        </div>

        <button class="btn-register">Register</button>
    </form>

    <div class="reg">Sudah punya akun? <a href="login.php">Login</a></div>
</div>

<?php if ($msg): ?>
<script>
Swal.fire({
    icon:"error",
    title:"Gagal",
    text:"<?= $msg ?>",
    confirmButtonColor:"#ff416c"
});
</script>
<?php endif; ?>

<?php if ($success): ?>
<script>
document.addEventListener("DOMContentLoaded", ()=>{
    setTimeout(()=>{
        Swal.fire({
            icon:"success",
            title:"Berhasil!",
            text:"Akun berhasil dibuat, silakan login.",
            confirmButtonText:"OK",
            allowOutsideClick:false,
            allowEscapeKey:false,
            heightAuto:false
        }).then(()=> window.location="login.php");
    }, 200);
});
</script>
<?php endif; ?>

<script>
function togglePassword(){
    const pw=document.getElementById("passwordInput");
    const icon=document.getElementById("eyeIcon");
    pw.type = (pw.type === "password") ? "text" : "password";
    icon.classList.toggle("bi-eye");
    icon.classList.toggle("bi-eye-slash");
}
</script>

</body>
</html>
