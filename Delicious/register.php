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
    $password = md5($_POST['password']);

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body{
    background: url('assets/img/bg-login.jpg') center/cover no-repeat;
    height:100vh; display:flex; justify-content:center; align-items:center;
    font-family: "Poppins", sans-serif; position:relative; margin:0; padding:0;
}
body::before{
    content:""; position:absolute; inset:0;
    background:rgba(0,0,0,0.50); backdrop-filter:blur(6px);
}
.register-box{
    position:relative; width:360px;
    background: rgba(255,255,255,0.22);
    border:1px solid rgba(255,255,255,0.35);
    backdrop-filter: blur(15px);
    border-radius:18px; padding:32px 28px;
    box-shadow:0 18px 38px rgba(0,0,0,.45);
    animation: zoom .5s ease-out;
    z-index:5;
}
@keyframes zoom{
    from{transform:scale(.85); opacity:0;}
    to{transform:scale(1); opacity:1;}
}
.register-title{
    font-weight:700; font-size:22px;
    text-align:center; margin-bottom:18px; color:#fff;
}
.input-group-text{
    background:rgba(255,255,255,.85);
    border:none; border-radius:12px 0 0 12px;
}
.form-control{
    border:none; border-radius:0 12px 12px 0;
    background:rgba(255,255,255,.85);
}
.form-control:focus{
    background:white;
}
.btn-register{
    background:linear-gradient(90deg,#ff4b2b,#ff416c);
    border:none; border-radius:12px;
    padding:10px; font-weight:600;
    width:100%; color:white;
    transition:.25s;
}
.btn-register:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(255,65,108,.45);
}
.reg{
    text-align:center; margin-top:14px;
    font-size:14px; color:#fff;
}
.reg a{
    text-decoration:none; font-weight:600; color:#ffbaba;
}
.reg a:hover{ text-decoration:underline; }

/* Eye Icon */
.password-wrapper{
    position:relative;
}
.password-eye{
    position:absolute;
    right:14px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    font-size:18px;
    opacity:.8;
    transition:.2s;
}
.password-eye:hover{
    opacity:1;
    transform:translateY(-50%) scale(1.1);
}
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

        <!-- Password field -->
        <div class="mb-3 password-wrapper">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" id="passwordInput" name="password" class="form-control" placeholder="Password" required>
            </div>
            <i class="bi bi-eye-slash password-eye" id="eyeIcon" onclick="togglePassword()"></i>
        </div>

        <button class="btn-register">Register</button>
    </form>

    <div class="reg">
        Sudah punya akun? <a href="login.php">Login</a>
    </div>
</div>

<?php if ($msg): ?>
<script>Swal.fire("Gagal", "<?= $msg ?>", "error");</script>
<?php endif; ?>

<?php if ($success): ?>
<script>
Swal.fire({title:"Berhasil!", text:"Akun berhasil dibuat, silakan login.", icon:"success"})
.then(()=> window.location="login.php");
</script>
<?php endif; ?>

<script>
function togglePassword(){
    const pw=document.getElementById("passwordInput");
    const icon=document.getElementById("eyeIcon");
    if(pw.type==="password"){
        pw.type="text";
        icon.classList.replace("bi-eye-slash","bi-eye");
    } else {
        pw.type="password";
        icon.classList.replace("bi-eye","bi-eye-slash");
    }
}
</script>

</body>
</html>
