<?php
require_once 'config.php';
require_once 'functions.php';
requireLogin();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO students (name, class, gender) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['name'], $_POST['class'], $_POST['gender']]);
            $message = "<div class='alert alert-success'>Siswa berhasil ditambahkan!</div>";
        } elseif ($_POST['action'] === 'edit') {
            $stmt = $pdo->prepare("UPDATE students SET name = ?, class = ?, gender = ? WHERE id = ?");
            $stmt->execute([$_POST['name'], $_POST['class'], $_POST['gender'], $_POST['student_id']]);
            $message = "<div class='alert alert-success'>Data siswa berhasil diperbarui!</div>";
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$_POST['student_id']]);
            $message = "<div class='alert alert-success'>Siswa berhasil dihapus!</div>";
        }
    }
}

$students = $pdo->query("SELECT * FROM students ORDER BY name ASC")->fetchAll();

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Manajemen Data Siswa</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="fa-solid fa-plus me-1"></i> Tambah Siswa
        </button>
    </div>

    <?= $message; ?>

    <div class="card table-custom border-0 p-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Jenis Kelamin</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#<?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($s['class']) ?></span></td>
                        <td><?= htmlspecialchars($s['gender'] == 'Male' ? 'Laki-laki' : 'Perempuan') ?></td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $s['id'] ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus siswa ini?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="student_id" value="<?= $s['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $s['id'] ?>" tabindex="-1">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                          <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold">Edit Siswa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form method="POST">
                              <div class="modal-body">
                                  <input type="hidden" name="action" value="edit">
                                  <input type="hidden" name="student_id" value="<?= $s['id'] ?>">
                                  
                                  <div class="mb-3">
                                      <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                                      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($s['name']) ?>" required>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label text-muted small fw-bold">Kelas</label>
                                      <input type="text" name="class" class="form-control" value="<?= htmlspecialchars($s['class']) ?>" required>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label text-muted small fw-bold">Jenis Kelamin</label>
                                      <select name="gender" class="form-select" required>
                                          <option value="Male" <?= $s['gender'] == 'Male' ? 'selected' : '' ?>>Laki-laki</option>
                                          <option value="Female" <?= $s['gender'] == 'Female' ? 'selected' : '' ?>>Perempuan</option>
                                      </select>
                                  </div>
                              </div>
                              <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                              </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (count($students) === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Data siswa kosong. Silakan tambah data!</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus me-2"></i>Tambah Siswa Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="add">
              
              <div class="mb-3">
                  <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                  <input type="text" name="name" class="form-control" required placeholder="Contoh: Budi Santoso">
              </div>
              <div class="mb-3">
                  <label class="form-label text-muted small fw-bold">Kelas</label>
                  <input type="text" name="class" class="form-control" required placeholder="Contoh: 10 IPA 1">
              </div>
              <div class="mb-3">
                  <label class="form-label text-muted small fw-bold">Jenis Kelamin</label>
                  <select name="gender" class="form-select" required>
                      <option value="Male">Laki-laki</option>
                      <option value="Female">Perempuan</option>
                  </select>
              </div>
          </div>
          <div class="modal-footer border-0 bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Siswa</button>
          </div>
      </form>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
