<?php
// includes/config.php

date_default_timezone_set('Asia/Makassar');

session_start();

// Laporkan error MySQLi sebagai exception
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// **Ganti sesuai kredensial Anda**
$host    = 'localhost';
$db      = 'absensi_db';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

// Buat koneksi
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset($charset);
