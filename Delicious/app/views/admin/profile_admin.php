<div class="page-fade">
<h3 class="fw-bold mb-3">Profil Admin 👤</h3>

<div class="card p-4 shadow-sm" style="max-width:420px">
    <div class="text-center mb-3">
        <img src="https://i.pravatar.cc/120" class="rounded-circle mb-2">
        <h5><?= $_SESSION['user']['nama'] ?></h5>
        <small class="text-muted">Administrator</small>
    </div>

    <form>
        <label class="mb-1">Nama</label>
        <input type="text" class="form-control mb-3" value="<?= $_SESSION['user']['nama'] ?>">

        <label class="mb-1">Username</label>
        <input type="text" class="form-control mb-3" value="<?= $_SESSION['user']['username'] ?>">

        <label class="mb-1">Password Baru</label>
        <input type="password" class="form-control mb-3" placeholder="Kosongkan jika tidak ganti">

        <button class="btn btn-primary w-100">Simpan Perubahan</button>
    </form>
</div>
</div>
