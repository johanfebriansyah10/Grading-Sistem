<?php
require_once 'config.php';
require_once 'functions.php';
requireLogin();

// Fetch students & their grade counts
$sql = "SELECT s.id as student_id, s.name, s.class, s.gender,
               (SELECT COUNT(id) FROM grades g WHERE g.student_id = s.id) as graded_subjects,
               (SELECT COUNT(id) FROM subjects) as total_subjects
        FROM students s 
        ORDER BY s.class ASC, s.name ASC";
$students = $pdo->query($sql)->fetchAll();

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Pilih Siswa untuk Input Nilai</h3>
    </div>

    <div class="alert alert-info border-0 small">
        <i class="fa-solid fa-circle-info"></i> Silakan pilih siswa, kemudian Anda bisa memasukkan nilai untuk setiap Mata Pelajarannya.
    </div>

    <div class="card table-custom border-0 p-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Gender</th>
                        <th>Status Penilaian</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $row): ?>
                    <tr>
                        <td class="ps-4 fw-bold">
                            <?= htmlspecialchars($row['name']) ?>
                        </td>
                        <td><?= htmlspecialchars($row['class']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td>
                            <?php if ($row['total_subjects'] > 0 && $row['graded_subjects'] >= $row['total_subjects']): ?>
                                <span class="badge bg-success"><i class="fa-solid fa-check"></i> Lengkap</span>
                            <?php elseif ($row['graded_subjects'] > 0): ?>
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-spinner"></i> Sebagian (<?= $row['graded_subjects'] ?>/<?= $row['total_subjects'] ?>)</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><i class="fa-solid fa-minus"></i> Belum Ada</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <a href="grade_input.php?student_id=<?= $row['student_id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-arrow-right"></i> Lihat Mapel
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (count($students) === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data siswa.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
