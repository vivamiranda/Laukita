<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GotongID - Donasi</title>
  <link rel="stylesheet" href="style_fixed.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fdfdfd;
      margin: 0;
    }

    .page-title {
      padding: 5.5rem 1rem 3rem; /* atas ditambah biar turun */
      margin-top: 60px; /* ini membantu kalau navbar fixed */
      background: linear-gradient(to right, #ff6b35, #ff9d5c);
      color: white;
      text-align: center;
    }


    .page-title h1 {
      margin: 0;
      font-size: 2rem;
    }

    .page-title p {
      font-size: 1.1rem;
      margin-top: 0.8rem;
    }

    .section-header {
      text-align: center;
      margin: 2rem 0 1rem;
    }

    .causes-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      padding: 0 2rem 3rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .cause-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: transform 0.2s ease;
    }

    .cause-card:hover {
      transform: translateY(-4px);
    }

    .img-placeholder {
      height: 180px;
      background-color: #f2f2f2;
      background-size: cover;
      background-position: center;
    }

    .card-body {
      padding: 1.2rem;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .card-body h3 {
      color: #ff6b35;
      font-size: 1.2rem;
      margin: 0 0 0.5rem 0;
    }

    .card-body p {
      font-size: 0.95rem;
      color: #444;
      margin-bottom: 0.7rem;
    }

    .progress-bar-container {
      background-color: #eee;
      border-radius: 4px;
      height: 10px;
      margin-bottom: 10px;
      overflow: hidden;
    }

    .progress-bar {
      background-color: #ff6b35;
      height: 100%;
      transition: width 0.3s;
    }

    .donation-stats {
      font-size: 0.9rem;
      color: #666;
    }

    .btn-details {
      display: inline-block;
      margin-top: 1rem;
      padding: 10px 18px;
      background-color: #ff6b35;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.2s;
    }

    .btn-details:hover {
      background-color: #e55a24;
    }

    @media (max-width: 600px) {
      .page-title h1 {
        font-size: 1.5rem;
      }
      .page-title p {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body class="donasi-page">

  <!-- Header -->
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

  <!-- Hero -->
  <section class="page-title">
    <h1>Ulurkan Tanganmu Hari Ini</h1>
    <p>Satu donasi kecil bisa menjadi harapan besar. Bersama kita bisa membantu lebih banyak!</p>
  </section>

  <!-- Campaigns -->
  <section>
    <div class="section-header">
      <h2>Daftar Kampanye Donasi</h2>
    </div>
    <div class="causes-grid">
      <?php
        $sql = "SELECT campaign_id, campaign_name, description, current_amount, target_amount, image_url FROM campaigns ORDER BY campaign_id DESC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
          while($row = mysqli_fetch_assoc($result)) {
            $progress = ($row["current_amount"] / $row["target_amount"]) * 100;
            if ($progress > 100) $progress = 100;
      ?>
        <div class="cause-card">
          <div class="img-placeholder" style="background-image: url('<?php echo htmlspecialchars($row["image_url"]); ?>');"></div>
          <div class="card-body">
            <h3><?php echo htmlspecialchars($row["campaign_name"]); ?></h3>
            <p><?php echo htmlspecialchars(substr($row["description"], 0, 100)) . '...'; ?></p>

            <div class="progress-bar-container">
              <div class="progress-bar" style="width: <?php echo $progress; ?>%;"></div>
            </div>

            <div class="donation-stats">
              <b>Rp <?php echo number_format($row["current_amount"], 0, ',', '.'); ?></b> dari <b>Rp <?php echo number_format($row["target_amount"], 0, ',', '.'); ?></b>
            </div>

            <a href="form-donasi.php?id=<?php echo $row["campaign_id"]; ?>" class="btn-details">Berikan Donasi</a>
          </div>
        </div>
      <?php
          }
        } else {
          echo "<p style='text-align:center;'>Belum ada kampanye donasi tersedia.</p>";
        }
        mysqli_close($conn);
      ?>
    </div>
  </section>

  <!-- Footer -->
  <footer class="get-in-touch-footer">
    <div class="footer-inner-container">
      <div class="footer-left-content">
        <h2>Yuk, Sapa GotongID!</h2>
        <p>Punya pertanyaan, ide kolaborasi, atau ingin tahu lebih lanjut tentang GotongID? Kami siap mendengarkan. Mari bersama membangun dampak positif!</p>
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

  <!-- Nav toggle -->
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
