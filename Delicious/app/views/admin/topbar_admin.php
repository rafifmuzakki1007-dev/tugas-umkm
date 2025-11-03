<div class="topbar d-flex justify-content-between align-items-center shadow-sm px-3 py-2"
     style="background:var(--card); border-bottom:1px solid #ddd;">

    <!-- Search -->
    <div style="width:300px">
        <div class="position-relative">
            <i class="bi bi-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--subtext)"></i>
            <input class="form-control"
                   style="border-radius:30px;padding-left:35px;background:rgba(0,0,0,.03);border:none;"
                   placeholder="Cari menu, data...">
        </div>
    </div>

    <!-- Right -->
    <div class="d-flex align-items-center gap-3">

        <!-- Theme Toggle -->
        <button class="btn btn-light border" onclick="toggleTheme()" title="Dark/Light Mode">
            <i class="bi bi-moon-stars"></i>
        </button>

        <!-- Profile -->
        <div class="dropdown">
            <div class="d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown" style="cursor:pointer;">
                <img src="https://i.pravatar.cc/40" class="rounded-circle">
                <div>
                    <strong><?= $_SESSION['user']['nama'] ?? 'Admin'; ?></strong><br>
                    <small style="color:var(--subtext)">Admin</small>
                </div>
            </div>

            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item" href="index.php?page=profile_admin"><i class="bi bi-person"></i> Profil</a></li>
                <li><a class="dropdown-item text-danger" href="index.php?page=logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</div>
