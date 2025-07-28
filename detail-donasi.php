<?php


session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php'; // Memanggil file koneksi.php untuk membuat koneksi ke database

$campaign = null;
$campaign_id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Ambil ID kampanye dari URL

if ($campaign_id > 0) {
    // Menggunakan prepared statement untuk mencegah SQL Injection
    $stmt = mysqli_prepare($conn, "SELECT id, campaign_name, description, current_amount, target_amount, image_url FROM campaigns WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $campaign_id); // "i" untuk integer
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $campaign = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

// Proses donasi jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['donate_amount']) && $campaign_id > 0) {
    $donate_amount = (float)$_POST['donate_amount'];
    $donatur_name = htmlspecialchars($_POST['donatur_name']); // Ambil nama donatur
    $donatur_email = htmlspecialchars($_POST['donatur_email']); // Ambil email donatur

    if ($donate_amount > 0) {
        // Mulai transaksi untuk memastikan integritas data
        mysqli_begin_transaction($conn);

        try {
            // 1. Update current_amount di tabel campaigns
            $stmt_update = mysqli_prepare($conn, "UPDATE campaigns SET current_amount = current_amount + ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt_update, "di", $donate_amount, $campaign_id); // "d" untuk double/float
            mysqli_stmt_execute($stmt_update);

            // 2. Catat donasi ke tabel terpisah (misal: 'donations')
            $stmt_insert = mysqli_prepare($conn, "INSERT INTO donations (campaign_id, donatur_name, donatur_email, amount) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "issd", $campaign_id, $donatur_name, $donatur_email, $donate_amount);
            mysqli_stmt_execute($stmt_insert);

            mysqli_commit($conn); // Commit transaksi jika semua berhasil
            $success_message = "Terima kasih! Donasi Anda sebesar Rp " . number_format($donate_amount, 0, ',', '.') . " berhasil.";
            // Refresh data kampanye setelah donasi
            $stmt = mysqli_prepare($conn, "SELECT id, campaign_name, description, current_amount, target_amount, image_url FROM campaigns WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $campaign_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $campaign = mysqli_fetch_assoc($result); // Update $campaign data
            mysqli_stmt_close($stmt);

        } catch (mysqli_sql_exception $exception) {
            mysqli_rollback($conn); // Rollback jika ada error
            $error_message = "Terjadi kesalahan saat memproses donasi: " . $exception->getMessage();
        }
    } else {
        $error_message = "Jumlah donasi harus lebih dari Rp 0.";
    }
}

mysqli_close($conn); // Tutup koneksi setelah semua operasi selesai

// Initialize auth variables
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && $_SESSION['user']['role'] === 'admin';
$userData = [];

if ($isLoggedIn) {
    $userData = [
        'name' => $_SESSION['user']['username'] ?? 'Admin',
        'profile_pic' => 'gambar/default-profile.jpg'
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Donasi <?php echo $campaign ? '- ' . htmlspecialchars($campaign['campaign_name']) : ''; ?> - GotongID</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="gambar/Logo U.png" alt="GotongID Logo">
            </div>
            <!-- Tombol hamburger untuk mobile -->
            <div class="menu-toggle" onclick="toggleMenu()">☰</div>
            <ul class="nav-menu">
                <li><a href="./index.php">Beranda</a></li>
                <li><a href="./donasi.php">Donasi</a></li>
                <li><a href="./detail-donasi.php" class="active">Detail donasi</a></li>
                <li><a href="./tentang-kami.php">Tentang Kami</a></li>
            </ul>
            

            <div class="profile-btn" id="auth-buttons">
                <?php if($isLoggedIn) { ?>
                    <?php if($isAdmin) { ?>
                        <a href="admin/dashboard.php" class="btn-admin">ADMIN</a>
                    <?php } ?>
                    <a href="logout.php" class="btn-logout">LOGOUT</a>
                <?php } else { ?>
                    <a href="login.php" class="btn-login">LOGIN</a>
                <?php } ?>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">
        <section class="page-content">
            <div class="container">
                <?php if ($campaign): ?>
                    <h1 class="page-title"><?php echo htmlspecialchars($campaign['campaign_name']); ?></h1>
                    <div class="detail-campaign-content">
                        <div class="campaign-image">
                            <img src="<?php echo htmlspecialchars($campaign['image_url']); ?>" alt="<?php echo htmlspecialchars($campaign['campaign_name']); ?>">
                        </div>
                        <div class="campaign-info">
                            <p><?php echo nl2br(htmlspecialchars($campaign['description'])); ?></p>
                            <p><strong>Terkumpul:</strong> Rp <?php echo number_format($campaign['current_amount'], 0, ',', '.'); ?></p>
                            <p><strong>Target:</strong> Rp <?php echo number_format($campaign['target_amount'], 0, ',', '.'); ?></p>
                            <?php
                                $progress = ($campaign['current_amount'] / $campaign['target_amount']) * 100;
                                if ($progress > 100) $progress = 100;
                            ?>
                            <div class="progress-bar-container" style="width: 100%; background-color: #e0e0e0; border-radius: 5px; margin-top: 15px;">
                                <div class="progress-bar" style="width: <?php echo $progress; ?>%; background-color: #ff6b35; height: 20px; border-radius: 5px; text-align: center; color: white; line-height: 20px;">
                                    <?php echo round($progress); ?>%
                                </div>
                            </div>

                            <h2 style="margin-top: 30px;">Donasi Sekarang</h2>
                            <?php if (isset($success_message)): ?>
                                <p style="color: green; font-weight: bold;"><?php echo $success_message; ?></p>
                            <?php endif; ?>
                            <?php if (isset($error_message)): ?>
                                <p style="color: red; font-weight: bold;"><?php echo $error_message; ?></p>
                            <?php endif; ?>
                            <form action="detail-donasi.php?id=<?php echo $campaign_id; ?>" method="POST" class="donation-form">
                                <div class="form-group">
                                    <label for="donatur_name">Nama (Opsional):</label>
                                    <input type="text" id="donatur_name" name="donatur_name" placeholder="Nama Anda">
                                </div>
                                <div class="form-group">
                                    <label for="donatur_email">Email (Opsional):</label>
                                    <input type="email" id="donatur_email" name="donatur_email" placeholder="Email Anda">
                                </div>
                                <div class="form-group">
                                    <label for="donate_amount">Jumlah Donasi (Rp):</label>
                                    <input type="number" id="donate_amount" name="donate_amount" min="1000" required placeholder="Contoh: 50000">
                                </div>
                                <button type="submit" class="btn primary-btn">Donasi</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <h1 class="page-title">Kampanye Tidak Ditemukan</h1>
                    <p style="text-align: center;">Maaf, kampanye donasi yang Anda cari tidak ditemukan atau ID tidak valid.</p>
                    <p style="text-align: center;"><a href="donasi.php" class="btn primary-btn">Lihat Semua Kampanye</a></p>
                <?php endif; ?>
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
          navMenu.classList.toggle('show');
        }

        // Highlight active nav
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