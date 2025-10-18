<?php
$mysqli = new mysqli("localhost", "root", "", "antrian_bioskop");
if ($mysqli->connect_errno) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8");
?>
