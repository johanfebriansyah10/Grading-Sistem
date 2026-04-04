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

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $student_id = $_POST['student_id'];
        $tugas = (float)$_POST['tugas'];
        $uts = (float)$_POST['uts'];
        $attendance = (float)$_POST['attendance'];

        // Lakukan prediksi
        $prediksi = hitungPrediksi($tugas, $uts, $attendance);
        $status = cekStatus($prediksi, $kkm);

        if ($_POST['action'] === 'save_grade') {
            $check = $pdo->prepare("SELECT id FROM grades WHERE student_id = ?");
            $check->execute([$student_id]);
            
            if ($check->rowCount() > 0) {
                // Update
                $stmt = $pdo->prepare("UPDATE grades SET tugas=?, uts=?, attendance=?, predicted_score=?, status=? WHERE student_id=?");
                $stmt->execute([$tugas, $uts, $attendance, $prediksi, $status, $student_id]);
                $message = "<div class='alert alert-info'>Nilai diperbarui dan hasil prediksi dihitung ulang!</div>";
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO grades (student_id, tugas, uts, attendance, predicted_score, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$student_id, $tugas, $uts, $attendance, $prediksi, $status]);
                $message = "<div class='alert alert-success'>Nilai berhasil disimpan dan hasil prediksi didapatkan!</div>";
            }
        }
    }
}

// Fetch students & grades
$sql = "SELECT s.id as student_id, s.name, s.class, 
               g.tugas, g.uts, g.attendance, g.predicted_score, g.status
        FROM students s 
        LEFT JOIN grades g ON s.id = g.student_id
        ORDER BY s.class ASC, s.name ASC";
$studentGrades = $pdo->query($sql)->fetchAll();

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Input Nilai & Analisa Prediksi</h3>
        <span class="badge bg-secondary p-2">KKM Saat Ini: <?= $kkm ?></span>
    </div>

    <?= $message; ?>

    <div class="card table-custom border-0 p-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Nilai Tugas</th>
                        <th>Nilai UTS</th>
                        <th>Nilai Hadir</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($studentGrades as $row): ?>
                    <tr>
                        <td class="ps-4 fw-bold">
                            <?= htmlspecialchars($row['name']) ?>
                            <?php if($row['predicted_score'] !== null): ?>
                                <i class="fa-solid fa-check-circle text-success ms-1 small" title="Sudah dinilai"></i>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['class']) ?></td>
                        <td><?= $row['tugas'] !== null ? $row['tugas'] : '-' ?></td>
                        <td><?= $row['uts'] !== null ? $row['uts'] : '-' ?></td>
                        <td><?= $row['attendance'] !== null ? $row['attendance'] : '-' ?></td>
                        <td class="text-end pe-4">
                            <!-- Input/Edit btn triggers modal -->
                            <button class="btn btn-sm <?= $row['predicted_score'] === null ? 'btn-primary' : 'btn-outline-primary' ?>" data-bs-toggle="modal" data-bs-target="#gradeModal<?= $row['student_id'] ?>">
                                <?= $row['predicted_score'] === null ? '<i class="fa-solid fa-plus"></i> Input Nilai' : '<i class="fa-solid fa-pen-to-square"></i> Edit Nilai' ?>
                            </button>
                        </td>
                    </tr>

                    <!-- Grade Modal -->
                    <div class="modal fade" id="gradeModal<?= $row['student_id'] ?>" tabindex="-1">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                          <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold">Kelola Nilai: <?= htmlspecialchars($row['name']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form method="POST">
                              <div class="modal-body">
                                  <input type="hidden" name="action" value="save_grade">
                                  <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                  
                                  <div class="alert alert-warning small border-0">
                                      <i class="fa-solid fa-circle-info"></i> Model prediksi akan dihitung otomatis setelah disimpan.
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
                    
                    <?php if (count($studentGrades) === 0): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data siswa.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
