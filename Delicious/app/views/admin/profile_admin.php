<?php
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$id = $user['id'];

$stmt = $koneksi->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$fotoURL = !empty($admin['foto'])
    ? "uploads/admin/".$admin['foto']
    : "https://ui-avatars.com/api/?background=random&name=".urlencode($admin['nama']);

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $fotoBaru = $admin['foto'];

    if (!empty($_FILES['foto']['name'])) {
        $file = time()."_".$_FILES['foto']['name'];
        $tmp  = $_FILES['foto']['tmp_name'];

        if (!is_dir("uploads/admin")) mkdir("uploads/admin", 0777, true);
        move_uploaded_file($tmp, "uploads/admin/".$file);
        $fotoBaru = $file;
    }

    $update = $koneksi->prepare("UPDATE users SET nama=?, username=?, foto=? WHERE id=?");
    $update->execute([$nama, $username, $fotoBaru, $id]);

    $_SESSION['user']['nama'] = $nama;
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['foto'] = $fotoBaru;

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
    Swal.fire({
    icon: 'success',
    title: 'Perubahan Disimpan!',
    text: 'Profil admin berhasil diperbarui',
    confirmButtonColor: '#3085d6'
}).then(() => {
    window.location.href='index.php?page=profile_admin';
});
</script>";

}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="d-flex justify-content-center mt-4">
<div class="card shadow-sm p-4 rounded-4" style="width:480px;">

    <h3 class="fw-bold text-center mb-4">Profil Admin <i class="bi bi-person-badge-fill"></i></h3>

    <div class="text-center mb-3">
        <img src="<?= $fotoURL ?>" 
            class="rounded-circle shadow"
            width="140" height="140" 
            style="object-fit:cover; border:4px solid #fff; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
    </div>

    <form method="POST" enctype="multipart/form-data">

        <label>Nama Lengkap</label>
        <input class="form-control rounded-pill mb-3" name="nama" value="<?= htmlspecialchars($admin['nama']) ?>" required>

        <label>Username</label>
        <input class="form-control rounded-pill mb-3" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>

        <label>Foto Baru</label>
        <input class="form-control mb-4" type="file" name="foto">

        <button class="btn btn-primary rounded-pill w-100 py-2" name="update">
            Simpan Perubahan
        </button>

    </form>
</div>
</div>
