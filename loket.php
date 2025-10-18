<?php
require_once 'db.php';
$counters_res = $mysqli->query("SELECT * FROM counters ORDER BY id");
$counters = []; while($r = $counters_res->fetch_assoc()) $counters[] = $r;
$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['call_next'])) {
    $counter_id = intval($_POST['counter_id']);
    $res = $mysqli->query("SELECT * FROM tickets WHERE status='waiting' ORDER BY created_at ASC LIMIT 1");
    if ($res && $res->num_rows>0) {
        $t = $res->fetch_assoc();
        $stmt = $mysqli->prepare("UPDATE tickets SET status='serving', counter_id=?, served_at=NOW() WHERE id=?");
        $stmt->bind_param('ii', $counter_id, $t['id']);
        $stmt->execute();
        header('Location: loket.php'); exit;
    } else { $msg = 'Tidak ada antrean.'; }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finish'])) {
    $ticket_id = intval($_POST['ticket_id']);
    $stmt = $mysqli->prepare("UPDATE tickets SET status='done' WHERE id=?"); $stmt->bind_param('i', $ticket_id); $stmt->execute();
    header('Location: loket.php'); exit;
}
$serving = []; foreach($counters as $c){ $res = $mysqli->query("SELECT * FROM tickets WHERE counter_id={$c['id']} AND status='serving' ORDER BY served_at DESC LIMIT 1"); $serving[$c['id']] = $res->fetch_assoc(); }
$waiting_count = $mysqli->query("SELECT COUNT(*) as cnt FROM tickets WHERE status='waiting'")->fetch_assoc()['cnt'];
include 'inc/header.php';
?>
<div class="card-ghost p-4 mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="text-white mb-0">Panel Petugas Loket</h3>
    <div class="text-muted-ghost">Menunggu: <strong><?php echo $waiting_count; ?></strong></div>
  </div>
  <?php if($msg): ?><div class="alert alert-warning"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
  <div class="row g-3">
    <?php foreach($counters as $c): ?>
      <div class="col-md-6">
        <div class="p-3 card-ghost">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <div class="h5 text-white mb-0"><?php echo htmlspecialchars($c['name']); ?></div>
              <small class="text-muted-ghost">ID: <?php echo $c['id']; ?></small>
            </div>
            <div>
              <?php if ($serving[$c['id']]): $t=$serving[$c['id']]; ?>
                <div class="text-end">
                  <div class="h3 mb-1" style="color:var(--red)"><?php echo htmlspecialchars($t['code']); ?></div>
                  <div class="text-muted-ghost"><?php echo htmlspecialchars($t['name']); ?></div>
                  <form method="post" class="mt-2"><input type="hidden" name="ticket_id" value="<?php echo $t['id']; ?>"><button name="finish" class="btn btn-sm btn-light">Selesai</button></form>
                </div>
              <?php else: ?>
                <form method="post"><input type="hidden" name="counter_id" value="<?php echo $c['id']; ?>"><button name="call_next" class="btn ticket-btn">Panggil Berikutnya</button></form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="mt-4"><a href="index.php" class="btn btn-outline-light btn-sm">Kembali</a> <a href="admin/login.php" class="btn btn-light btn-sm">Admin</a></div>
</div>
<?php include 'inc/footer.php'; ?>