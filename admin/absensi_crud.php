<?php
// admin/absensi_crud.php

$page_title = 'Kelola Absensi';

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
/* Navigasi aksi */
.action-nav {
  width: 100%;
  display: flex;
  justify-content: center;
  gap: 1.2rem;
  margin-bottom: 2rem;
}
.action-nav .btn {
  padding: .7rem 1.5rem;
  background: linear-gradient(90deg, #004085 60%, #007bff 100%);
  color: #fff;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1rem;
  box-shadow: 0 2px 8px rgba(0,64,133,0.10);
  transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
  border: none;
}
.action-nav .btn.active,
.action-nav .btn:hover {
  background: linear-gradient(90deg, #003366 60%, #0056b3 100%);
  box-shadow: 0 4px 16px rgba(0,64,133,0.14);
  transform: translateY(-2px) scale(1.03);
}
/* Filter & Sort */
.filter-form {
  width: 100%;
  display: flex;
  flex-wrap: wrap;
  gap: 1.2rem;
  justify-content: center;
  margin-bottom: 2rem;
}
.filter-form label {
  display: flex;
  flex-direction: column;
  font-weight: 500;
  font-size: .95rem;
  color: #004085;
}
.filter-form input,
.filter-form select {
  padding: .6rem .8rem;
  border: 1px solid #e3eafc;
  border-radius: 6px;
  font-size: .98rem;
}
.filter-form button,
.filter-form .reset {
  padding: .7rem 1.5rem;
  border: none;
  border-radius: 6px;
  color: #fff;
  cursor: pointer;
  font-weight: 500;
  font-size: 1rem;
  box-shadow: 0 2px 8px rgba(40,167,69,0.08);
  transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
}
.filter-form button {
  background: linear-gradient(90deg, #28a745 60%, #43e97b 100%);
}
/* Tombol reset */
.filter-form .reset {
  background: linear-gradient(90deg, #6c757d 60%, #adb5bd 100%);
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
}
.filter-form button:hover {
  background: linear-gradient(90deg, #218838 60%, #43e97b 100%);
  box-shadow: 0 4px 16px rgba(40,167,69,0.12);
  transform: translateY(-2px) scale(1.03);
}
.filter-form .reset:hover {
  background: linear-gradient(90deg, #495057 60%, #adb5bd 100%);
}
/* Tabel wrapper */
.table-wrapper {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-bottom: 2rem;
}
.table-wrapper table {
  width: 32%;
  max-width: 140px;
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
  padding: .38rem .35rem;
  border: 1px solid #e3eafc;
  text-align: center;
  font-size: 0.78rem;
}
.table-wrapper th {
  background: #e9ecef;
  color: #004085;
  font-weight: 600;
}
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
form select, form textarea, form input[type=file] {
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
/* Back link */
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
/* Thumbnail */
.foto-thumb {
  width: 60px;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0,40,120,0.10);
}
CSS;

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_admin.php';
include     __DIR__ . '/../includes/header.php';

// Tentukan aksi: list, add, edit
$action = $_GET['action'] ?? 'list';

// --- DELETE via action=delete&id=...
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->prepare("DELETE FROM absensi WHERE id = ?")
         ->bind_param("i", $id)
         ->execute();
    header('Location: admin/absensi_crud.php?action=list');
    exit;
}

// --- EDIT mode: ambil data ---
$editData = [];
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM absensi WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $editData = $res->fetch_assoc() ?: [];
}

// --- TANGANI FORM SUBMIT (add/edit) ---
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mid    = intval($_POST['mahasiswa_id']);
    $jid    = intval($_POST['jadwal_id']);
    $materi = trim($_POST['materi']);
    $isEdit = !empty($_POST['id']);

    if (!$mid)    $errors[] = 'Silakan pilih mahasiswa.';
    if (!$jid)    $errors[] = 'Silakan pilih jadwal.';
    if ($materi==='') $errors[] = 'Materi tidak boleh kosong.';
    if (!isset($_FILES['foto']) || $_FILES['foto']['error']!==UPLOAD_ERR_OK) {
        $errors[] = 'Foto wajib diunggah.';
    }

    if (empty($errors)) {
        // Upload foto
        $file = $_FILES['foto'];
        $ext  = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
        if (!in_array($ext,['jpg','jpeg','png','gif'])) {
            $errors[] = 'Format foto tidak diizinkan.';
        } else {
            $newName   = time().'_'.random_int(1000,9999).'.'.$ext;
            $uploadDir = __DIR__.'/../uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir,0755,true);
            move_uploaded_file($file['tmp_name'],$uploadDir.$newName);
        }
    }

    if (empty($errors)) {
        if ($isEdit) {
            // Hapus foto lama
            if (!empty($editData['foto'])
              && file_exists(__DIR__."/../uploads/{$editData['foto']}")) {
                unlink(__DIR__."/../uploads/{$editData['foto']}");
            }
            // Update
            $stmt = $conn->prepare("
              UPDATE absensi
                 SET mahasiswa_id=?, jadwal_id=?, materi=?, foto=?
               WHERE id=?
            ");
            $stmt->bind_param("iissi",
              $mid, $jid, $materi, $newName, intval($_POST['id'])
            );
            $stmt->execute();
        } else {
            // Insert baru
            $stmt = $conn->prepare("
              INSERT INTO absensi
                (mahasiswa_id,jadwal_id,materi,foto)
              VALUES (?,?,?,?)
            ");
            $stmt->bind_param("iiss",
              $mid, $jid, $materi, $newName
            );
            $stmt->execute();
        }
        header('Location: admin/absensi_crud.php?action=list');
        exit;
    }
}

// --- TOMBOL NAVIGASI ---
echo '<div class="action-nav">',
     '<a href="?action=list" class="btn '.($action==='list'?'active':'').'">Daftar Absensi</a>',
     '<a href="?action=add"  class="btn '.($action==='add'?'active':'').'">Tambah Absensi</a>',
     '</div>';

if ($action === 'list') {
    // Ambil opsi filter
    $mata  = $conn->query("SELECT id,nama_matkul FROM mata_kuliah ORDER BY nama_matkul");
    $dosen = $conn->query("SELECT id,nama FROM dosen ORDER BY nama");
    $mhs   = $conn->query("SELECT id,nomor_absen,nama FROM mahasiswa ORDER BY nomor_absen");

    // Baca filter & sort
    $f_date  = $_GET['filter_date']  ?? '';
    $f_mk    = intval($_GET['filter_mk']    ?? 0);
    $f_dosen = intval($_GET['filter_dosen'] ?? 0);
    $f_mhs   = intval($_GET['filter_mahasiswa'] ?? 0);
    // aman: default ke "tanggal" jika tidak ada atau tidak valid
    $allowedSort = ['tanggal', 'nama'];
    $sortParam   = $_GET['sort_by'] ?? 'tanggal';
    $sort        = in_array($sortParam, $allowedSort)
                  ? $sortParam
                  : 'tanggal';


    // Form filter
    echo '<form method="get" class="filter-form">',
         '<input type="hidden" name="action" value="list">',
         '<label>Tanggal:<input type="date" name="filter_date" value="'.htmlspecialchars($f_date).'"></label>',
         '<label>Matakuliah:<select name="filter_mk"><option value="">Semua</option>';
    while($r=$mata->fetch_assoc()){
        $sel = $r['id']==$f_mk?' selected':'';
        echo "<option value=\"{$r['id']}\"$sel>{$r['nama_matkul']}</option>";
    }
    echo '</select></label>',
         '<label>Dosen:<select name="filter_dosen"><option value="">Semua</option>';
    while($r=$dosen->fetch_assoc()){
        $sel = $r['id']==$f_dosen?' selected':'';
        echo "<option value=\"{$r['id']}\"$sel>".htmlspecialchars($r['nama'])."</option>";
    }
    echo '</select></label>',
         '<label>Mahasiswa:<select name="filter_mahasiswa"><option value="">Semua</option>';
    while($r=$mhs->fetch_assoc()){
        $sel = $r['id']==$f_mhs?' selected':'';
        echo "<option value=\"{$r['id']}\"$sel>"
           . "{$r['nomor_absen']} – ".htmlspecialchars($r['nama'])
           ."</option>";
    }
    echo '</select></label>',
         '<label>Sort by:<select name="sort_by">',
         '<option value="tanggal"'.($sort==='tanggal'?' selected':'').'>Tanggal</option>',
         '<option value="nama"'.($sort==='nama'?' selected':'').'>Nama</option>',
         '</select></label>',
         '<button type="submit">Terapkan</button>',
         '<button type="reset" class="reset" onclick="window.location.href=\'absensi_crud.php?action=list\'">Reset</button>',
         '</form>';

    // Bangun query dinamis
    $sql  = "
      SELECT a.id, m.nomor_absen, m.nama,
             DATE(a.tanggal_absensi) AS tanggal,
             CONCAT(j.jam_mulai,'–',j.jam_selesai) AS jam,
             mk.nama_matkul, d.nama AS dosen, a.materi, a.foto
      FROM absensi a
      JOIN mahasiswa m    ON a.mahasiswa_id   = m.id
      JOIN jadwal j       ON a.jadwal_id      = j.id
      JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
      JOIN dosen d        ON j.dosen_id       = d.id
      WHERE 1
    ";
    $params = [];
    $types  = '';

    if ($f_date) {
        $sql     .= " AND DATE(a.tanggal_absensi)=?";
        $params[] = $f_date;
        $types   .= 's';
    }
    if ($f_mk) {
        $sql     .= " AND mk.id=?";
        $params[] = $f_mk;
        $types   .= 'i';
    }
    if ($f_dosen) {
        $sql     .= " AND d.id=?";
        $params[] = $f_dosen;
        $types   .= 'i';
    }
    if ($f_mhs) {
        $sql     .= " AND m.id=?";
        $params[] = $f_mhs;
        $types   .= 'i';
    }

    $orderCol = $sort === 'nama' ? 'm.nama' : 'a.tanggal_absensi';
    $sql     .= " ORDER BY {$orderCol} ASC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();

    // Tampilkan tabel
    echo '<div class="table-wrapper"><table>',
         '<thead><tr>',
           '<th>No.</th><th>Absen</th><th>Nama</th><th>Tanggal</th>',
           '<th>Jam</th><th>Matakuliah</th><th>Dosen</th>',
           '<th>Materi</th><th>Foto</th><th>Aksi</th>',
         '</tr></thead><tbody>';
    $no=1;
    while($r=$res->fetch_assoc()){
        echo '<tr>',
             "<td>{$no}</td>",
             "<td>{$r['nomor_absen']}</td>",
             '<td>'.htmlspecialchars($r['nama']).'</td>',
             '<td>'.htmlspecialchars($r['tanggal']).'</td>',
             '<td>'.htmlspecialchars($r['jam']).'</td>',
             '<td>'.htmlspecialchars($r['nama_matkul']).'</td>',
             '<td>'.htmlspecialchars($r['dosen']).'</td>',
             '<td>'.nl2br(htmlspecialchars($r['materi'])).'</td>',
             '<td>';
        if($r['foto'] && file_exists(__DIR__."/../uploads/{$r['foto']}")){
            echo "<img src=\"../uploads/{$r['foto']}\" class=\"foto-thumb\">";
        } else { echo '(–)'; }
        echo '</td>',
             "<td class=\"aksi\">",
               "<a href=\"?action=edit&id={$r['id']}\">Edit</a> ",
               "<a href=\"?action=delete&id={$r['id']}\" class=\"delete\" ",
               "onclick=\"return confirm('Yakin hapus?')\">Hapus</a>",
             "</td>",
             '</tr>';
        $no++;
    }
    echo '</tbody></table></div>';
}

// --- FORM ADD / EDIT ---
if ($action === 'add' || $action === 'edit') {
    // Ambil opsi dropdown
    $mahas = $conn->query("SELECT id,nomor_absen,nama FROM mahasiswa ORDER BY nomor_absen");
    $jdwls = $conn->query("
      SELECT j.id,
             CONCAT(j.hari,' ',j.jam_mulai,'–',j.jam_selesai,' | ',
                    mk.nama_matkul,' – ',d.nama) AS label
      FROM jadwal j
      JOIN mata_kuliah mk ON j.mata_kuliah_id=mk.id
      JOIN dosen d       ON j.dosen_id=d.id
      ORDER BY FIELD(j.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), j.jam_mulai
    ");
    echo '<h2>'.($action==='edit'?'Edit':'Tambah').' Absensi</h2>';
    if ($errors) {
        echo '<div class="error">';
        foreach($errors as $e) echo '<p>'.htmlspecialchars($e).'</p>';
        echo '</div>';
    }
    echo '<form method="post" enctype="multipart/form-data">';
    if ($action==='edit') {
        echo "<input type=\"hidden\" name=\"id\" value=\"{$editData['id']}\">";
    }
    // Mahasiswa
    echo '<div class="form-group"><label>Mahasiswa:</label>',
         '<select name="mahasiswa_id" required><option value="">— Pilih —</option>';
    while($m=$mahas->fetch_assoc()){
        $sel = ($action==='edit' && $m['id']==$editData['mahasiswa_id'])?' selected':'';
        echo "<option value=\"{$m['id']}\"$sel>{$m['nomor_absen']} – ".htmlspecialchars($m['nama'])."</option>";
    }
    echo '</select></div>';
    // Jadwal
    echo '<div class="form-group"><label>Jadwal:</label>',
         '<select name="jadwal_id" required><option value="">— Pilih —</option>';
    while($j=$jdwls->fetch_assoc()){
        $sel = ($action==='edit' && $j['id']==$editData['jadwal_id'])?' selected':'';
        echo "<option value=\"{$j['id']}\"$sel>".htmlspecialchars($j['label'])."</option>";
    }
    echo '</select></div>';
    // Materi
    $matv = $_POST['materi'] ?? $editData['materi'] ?? '';
    echo '<div class="form-group"><label>Materi:</label>',
         "<textarea name=\"materi\" rows=\"3\" required>".htmlspecialchars($matv)."</textarea></div>";
    // Foto lama
    if ($action==='edit' && !empty($editData['foto'])) {
        echo '<div class="form-group"><label>Foto Lama:</label><br>',
             "<img src=\"../uploads/{$editData['foto']}\" class=\"foto-thumb\"></div>";
    }
    // Upload baru
    echo '<div class="form-group"><label>Upload Foto '.($action==='edit'?'Baru':'').':</label>',
         '<input type="file" name="foto" accept="image/*" required></div>';
    echo '<button type="submit">'.($action==='edit'?'Simpan Perubahan':'Tambah Absensi').'</button>';
    if ($action==='edit') {
        echo ' <a href="admin/absensi_crud.php?action=list" class="cancel">Batal</a>';
    }
    echo '</form>';
}

// Back to dashboard
echo '<div style="text-align:center;"><a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a></div>';

include __DIR__ . '/../includes/footer.php';
