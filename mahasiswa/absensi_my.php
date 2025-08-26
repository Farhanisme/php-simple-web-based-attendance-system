
<?php
// mahasiswa/absensi_my.php

$page_title = 'Rekap Absensi Saya';
$page_css = <<<CSS
/* Absensi Saya - Simetris & Modern (Konsisten dengan Dashboard/Absensi List) */
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
  margin-top: 1.2rem;
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
  margin-bottom: 1.5rem;
  text-align: center;
}
.back-link:hover {
  background: linear-gradient(90deg, #003366 60%, #0056b3 100%);
  box-shadow: 0 4px 16px rgba(0,64,133,0.12);
  transform: translateY(-2px) scale(1.03);
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
  background: #f7faff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,40,120,0.08);
}
th, td {
  padding: .85rem;
  border: 1px solid #e3eafc;
  text-align: left;
  font-size: 1rem;
}
th {
  background: #e9ecef;
  color: #004085;
  font-weight: 600;
}
.foto-thumb {
  width: 60px;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0,40,120,0.10);
}
.download-link {
  display: inline-block;
  margin-top: 1rem;
  padding: .7rem 1.5rem;
  background: linear-gradient(90deg, #28a745 60%, #43e97b 100%);
  color: #fff;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 500;
  font-size: 1rem;
  box-shadow: 0 2px 8px rgba(40,167,69,0.08);
  transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
  border: none;
}
.download-link:hover {
  background: linear-gradient(90deg, #218838 60%, #43e97b 100%);
  box-shadow: 0 4px 16px rgba(40,167,69,0.12);
  transform: translateY(-2px) scale(1.03);
}
CSS;

require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/auth_user.php';
include     __DIR__.'/../includes/header.php';

$mahasiswa_id = $_SESSION['mahasiswa_id'];
$currentYM    = date('Y-m');

// Ambil data absensi bulan ini
$stmt = $conn->prepare("
  SELECT a.tanggal_absensi, j.jam_mulai, j.jam_selesai,
         mk.nama_matkul, j.ruangan, a.materi, a.foto
  FROM absensi a
  JOIN jadwal j        ON a.jadwal_id       = j.id
  JOIN mata_kuliah mk  ON j.mata_kuliah_id  = mk.id
  WHERE a.mahasiswa_id = ? 
    AND DATE_FORMAT(a.tanggal_absensi, '%Y-%m') = ?
  ORDER BY a.tanggal_absensi ASC, j.jam_mulai ASC
");
$stmt->bind_param('is', $mahasiswa_id, $currentYM);
$stmt->execute();
$res = $stmt->get_result();
?>


  <h2>Rekap Absensi Saya Bulan <?= date('F Y'); ?></h2>

  <?php if ($res->num_rows === 0): ?>
    <p>Belum ada absensi pada bulan ini.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>No.</th>
          <th>Tanggal</th>
          <th>Jam</th>
          <th>Mata Kuliah</th>
          <th>Ruangan</th>
          <th>Materi</th>
          <th>Foto</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; while ($row = $res->fetch_assoc()): ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= date('d-m-Y', strtotime($row['tanggal_absensi'])); ?></td>
          <td><?= htmlspecialchars($row['jam_mulai']) ?>–<?= htmlspecialchars($row['jam_selesai']) ?></td>
          <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
          <td><?= htmlspecialchars($row['ruangan']) ?></td>
          <td><?= nl2br(htmlspecialchars($row['materi'])) ?></td>
          <td>
            <?php if ($row['foto'] && file_exists(__DIR__ . "/../uploads/{$row['foto']}")): ?>
              <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>"
                   class="foto-thumb" alt="Foto">
            <?php else: ?>
              (–)
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <a href="absensi_download.php" class="download-link">
      ⬇️ Download CSV Rekap
    </a>
  <?php endif; ?>

  <div style="margin-top:1.5rem; text-align:center;">
    <a href="dashboard.php" class="back-link">&larr; Kembali ke Dashboard</a>
  </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
