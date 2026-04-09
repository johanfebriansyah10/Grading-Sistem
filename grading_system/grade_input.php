<?php
require_once 'config.php';
require_once 'functions.php';
requireLogin();

// Fetch dynamic KKM
$kkm = 70;
$kkmRow = $pdo->query("SELECT value FROM kkm ORDER BY id DESC LIMIT 1")->fetch();
if ($kkmRow) {
    $kkm = (float)$kkmRow['value'];
}

if (!isset($_GET['student_id'])) {
    header("Location: grades.php");
    exit;
}

$student_id = (int)$_GET['student_id'];
$student = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$student->execute([$student_id]);
$student = $student->fetch();

if (!$student) {
    die("Data siswa tidak ditemukan.");
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_grade') {
        $subject_id = (int)$_POST['subject_id'];
        $tugas = (float)$_POST['tugas'];
        $uts = (float)$_POST['uts'];
        $uas = (float)$_POST['uas'];
        $attendance = (float)$_POST['attendance'];

        // Lakukan prediksi
        $prediksi = hitungPrediksi($tugas, $uts, $uas, $attendance);
        $status = cekStatus($prediksi, $kkm);

        $check = $pdo->prepare("SELECT id FROM grades WHERE student_id = ? AND subject_id = ?");
        $check->execute([$student_id, $subject_id]);
        
        if ($check->rowCount() > 0) {
            // Update
            $stmt = $pdo->prepare("UPDATE grades SET tugas=?, uts=?, uas=?, attendance=?, predicted_score=?, status=? WHERE student_id=? AND subject_id=?");
            $stmt->execute([$tugas, $uts, $uas, $attendance, $prediksi, $status, $student_id, $subject_id]);
            $message = "<div class='alert alert-info'>Nilai diperbarui dan hasil prediksi dihitung ulang!</div>";
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, tugas, uts, uas, attendance, predicted_score, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$student_id, $subject_id, $tugas, $uts, $uas, $attendance, $prediksi, $status]);
            $message = "<div class='alert alert-success'>Nilai berhasil disimpan dan hasil prediksi didapatkan!</div>";
        }
    }
}

// Fetch all subjects and their grades for this student
$sql = "SELECT sub.id as subject_id, sub.name as subject_name, 
               g.tugas, g.uts, g.uas, g.attendance, g.predicted_score, g.status
        FROM subjects sub 
        LEFT JOIN grades g ON sub.id = g.subject_id AND g.student_id = ?
        ORDER BY sub.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$student_id]);
$mapelGrades = $stmt->fetchAll();

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="grades.php" class="btn btn-sm btn-secondary mb-2"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            <h3 class="fw-bold mb-0">Input Nilai: <?= htmlspecialchars($student['name']) ?></h3>
            <p class="text-muted mb-0">Kelas: <span class="badge bg-info"><?= htmlspecialchars($student['class']) ?></span></p>
        </div>
        <span class="badge bg-secondary p-2">KKM Saat Ini: <?= $kkm ?></span>
    </div>

    <?= $message; ?>

    <div class="card table-custom border-0 p-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Mata Pelajaran</th>
                        <th>Nilai Tugas</th>
                        <th>Nilai UTS</th>
                        <th>Nilai UAS</th>
                        <th>Nilai Hadir</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mapelGrades as $row): ?>
                    <tr>
                        <td class="ps-4 fw-bold">
                            <?= htmlspecialchars($row['subject_name']) ?>
                            <?php if($row['predicted_score'] !== null): ?>
                                <i class="fa-solid fa-check-circle text-success ms-1 small" title="Sudah dinilai"></i>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['tugas'] !== null ? $row['tugas'] : '-' ?></td>
                        <td><?= $row['uts'] !== null ? $row['uts'] : '-' ?></td>
                        <td><?= $row['uas'] !== null ? $row['uas'] : '-' ?></td>
                        <td><?= $row['attendance'] !== null ? $row['attendance'] : '-' ?></td>
                        <td>
                            <?php if ($row['status']): ?>
                                <?php if ($row['status'] === 'Aman'): ?>
                                    <span class="badge badge-safe"><i class="fa-solid fa-check"></i> Aman</span>
                                <?php else: ?>
                                    <span class="badge badge-risk"><i class="fa-solid fa-triangle-exclamation"></i> Berisiko</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-light text-dark">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <!-- Input/Edit btn triggers modal -->
                            <button class="btn btn-sm <?= $row['predicted_score'] === null ? 'btn-primary' : 'btn-outline-primary' ?>" data-bs-toggle="modal" data-bs-target="#gradeModal<?= $row['subject_id'] ?>">
                                <?= $row['predicted_score'] === null ? '<i class="fa-solid fa-plus"></i> Input Nilai' : '<i class="fa-solid fa-pen-to-square"></i> Edit' ?>
                            </button>
                        </td>
                    </tr>

                    <!-- Grade Modal -->
                    <div class="modal fade" id="gradeModal<?= $row['subject_id'] ?>" tabindex="-1">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                          <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold">Nilai: <?= htmlspecialchars($row['subject_name']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form method="POST">
                              <div class="modal-body">
                                  <input type="hidden" name="action" value="save_grade">
                                  <input type="hidden" name="subject_id" value="<?= $row['subject_id'] ?>">
                                  
                                  <div class="alert alert-warning small border-0">
                                      <i class="fa-solid fa-circle-info"></i> Memasukkan: Tugas (30%), UTS (20%), UAS (30%), Kehadiran (20%).
                                  </div>

                                  <div class="mb-3">
                                      <label class="form-label text-muted small fw-bold">Nilai Tugas [0-100]</label>
                                      <input type="number" step="0.01" max="100" min="0" name="tugas" class="form-control" value="<?= $row['tugas'] !== null ? $row['tugas'] : '' ?>" required>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label text-muted small fw-bold">Nilai UTS [0-100]</label>
                                      <input type="number" step="0.01" max="100" min="0" name="uts" class="form-control" value="<?= $row['uts'] !== null ? $row['uts'] : '' ?>" required>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label text-muted small fw-bold">Nilai UAS [0-100]</label>
                                      <input type="number" step="0.01" max="100" min="0" name="uas" class="form-control" value="<?= $row['uas'] !== null ? $row['uas'] : '' ?>" required>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label text-muted small fw-bold">Nilai Kehadiran [0-100]</label>
                                      <input type="number" step="0.01" max="100" min="0" name="attendance" class="form-control" value="<?= $row['attendance'] !== null ? $row['attendance'] : '' ?>" required>
                                  </div>
                              </div>
                              <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan & Prediksi</button>
                              </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (count($mapelGrades) === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data Mata Pelajaran. Tambahkan di menu Admin.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
