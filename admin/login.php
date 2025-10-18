<?php
session_start();
require_once '../db.php';
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $hash = md5($password);
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? AND password=? LIMIT 1");
    $stmt->bind_param('ss', $username, $hash);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows>0) { $_SESSION['user'] = $res->fetch_assoc(); header('Location: dashboard.php'); exit; } else { $error = 'Login gagal.'; }
}
include '../inc/header.php';
?>
<div class="center-screen">
  <div class="card-ghost p-4" style="max-width:420px;width:100%">
    <h4 class="text-white mb-3">Admin Login</h4>
    <?php if(isset($error)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <div class="mb-2"><label class="form-label text-muted-ghost">Username</label><input name="username" class="form-control" required></div>
      <div class="mb-3"><label class="form-label text-muted-ghost">Password</label><input type="password" name="password" class="form-control" required></div>
      <div class="d-grid"><button name="login" class="btn ticket-btn">Masuk</button></div>
    </form>
    <div class="mt-3 text-center text-muted-ghost"><small>Username: admin • Password: admin123</small></div>
  </div>
</div>
<?php include '../inc/footer.php'; ?>