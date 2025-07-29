<?php

session_start();
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
?>
<?php
include 'koneksi.php'; // Pastikan sudah ada

$newsList = [];
$result = mysqli_query($conn, "SELECT news_id, title, content, image_url, publish_date FROM news ORDER BY publish_date DESC LIMIT 3");
while ($row = mysqli_fetch_assoc($result)) {
    $newsList[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setitik Asa Sejuta Makna - GotongID</title>
    <link rel="stylesheet" href="/Laukita/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="page-wrapper">
        <nav class="navbar">
            <div class="nav-container">
                <div class="logo">
                    <img src="gambar/Logo U.png" alt="GotongID Logo">
                </div>
                <!-- Tombol hamburger untuk mobile -->
                <div class="menu-toggle" onclick="toggleMenu()">☰</div>
                    <ul class="nav-menu">
                        <li><a href="./index.php" class="active">Beranda</a></li>
                        <li><a href="./donasi.php">Donasi</a></li>
                        <?php if($isAdmin): ?>
                            <li><a href="./detail-donasi.php">Detail donasi</a></li>
                        <?php endif; ?>
                        <li><a href="./tentang-kami.php">Tentang Kami</a></li>
                    </ul>
                <div class="profile-btn" id="auth-buttons">
                    <?php if($isLoggedIn): ?>
                        <?php if($isAdmin): ?>
                            <a href="admin.php" class="btn-admin">Tambah Berita Donasi</a>
                        <?php endif; ?>
                        <a href="logout.php" class="btn-logout">LOGOUT</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-login">LOGIN</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <section class="hero">
            <div class="hero-wrapper">
            <div class="hero-container">
                <h1>Setitik Asa<br>Sejuta Makna</h1>
                <p>Mari bersama membangun kebaikan dan membantu sesama. Website ini adalah platform
                    penggalangan dana yang didedikasikan untuk mengumpulkan donasi bagi mereka yang
                    membutuhkan bantuan, mewujudkan harapan, dan meringankan beban melalui aksi nyata kepedulian.</p>

            </div>
            </div>
        </section>

        <section class="main-content">
            <div class="content-container">
                <div class="image-placeholder">
                    <div><img src="gambar/image1.png" alt="Mengubah niat baik menjadi tindakan baik"></div>
                </div>

                <div class="content-text">
                    <h2>Mengubah niat baik, Menjadi tindakan baik</h2>
                    <p>"Setiap niat baik memiliki kekuatan untuk membawa perubahan. Melalui platform ini, kami mengajukan untuk menjadikan niat itu sebagai aksi nyata yang berdampak. Mulai dari memilih tujuan, mendaftar, hingga berdonasi — setiap langkahmu berarti bagi mereka yang membutuhkan. Bersama, kita bisa menciptakan dunia yang lebih peduli dan penuh harapan."</p>

                    <div class="features-grid">
                        <div class="feature-item">
                            <div class="feature-icon">1</div>
                            <div class="feature-text">Pengabdian masyarakat</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">2</div>
                            <div class="feature-text">Platform Kolaborasi Mahasiswa</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">3</div>
                            <div class="feature-text">Donasi Fleksibel</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">4</div>
                            <div class="feature-text">Transparansi & Laporan</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
        $campaigns = [];
        $result = mysqli_query($conn, "SELECT * FROM campaigns ORDER BY campaign_id DESC LIMIT 3");
        while ($row = mysqli_fetch_assoc($result)) {
            $campaigns[] = $row;
        }
        ?>


        <div class="nav-bar">
            <!-- Periksa apakah ini benar-benar navigasi atau hanya logo mitra.
                 Jika navigasi, perlu link yang jelas. Jika logo, mungkin pindahkan ke footer atau bagian lain. -->
            <div class="nav-icon"><img src="gambar/Logo1.png" alt="Logo 1" /></div>
            <div class="nav-icon"><img src="gambar/Logo2.png" alt="Logo 2" /></div>
            <div class="nav-icon"><img src="gambar/Logo3.png" alt="Logo 3" /></div>
            <div class="nav-icon"><img src="gambar/Logo4.png" alt="Logo 4" /></div>
            <div class="nav-icon"><img src="gambar/Logo5.png" alt="Logo 5" /></div>
        </div>

        <section class="news-section">
            <div class="section-header">
                <h2>Latest News and Blog</h2>
                <button class="more-news">MORE NEWS</button>
            </div>
            <div class="news-cards">
                <?php foreach ($newsList as $news): ?>
                <div class="card">
                    <div class="image-placeholder">
                        <img src="<?php echo htmlspecialchars($news['image_url']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" class="news-image" />
                    </div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                        <p><?php echo htmlspecialchars(mb_strimwidth($news['content'], 0, 100, '...')); ?></p>
                        <small style="color:#888;"><?php echo date('d M Y', strtotime($news['publish_date'])); ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($newsList)): ?>
                    <p>Tidak ada berita terbaru.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="projects-section">
            <h2>Jangkauan Kebaikan Kita</h2>
            <p>Jangkauan kepedulian Anda kini mendunia.
                GotongID menjadi jembatan bagi Anda
                untuk membantu sesama di berbagai wilayah,
                membawa harapan ke setiap titik di peta ini.</p>
            <div class="map-placeholder">
                <img src="gambar/Peta.png" alt="Projects Map">
            </div>
        </section>

<section class="dampak-kebaikan-section">
    <h2>Dampak Nyata</h2>
    <div class="dampak-kebaikan-container">

        <div class="dampak-kebaikan-card">
            <div class="dampak-kebaikan-image-wrapper">
                <img src="gambar/Donasi3.png" alt="Dampak untuk Sesama" />
            </div>
            <div class="dampak-kebaikan-text-content">
                <h3>Dampak untuk Sesama</h3>
                <p>Setiap donasimu adalah secercah harapan. Kamu membantu menyediakan pendidikan, pangan, dan tempat tinggal layak bagi mereka yang membutuhkan, membuka jalan bagi masa depan yang lebih cerah dan penuh senyum.</p>
            </div>
        </div>

        <div class="dampak-kebaikan-card">
            <div class="dampak-kebaikan-image-wrapper">
                <img src="gambar/Donasi1.png" alt="Dampak untuk Diri Sendiri" />
            </div>
            <div class="dampak-kebaikan-text-content">
                <h3>Dampak untuk Diri Sendiri</h3>
                <p>Rasakan kebahagiaan sejati yang tak ternilai. Berbagi menumbuhkan rasa syukur, empati mendalam, dan kepuasan batin yang tak bisa dibeli, menjadikanmu pribadi yang lebih berarti.</p>
            </div>
        </div>

        <div class="dampak-kebaikan-card">
            <div class="dampak-kebaikan-image-wrapper">
                <img src="gambar/Donasi2.png" alt="Dampak untuk Lingkungan & Komunitas" />
            </div>
            <div class="dampak-kebaikan-text-content">
                <h3>Dampak untuk Lingkungan</h3>
                <p>Kontribusimu membangun fondasi masyarakat yang lebih kuat, harmonis, dan berkelanjutan. Bersama, kita menciptakan lingkungan yang peduli dan saling mendukung untuk semua.</p>
            </div>
        </div>

    </div>
</section>
        </div>
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

<script>
    // Toggle responsive menu
    function toggleMenu() {
      const navMenu = document.querySelector('.nav-menu');
      navMenu.classList.toggle('show'); // Tambahkan/hapus class 'show'
    }

    // Highlight active nav
    // Perlu sedikit penyesuaian karena ini PHP, bukan HTML statis
    const currentFile = location.pathname.split("/").pop();
    document.querySelectorAll(".nav-menu a").forEach(link => {
      const linkFile = link.getAttribute("href").split("/").pop();
      if (linkFile === currentFile) {
        link.classList.add("active");
      }
    });
</script>
</body>
</html>
