<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin') { header('Location: login.php'); exit; }
header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="riwayat_antrean.csv"');
$out = fopen('php://output','w'); fputcsv($out, ['kode','nama','film','waktu_layani','loket']);
$res = $mysqli->query("SELECT t.code, t.name, f.title as film, t.served_at, c.name as loket FROM tickets t LEFT JOIN films f ON t.film_id=f.id LEFT JOIN counters c ON t.counter_id=c.id WHERE t.status='done' ORDER BY t.served_at DESC");
while($r=$res->fetch_assoc()) fputcsv($out, [$r['code'],$r['name'],$r['film'],$r['served_at'],$r['loket']]);
fclose($out); exit;
?>