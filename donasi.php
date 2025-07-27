<?php
include 'koneksi.php'; // Memanggil file koneksi database
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GotongID - Donasi</title>
  <!-- Menggunakan style.css utama untuk konsistensi -->
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="donasi-page">
  <header class="navbar">
    <div class="nav-container">
      <div class="logo">
        <img src="gambar/Logo U.png" alt="GotongID Logo">
      </div>

      <!-- Tombol hamburger -->
      <div class="menu-toggle" onclick="toggleMenu()">☰</div>

      <nav>
        <ul class="nav-menu">
          <li><a href="index.php">Beranda</a></li>
          <li><a href="donasi.php" class="active">Donasi</a></li>
          <li><a href="detail-donasi.php">Detail Donasi</a></li>
          <li><a href="tentang-kami.html">Tentang Kami</a></li>
        </ul>
      </nav>

      <a href="donasi.php" class="btn-donate">Donate</a>
    </div>
  </header>

  <main class="main-content">
    <section class="page-title">
      <div class="container">
        <p id="breadcrumb"></p>
        <h1>Ulurkan Tanganmu Hari Ini</h1>
        <p class="description">Satu donasi kecil dari Anda bisa menjadi harapan besar bagi mereka yang membutuhkan. Mari bergerak bersama untuk kebaikan.</p>
      </div>
    </section>

    <section class="causes-section">
      <div class="container">
        <div class="section-header">
          <h2>Latest Causes</h2>
          <button class="btn-outline">All Causes</button>
        </div>

        <div class="causes-grid">
          <?php
          // Mengambil data kampanye dari database
          $sql = "SELECT campaign_id, campaign_name, description, current_amount, target_amount, image_url FROM campaigns ORDER BY created_at DESC";
          $result = mysqli_query($conn, $sql);

          if (mysqli_num_rows($result) > 0) {
              while($row = mysqli_fetch_assoc($result)) {
                  $progress = ($row["current_amount"] / $row["target_amount"]) * 100;
                  if ($progress > 100) $progress = 100; // Batasi progress maksimal 100%
          ?>
          <div class="cause-card">
              <div class="img-placeholder" style="background-image: url('<?php echo htmlspecialchars($row["image_url"]); ?>');">
                  <!-- Gambar kampanye akan ditampilkan sebagai background-image -->
              </div>
              <div class="card-body">
                  <h3><?php echo htmlspecialchars($row["campaign_name"]); ?></h3>
                  <p><?php echo htmlspecialchars(substr($row["description"], 0, 100)) . '...'; // Batasi deskripsi ?></p>
                  <div class="progress-bar-container" style="margin-bottom: 10px;">
                      <div class="progress-bar" style="width: <?php echo $progress; ?>%; background-color: #ff6b35; height: 8px; border-radius: 4px;"></div>
                  </div>
                  <p style="font-size: 0.85rem; color: #777;">Terkumpul: Rp <?php echo number_format($row["current_amount"], 0, ',', '.'); ?> dari Rp <?php echo number_format($row["target_amount"], 0, ',', '.'); ?></p>
                  <a href="detail-donasi.php?id=<?php echo $row["id"]; ?>" class="btn-details">View Details</a>
              </div>
          </div>
          <?php
              }
          } else {
              echo "<p>Tidak ada kampanye donasi yang tersedia saat ini.</p>";
          }
          mysqli_close($conn); // Tutup koneksi setelah selesai
          ?>
        </div>
      </div>
    </section>
  </main>

  <footer class="get-in-touch-footer">
  <div class="footer-inner-container">
    <div class="footer-left-content">
      <h2>Yuk, Sapa GotongID!</h2>
      <p>Punya pertanyaan, ide kolaborasi, atau ingin tahu lebih lanjut tentang GotongID?
        Kami siap mendengarkan. Mari bersama membangun dampak positif!</p>
      <div class="footer-social-icons">
        <a href="https://www.instagram.com/gotongid?igsh=ODZxeWR4cmJjdmw=" target="_blank" aria-label="Instagram">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="https://www.tiktok.com/@gotongid?_t=ZS-8yFsumEsHm1&_r=1" target="_blank" aria-label="TikTok">
          <i class="fab fa-tiktok"></i>
        </a>
      </div>
    </div>

    <div class="footer-right-cards">
      <div class="contact-card">
        <i class="fas fa-globe"></i>
        <span>
          <a href="https://www.gotongid.com" target="_blank" class="footer-link">
            www.gotongid.com
          </a>
        </span>
      </div>
      <div class="contact-card">
        <i class="fas fa-envelope"></i>
        <span>
          <a href="mailto:hallogotongid@gmail.com" class="footer-link">
            hallogotongid@gmail.com
          </a>
        </span>
      </div>
    </div>
  </div>
</footer>

  <!-- Tombol floating donate untuk mobile -->
  <a href="donasi.php" class="floating-donate">Donate</a>

  <script>
    // Toggle responsive menu
    function toggleMenu() {
      const navMenu = document.querySelector('.nav-menu'); // Menggunakan .nav-menu
      navMenu.classList.toggle('show');
    }

    // Highlight active nav
    const currentFile = location.pathname.split("/").pop();
    document.querySelectorAll(".nav-menu a").forEach(link => { // Menggunakan .nav-menu
      const linkFile = link.getAttribute("href").split("/").pop();
      if (linkFile === currentFile) {
        link.classList.add("active");
      }
    });

    // Dynamic breadcrumb
    const bread = {
      "donasi.php": "Beranda > Donasi", // Ubah .html ke .php
      "index.php": "Beranda" // Tambahkan ini untuk index.php
    };
    const currentPath = location.pathname.split("/").pop();
    document.getElementById("breadcrumb").textContent = bread[currentPath] || "Home";
  </script>
</body>
</html>
