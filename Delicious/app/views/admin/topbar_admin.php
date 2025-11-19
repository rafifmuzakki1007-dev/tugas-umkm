<?php 
$lowStockCount = isset($lowStockCount) ? (int)$lowStockCount : 0;
$lowStockMenus = isset($lowStockMenus) && is_array($lowStockMenus) ? $lowStockMenus : [];
$user = $_SESSION['user'] ?? [];

// Nama user
$rawName = $user['username'] ?? $user['nama'] ?? 'Admin';
$displayName = (strpos($rawName, '@') !== false) ? explode('@', $rawName)[0] : $rawName;
$shortName = strlen($displayName) > 14 ? substr($displayName, 0, 12) . "…" : $displayName;

// Avatar
$avatar = !empty($user['foto'])
    ? "app/views/uploads/admin/" . $user['foto']
    : "https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=" . urlencode($displayName);
?>

<!-- TOPBAR FINAL -->
<div class="topbar-wrapper">
    <div class="topbar">

        <!-- LEFT -->
        <div class="topbar-left">
            <form method="GET" action="index.php" class="search-form">
                <input type="hidden" name="page" value="menu_admin">
                <i class="bi bi-search search-icon"></i>
                <input 
                    class="form-control search-input"
                    placeholder="Cari menu, data..."
                    name="search"
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                >
            </form>
        </div>

        <!-- RIGHT -->
        <div class="topbar-right">

            <!-- Theme -->
            <button class="btn action-btn" onclick="toggleTheme()">
                <i class="bi bi-moon-stars"></i>
            </button>

            <!-- Notif -->
            <div class="dropdown">
                <button class="btn action-btn position-relative <?= ($lowStockCount>0?'bell-alert':'') ?>"
                        data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>

                    <?php if($lowStockCount>0): ?>
                        <span class="notif-badge"><?= $lowStockCount ?></span>
                    <?php endif ?>
                </button>

                <div class="dropdown-menu dropdown-menu-end shadow p-0" style="min-width:310px;">
                    <div class="p-3 border-bottom d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        <strong>Stok Hampir Habis</strong>
                    </div>

                    <?php if($lowStockCount > 0): ?>
                        <div class="list-group list-group-flush" style="max-height:260px; overflow-y:auto;">
                            <?php foreach($lowStockMenus as $m): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold"><?= htmlspecialchars($m['nama_menu']) ?></div>
                                    <small class="text-muted">ID: <?= htmlspecialchars($m['id_menu']) ?></small>
                                </div>
                                <span class="badge bg-warning text-dark"><?= (int)$m['stok'] ?></span>
                            </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <div class="p-3 text-success">
                            <i class="bi bi-check2"></i> Semua stok aman
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <!-- Profile -->
            <div class="dropdown">
                <div class="profile-wrap" data-bs-toggle="dropdown">

                    <img src="<?= htmlspecialchars($avatar) ?>" 
                         class="rounded-circle profile-avatar" width="38" height="38">

                    <div class="profile-text d-none d-md-block">
                        <div class="fw-semibold profile-name"><?= htmlspecialchars($shortName) ?></div>
                        <small class="text-muted">Admin</small>
                    </div>

                    <!-- FIX caret kecil -->
                    <i class="bi bi-chevron-down small ms-1 text-muted caret-profile"></i>
                </div>

                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="index.php?page=profile_admin"><i class="bi bi-person me-2"></i> Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" id="btnLogout" href="index.php?do_logout=1">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>

<style>
.topbar-wrapper { width:100%; }

/* MAIN CONTAINER */
.topbar {
    background:var(--card);
    padding:10px 14px;
    border-radius:10px;
    border:1px solid rgba(0,0,0,0.05);
    box-shadow:0 3px 10px rgba(0,0,0,0.04);
    display:flex;
    align-items:center;
    gap:15px;
}

/* LEFT */
.topbar-left { flex:1; }
.search-form { position:relative; }
.search-icon {
    position:absolute; left:14px; top:50%; transform:translateY(-50%);
    color:var(--subtext);
}
.search-input {
    padding:10px 15px 10px 40px;
    border-radius:28px;
    background:#f3f4f6;
    border:none;
    min-height:42px;
}

/* RIGHT */
.topbar-right {
    display:flex; align-items:center; gap:6px;
}

.action-btn {
    width:40px; height:40px;
    display:flex; align-items:center; justify-content:center;
    border-radius:10px;
    background:transparent;
}
.action-btn:hover { background:#f5f5f5; }

/* Notif badge */
.notif-badge {
    position:absolute;
    top:4px; right:4px;
    background:#ef4444; color:white;
    font-size:10px;
    padding:2px 6px; border-radius:999px;
}

/* Profile */
.profile-wrap {
    display:flex; align-items:center;
    gap:8px; padding:6px 10px;
    border-radius:10px; cursor:pointer;
}
.profile-wrap:hover { background:#f3f4f6; }

.profile-avatar { object-fit:cover; }

.caret-profile {
    font-size: .75rem;       /* kecil */
    margin-left: 4px;
    margin-top: 2px;
}

/* Remove BS caret */
.profile-wrap::after { display:none !important; }

/* Bell animation */
@keyframes ring {
    0%{transform:rotate(0)} 10%{transform:rotate(10deg)}
    20%{transform:rotate(-10deg)} 30%{transform:rotate(6deg)}
    40%{transform:rotate(-6deg)} 50%{transform:rotate(3deg)}
    60%{transform:rotate(-3deg)} 100%{transform:rotate(0)}
}
.bell-alert i { animation:ring .9s infinite; transform-origin:top center; }
</style>

<script>
// FIX: cegah loader saat buka dropdown profil & bell
document.querySelectorAll('.profile-wrap, .action-btn[data-bs-toggle="dropdown"]').forEach(el => {
    el.addEventListener('click', e => e.stopPropagation());
});
</script>
