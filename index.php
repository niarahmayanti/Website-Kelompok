<?php
require_once 'db.php';
session_start();

// --- LOGIN PETUGAS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_petugas'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Ganti ini sesuai kebutuhan
    $valid_user = 'petugas';
    $valid_pass = '12345';

    if ($username === $valid_user && $password === $valid_pass) {
        $_SESSION['petugas'] = $username;
        header('Location: loket.php');
        exit;
    } else {
        $error_login = "❌ Username atau password salah!";
    }
}

// --- AMBIL DAFTAR FILM ---
$films = $mysqli->query("SELECT * FROM films ORDER BY id");

// --- AMBIL TIKET ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ambil'])) {
    $name = trim($_POST['name']) ?: 'Pengunjung';
    $film_id = intval($_POST['film_id']);

    // Pastikan tabel & kolom ada
    $check_table = $mysqli->query("SHOW TABLES LIKE 'tickets'");
    if ($check_table->num_rows == 0) {
        die("<b>❌ Error:</b> Tabel <code>tickets</code> belum dibuat di database.");
    }

    $check_col = $mysqli->query("SHOW COLUMNS FROM tickets LIKE 'film_id'");
    if ($check_col->num_rows == 0) {
        die("<b>❌ Error:</b> Kolom <code>film_id</code> belum ada di tabel tickets.<br>
        Jalankan SQL: <code>ALTER TABLE tickets ADD COLUMN film_id INT NULL;</code>");
    }

    // Buat kode tiket
    $res = $mysqli->query("SELECT MAX(id) as mx FROM tickets");
    $row = $res->fetch_assoc();
    $next = intval($row['mx']) + 1;
    $code = 'B-' . str_pad($next, 3, '0', STR_PAD_LEFT);

    // Simpan tiket baru
    $sql = "INSERT INTO tickets (code, name, film_id, status) VALUES (?, ?, ?, 'waiting')";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssi', $code, $name, $film_id);
    $stmt->execute();

    $ticket_id = $stmt->insert_id;
    header('Location: print_ticket.php?id=' . $ticket_id);
    exit;
}

// --- DATA ANTRIAN ---
$waiting = $mysqli->query("SELECT COUNT(*) as cnt FROM tickets WHERE status='waiting'")->fetch_assoc()['cnt'] ?? 0;
$current = $mysqli->query("SELECT code FROM tickets WHERE status='serving' ORDER BY id DESC LIMIT 1");
$current_code = ($current && $current->num_rows > 0) ? $current->fetch_assoc()['code'] : 'B-XXX';

include 'inc/header.php';
?>

<div class="card-ghost p-4 mb-4">
  <div class="row g-4">
    <!-- Form Ambil Tiket -->
    <div class="col-md-7">
      <div class="p-4 ticket-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h2 class="mb-0 text-white">Ambil Tiket Antrean</h2>
            <small class="text-muted-ghost">Pilih film dan ambil tiket Anda</small>
          </div>
          <div class="text-end text-muted-ghost">
            <div>Menunggu</div>
            <div class="h3 mb-0"><?php echo $waiting; ?></div>
          </div>
        </div>

        <form method="post" class="row g-2 align-items-end">
          <div class="col-12">
            <label class="form-label text-muted-ghost">Pilih Film</label>
            <select name="film_id" class="form-select form-control" required>
              <option value="">-- Pilih Film --</option>
              <?php while($f = $films->fetch_assoc()): ?>
                <option value="<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['title']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-12 col-sm-8">
            <label class="form-label text-muted-ghost">Nama (opsional)</label>
            <input type="text" name="name" class="form-control" placeholder="Nama Anda">
          </div>
          <div class="col-12 col-sm-4 text-sm-end">
            <button name="ambil" class="ticket-btn w-100">Ambil Tiket <i class="fa-solid fa-ticket ms-2"></i></button>
          </div>
        </form>
      </div>

      <div class="mt-4 text-muted-ghost">
        <h6 class="text-white">Cara kerja</h6>
        <ol>
          <li>Ambil tiket untuk film yang diinginkan.</li>
          <li>Tunggu sampai nomor dipanggil di loket.</li>
          <li>Tunjukkan tiket saat dipanggil.</li>
        </ol>
      </div>
    </div>

    <!-- Preview Tiket -->
    <div class="col-md-5">
      <div class="p-4 card-ghost text-center">
        <h5 class="text-white">Preview Tiket</h5>
        <div class="mt-3 ticket-card text-center">
          <div class="ticket-code text-danger fw-bold" style="font-size:2.5rem;">
            <?php echo htmlspecialchars($current_code); ?>
          </div>
          <div class="ticket-meta mt-2">
            <?php if($current_code !== 'B-XXX'): ?>
              Nomor antrian yang sedang berlangsung
            <?php else: ?>
              Belum ada antrian berlangsung
            <?php endif; ?>
          </div>
        </div>
        <div class="mt-3 text-center">
          <!-- Tombol login petugas -->
          <button class="btn btn-outline-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#loginPetugasModal">
            Panel Petugas
          </button>
          <a href="admin/login.php" class="btn btn-light btn-sm">Login Admin</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Login Petugas -->
<div class="modal fade" id="loginPetugasModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light">
      <div class="modal-header border-secondary">
        <h5 class="modal-title">🔐 Login Petugas</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="post">
        <div class="modal-body">
          <?php if(!empty($error_login)): ?>
            <div class="alert alert-danger py-2"><?php echo $error_login; ?></div>
          <?php endif; ?>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer border-secondary">
          <button type="submit" name="login_petugas" class="btn btn-primary w-100">Masuk</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'inc/footer.php'; ?>
