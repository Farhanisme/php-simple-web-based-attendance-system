<?php
// mahasiswa/absensi_add.php

$page_title = 'Catat Absensi Hari Ini';
$page_css = <<<CSS
/* Form Absensi - Simetris & Modern (Konsisten dengan Dashboard/Index) */
.container {
  max-width: 520px;
  margin: 2.5rem auto;
  padding: 2.5rem 2rem;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 8px 32px rgba(0,40,120,0.12);
  border: 1px solid #e3eafc;
  display: flex;
  flex-direction: column;
  align-items: center;
}
h2 {
  margin-bottom: 1.2rem;
  font-size: 1.7rem;
  font-weight: 700;
  color: #007bff;
  text-align: center;
  text-shadow: 0 2px 8px rgba(0,64,133,0.08);
}
.error {
  background: #ffe3e3;
  color: #b71c1c;
  padding: .8rem 1.2rem;
  border-radius: 6px;
  margin-bottom: 1.2rem;
  border: 1px solid #ffcdd2;
  font-weight: 500;
  text-align: center;
}
form {
  max-width: 420px;
  margin: 0 auto;
  background: #f7faff;
  border-radius: 14px;
  box-shadow: 0 2px 12px rgba(0,40,120,0.08);
  padding: 2rem 1.5rem 1.5rem 1.5rem;
  border: 1px solid #e3eafc;
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
  align-items: center;
}
.form-group {
  margin-bottom: 1rem;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
form label {
  display: block;
  margin-bottom: .4rem;
  font-weight: 500;
  color: #004085;
}
form select,
form textarea,
form input[type=file] {
  width: 100%;
  padding: .65rem;
  border: 1px solid #bfcbe3;
  border-radius: 6px;
  font-size: 1rem;
  background: #fff;
  margin-bottom: .5rem;
  transition: border-color 0.2s;
  box-sizing: border-box;
  font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
  font-weight: 500;
  color: #004085;
}
form select:focus,
form textarea:focus,
form input[type=file]:focus {
  border-color: #007bff;
  outline: none;
}
form button {
  width: 100%;
  padding: .7rem 0;
  background: linear-gradient(90deg, #004085 60%, #007bff 100%);
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 1.05rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,64,133,0.08);
  transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
  margin-top: 0.5rem;
}
form button:hover {
  background: linear-gradient(90deg, #003366 60%, #0056b3 100%);
  box-shadow: 0 4px 16px rgba(0,64,133,0.12);
  transform: translateY(-2px) scale(1.03);
}
CSS;

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_user.php';
include     __DIR__ . '/../includes/header.php';

$errors = [];

// Fetch jadwal hari ini
$weekdayMap = [
  'Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
  'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'
];
$todayInd = $weekdayMap[date('l')];
$stmtJ = $conn->prepare("
  SELECT j.id, m.nama_matkul, j.ruangan, j.jam_mulai, j.jam_selesai, d.nama AS dosen
  FROM jadwal j
  JOIN mata_kuliah m ON j.mata_kuliah_id = m.id
  JOIN dosen d       ON j.dosen_id       = d.id
  WHERE j.hari = ?
  ORDER BY j.jam_mulai
");
$stmtJ->bind_param('s', $todayInd);
$stmtJ->execute();
$schedule = $stmtJ->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mahasiswa_id = $_SESSION['mahasiswa_id'];
    $jadwal_id    = intval($_POST['jadwal_id'] ?? 0);
    $materi       = trim($_POST['materi'] ?? '');

    if (!$jadwal_id) {
        $errors[] = 'Silakan pilih jadwal.';
    }
    if (empty($materi)) {
        $errors[] = 'Materi tidak boleh kosong.';
    }
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Foto wajib diunggah.';
    }

    if (empty($errors)) {
        // Proses upload
        $file = $_FILES['foto'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format foto tidak diizinkan.';
        } else {
            $newName = time() . '_' . random_int(1000,9999) . '.' . $ext;
            $dest = __DIR__ . '/../uploads/' . $newName;
            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                $errors[] = 'Gagal menyimpan foto.';
            }
        }
    }

    if (empty($errors)) {
        $ins = $conn->prepare("
          INSERT INTO absensi (mahasiswa_id, jadwal_id, materi, foto)
          VALUES (?, ?, ?, ?)
        ");
        $ins->bind_param("iiss", $mahasiswa_id, $jadwal_id, $materi, $newName);
        $ins->execute();

        echo "<script>
                alert('Absensi berhasil disimpan!');
                window.location = 'absensi_list.php';
              </script>";
        exit;
    }
}
?>

  <h2>Catat Absensi Hari Ini (<?= $todayInd ?>)</h2>

  <?php if ($errors): ?>
    <div class="error">
      <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
    </div>
  <?php endif; ?>

  <?php if ($schedule->num_rows === 0): ?>
    <p>Tidak ada jadwal untuk hari ini.</p>
  <?php else: ?>
    <form method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
      <div class="form-group">
        <label for="jadwal_id">Pilih Jadwal:</label>
        <select id="jadwal_id" name="jadwal_id" required>
          <option value="">— Pilih —</option>
          <?php while ($r = $schedule->fetch_assoc()): ?>
            <option value="<?= $r['id'] ?>">
              <?= "{$r['jam_mulai']}–{$r['jam_selesai']} | {$r['nama_matkul']} ({$r['ruangan']}) – {$r['dosen']}" ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="materi">Materi Perkuliahan:</label>
        <textarea id="materi" name="materi" rows="4" required><?= htmlspecialchars($_POST['materi'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="foto">Upload Foto:</label>
        <input type="file" id="foto" name="foto" accept="image/*" required>
      </div>

      <button type="submit">Simpan</button>
    </form>

    <div style="margin-top:1.5rem; text-align:center;">
      <a href="dashboard.php" class="button" style="display:inline-block;min-width:140px;padding:.7rem 1.5rem;background:linear-gradient(90deg,#004085 60%,#007bff 100%);color:#fff;border-radius:6px;text-decoration:none;font-weight:500;font-size:1rem;box-shadow:0 2px 8px rgba(0,64,133,0.08);transition:background 0.2s,box-shadow 0.2s,transform 0.2s;border:none;">&larr; Kembali ke Dashboard</a>
    </div>

    <script>
    function validateForm() {
      if (!confirm('Apakah Anda yakin ingin menyimpan absensi ini?')) return false;
      var fileInput = document.getElementById('foto');
      if (!fileInput.value) {
        alert('Foto wajib diunggah.');
        return false;
      }
      return true;
    }
    </script>
  <?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
