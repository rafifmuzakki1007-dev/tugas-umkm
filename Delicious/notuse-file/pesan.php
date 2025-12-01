<?php include 'app/views/sections/header_nav.php'; ?>

<div class="container" style="margin-top:150px; max-width:600px;">
    <h3 class="text-center mb-4">Pesan Menu</h3>

    <div class="card shadow p-4">

        <form action="index.php?page=pesan_process" method="POST">

            <input type="hidden" name="id_menu" value="<?= $_GET['id']; ?>">

            <div class="mb-3">
                <label class="form-label">Nama Menu</label>
                <input type="text" class="form-control" value="<?= $_GET['menu']; ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Harga Satuan (Rp)</label>
                <input type="text" class="form-control" value="<?= number_format($_GET['harga'],0,',','.'); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Pesanan</label>
                <input type="number" name="jumlah" class="form-control" min="1" value="1" required>
            </div>

            <button type="submit" class="btn btn-warning w-100 fw-bold">
                Konfirmasi Pesanan
            </button>
        </form>

    </div>
</div>
