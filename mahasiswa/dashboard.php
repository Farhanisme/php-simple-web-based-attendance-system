<?php
// mahasiswa/dashboard.php

// Judul dan CSS khusus halaman
$page_title = 'Dashboard Mahasiswa';
$page_css = <<<CSS
/* Dashboard Mahasiswa - Layout Simetris & Modern (Internal CSS) */
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
  font-size: 1.7rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: #007bff;
  letter-spacing: 1px;
  text-align: center;
  text-shadow: 0 2px 8px rgba(0,64,133,0.08);
}
p {
  font-size: 1.1rem;
  color: #444;
  text-align: center;
  margin-bottom: 2rem;
}
nav {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}
nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
  align-items: center;
}
nav ul li {
  width: 100%;
  display: flex;
  justify-content: center;
}
nav ul li a {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  width: 100%;
  max-width: 400px;
  padding: 1rem 1.5rem;
  background: linear-gradient(90deg, #004085 60%, #007bff 100%);
  color: #fff;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.08rem;
  box-shadow: 0 2px 8px rgba(0,64,133,0.10);
  transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
  text-align: left;
  border: none;
  justify-content: flex-start;
}
nav ul li a:hover {
  background: linear-gradient(90deg, #003366 60%, #0056b3 100%);
  box-shadow: 0 4px 16px rgba(0,64,133,0.14);
  transform: translateY(-2px) scale(1.03);
}
CSS;

// Load konfigurasi, cek session Mahasiswa, lalu header
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_user.php';
include     __DIR__ . '/../includes/header.php';
?>

  <h2>Halo, <?= htmlspecialchars($_SESSION['mahasiswa_nama']) ?>!</h2>
  <p>Pilih menu di bawah untuk mengelola absensi Anda:</p>

  <nav>
    <ul>
      <li><a href="absensi_add.php">📥 Catat Absensi Hari Ini</a></li>
      <li><a href="absensi_list.php">✏️ Edit/Hapus Absensi Hari Ini</a></li>
      <li><a href="absensi_kelas.php">📋 Lihat Absensi Kelas</a></li>
      <li><a href="absensi_my.php">👤 Lihat Absensi Saya</a></li>
      <li><a href="absensi_download.php">⬇️ Download Rekap CSV</a></li>
      <li><a href="../index.php?logout=1">🔓 Logout</a></li>
    </ul>
  </nav>

<?php include __DIR__ . '/../includes/footer.php'; ?>
