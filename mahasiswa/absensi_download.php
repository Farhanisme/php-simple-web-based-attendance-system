<?php
// mahasiswa/absensi_download.php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth_user.php';

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

// Siapkan header CSV
// Ambil data mahasiswa untuk nama file
$stmtU = $conn->prepare("
  SELECT nomor_absen, nama, nim
  FROM mahasiswa
  WHERE id = ?
");
$stmtU->bind_param("i", $mahasiswa_id);
$stmtU->execute();
$user = $stmtU->get_result()->fetch_assoc();

// Sanitasi nama: huruf kecil, spasi → underscore, buang karakter non-alfanumerik/underscore
$cleanName = strtolower($user['nama']);
$cleanName = preg_replace('/[^a-z0-9 ]/', '', $cleanName);
$cleanName = str_replace(' ', '_', $cleanName);

// Buat filename sesuai format no_nama_nim_rekap_absensi.csv
$filename = sprintf(
  '%s_%s_%s_rekap_absensi.csv',
  $user['nomor_absen'],
  $cleanName,
  $user['nim']
);

// Header CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Tulis CSV
$out = fopen('php://output', 'w');
fputcsv($out, ['No', 'Tanggal', 'Jam', 'Mata Kuliah', 'Ruangan', 'Materi', 'Foto']);
$no = 1;
while ($row = $res->fetch_assoc()) {
    $tanggal = date('d-m-Y', strtotime($row['tanggal_absensi']));
    $jam     = $row['jam_mulai'] . '–' . $row['jam_selesai'];
    fputcsv($out, [
      $no++, $tanggal, $jam,
      $row['nama_matkul'],
      $row['ruangan'],
      $row['materi'],
      $row['foto']
    ]);
}
fclose($out);
exit;
