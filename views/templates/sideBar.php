<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
        <div class="sidebar-brand-icon mx-3 mt-3">
            <i class="fas fa-layer-group"></i>
        </div>
        <div class="sidebar-brand-text mx-3 mt-3">SIMPLE ADDITIVE WEIGHTING</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-3">

    <!-- Nav Item - Dashboard -->
    <!-- Cek apakah $pageTitle adalah 'Dashboard', jika ya, tambahkan kelas 'active' -->
    <li class="nav-item <?php echo (isset($pageTitle) && $pageTitle == 'dashboard') ? 'active' : ''; ?>">
        <a class="nav-link" href="dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <!-- Nav Item - Data Kriteria -->
    <li class="nav-item <?php echo (isset($pageTitle) && $pageTitle == 'criterias') ? 'active' : ''; ?>">
        <a class="nav-link" href="criterias.php">
            <i class="fas fa-fw fa-file"></i>
            <span>Data Kriteria</span></a>
    </li>
    <!-- Nav Item - Data Sub Kriteria -->
    <li class="nav-item <?php echo (isset($pageTitle) && $pageTitle == 'subcriterias') ? 'active' : ''; ?>">
        <a class="nav-link" href="subcriterias.php">
            <i class="fas fa-fw fa-file-invoice"></i>
            <span>Data Sub Kriteria</span></a>
    </li>
    <!-- Nav Item - Data Alternatif -->
    <li class="nav-item <?php echo (isset($pageTitle) && $pageTitle == 'alternatives') ? 'active' : ''; ?>">
        <a class="nav-link" href="alternatives.php">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Data Alternatif</span></a>
    </li>
    <!-- Nav Item - Data Perhitungan -->
    <li class="nav-item <?php echo (isset($pageTitle) && $pageTitle == 'calculations') ? 'active' : ''; ?>">
        <a class="nav-link" href="calculations.php">
            <i class="fas fa-fw fa-calculator"></i>
            <span>Data Perhitungan</span></a>
    </li>
    <!-- Nav Item - Data Hasil -->
    <li class="nav-item <?php echo (isset($pageTitle) && $pageTitle == 'results') ? 'active' : ''; ?>">
        <a class="nav-link" href="results.php">
            <i class="fas fa-fw fa-trophy"></i>
            <span>Data Hasil</span></a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->
