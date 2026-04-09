<?php
require_once 'config.php';
require_once 'functions.php';
requireLogin();

// Fetch grades with subject
$sql = "SELECT s.id as student_id, s.name, s.class, 
               sub.name as subject_name,
               g.tugas, g.uts, g.uas, g.attendance, g.predicted_score, g.status
        FROM students s 
        INNER JOIN grades g ON s.id = g.student_id
        INNER JOIN subjects sub ON sub.id = g.subject_id
        ORDER BY s.class ASC, s.name ASC, sub.name ASC";
$reports = $pdo->query($sql)->fetchAll();

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Laporan Penilaian & Risiko (Per Mapel)</h3>
        <button class="btn btn-outline-success" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Cetak Laporan
        </button>
    </div>

    <div class="card table-custom border-0 p-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Siswa</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Tugas</th>
                        <th>UTS</th>
                        <th>UAS</th>
                        <th>Kehadiran</th>
                        <th>Prediksi Skor (Y)</th>
                        <th class="pe-4">Status Risiko</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $row): ?>
                    <tr>
                        <td class="ps-4 fw-bold"><?= htmlspecialchars($row['name']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row['class']) ?></span></td>
                        <td class="fw-bold text-info"><?= htmlspecialchars($row['subject_name']) ?></td>
                        <td><?= $row['tugas'] ?></td>
                        <td><?= $row['uts'] ?></td>
                        <td><?= $row['uas'] ?></td>
                        <td><?= $row['attendance'] ?></td>
                        <td class="fw-bold text-primary"><?= $row['predicted_score'] ?></td>
                        <td class="pe-4">
                            <?php if ($row['status'] === 'Aman'): ?>
                                <span class="badge badge-safe"><i class="fa-solid fa-check me-1"></i> Aman</span>
                            <?php else: ?>
                                <span class="badge badge-risk"><i class="fa-solid fa-triangle-exclamation me-1"></i> Berisiko</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (count($reports) === 0): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Belum ada nilai siswa yang diinputkan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
