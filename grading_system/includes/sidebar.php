<!-- Sidebar -->
<div class="sidebar p-3" style="width: 250px;">
    <div class="d-flex align-items-center mb-4 px-2 mt-2">
        <i class="fa-solid fa-graduation-cap fa-2x text-primary me-2"></i>
        <h5 class="mb-0 fw-bold brand-text">Sistem<br>Penilaian</h5>
    </div>
    <hr>
    
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line"></i> Beranda
            </a>
        </li>
        <li class="nav-item">
            <a href="students.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Data Siswa
            </a>
        </li>
        <li class="nav-item">
            <a href="grades.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'grades.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-star"></i> Input Nilai
            </a>
        </li>
        <li class="nav-item">
            <a href="reports.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-contract"></i> Laporan & Risiko
            </a>
        </li>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li class="nav-item mt-3">
            <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted text-uppercase" style="font-size: 0.75rem;">
                <span>Panel Admin</span>
            </h6>
        </li>
        <li class="nav-item">
            <a href="subjects.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'subjects.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-book"></i> Mata Pelajaran
            </a>
        </li>
        <li class="nav-item">
            <a href="teachers.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'teachers.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-chalkboard-user"></i> Manajemen Guru
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <hr class="mt-auto">
    
    <div class="px-2 pb-3">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                <i class="fa-solid fa-user"></i>
            </div>
            <div>
                <small class="d-block text-muted">Login sebagai</small>
                <div class="fw-bold" style="font-size: 0.9rem;">
                    <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User' ?> 
                    <span class="badge bg-secondary"><?= isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : '' ?></span>
                </div>
            </div>
        </div>
        <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar
        </a>
    </div>
</div>
<!-- Main Content Wrapper -->
<div class="flex-grow-1 bg-light main-content">
