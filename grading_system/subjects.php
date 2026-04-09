<?php
require_once 'config.php';
require_once 'functions.php';
requireLogin();
requireAdmin();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO subjects (name) VALUES (?)");
            try {
                $stmt->execute([$_POST['name']]);
                $message = "<div class='alert alert-success'>Mata Pelajaran berhasil ditambahkan!</div>";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "<div class='alert alert-danger'>Mata Pelajaran tersebut sudah ada!</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Terjadi kesalahan: " . $e->getMessage() . "</div>";
                }
            }
        } elseif ($_POST['action'] === 'edit') {
            $stmt = $pdo->prepare("UPDATE subjects SET name = ? WHERE id = ?");
            try {
                $stmt->execute([$_POST['name'], $_POST['subject_id']]);
                $message = "<div class='alert alert-success'>Mata Pelajaran berhasil diperbarui!</div>";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "<div class='alert alert-danger'>Mata Pelajaran tersebut sudah ada!</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Terjadi kesalahan: " . $e->getMessage() . "</div>";
                }
            }
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
            $stmt->execute([$_POST['subject_id']]);
            $message = "<div class='alert alert-success'>Mata Pelajaran berhasil dihapus!</div>";
        }
    }
}

$subjects = $pdo->query("SELECT * FROM subjects ORDER BY name ASC")->fetchAll();

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Manajemen Mata Pelajaran</h3>
        <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
            <i class="fa-solid fa-plus me-1"></i> Tambah Mapel
        </button>
    </div>

    <?= $message; ?>

    <div class="card table-custom border-0 p-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nama Mata Pelajaran</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $s): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#<?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td class="text-end pe-4">
                            <!-- Edit btn triggers modal -->
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $s['id'] ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <!-- Delete form -->
                            <form method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mapel ini? Semua nilai terkait juga akan terhapus.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="subject_id" value="<?= $s['id'] ?>">
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
                            <h5 class="modal-title fw-bold">Edit Mapel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form method="POST">
                              <div class="modal-body">
                                  <input type="hidden" name="action" value="edit">
                                  <input type="hidden" name="subject_id" value="<?= $s['id'] ?>">
                                  
                                  <div class="mb-3">
                                      <label class="form-label text-muted small fw-bold">Nama Mata Pelajaran</label>
                                      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($s['name']) ?>" required>
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
                    
                    <?php if (count($subjects) === 0): ?>
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">Data Mapel kosong. Silakan tambah data!</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-info text-white border-0">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-book me-2"></i>Tambah Mapel Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="add">
              
              <div class="mb-3">
                  <label class="form-label text-muted small fw-bold">Nama Mata Pelajaran</label>
                  <input type="text" name="name" class="form-control" required placeholder="Contoh: Fisika Dasar">
              </div>
          </div>
          <div class="modal-footer border-0 bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Mapel</button>
          </div>
      </form>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
