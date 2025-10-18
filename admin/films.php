<?php
session_start();
require_once '../db.php';

// Cek login admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Tambah film baru
if (isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $duration = trim($_POST['duration']);
    $genre = trim($_POST['genre']);

    if ($title != '') {
        $stmt = $mysqli->prepare("INSERT INTO films (title, duration, genre) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $title, $duration, $genre);
        $stmt->execute();
    }
    header('Location: films.php');
    exit;
}

// Hapus film
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $mysqli->query("DELETE FROM films WHERE id = $id");
    header('Location: films.php');
    exit;
}

// Edit film
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $duration = trim($_POST['duration']);
    $genre = trim($_POST['genre']);

    $stmt = $mysqli->prepare("UPDATE films SET title=?, duration=?, genre=? WHERE id=?");
    $stmt->bind_param('sssi', $title, $duration, $genre, $id);
    $stmt->execute();
    header('Location: films.php');
    exit;
}

// Ambil semua film
$films = $mysqli->query("SELECT * FROM films ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Film - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #111; color: #fff; }
    .card { background-color: #1b1b1b; border: none; }
    .btn-danger, .btn-warning { font-size: 0.85rem; }
  </style>
</head>
<body class="p-4">
  <div class="container">
    <h2 class="mb-4 text-center">🎬 Kelola Film</h2>

    <!-- Form Tambah Film -->
    <div class="card p-3 mb-4">
      <h5>Tambah Film Baru</h5>
      <form method="post" class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label">Judul Film</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Durasi</label>
          <input type="text" name="duration" class="form-control" placeholder="misal: 120 menit">
        </div>
        <div class="col-md-3">
          <label class="form-label">Genre</label>
          <input type="text" name="genre" class="form-control" placeholder="misal: Aksi">
        </div>
        <div class="col-md-2">
          <button name="add" class="btn btn-success w-100">Tambah</button>
        </div>
      </form>
    </div>

    <!-- Daftar Film -->
    <div class="card p-3">
      <h5>Daftar Film</h5>
      <table class="table table-dark table-bordered align-middle text-center mt-3">
        <thead>
          <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Durasi</th>
            <th>Genre</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($f = $films->fetch_assoc()): ?>
          <tr>
            <td><?= $f['id']; ?></td>
            <td><?= htmlspecialchars($f['title']); ?></td>
            <td><?= htmlspecialchars($f['duration']); ?></td>
            <td><?= htmlspecialchars($f['genre']); ?></td>
            <td>
              <!-- Tombol Edit -->
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $f['id']; ?>">Edit</button>
              <!-- Tombol Hapus -->
              <a href="?delete=<?= $f['id']; ?>" onclick="return confirm('Yakin hapus film ini?')" class="btn btn-danger btn-sm">Hapus</a>
            </td>
          </tr>

          <!-- Modal Edit -->
          <div class="modal fade" id="editModal<?= $f['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content bg-dark text-white">
                <form method="post">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Film</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $f['id']; ?>">
                    <div class="mb-3">
                      <label class="form-label">Judul</label>
                      <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($f['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Durasi</label>
                      <input type="text" name="duration" class="form-control" value="<?= htmlspecialchars($f['duration']); ?>">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Genre</label>
                      <input type="text" name="genre" class="form-control" value="<?= htmlspecialchars($f['genre']); ?>">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button name="edit" class="btn btn-primary">Simpan Perubahan</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-3">
      <a href="../index.php" class="btn btn-outline-light">⬅ Kembali ke Halaman Utama</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
