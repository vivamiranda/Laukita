<?php
include 'koneksi.php';

$campaigns = [];
$result = mysqli_query($conn, "SELECT campaign_id, campaign_name, description, image_url, current_amount, target_amount FROM campaigns ORDER BY start_date DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $campaigns[] = $row;
}

// Tambahkan logika ambil detail kampanye jika ada parameter id
$campaign = null;
$donasiList = [];
$total_donatur = 0;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $campaign_result = mysqli_query($conn, "SELECT * FROM campaigns WHERE campaign_id = $id");
    if ($campaign_result && mysqli_num_rows($campaign_result) > 0) {
        $campaign = mysqli_fetch_assoc($campaign_result);

        // Ambil riwayat donasi
        $donasi_query = "SELECT d.amount, d.donation_date, d.is_anonymous, d.message, u.full_name 
                         FROM donations d
                         LEFT JOIN users u ON d.user_id = u.user_id
                         WHERE d.campaign_id = $id
                         ORDER BY d.donation_date DESC";
        $donasi_result = mysqli_query($conn, $donasi_query);
        if ($donasi_result) {
            while ($row = mysqli_fetch_assoc($donasi_result)) {
                $donasiList[] = $row;
            }
            $total_donatur = count($donasiList);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title align="center">Daftar Kampanye Donasi - GotongID</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_fixed.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fefefe;
            margin: 0;
            padding: 0;
        }
        .campaigns-container {
            max-width: 1000px;
            margin: 100px auto;
            padding: 0 20px;
        }
        .campaign-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .campaign-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s;
        }
        .campaign-card:hover {
            transform: translateY(-5px);
        }
        .campaign-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .campaign-card-content {
            padding: 16px;
        }
        .campaign-card-content h3 {
            margin: 0 0 10px;
            font-size: 1.2rem;
            color: #d94e1f;
        }
        .campaign-card-content p {
            font-size: 0.9rem;
            color: #444;
        }
        .progress-bar-container {
            background: #ddd;
            height: 8px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .progress-bar {
            height: 8px;
            background: #ff6b35;
            border-radius: 4px;
        }
        .campaign-card a {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 14px;
            background: #ff6b35;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .campaign-card a:hover {
            background: #e3571f;
        }
        .campaign-container h2 {
            color: #ff6b35;
        }
        .campaign-container img.campaign-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin: 15px 0;
        }
        .info-box {
            background: #fffaf5;
            border-left: 4px solid #ff6b35;
            padding: 12px 16px;
            margin: 16px 0;
            font-size: 0.95rem;
            border-radius: 4px;
        }
        .donation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }
        .donation-table th, .donation-table td {
            border: 1px solid #ccc;
            padding: 10px;
            font-size: 0.95rem;
            text-align: left;
        }
        .donation-table th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
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

<div class="campaigns-container">
    <?php if ($campaign): ?>
        <div class="campaign-container">
            <h2><?php echo htmlspecialchars($campaign['campaign_name']); ?></h2>
            <?php if ($campaign['image_url']): ?>
                <img src="<?php echo htmlspecialchars($campaign['image_url']); ?>" class="campaign-image" alt="Gambar Kampanye">
            <?php endif; ?>

            <div class="info-box">
                <p><strong>Tanggal Mulai:</strong> <?php echo !empty($campaign['start_date']) ? date('d M Y', strtotime($campaign['start_date'])) : '-'; ?></p>
                <p><strong>Tanggal Berakhir:</strong> <?php echo !empty($campaign['end_date']) ? date('d M Y', strtotime($campaign['end_date'])) : '-'; ?></p>
                <p><strong>Total Donatur:</strong> <?php echo $total_donatur; ?> orang</p>
            </div>

            <p style="margin-top: 1.2rem; white-space: pre-line;">
                <?php echo htmlspecialchars($campaign['description']); ?>
            </p>

            <?php
            $progress = ($campaign['current_amount'] / $campaign['target_amount']) * 100;
            if ($progress > 100) $progress = 100;
            ?>

            <div class="progress-bar-container">
                <div class="progress-bar" style="width: <?php echo $progress; ?>%;"></div>
            </div>
            <p style="font-size: 0.95rem; color: #777; margin-bottom: 8px;">
                Terkumpul: <b>Rp <?php echo number_format($campaign['current_amount'], 0, ',', '.'); ?></b> dari <b>Rp <?php echo number_format($campaign['target_amount'], 0, ',', '.'); ?></b>
            </p>

            <h3 style="margin-top:3rem; color:#d94e1f">Riwayat Donasi</h3>
            <table class="donation-table">
                <thead>
                    <tr>
                        <th>Nama Donatur</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Pesan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($donasiList)): ?>
                        <?php foreach ($donasiList as $d): ?>
                            <tr>
                                <td><?php echo $d['is_anonymous'] ? 'Anonim' : htmlspecialchars($d['full_name'] ?: 'Anonim'); ?></td>
                                <td>Rp <?php echo number_format($d['amount'], 0, ',', '.'); ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($d['donation_date'])); ?></td>
                                <td><?php echo htmlspecialchars($d['message']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Belum ada donasi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <h2 style="color: #ff6b35;">Daftar Kampanye Donasi</h2>
        <div class="campaign-grid">
            <?php foreach ($campaigns as $c): 
                $progress = ($c['target_amount'] > 0) ? ($c['current_amount'] / $c['target_amount']) * 100 : 0;
                if ($progress > 100) $progress = 100;
            ?>
            <div class="campaign-card">
                <?php if ($c['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($c['image_url']); ?>" alt="Gambar Kampanye">
                <?php endif; ?>
                <div class="campaign-card-content">
                    <h3><?php echo htmlspecialchars($c['campaign_name']); ?></h3>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?php echo $progress; ?>%;"></div>
                    </div>
                    <p><strong>Terkumpul:</strong> Rp <?php echo number_format($c['current_amount'], 0, ',', '.'); ?> / Rp <?php echo number_format($c['target_amount'], 0, ',', '.'); ?></p>
                    <p><?php echo htmlspecialchars(mb_strimwidth($c['description'], 0, 80, '...')); ?></p>
                    <a href="?id=<?php echo $c['campaign_id']; ?>">Lihat Donasi</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
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
