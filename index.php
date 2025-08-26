<?php

require_once 'includes/config.php';
// — Logout Handler —
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// — Handle Login Submission —
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';

    if ($role === 'mahasiswa') {
        $nomor_absen = intval($_POST['nomor_absen']);
        $stmt = $conn->prepare("SELECT id, nama FROM mahasiswa WHERE nomor_absen = ?");
        $stmt->bind_param("i", $nomor_absen);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $m = $res->fetch_assoc();
            $_SESSION['mahasiswa_id']   = $m['id'];
            $_SESSION['mahasiswa_nama'] = $m['nama'];
            header("Location: mahasiswa/dashboard.php");
            exit;
        } else {
            echo "<p class='error'>Nomor absen tidak valid.</p>";
        }

    } elseif ($role === 'dosen') {
        $password = $_POST['password'] ?? '';
        if ($password === '1111') {
            $_SESSION['admin_logged_in'] = true;
            header("Location: admin/dashboard.php");
            exit;
        } else {
            echo "<p class='error'>Password salah.</p>";
        }
    }
}

$page_title = 'Selamat Datang';
// CSS khusus untuk landing & login
$page_css = <<<CSS
/* Layout utama simetris dan modern */
.container {
  max-width: 500px;
  margin: 2.5rem auto;
  padding: 2.5rem 2rem;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(0,40,120,0.10);
  border: 1px solid #e3eafc;
  display: flex;
  flex-direction: column;
  align-items: center;
}
h2 {
  font-size: 1.7rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: #004085;
  letter-spacing: 1px;
  text-align: center;
}
.button {
  display: inline-block;
  margin: .5rem;
  padding: .85rem 2rem;
  background: linear-gradient(90deg, #004085 60%, #007bff 100%);
  color: #fff;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  font-size: 1rem;
  box-shadow: 0 2px 8px rgba(0,64,133,0.08);
  transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
  border: none;
  text-align: center;
  min-width: 140px;
}
.button:hover, form button:hover {
  background: linear-gradient(90deg, #003366 60%, #0056b3 100%);
  box-shadow: 0 4px 16px rgba(0,64,133,0.12);
  transform: translateY(-2px) scale(1.03);
}
form {
  margin-top: 2rem;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}
form label {
  display: block;
  margin: 1rem 0 .4rem;
  font-weight: 500;
  color: #004085;
  text-align: left;
  width: 100%;
}
form select,
form input {
  width: 100%;
  padding: .65rem;
  border: 1px solid #bfcbe3;
  border-radius: 6px;
  font-size: 1rem;
  background: #f7faff;
  margin-bottom: 1rem;
  transition: border-color 0.2s;
  box-sizing: border-box;
   font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
   font-weight: 500;
   color: #004085;
}
form select option {
   font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
   font-size: 1rem;
   font-weight: 500;
   color: #222;
}
form select:focus,
form input:focus {
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
.error {
  background: #ffe3e3;
  color: #b71c1c;
  padding: .7rem 1rem;
  border-radius: 6px;
  margin-bottom: 1.2rem;
  border: 1px solid #ffcdd2;
  font-weight: 500;
  box-shadow: 0 2px 8px rgba(183,28,28,0.06);
  width: 100%;
  text-align: center;
}
.role-buttons {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin: 2rem 0;
}
CSS;

include  'includes/header.php';


?>

<div style="text-align:center; margin:2rem 0;">
  <a href="?role=mahasiswa" class="button">Mahasiswa</a>
  <a href="?role=dosen"      class="button">Dosen</a>
</div>

<?php if (isset($_GET['role']) && $_GET['role'] === 'mahasiswa'): ?>
  <h2>Login Mahasiswa</h2>
  <form method="post">
    <input type="hidden" name="role" value="mahasiswa">
    <label for="nomor_absen">Nomor Absen:</label>
    <select id="nomor_absen" name="nomor_absen" required>
      <option value="">— Pilih Nomor Absen —</option>
      <?php
      $rs = $conn->query("SELECT nomor_absen, nama FROM mahasiswa ORDER BY nomor_absen");
      while ($row = $rs->fetch_assoc()) {
          echo "<option value=\"{$row['nomor_absen']}\">"
             . "{$row['nomor_absen']} – " . htmlspecialchars($row['nama'])
             . "</option>";
      }
      ?>
    </select>
    <button type="submit">Masuk</button>
  </form>

<?php elseif (isset($_GET['role']) && $_GET['role'] === 'dosen'): ?>
  <h2>Login Dosen</h2>
  <form method="post">
    <input type="hidden" name="role" value="dosen">
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <button type="submit">Masuk</button>
  </form>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
