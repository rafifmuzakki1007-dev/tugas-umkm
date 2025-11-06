<?php
// ------- Fallback data -------
$lowStockCount = isset($lowStockCount) ? (int)$lowStockCount : 0;
$lowStockMenus = isset($lowStockMenus) && is_array($lowStockMenus) ? $lowStockMenus : [];
$user = $_SESSION['user'] ?? [];

// Nama asli dari session
$rawName = $user['username'] ?? $user['nama'] ?? 'Admin';

// Jika email, tampilkan sebelum "@"
if (strpos($rawName, '@') !== false) {
    $displayName = explode('@', $rawName)[0];
} else {
    $displayName = $rawName;
}

// Short name utk UI (antisipasi panjang)
$shortName = strlen($displayName) > 14 ? substr($displayName, 0, 12) . "…" : $displayName;

// Avatar
$avatar = !empty($user['foto'])
    ? 'uploads/admin/' . $user['foto']
    : 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode($displayName);
?>

<div class="topbar d-flex justify-content-between align-items-center shadow-sm px-3 py-2"
     style="background:var(--card); border-bottom:1px solid #ddd;">

    <!-- ✅ SEARCH MENU (FIXED) -->
    <div style="width:320px">
        <form method="GET" action="index.php" class="position-relative">
            <input type="hidden" name="page" value="menu_admin">

            <i class="bi bi-search position-absolute"
            style="left:12px;top:50%;transform:translateY(-50%);color:var(--subtext)"></i>

            <input 
                class="form-control"
                style="border-radius:30px;padding-left:35px;background:rgba(0,0,0,.03);border:none;"
                placeholder="Cari menu, data..."
                name="search"
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
            >
        </form>
    </div>

    <!-- Right section -->
    <div class="d-flex align-items-center gap-2 gap-md-3">

        <!-- Theme toggle -->
        <button class="btn btn-light border d-flex align-items-center justify-content-center"
                style="width:42px;height:42px;border-radius:12px"
                onclick="toggleTheme()" title="Dark/Light Mode">
            <i class="bi bi-moon-stars"></i>
        </button>

        <!-- Notification -->
        <div class="dropdown">
            <button class="btn position-relative d-flex align-items-center justify-content-center <?php echo ($lowStockCount>0 ? 'bell-alert' : ''); ?>"
                    data-bs-toggle="dropdown"
                    style="width:42px;height:42px;border:none;background:none;border-radius:12px;">
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                      style="<?php echo $lowStockCount>0 ? '' : 'display:none;'; ?>">
                    <?php echo $lowStockCount; ?>
                </span>
            </button>

            <div class="dropdown-menu dropdown-menu-end p-0 shadow" style="min-width:320px;">
                <div class="p-3 border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                    <strong>Stok Hampir Habis</strong>
                </div>

                <?php if($lowStockCount > 0): ?>
                    <div class="list-group list-group-flush" style="max-height:260px; overflow:auto;">
                        <?php foreach($lowStockMenus as $m): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold"><?php echo htmlspecialchars($m['nama_menu']); ?></div>
                                <small class="text-muted">ID: <?php echo htmlspecialchars($m['id_menu']); ?></small>
                            </div>
                            <span class="badge rounded-pill bg-warning text-dark">
                                <?php echo (int)$m['stok']; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="p-2 text-end">
                        <a href="index.php?page=menu_admin" class="btn btn-sm btn-outline-primary">Kelola Stok</a>
                    </div>
                <?php else: ?>
                    <div class="p-3">
                        <span class="text-success"><i class="bi bi-check2-square"></i> Semua stok aman</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Profile -->
        <div class="dropdown">
            <div class="profile-wrap dropdown-toggle" data-bs-toggle="dropdown" style="cursor:pointer;">
                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="avatar"
                     class="rounded-circle flex-shrink-0"
                     width="36" height="36" style="object-fit:cover;">
                <div class="name-stack d-none d-md-flex">
                    <strong class="user-name-short" title="<?php echo htmlspecialchars($rawName); ?>">
                        <?php echo htmlspecialchars($shortName); ?>
                    </strong>
                    <small class="text-muted">Admin</small>
                </div>
                <i class="bi bi-caret-down-fill text-muted ms-1 d-none d-md-inline"></i>
            </div>

            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item" href="index.php?page=profile_admin"><i class="bi bi-person"></i> Profil</a></li>
                <li><a class="dropdown-item text-danger" href="index.php?page=logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</div>

<style>
.topbar .profile-wrap{
    display:flex;
    align-items:center;
    gap:10px;
    min-height:42px;
}
.topbar .profile-wrap.dropdown-toggle::after{
    display: none !important;
}
.topbar .name-stack{
    display:flex;
    flex-direction:column;
    justify-content:center;
    min-height:36px;
    line-height:1.1;
}
.topbar .user-name-short{
    max-width:150px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

/* Bell animation */
@keyframes ring {
  0% { transform: rotate(0); }
  10% { transform: rotate(10deg); }
  20% { transform: rotate(-10deg); }
  30% { transform: rotate(6deg); }
  40% { transform: rotate(-6deg); }
  50% { transform: rotate(3deg); }
  60% { transform: rotate(-3deg); }
  100% { transform: rotate(0); }
}
.bell-alert i { animation: ring .8s ease infinite; transform-origin: top center; }
</style>
