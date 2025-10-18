<?php
session_start();
include '../koneksi.php';

// Cek login admin
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil ID film
$id = $_GET['id'];

// Ambil data film berdasarkan ID
$result = mysqli_query($conn, "SELECT * FROM films WHERE id='$id'");
$film = mysqli_fetch_assoc($result);

// Update data film
if (isset($_POST['update'])) {
    $judul = $_POST['judul'];
    $genre = $_POST['genre'];
    $duration = $_POST['duration'];
    $tanggal_tayang = $_POST['tanggal_tayang'];

    // Jika admin mengganti gambar
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = $_FILES['gambar']['name'];
        $target = "../uploads/" . basename($gambar);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target);
        mysqli_query($conn, "UPDATE films SET judul='$judul', genre='$genre', duration='$duration', tanggal_tayang='$tanggal_tayang', gambar='$gambar' WHERE id='$id'");
    } else {
        mysqli_query($conn, "UPDATE films SET judul='$judul', genre='$genre', duration='$duration', tanggal_tayang='$tanggal_tayang' WHERE id='$id'");
    }

    header("Location: kelola_film.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Film</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2 class="mb-4">🎬 Edit Film</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Judul Film</label>
                <input type="text" name="judul" class="form-control" value="<?= $film['judul'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Genre</label>
                <input type="text" name="genre" class="form-control" value="<?= $film['genre'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Durasi (menit)</label>
                <input type="text" name="duration" class="form-control" value="<?= $film['duration'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Tayang</label>
                <input type="date" name="tanggal_tayang" class="form-control" value="<?= $film['tanggal_tayang'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Gambar</label><br>
                <img src="../uploads/<?= $film['gambar'] ?>" width="120" class="mb-2"><br>
                <input type="file" name="gambar" class="form-control">
                <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
            </div>

            <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
            <a href="kelola_film.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
