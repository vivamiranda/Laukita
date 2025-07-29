<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "ID kampanye tidak ditemukan.";
    exit;
}

$campaign_id = (int) $_GET['id'];
$query = "SELECT * FROM campaigns WHERE campaign_id = $campaign_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "Kampanye tidak ditemukan.";
    exit;
}

$campaign = mysqli_fetch_assoc($result);
$progress = ($campaign["current_amount"] / $campaign["target_amount"]) * 100;
if ($progress > 100) $progress = 100;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($campaign['campaign_name']) ?> - Deskripsi Kampanye</title>
  <link rel="stylesheet" href="style_fixed.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fdfdfd;
      margin: 0;
      min-height: 100vh;
      display: block;
      overflow-x: hidden;
    }

    .campaign-detail-container {
      max-width: 900px;
      margin: 6rem auto 4rem;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
      overflow: hidden;
    }

    .campaign-detail-image {
      width: 100%;
      height: 350px;
      object-fit: cover;
    }

    .campaign-detail-body {
      padding: 2rem;
    }

    .campaign-detail-body h1 {
      color: #ff6b35;
      margin-top: 0;
    }

    .campaign-detail-body p {
      color: #444;
      line-height: 1.6;
      white-space: pre-line;
    }

    .progress-bar-container {
      background-color: #eee;
      border-radius: 4px;
      height: 12px;
      margin-top: 1rem;
      overflow: hidden;
    }

    .progress-bar {
      background-color: #ff6b35;
      height: 100%;
      transition: width 0.3s;
    }

    .donation-stats {
      font-size: 1rem;
      color: #666;
      margin-top: 0.5rem;
    }

    .btn-donasi {
      display: inline-block;
      margin-top: 2rem;
      padding: 12px 24px;
      background-color: #ff6b35;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.2s;
    }

    .btn-donasi:hover {
      background-color: #e55a24;
    }

    .back-link {
      display: inline-block;
      margin: 2rem;
      color: #666;
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body class="donasi-page">

  <!-- HEADER -->
  <header class="navbar">
    <div class="nav-container">
      <div class="logo">
        <img src="gambar/Logo U.png" alt="GotongID Logo">
      </div>
      <div class="menu-toggle" onclick="toggleMenu()">☰</div>
      <nav>
        <ul class="nav-menu">
          <li><a href="./index.php">Beranda</a></li>
          <li><a href="./donasi.php" class="active">Donasi</a></li>
          <li><a href="tentang-kami.php">Tentang Kami</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- DETAIL KAMPANYE -->
  <div class="campaign-detail-container">
    <img class="campaign-detail-image" src="<?= htmlspecialchars($campaign['image_url']) ?>" alt="Gambar Kampanye">
    <div class="campaign-detail-body">
      <h1><?= htmlspecialchars($campaign['campaign_name']) ?></h1>
      <p><?= nl2br(htmlspecialchars($campaign['description'])) ?></p>

      <div class="progress-bar-container">
        <div class="progress-bar" style="width: <?= $progress ?>%;"></div>
      </div>

      <div class="donation-stats">
        <strong>Rp <?= number_format($campaign['current_amount'], 0, ',', '.') ?></strong>
        dari <strong>Rp <?= number_format($campaign['target_amount'], 0, ',', '.') ?></strong>
      </div>

      <a href="form-donasi.php?id=<?= $campaign['campaign_id'] ?>" class="btn-donasi">Donasi Sekarang</a>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="get-in-touch-footer">
    <div class="footer-inner-container">
      <div class="footer-left-content">
        <h2>Yuk, Sapa GotongID!</h2>
        <p>Punya pertanyaan, ide kolaborasi, atau ingin tahu lebih lanjut tentang GotongID? Kami siap mendengarkan.</p>
        <div class="footer-social-icons">
          <a href="https://www.instagram.com/gotongid" target="_blank"><i class="fab fa-instagram"></i></a>
          <a href="https://www.tiktok.com/@gotongid" target="_blank"><i class="fab fa-tiktok"></i></a>
        </div>
      </div>
      <div class="footer-right-cards">
        <div class="contact-card">
          <i class="fas fa-globe"></i>
          <span><a href="https://www.gotongid.com" target="_blank" class="footer-link">www.gotongid.com</a></span>
        </div>
        <div class="contact-card">
          <i class="fas fa-envelope"></i>
          <span><a href="mailto:hallogotongid@gmail.com" class="footer-link">hallogotongid@gmail.com</a></span>
        </div>
      </div>
    </div>
  </footer>

  <script>
    function toggleMenu() {
      document.querySelector('.nav-menu').classList.toggle('show');
    }

    const currentFile = location.pathname.split("/").pop();
    document.querySelectorAll(".nav-menu a").forEach(link => {
      const linkFile = link.getAttribute("href").split("/").pop();
      if (linkFile === currentFile) link.classList.add("active");
    });
  </script>
</body>
</html>
