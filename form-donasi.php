<?php
include 'koneksi.php';


$campaign_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$campaign = null;

if ($campaign_id > 0) {
    $result = mysqli_query($conn, "SELECT * FROM campaigns WHERE campaign_id = $campaign_id");
    $campaign = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Donasi - GotongID</title>
    <link rel="stylesheet" href="style_fixed.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .donation-container {
            max-width: 600px;
            margin: 4rem auto;
            background: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }

        .donation-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #ff6b35;
        }

        .donation-container label {
            font-weight: 600;
            display: block;
            margin-top: 1rem;
        }

        .donation-container input[type="number"],
        .donation-container textarea {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            transition: border 0.2s;
        }

        .donation-container input[type="number"]:focus,
        .donation-container textarea:focus {
            border-color: #ff6b35;
            outline: none;
        }

        .donation-container button {
            margin-top: 1.8rem;
            background-color: #ff6b35;
            color: white;
            padding: 12px 20px;
            border: none;
            font-weight: bold;
            font-size: 1rem;
            border-radius: 6px;
            width: 100%;
            cursor: pointer;
            transition: background 0.2s;
        }

        .donation-container button:hover {
            background-color: #e55a24;
        }

        .donation-container .back-link {
            display: block;
            margin-top: 1rem;
            text-align: center;
            color: #555;
            text-decoration: none;
        }

        .donation-container .back-link:hover {
            text-decoration: underline;
        }

        .anonymous-check {
            margin-top: 1rem;
        }

        .campaign-info {
            font-size: 0.95rem;
            background: #fdf3f0;
            padding: 1rem;
            border-left: 5px solid #ff6b35;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 600px) {
            .donation-container {
                margin: 2rem 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body class="donasi-page">
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
    <div class="donation-container">
        <?php if ($campaign): ?>
            <h2>Donasi untuk "<?php echo htmlspecialchars($campaign['campaign_name']); ?>"</h2>

            <div class="campaign-info">
                <strong>Terkumpul:</strong> Rp <?php echo number_format($campaign['current_amount'], 0, ',', '.'); ?><br>
                <strong>Target:</strong> Rp <?php echo number_format($campaign['target_amount'], 0, ',', '.'); ?>
            </div>

            <hr style="margin: 3rem 0;" />

            <section class="alternatif-donasi">
            <h2>Alternatif Pembayaran Donasi</h2>
            <p>Jika Anda lebih nyaman melakukan donasi dengan cara lain, silakan pilih salah satu opsi di bawah ini:</p>

            <div class="alternatif-container">

                <!-- ✅ QRIS -->
                <div class="alternatif-card">
                <h3>QRIS</h3>
                <img src="gambar/qr.png" alt="QRIS GotongID" class="qris-image" />
                <p>Scan kode QR di atas menggunakan aplikasi pembayaran digital seperti GoPay, OVO, Dana, ShopeePay, dll.</p>
                </div>

                <!-- ✅ TRANSFER BANK -->
                <div class="alternatif-card">
                <h3>Transfer Bank</h3>
                <p><strong>Bank:</strong> BCA</p>
                <p><strong>No. Rekening:</strong> 1234567890</p>
                <p><strong>Atas Nama:</strong> Yayasan GotongID Indonesia</p>
                <p>Setelah melakukan transfer, mohon konfirmasi melalui WhatsApp: <strong>0812-3456-7890</strong> atau kirim bukti ke <a href="mailto:hallogotongid@gmail.com">hallogotongid@gmail.com</a>.</p>
                </div>

            </div>
            </section>

 
            <a class="back-link" href="donasi.php">← Kembali ke Daftar Kampanye</a>
        <?php else: ?>
            <p style="color:red;">Kampanye tidak ditemukan.</p>
            <a class="back-link" href="donasi.php">← Kembali ke Donasi</a>
        <?php endif; ?>
    </div>
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
</body>
</html>
