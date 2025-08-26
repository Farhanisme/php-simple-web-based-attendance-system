<?php
// includes/auth_user.php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['mahasiswa_id'])) {
    // belum login → kembalikan ke landing
    header('Location: /index.php?error=login');
    exit;
}

// data mahasiswa saat ini bisa diambil dengan:
// $mahasiswa_id = $_SESSION['mahasiswa_id'];
