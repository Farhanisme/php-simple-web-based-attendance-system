
<?php
 $page_title = 'Edit Absensi';
 $page_css = <<<CSS
.container {
  max-width: 540px;
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
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1.2rem;
  color: #007bff;
  text-align: center;
  letter-spacing: 1px;
  text-shadow: 0 2px 8px rgba(0,64,133,0.08);
}
form {
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
  align-items: center;
}
.form-group {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-bottom: 0;
}
label {
  font-weight: 500;
  color: #004085;
  margin-bottom: .3rem;
}
textarea, input[type="file"] {
  width: 100%;
  padding: .7rem;
  border: 1px solid #e3eafc;
  border-radius: 6px;
  font-size: 1rem;
  margin-bottom: .2rem;
  box-sizing: border-box;
}
button {
  width: 100%;
  padding: .7rem 1.5rem;
  background: linear-gradient(90deg, #004085 60%, #007bff 100%);
  color: #fff;
  border-radius: 6px;
  border: none;
  font-weight: 500;
  font-size: 1rem;
  box-shadow: 0 2px 8px rgba(0,64,133,0.08);
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
  margin-top: .5rem;
}
button:hover {
  background: linear-gradient(90deg, #003366 60%, #0056b3 100%);
  box-shadow: 0 4px 16px rgba(0,64,133,0.12);
  transform: translateY(-2px) scale(1.03);
}
.back-link {
  display: inline-block;
  min-width: 140px;
  padding: .7rem 1.5rem;
  background: linear-gradient(90deg, #004085 60%, #007bff 100%);
  color: #fff;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  font-size: 1rem;
  box-shadow: 0 2px 8px rgba(0,64,133,0.08);
  transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
  border: none;
  margin-top: 2rem;
  text-align: center;
}
.back-link:hover {
  background: linear-gradient(90deg, #003366 60%, #0056b3 100%);
  box-shadow: 0 4px 16px rgba(0,64,133,0.12);
  transform: translateY(-2px) scale(1.03);
}
.foto-thumb {
  width: 60px;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0,40,120,0.10);
  margin-bottom: .5rem;
}
.error {
  background: #f8d7da;
  color: #721c24;
  padding: .75rem;
  border-radius:6px;
  margin-bottom:1rem;
  font-size: 1rem;
  width: 100%;
}
CSS;


require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_user.php';

$mahasiswa_id = $_SESSION['mahasiswa_id'];
$id = intval($_GET['id'] ?? 0);
$errors = [];

// Ambil data absensi
$stmt = $conn->prepare("
  SELECT a.id, j.jam_mulai, j.jam_selesai, m.nama_matkul, j.ruangan,
         a.materi, a.foto
  FROM absensi a
  JOIN jadwal j      ON a.jadwal_id = j.id
  JOIN mata_kuliah m ON j.mata_kuliah_id = m.id
  WHERE a.id = ? AND a.mahasiswa_id = ?
");
$stmt->bind_param("ii", $id, $mahasiswa_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

// Handle submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $data) {
  $materi = trim($_POST['materi'] ?? '');
  if ($materi === '') $errors[] = 'Materi tidak boleh kosong.';
  $newFoto = $data['foto'];
  if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['foto'];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
      $errors[] = 'Format foto tidak diizinkan.';
    } else {
      $newFoto = time().'_'.random_int(1000,9999).'.'.$ext;
      $uploadDir = __DIR__.'/../uploads/';
      if (!is_dir($uploadDir)) mkdir($uploadDir,0755,true);
      move_uploaded_file($file['tmp_name'], $uploadDir.$newFoto);
      // Hapus foto lama
      if ($data['foto'] && file_exists($uploadDir.$data['foto'])) {
        unlink($uploadDir.$data['foto']);
      }
    }
  }
  if (empty($errors)) {
    $stmt = $conn->prepare("UPDATE absensi SET materi=?, foto=? WHERE id=? AND mahasiswa_id=?");
    $stmt->bind_param("ssii", $materi, $newFoto, $id, $mahasiswa_id);
    $stmt->execute();
    header('Location: absensi_list.php');
    exit;
  }
}

include     __DIR__ . '/../includes/header.php';

if (!$data) {
  echo '<div class="container"><div class="error">Data absensi tidak ditemukan.</div></div>';
  include __DIR__ . '/../includes/footer.php';
  exit;
}

echo '<div class="container">';
echo '<h2>Edit Absensi</h2>';
if ($errors) {
  echo '<div class="error">';
  foreach ($errors as $e) echo '<p>'.htmlspecialchars($e).'</p>';
  echo '</div>';
}
echo '<form method="post" enctype="multipart/form-data">';
echo '<div class="form-group"><label>Jam:</label><div style="font-weight:600;color:#007bff">'.htmlspecialchars($data['jam_mulai']).' – '.htmlspecialchars($data['jam_selesai']).'</div></div>';
echo '<div class="form-group"><label>Mata Kuliah:</label><div style="font-weight:600;color:#007bff">'.htmlspecialchars($data['nama_matkul']).'</div></div>';
echo '<div class="form-group"><label>Ruangan:</label><div style="font-weight:600;color:#007bff">'.htmlspecialchars($data['ruangan']).'</div></div>';
echo '<div class="form-group"><label>Materi:</label><textarea name="materi" rows="3" required>'.htmlspecialchars($_POST['materi'] ?? $data['materi']).'</textarea></div>';
if ($data['foto'] && file_exists(__DIR__ . '/../uploads/' . $data['foto'])) {
  echo '<div class="form-group"><label>Foto Lama:</label><br><img src="../uploads/'.htmlspecialchars($data['foto']).'" class="foto-thumb"></div>';
}
echo '<div class="form-group"><label>Upload Foto Baru:</label><input type="file" name="foto" accept="image/*"></div>';
echo '<button type="submit">Simpan Perubahan</button>';
echo '<div style="text-align:center;"><a href="absensi_list.php" class="back-link">← Kembali ke Daftar Absensi</a></div>';
echo '</form>';
echo '</div>';
include __DIR__ . '/../includes/footer.php';
