<!-- SEARCH ADMIN -->
<form method="GET" action="index.php" class="d-none d-md-flex align-items-center me-3" style="width:250px;">
    <input type="hidden" name="page" value="menu_admin">
    
    <div class="input-group input-group-sm">
        <input 
            type="text" 
            name="search" 
            class="form-control"
            placeholder="Cari menu..."
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
        >
        <button class="btn btn-outline-primary" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>
