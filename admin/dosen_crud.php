<?php
// admin/dosen_crud.php

$page_title = 'Kelola Daftar Dosen';
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
  margin-bottom: 2rem;
  text-align: center;
}
.back-link:hover {
  background: linear-gradient(90deg, #003366 60%, #0056b3 100%);
  box-shadow: 0 4px 16px rgba(0,64,133,0.12);
  transform: translateY(-2px) scale(1.03);
}
/* Tabel Dosen */
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
form input[type="text"],
form input[type="email"] {
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

// --- Hapus ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM dosen WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: admin/dosen_crud.php');
    exit;
}

// --- Edit mode: ambil data dosen ---
$edit     = false;
$editData = [];
if (isset($_GET['edit'])) {
    $edit = true;
    $id   = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM dosen WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res  = $stmt->get_result();
    if ($res->num_rows === 1) {
        $editData = $res->fetch_assoc();
    } else {
        header('Location: admin/dosen_crud.php');
        exit;
    }
}

// --- Handle Submit ---
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']   ?? '');
    $email  = trim($_POST['email']  ?? '');
    $kontak = trim($_POST['kontak'] ?? '');
    $isEdit = !empty($_POST['id']);

    if ($nama === '')   $errors[] = 'Nama dosen wajib diisi.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }
    if ($kontak === '') $errors[] = 'Kontak wajib diisi.';

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $conn->prepare("
              UPDATE dosen
                 SET nama=?, email=?, kontak=?
               WHERE id=?
            ");
            $stmt->bind_param("sssi", $nama, $email, $kontak, $_POST['id']);
        } else {
            $stmt = $conn->prepare("
              INSERT INTO dosen (nama, email, kontak)
              VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sss", $nama, $email, $kontak);
        }
        $stmt->execute();
        header('Location: admin/dosen_crud.php');
        exit;
    }
}

// --- Daftar Dosen ---
$containerOpen = '<div class="container">';
echo $containerOpen;
echo '<h2 style="margin-bottom:1.5rem;">Daftar Dosen</h2>';
$res = $conn->query("SELECT * FROM dosen ORDER BY nama");
if ($res->num_rows === 0) {
    echo '<p>Belum ada data dosen.</p>';
} else {
    echo '<div class="table-wrapper"><table>';
    echo '<thead><tr>
          <th>No.</th>
          <th>Nama Dosen</th>
          <th>Email</th>
          <th>Kontak</th>
          <th>Aksi</th>
        </tr></thead><tbody>';
    $no = 1;
    while ($r = $res->fetch_assoc()) {
        echo '<tr>',
             "<td>{$no}</td>",
             '<td>' . htmlspecialchars($r['nama']) . '</td>',
             '<td>' . htmlspecialchars($r['email']) . '</td>',
             '<td>' . htmlspecialchars($r['kontak']) . '</td>',
             '<td class="aksi">',
               "<a href=\"?edit={$r['id']}\">Edit</a> ",
               "<a href=\"?delete={$r['id']}\" class=\"delete\" ",
               "onclick=\"return confirm('Yakin ingin menghapus dosen ini?')\">Hapus</a>",
             '</td>',
             '</tr>';
        $no++;
    }
    echo '</tbody></table></div>';
}

// --- Form Tambah/Edit ---
echo '<h2 style="margin-bottom:1.2rem;">' . ($edit ? 'Edit' : 'Tambah') . ' Dosen</h2>';
if ($errors) {
    echo '<div class="error">';
    foreach ($errors as $e) {
        echo '<p>' . htmlspecialchars($e) . '</p>';
    }
    echo '</div>';
}
echo '<form method="post">',
     ($edit ? "<input type=\"hidden\" name=\"id\" value=\"{$editData['id']}\">" : ''),
     '<div class="form-group">
        <label for="nama">Nama Dosen:</label>
        <input type="text" id="nama" name="nama" value="'
       . htmlspecialchars($_POST['nama'] ?? $editData['nama'] ?? '')
       . '" required>
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="'
       . htmlspecialchars($_POST['email'] ?? $editData['email'] ?? '')
       . '" required>
      </div>
      <div class="form-group">
        <label for="kontak">Kontak:</label>
        <input type="text" id="kontak" name="kontak" value="'
       . htmlspecialchars($_POST['kontak'] ?? $editData['kontak'] ?? '')
       . '" required>
      </div>
      <button type="submit">' . ($edit ? 'Simpan Perubahan' : 'Tambah Dosen') . '</button>';
if ($edit) {
    echo ' <a href="admin/dosen_crud.php" class="cancel">Batal</a>';
}
echo '</form>';
echo '<div style="text-align:center;"><a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a></div>';
echo '</div>';

include __DIR__ . '/../includes/footer.php';
