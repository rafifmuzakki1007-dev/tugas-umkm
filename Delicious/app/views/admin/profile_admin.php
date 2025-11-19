<?php
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$id   = $user['id'];

// ambil data admin
$stmt = $koneksi->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// HANDLE KOSONG (biar tidak undefined)
$admin['alamat_pribadi'] = $admin['alamat_pribadi'] ?? '';
$admin['kota']           = $admin['kota'] ?? '';
$admin['nama_usaha']     = $admin['nama_usaha'] ?? '';
$admin['telepon']        = $admin['telepon'] ?? '';
$admin['alamat']         = $admin['alamat'] ?? '';

// FOLDER UPLOAD
$dir = realpath(__DIR__ . "/../uploads/admin");
if (!is_dir($dir)) mkdir($dir, 0777, true);

// FOTO
$webBase = "app/views/uploads/admin/";
if (!empty($admin['foto']) && file_exists($dir . "/" . $admin['foto'])) {
    $fotoURL = $webBase . $admin['foto'];
} else {
    $fotoURL = "https://ui-avatars.com/api/?background=random&name=" . urlencode($admin['nama']);
}


// PROSES UPDATE
if (isset($_POST['update'])) {

    $nama            = $_POST['nama'] ?? $admin['nama'];
    $username        = $_POST['username'] ?? $admin['username'];
    $nama_usaha      = $_POST['nama_usaha'] ?? $admin['nama_usaha'];
    $telepon         = $_POST['telepon'] ?? $admin['telepon'];
    $alamat_usaha    = $_POST['alamat'] ?? $admin['alamat'];

    // tambahan baru
    $alamat_pribadi  = $_POST['alamat_pribadi'] ?? $admin['alamat_pribadi'];
    $kota            = $_POST['kota'] ?? $admin['kota'];

    $fotoBaru = $admin['foto'];

    // HAPUS FOTO
    if (isset($_POST['hapus_foto']) && $_POST['hapus_foto'] === '1') {
        $oldFile = $dir . "/" . $admin['foto'];
        if (!empty($admin['foto']) && file_exists($oldFile)) @unlink($oldFile);
        $fotoBaru = "";
    }

    // UPLOAD BARU
    if (!empty($_FILES['foto']['name'])) {

        $allowed = ['image/png','image/jpeg','image/jpg'];
        if (!in_array($_FILES['foto']['type'], $allowed)) {
            echo "<script>alert('Format foto harus JPG/PNG');history.back();</script>";
            exit;
        }
        if ($_FILES['foto']['size'] > 2*1024*1024) {
            echo "<script>alert('Foto maksimal 2MB');history.back();</script>";
            exit;
        }

        $fileName = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/","", $_FILES['foto']['name']);
        $target   = $dir . "/" . $fileName;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            if (!empty($admin['foto'])) {
                $old = $dir . "/" . $admin['foto'];
                if (file_exists($old)) @unlink($old);
            }
            $fotoBaru = $fileName;
        }
    }

    // UPDATE DB
    $update = $koneksi->prepare("
        UPDATE users 
        SET nama=?, username=?, nama_usaha=?, telepon=?, alamat=?,
            alamat_pribadi=?, kota=?, foto=?
        WHERE id=?
    ");
    $update->execute([
        $nama, $username, $nama_usaha, $telepon, $alamat_usaha,
        $alamat_pribadi, $kota, $fotoBaru, $id
    ]);

    // UPDATE SESSION
    $_SESSION['user']['nama'] = $nama;
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['foto'] = $fotoBaru;

    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({icon:'success',title:'Berhasil!',text:'Profil berhasil diperbarui.'})
        .then(()=>{ window.location.href='index.php?page=profile_admin'; });
    </script>";
    exit;
}
?>

<!-- LOAD TOMSELECT -->
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>


