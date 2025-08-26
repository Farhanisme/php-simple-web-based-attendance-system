<?php
// admin/statistik.php

// 1) Siapkan judul & CSS (di-emit oleh header.php)
$page_title = 'Statistik Kehadiran';
$page_css   = <<<CSS
/* Container utama */
main.container {
  max-width: 900px !important;
  margin: 2rem auto !important;
}

/* Center judul & link */
h2 {
  margin: 2rem 0 .5rem;
  text-align: center;
}
.back-link {
  display: block;
  margin: 0 auto 1rem;
  color: #004085;
  text-decoration: none;
  font-weight: 500;
}
.back-link:hover { text-decoration: underline; }

/* Chart area */
.chart-container {
  width: 100%;
  max-width: 800px;
  margin: 2rem auto;
}

/* Paragraf center */
p {
  text-align: center;
  font-size: 1rem;
  margin-bottom: 1rem;
}

/* Table bawah 75% */
.table-wrapper {
  max-width: 800px;
  margin: 2rem auto;
  overflow-x: auto;
}
.table-wrapper table {
  width: 100%;
  border-collapse: collapse;
}
.table-wrapper th,
.table-wrapper td {
  padding: .75rem;
  border: 1px solid #ccc;
  text-align: left;
}
.table-wrapper th {
  background: #e9ecef;
  font-weight: 600;
}
CSS;

// 2) Load koneksi & proteksi admin
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_admin.php';

// 3) Query dan hitung semua data

// Total sesi unik (tanggal + jadwal)
$total_sessions = (int)$conn
  ->query("SELECT COUNT(DISTINCT DATE(tanggal_absensi), jadwal_id) AS cnt FROM absensi")
  ->fetch_assoc()['cnt'];

// 3.1) Persentase per mata kuliah
$mk_labels = []; $mk_values = [];
$res = $conn->query("
  SELECT mk.nama_matkul,
         COUNT(a.id)    AS hadir,
         COUNT(DISTINCT DATE(a.tanggal_absensi), a.jadwal_id) AS sesi,
         ROUND(
           COUNT(a.id) 
           / (COUNT(DISTINCT DATE(a.tanggal_absensi), a.jadwal_id) 
              * (SELECT COUNT(*) FROM mahasiswa)
             )
           * 100, 2
         ) AS persentase
  FROM absensi a
  JOIN jadwal j        ON a.jadwal_id      = j.id
  JOIN mata_kuliah mk  ON j.mata_kuliah_id = mk.id
  GROUP BY mk.id
");
while ($r = $res->fetch_assoc()) {
    $mk_labels[] = $r['nama_matkul'];
    $mk_values[] = $r['persentase'];
}

// 3.2) Persentase per mahasiswa
$st_labels = []; $st_values = []; $below75 = [];
$res = $conn->query("
  SELECT m.nomor_absen, m.nama,
         COUNT(a.id) AS hadir,
         ROUND(COUNT(a.id)/{$total_sessions}*100, 2) AS persentase
    FROM mahasiswa m
    LEFT JOIN absensi a ON a.mahasiswa_id = m.id
    GROUP BY m.id
    ORDER BY m.nomor_absen
");
while ($r = $res->fetch_assoc()) {
    $label = "{$r['nomor_absen']} – {$r['nama']}";
    $st_labels[] = $label;
    $st_values[] = $r['persentase'];
    if ($r['persentase'] < 75) {
        $below75[] = $r;
    }
}

// 3.3) Rata-rata per hari
$day_labels = []; $day_values = [];
$res = $conn->query("
  SELECT DATE(tanggal_absensi) AS tgl,
         ROUND(COUNT(DISTINCT mahasiswa_id)/(SELECT COUNT(*) FROM mahasiswa)*100, 2) 
           AS persentase
    FROM absensi
   GROUP BY DATE(tanggal_absensi)
   ORDER BY tgl
");
while ($r = $res->fetch_assoc()) {
    $day_labels[] = $r['tgl'];
    $day_values[] = $r['persentase'];
}
// Hitung rata-rata
$avgDaily = count($day_values)
          ? round(array_sum($day_values)/count($day_values), 2)
          : 0;

// 4) Sertakan header (emit <main class="container"> …)
include __DIR__ . '/../includes/header.php';
?>

<h2>Persentase Kehadiran Per Mata Kuliah</h2>
<div class="chart-container">
  <canvas id="chartMatkul"></canvas>
</div>

<h2>Persentase Kehadiran Per Mahasiswa</h2>
<div class="chart-container">
  <canvas id="chartMahasiswa"></canvas>
</div>

<h2>Rata-rata Kehadiran Per Hari</h2>
<p>Rata-rata kehadiran harian: <strong><?= $avgDaily ?>%</strong></p>
<div class="chart-container">
  <canvas id="chartDaily"></canvas>
</div>

<h2>Daftar Mahasiswa di Bawah Ambang Kehadiran (&lt;75%)</h2>
<?php if (empty($below75)): ?>
  <p>Tidak ada mahasiswa di bawah 75%.</p>
<?php else: ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>No. Absen</th><th>Nama</th><th>Persentase (%)</th></tr>
      </thead>
      <tbody>
        <?php foreach ($below75 as $b): ?>
        <tr>
          <td><?= htmlspecialchars($b['nomor_absen']) ?></td>
          <td><?= htmlspecialchars($b['nama']) ?></td>
          <td><?= $b['persentase'] ?>%</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<h2>Trend Kehadiran Harian</h2>
<div class="chart-container">
  <canvas id="chartTrend"></canvas>
</div>

<div style="text-align:center; margin-top:2.5rem;">
  <a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // 1) Bar per matkul
  new Chart(document.getElementById('chartMatkul'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($mk_labels) ?>,
      datasets: [{ label: 'Persentase', data: <?= json_encode($mk_values) ?> }]
    },
    options: { responsive: true }
  });
  // 2) Bar per mahasiswa
  new Chart(document.getElementById('chartMahasiswa'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($st_labels) ?>,
      datasets: [{ label: 'Persentase', data: <?= json_encode($st_values) ?> }]
    },
    options: {
      responsive: true,
      scales: { x: { ticks: { autoSkip: false } } }
    }
  });
  // 3) Bar rata-rata harian
  new Chart(document.getElementById('chartDaily'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($day_labels) ?>,
      datasets: [{ label: 'Kehadiran (%)', data: <?= json_encode($day_values) ?> }]
    },
    options: { responsive: true }
  });
  // 4) Trend line
  new Chart(document.getElementById('chartTrend'), {
    type: 'line',
    data: {
      labels: <?= json_encode($day_labels) ?>,
      datasets: [{
        label: 'Trend Kehadiran',
        data: <?= json_encode($day_values) ?>,
        fill: false,
        tension: 0.1
      }]
    },
    options: { responsive: true }
  });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
