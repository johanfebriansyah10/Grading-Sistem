<?php
require_once 'config.php';
require_once 'functions.php';
requireLogin();
requireAdmin();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO teachers (name, subject) VALUES (?, ?)");
            $stmt->execute([$_POST['name'], $_POST['subject']]);
            $message = "<div class='alert alert-success'>Guru berhasil ditambahkan!</div>";
        } elseif ($_POST['action'] === 'edit') {
            $stmt = $pdo->prepare("UPDATE teachers SET name = ?, subject = ? WHERE id = ?");
            $stmt->execute([$_POST['name'], $_POST['subject'], $_POST['teacher_id']]);
            $message = "<div class='alert alert-success'>Data guru berhasil diperbarui!</div>";
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
            $stmt->execute([$_POST['teacher_id']]);
            $message = "<div class='alert alert-success'>Guru berhasil dihapus!</div>";
        }
    }
}

$teachers = $pdo->query("SELECT * FROM teachers ORDER BY name ASC")->fetchAll();

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Manajemen Data Guru</h3>
        <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
            <i class="fa-solid fa-plus me-1"></i> Tambah Guru
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
                        <th>Mata Pelajaran</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $t): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#<?= $t['id'] ?></td>
                        <td><?= htmlspecialchars($t['name']) ?></td>
                        <td><?= htmlspecialchars($t['subject']) ?></td>
                        <td class="text-end pe-4">
                            <!-- Edit btn triggers modal -->
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $t['id'] ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <!-- Delete form -->
                            <form method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus guru ini?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="teacher_id" value="<?= $t['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $t['id'] ?>" tabindex="-1">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                          <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold">Edit Guru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form method="POST">
                              <div class="modal-body">
                                  <input type="hidden" name="action" value="edit">
                                  <input type="hidden" name="teacher_id" value="<?= $t['id'] ?>">
                                  
                                  <div class="mb-3">
                                      <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                                      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($t['name']) ?>" required>
                                  </div>
                                  <div class="mb-3">
                                      <label class="form-label text-muted small fw-bold">Mata Pelajaran</label>
                                      <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($t['subject']) ?>" required>
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
                    
                    <?php if (count($teachers) === 0): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Data guru kosong. Silakan tambah data!</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-info text-white border-0">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus me-2"></i>Tambah Guru Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="add">
              
              <div class="mb-3">
                  <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                  <input type="text" name="name" class="form-control" required placeholder="Contoh: Siti Aisyah">
              </div>
              <div class="mb-3">
                  <label class="form-label text-muted small fw-bold">Mata Pelajaran</label>
                  <input type="text" name="subject" class="form-control" required placeholder="Contoh: Matematika">
              </div>
          </div>
          <div class="modal-footer border-0 bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Guru</button>
          </div>
      </form>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
