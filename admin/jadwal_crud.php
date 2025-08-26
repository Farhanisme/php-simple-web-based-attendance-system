<?php
// admin/jadwal_crud.php

$page_title = 'Kelola Jadwal Mata Kuliah';
$page_css = <<<CSS
/* Container utama */
.container {
  max-width: 700px;
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
/* Tombol Kembali */
.back-link {
  display: inline-block;
  min-width: 160px;
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
/* Tabel Jadwal */
.table-wrapper {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-bottom: 2rem;
}
.table-wrapper table {
  width: 100%;
  max-width: 900px;
  margin: 0 auto;
  border-collapse: collapse;
  background: #f7faff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,40,120,0.08);
  text-align: center;
}
.table-wrapper th,
.table-wrapper td {
  padding: .55rem .45rem;
  border: 1px solid #e3eafc;
  text-align: center;
  font-size: 0.98rem;
}
.table-wrapper th {
  background: #e9ecef;
  color: #004085;
  font-weight: 600;
}
.aksi a {
  margin-right: .5rem;
  color: #004085;
  text-decoration: none;
  font-weight: 500;
}
.aksi a.delete { color: #dc3545; }
.aksi a:hover { text-decoration: underline; }
/* Form Tambah/Edit */
form {
  width: 100%;
  max-width: 540px;
  margin: 0 auto;
  background: #f7faff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,40,120,0.08);
  padding: 2rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}
.form-group {
  margin-bottom: 0;
  display: flex;
  flex-direction: column;
}
form label {
  margin-bottom: .25rem;
  font-weight: 500;
  color: #004085;
}
form select,
form input[type="time"],
form input[type="text"] {
  width: 100%;
  padding: .7rem;
  border:1px solid #e3eafc;
  border-radius:6px;
  font-size: 1rem;
}
form button {
  padding: .7rem 1.5rem;
  background: linear-gradient(90deg, #004085 60%, #007bff 100%);
  color:#fff;
  border:none;
  border-radius:6px;
  cursor:pointer;
  font-weight: 500;
  font-size: 1rem;
  box-shadow: 0 2px 8px rgba(0,64,133,0.08);
  transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
}
form button:hover {
  background: linear-gradient(90deg, #003366 60%, #0056b3 100%);
  box-shadow: 0 4px 16px rgba(0,64,133,0.12);
  transform: translateY(-2px) scale(1.03);
}
form a.cancel {
  margin-left: 1rem;
  color: #dc3545;
  text-decoration:none;
  font-weight: 500;
}
form a.cancel:hover {
  text-decoration: underline;
}
.error {
  background: #f8d7da;
  color: #721c24;
  padding: .75rem;
  border-radius:6px;
  margin-bottom:1rem;
  font-size: 1rem;
}
CSS;

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_admin.php';
include     __DIR__ . '/../includes/header.php';

// Fetch data untuk dropdown
$mks    = $conn->query("SELECT id, nama_matkul FROM mata_kuliah ORDER BY nama_matkul");
$dosens = $conn->query("SELECT id, nama FROM dosen ORDER BY nama");

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM jadwal WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: admin/jadwal_crud.php');
    exit;
}

// Handle edit mode
$edit     = false;
$editData = [];
if (isset($_GET['edit'])) {
    $edit = true;
    $id   = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM jadwal WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res  = $stmt->get_result();
    if ($res->num_rows === 1) {
        $editData = $res->fetch_assoc();
    } else {
        header('Location: admin/jadwal_crud.php');
        exit;
    }
}

// Handle form submit
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mkid       = intval($_POST['mata_kuliah_id'] ?? 0);
    $did        = intval($_POST['dosen_id']        ?? 0);
    $hari       = $_POST['hari']                  ?? '';
    $jam_mulai  = $_POST['jam_mulai']             ?? '';
    $jam_selesai= $_POST['jam_selesai']           ?? '';
    $ruangan    = trim($_POST['ruangan']          ?? '');
    $isEdit     = !empty($_POST['id']);

    if (!$mkid)         $errors[] = 'Silakan pilih mata kuliah.';
    if (!$did)          $errors[] = 'Silakan pilih dosen.';
    if (!in_array($hari, ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'])) {
        $errors[] = 'Hari tidak valid.';
    }
    if ($jam_mulai==='')  $errors[] = 'Jam mulai wajib diisi.';
    if ($jam_selesai==='')$errors[] = 'Jam selesai wajib diisi.';
    if ($ruangan==='')    $errors[] = 'Ruangan wajib diisi.';

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $conn->prepare("
              UPDATE jadwal
                 SET mata_kuliah_id=?, dosen_id=?, hari=?, jam_mulai=?, jam_selesai=?, ruangan=?
               WHERE id=?
            ");
            $stmt->bind_param(
              "iissssi",
              $mkid, $did, $hari, $jam_mulai, $jam_selesai, $ruangan, $_POST['id']
            );
        } else {
            $stmt = $conn->prepare("
              INSERT INTO jadwal
                (mata_kuliah_id,dosen_id,hari,jam_mulai,jam_selesai,ruangan)
              VALUES (?,?,?,?,?,?)
            ");
            $stmt->bind_param(
              "iissss",
              $mkid, $did, $hari, $jam_mulai, $jam_selesai, $ruangan
            );
        }
        $stmt->execute();
        header('Location: admin/jadwal_crud.php');
        exit;
    }
}

// --- Daftar Jadwal ---
echo '<div class="container">';
echo '<h2 style="margin-bottom:1.5rem;">Daftar Jadwal Mata Kuliah</h2>';
$res = $conn->query("
  SELECT j.id, j.hari, j.jam_mulai, j.jam_selesai,
         mk.nama_matkul, j.ruangan, d.nama AS dosen
    FROM jadwal j
    JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
    JOIN dosen d       ON j.dosen_id       = d.id
    ORDER BY FIELD(j.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'),
             j.jam_mulai
");
if ($res->num_rows === 0) {
    echo '<p>Belum ada jadwal.</p>';
} else {
    echo '<div class="table-wrapper"><table>';
    echo '<thead><tr>
          <th>Hari</th>
          <th>Jam</th>
          <th>Matakuliah</th>
          <th>Ruangan</th>
          <th>Dosen</th>
          <th>Aksi</th>
        </tr></thead><tbody>';
    while ($r = $res->fetch_assoc()) {
        echo '<tr>',
             '<td>'.htmlspecialchars($r['hari']).'</td>',
             '<td>'.htmlspecialchars($r['jam_mulai']).'–'.htmlspecialchars($r['jam_selesai']).'</td>',
             '<td>'.htmlspecialchars($r['nama_matkul']).'</td>',
             '<td>'.htmlspecialchars($r['ruangan']).'</td>',
             '<td>'.htmlspecialchars($r['dosen']).'</td>',
             '<td class="aksi">',
               "<a href=\"?edit={$r['id']}\">Edit</a>",
               " <a href=\"?delete={$r['id']}\" class=\"delete\"",
               " onclick=\"return confirm('Yakin ingin menghapus jadwal ini?')\">Hapus</a>",
             '</td>',
             '</tr>';
    }
    echo '</tbody></table></div>';
}

// --- Form Tambah/Edit ---
echo '<h2 style="margin-bottom:1.2rem;">' . ($edit ? 'Edit' : 'Tambah') . ' Jadwal</h2>';
if ($errors) {
    echo '<div class="error">';
    foreach ($errors as $e) {
        echo '<p>' . htmlspecialchars($e) . '</p>';
    }
    echo '</div>';
}
$days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
echo '<form method="post">',
     ($edit ? "<input type=\"hidden\" name=\"id\" value=\"{$editData['id']}\">" : ''),
     '<div class="form-group">
        <label for="mata_kuliah_id">Mata Kuliah:</label>
        <select id="mata_kuliah_id" name="mata_kuliah_id" required>
          <option value="">— Pilih —</option>';
foreach ($mks as $m) {
    $sel = $edit && $m['id']==$editData['mata_kuliah_id'] ? ' selected' : '';
    echo "<option value=\"{$m['id']}\"$sel>" . htmlspecialchars($m['nama_matkul']) . "</option>";
}
echo '  </select>
      </div>
      <div class="form-group">
        <label for="dosen_id">Dosen:</label>
        <select id="dosen_id" name="dosen_id" required>
          <option value="">— Pilih —</option>';
foreach ($dosens as $d) {
    $sel = $edit && $d['id']==$editData['dosen_id'] ? ' selected' : '';
    echo "<option value=\"{$d['id']}\"$sel>" . htmlspecialchars($d['nama']) . "</option>";
}
echo '  </select>
      </div>
      <div class="form-group">
        <label for="hari">Hari:</label>
        <select id="hari" name="hari" required>';
foreach ($days as $day) {
    $sel = $edit && $day==$editData['hari'] ? ' selected' : '';
    echo "<option value=\"$day\"$sel>$day</option>";
}
echo '  </select>
      </div>
      <div class="form-group">
        <label for="jam_mulai">Jam Mulai:</label>
        <input type="time" id="jam_mulai" name="jam_mulai" value="'
      . ($edit ? htmlspecialchars($editData['jam_mulai']) : '')
      . '" required>
      </div>
      <div class="form-group">
        <label for="jam_selesai">Jam Selesai:</label>
        <input type="time" id="jam_selesai" name="jam_selesai" value="'
      . ($edit ? htmlspecialchars($editData['jam_selesai']) : '')
      . '" required>
      </div>
      <div class="form-group">
        <label for="ruangan">Ruangan:</label>
        <input type="text" id="ruangan" name="ruangan" value="'
      . ($edit ? htmlspecialchars($editData['ruangan']) : '')
      . '" required>
      </div>
      <button type="submit">' . ($edit ? 'Simpan Perubahan' : 'Tambah Jadwal') . '</button>';
if ($edit) {
    echo ' <a href="admin/jadwal_crud.php" class="cancel">Batal</a>';
}
echo '</form>';
echo '<div style="text-align:center;"><a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a></div>';
echo '</div>';

include __DIR__ . '/../includes/footer.php';
