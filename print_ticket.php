<?php
require_once 'db.php';
$id = intval($_GET['id'] ?? 0);
$res = $mysqli->query("SELECT t.*, f.title as film_title FROM tickets t JOIN films f ON t.film_id=f.id WHERE t.id={$id} LIMIT 1");
$ticket = $res->fetch_assoc();
if (!$ticket) { echo 'Tiket tidak ditemukan.'; exit; }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Tiket <?php echo htmlspecialchars($ticket['code']); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;700&display=swap" rel="stylesheet">
  <style>body{font-family:'Montserrat',sans-serif;background:#111;color:#fff;padding:40px}.ticket{width:420px;margin:0 auto;background:linear-gradient(180deg,#1a0b0b,#0b0b0b);padding:22px;border-radius:14px;border:1px solid rgba(255,255,255,0.04)}.brand{display:flex;justify-content:space-between;align-items:center}.brand .name{font-weight:700;color:#ffeded}.code{font-size:56px;color:#ff6464;font-weight:800;letter-spacing:4px;margin:14px 0}.meta{color:#cbd5e1}.small{font-size:12px;color:#9ca3af;margin-top:12px}@media print { body{background:#fff;color:#000} .ticket{border:none;background:transparent} }</style>
</head>
<body onload="setTimeout(()=>window.print(),300)">
  <div class="ticket">
    <div class="brand">
      <div>
        <div class="name">Cinema Abditama</div>
        <div class="meta">Tiket Resmi</div>
      </div>
      <div class="meta">Film: <?php echo htmlspecialchars($ticket['film_title']); ?></div>
    </div>
    <div class="code"><?php echo htmlspecialchars($ticket['code']); ?></div>
    <div class="meta">Nama: <?php echo htmlspecialchars($ticket['name']); ?></div>
    <div class="meta">Waktu: <?php echo $ticket['created_at']; ?></div>
    <div class="small">Tunjukkan tiket ini saat dipanggil.</div>
  </div>
</body>
</html>