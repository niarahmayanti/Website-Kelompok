<?php
session_start();
include '../koneksi.php';

// Cek login admin
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Hapus film
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM films WHERE id='$id'");
    header("Location: kelola_film.php");
    exit();
}

// Tambah film baru
if (isset($_POST['tambah'])) {
    $judul = $_POST['judul'];
    $genre = $_POST['genre'];
    $durasi = $_POST['duration'];
    $tanggal = $_POST['tanggal_tayang'];
    $gambar = $_FILES['gambar']['name'];

    $target = "../uploads/" . basename($gambar);
    move_uploaded_file($_FILES['gambar']['tmp_name'], $target);

    mysqli_query($conn, "INSERT INTO films (judul, genre, duration, tanggal_tayang, gambar) 
                         VALUES ('$judul', '$genre', '$durasi', '$tanggal', '$gambar')");
    header("Location: kelola_film.php");
    exit();
}

// Ambil data film
$result = mysqli_query($conn, "SELECT * FROM films");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Film</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2>🎬 Kelola Film</h2>

    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="judul" class="form-control" placeholder="Judul Film" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="genre" class="form-control" placeholder="Genre" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="duration" class="form-control" placeholder="Durasi (menit)" required>
            </div>
            <div class="col-md-3">
                <input type="date" name="tanggal_tayang" class="form-control" required>
            </div>
            <div class="col-md-2">
                <input type="file" name="gambar" class="form-control" required>
            </div>
        </div>
        <button type="submit" name="tambah" class="btn btn-primary mt-3">Tambah Film</button>
    </form>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Genre</th>
                <th>Durasi</th>
                <th>Tanggal Tayang</th>
                <th>Gambar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php $no = 1; while($film = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $film['judul'] ?></td>
                <td><?= $film['genre'] ?></td>
                <td><?= $film['duration'] ?> menit</td>
                <td><?= $film['tanggal_tayang'] ?></td>
                <td><img src="../uploads/<?= $film['gambar'] ?>" width="80"></td>
                <td>
                    <a href="edit_film.php?id=<?= $film['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="kelola_film.php?hapus=<?= $film['id'] ?>" onclick="return confirm('Hapus film ini?')" class="btn btn-sm btn-danger">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
