<?php
// includes/header.php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($page_title ?? 'Sistem Absensi') ?></title>

  <!-- Google Fonts & Font Awesome -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
  <!-- Page‑specific CSS -->
  <?php if (!empty($page_css)): ?>
  <style><?= $page_css ?></style>
  <?php endif; ?>

  <!-- Header styling -->
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #e3eafc;
      color: #222;
    }
    header {
      background: linear-gradient(90deg, #004085 0%, #007bff 100%);
      color: #fff;
      padding: 1rem 0;
      box-shadow: 0 4px 16px rgba(0,0,0,0.05);
    }
    .header-content {
      max-width: 900px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 1rem;
    }
    .header-title {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 1.8rem;
      font-weight: 600;
    }
    .header-title i {
      font-size: 2.2rem;
    }
    .header-info {
      text-align: right;
      font-size: 0.95rem;
      line-height: 1.3;
    }
    .header-info .role {
      font-weight: 500;
    }
    .header-info .datetime {
      font-size: 0.85rem;
      opacity: 0.8;
    }
    main.container {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(0,40,120,0.1);
      padding: 2.5rem 2rem;
      margin: 2rem auto;
      max-width: 800px;
    }
  </style>
</head>
<body>
  <header>
    <div class="header-content">
      <div class="header-title">
        <i class="fas fa-user-graduate"></i>
        <span>Sistem Absensi Mahasiswa</span>
      </div>
      <div class="header-info">
        <?php if (isset($_SESSION['mahasiswa_nama'])): ?>
          <div class="role">Mahasiswa: <?= htmlspecialchars($_SESSION['mahasiswa_nama']) ?></div>
        <?php elseif (!empty($_SESSION['admin_logged_in'])): ?>
          <div class="role">Dosen (Admin)</div>
        <?php endif; ?>
        <div class="datetime"><?= date('d M Y, H:i') ?></div>
      </div>
    </div>
  </header>
  <main class="container">