<!-- UI PROFILE -->
<div class="d-flex justify-content-center mt-4">
<div class="card shadow-lg border-0 p-5 rounded-4 profile-card">

    <div class="text-center mb-4">
        <h2 class="fw-bold">Profil Admin</h2>
        <p class="text-muted">Kelola informasi akun & identitas usaha Anda</p>
    </div>

    <form method="POST" enctype="multipart/form-data" id="formProfil">
        <input type="hidden" name="update" value="1">
        <input type="hidden" name="hapus_foto" id="hapusFotoField" value="0">

        <div class="text-center mb-3">
            <img id="previewFoto" src="<?= htmlspecialchars($fotoURL) ?>" 
                 class="rounded-circle profil-foto" width="150" height="150" style="object-fit:cover;">
        </div>

        <div class="d-flex justify-content-center gap-2 mb-4">
            <label class="btn btn-outline-primary rounded-pill px-4" style="cursor:pointer">
                <i class="bi bi-camera"></i> Pilih Foto
                <input type="file" name="foto" id="fotoInput" accept="image/*" hidden>
            </label>

            <?php if (!empty($admin['foto'])): ?>
            <button type="button" id="hapusFotoBtn" class="btn btn-outline-danger rounded-pill px-4">
                <i class="bi bi-trash"></i> Hapus Foto
            </button>
            <?php endif; ?>
        </div>


        <!-- INFORMASI PRIBADI -->
        <div class="section-box mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-badge"></i> Informasi Pribadi</h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input class="form-control input-soft" name="nama" value="<?= htmlspecialchars($admin['nama']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Username / Email</label>
                    <input class="form-control input-soft" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Alamat Lengkap</label>
                    <input class="form-control input-soft" name="alamat_pribadi" 
                           value="<?= htmlspecialchars($admin['alamat_pribadi']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kabupaten / Kota</label>
                    <select id="selectKota" name="kota" placeholder="Pilih Kabupaten / Kota">
                        <option value="">-- Pilih --</option>
                        <?php 
                        $kotas = ["Nganjuk","Kediri","Jombang","Madiun","Surabaya","Malang","Blitar","Tuban","Bojonegoro"];
                        foreach ($kotas as $k) {
                            $sel = ($admin['kota'] === $k) ? "selected" : "";
                            echo "<option value='$k' $sel>$k</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>


        <!-- INFORMASI USAHA -->
        <div class="section-box mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-shop"></i> Informasi Usaha</h5>

            <label class="form-label">Nama Usaha / UMKM</label>
            <input class="form-control input-soft mb-3" name="nama_usaha" 
                   value="<?= htmlspecialchars($admin['nama_usaha']) ?>" required>

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Telepon (Opsional)</label>
                    <input class="form-control input-soft" name="telepon" 
                           value="<?= htmlspecialchars($admin['telepon']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Alamat (Opsional)</label>
                    <input class="form-control input-soft" name="alamat" 
                           value="<?= htmlspecialchars($admin['alamat']) ?>">
                </div>
            </div>
        </div>

        <button class="btn btn-primary rounded-pill w-100 py-2 fw-semibold">
            Simpan Perubahan
        </button>

    </form>
</div>
</div>

<style>
.profil-foto { border:5px solid #fff; box-shadow:0 10px 25px rgba(0,0,0,0.18); transition:.3s; }
.profil-foto:hover { transform:scale(1.03); box-shadow:0 14px 35px rgba(0,0,0,0.26); }
.section-box { padding:18px; background:#f8fafc; border-radius:14px; border:1px solid #e6eef6; }
.input-soft { border-radius:14px; background:#f1f5f9; border:1px solid #e5e7eb; }
.input-soft:focus { background:#fff; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.25); }

.profile-card {
    width: 650px;
    background: var(--card);
}

@media(max-width: 768px){
    .profile-card {
        width: 100%;
        padding: 25px !important;
    }
}

</style>


<script>
new TomSelect("#selectKota",{
    placeholder: "Pilih Kabupaten / Kota",
    allowEmptyOption: true,
    create: false,
    maxOptions: 100,
    persist: false,
    render:{
        option:function(data,escape){
            return `<div class='py-2 px-2'>${escape(data.text)}</div>`;
        }
    }
});

// Preview foto
document.getElementById('fotoInput').addEventListener('change', function(){
    const f = this.files[0];
    if (!f) return;
    if (!['image/png','image/jpeg','image/jpg'].includes(f.type)) {
        Swal.fire('Format tidak valid','Format harus JPG/JPEG/PNG','error'); 
        this.value=''; 
        return;
    }
    if (f.size > 2*1024*1024) {
        Swal.fire('File terlalu besar','Maks 2MB','error'); 
        this.value=''; 
        return;
    }
    document.getElementById('previewFoto').src = URL.createObjectURL(f);
    document.getElementById('hapusFotoField').value = "0";
});

// Hapus foto
<?php if (!empty($admin['foto'])): ?>
document.getElementById('hapusFotoBtn').addEventListener('click', function(){
    Swal.fire({
        icon:'warning',
        title:'Hapus Foto Profil?',
        text:'Foto akan dihapus permanen dari server.',
        showCancelButton:true,
        confirmButtonColor:'#d33',
        confirmButtonText:'Ya, Hapus'
    }).then(res=>{
        if(res.isConfirmed){
            document.getElementById('hapusFotoField').value='1';
            document.getElementById('formProfil').submit();
        }
    });
});
<?php endif; ?>
</script>
