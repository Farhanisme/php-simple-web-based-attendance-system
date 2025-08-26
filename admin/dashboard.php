<?php
// admin/dashboard.php

// Judul dan CSS khusus halaman
$page_title = 'Dashboard Dosen (Admin)';


$page_css = <<<CSS
/* Container simetris dan modern */
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
p {
  font-size: 1.05rem;
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

// Load konfigurasi, cek session Admin, lalu header
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_admin.php';
include     __DIR__ . '/../includes/header.php';
?>

  <h2>Halo, Dosen!</h2>
  <p>Pilih menu untuk mengelola sistem absensi:</p>

  <nav>
    <ul>
      <li><a href="absensi_crud.php">📋 Kelola Absensi (CRUD)</a></li>
      <li><a href="dosen_crud.php">👨‍🏫 Kelola Daftar Dosen</a></li>
      <li><a href="jadwal_crud.php">📆 Kelola Jadwal Mata Kuliah</a></li>
      <li><a href="statistik.php">📊 Statistik Kehadiran</a></li>
      <li><a href="../index.php?logout=1">🔓 Logout</a></li>
    </ul>
  </nav>


<?php include __DIR__ . '/../includes/footer.php'; ?>
