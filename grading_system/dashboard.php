<?php
require_once 'config.php';
require_once 'functions.php';
requireLogin();

// Fetch summary metrics
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalTeachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$studentsAtRisk = $pdo->query("SELECT COUNT(*) FROM grades WHERE status = 'Berisiko'")->fetchColumn();
$safeStudents = $pdo->query("SELECT COUNT(*) FROM grades WHERE status = 'Aman'")->fetchColumn();
$totalSubjects = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();

require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Format tanggal ke Bahasa Indonesia (Sederhana)
$hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$bulan = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
$tanggal_indo = $hari[date('l')] . ', ' . date('d') . ' ' . $bulan[date('F')] . ' ' . date('Y');
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Ringkasan Beranda</h3>
        <span class="text-muted"><?= $tanggal_indo ?></span>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded p-3 me-3 card-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Total Siswa</p>
                        <h3 class="fw-bold mb-0"><?= $totalStudents ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-info text-dark rounded p-3 me-3 card-icon">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Total Mapel</p>
                        <h3 class="fw-bold mb-0"><?= $totalSubjects ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded p-3 me-3 card-icon">
                        <i class="fa-solid fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Penilaian Aman</p>
                        <h3 class="fw-bold mb-0"><?= $safeStudents ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-danger text-white rounded p-3 me-3 card-icon">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Penilaian Berisiko</p>
                        <h3 class="fw-bold mb-0"><?= $studentsAtRisk ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Add any charts or quick lists here -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-bar text-primary me-2"></i>Informasi Model Prediksi</h5>
                <p>Sistem ini menggunakan regresi linier berganda untuk memprediksi risiko per individu untuk masing-masing Mata Pelajaran (Mapel):</p>
                <div class="bg-light p-3 rounded font-monospace" style="display:inline-block">
                    <strong>Y = 15 + 0.3 &times; (Tugas) + 0.2 &times; (UTS) + 0.3 &times; (UAS) + 0.2 &times; (Kehadiran)</strong>
                </div>
                <ul class="mt-3 text-muted">
                    <li>Jika prediksi nilai <code>Y</code> kurang dari KKM (Default: 70), siswa masuk ke status <strong>Berisiko</strong> pada mapel tersebut.</li>
                    <li>Jika <code>Y</code> &ge; KKM, siswa diprediksi <strong>Aman</strong>.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
