<?php
include 'koneksi.php';

// Ambil campaign berdasarkan ID dari URL
$campaign_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$campaign = null;

if ($campaign_id > 0) {
    $result = mysqli_query($conn, "SELECT * FROM campaign WHERE id = $campaign_id");
    $campaign = mysqli_fetch_assoc($result);
}

// Jika campaign tidak ditemukan
if (!$campaign) {
    echo "Kampanye tidak ditemukan.";
    exit;
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donor_name = trim($_POST['donor_name']);
    $amount = (int)$_POST['amount'];
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $payment_method = $_POST['payment_method'];

    if ($amount < 10000) {
        echo "<script>alert('Jumlah minimal donasi adalah Rp10.000');</script>";
    } else {
        // Simpan ke tabel donasi
        $stmt = $conn->prepare("INSERT INTO donasi (campaign_id, donor_name, amount, is_anonymous, payment_method) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdis", $campaign_id, $donor_name, $amount, $is_anonymous, $payment_method);
        $stmt->execute();

        // Redirect ke halaman terima kasih
        header("Location: terima-kasih.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simulasi Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        .qr-image {
            text-align: center;
            margin-top: 20px;
        }
        .qr-image img {
            max-width: 200px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .note {
            font-size: 0.9em;
            color: #777;
            text-align: center;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            color: #007BFF;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Donasi untuk: <?= htmlspecialchars($campaign['judul']) ?></h2>
    <p><?= nl2br(htmlspecialchars($campaign['deskripsi'])) ?></p>

    <form method="POST">
        <label>Nama Donatur</label>
        <input type="text" name="donor_name" placeholder="Nama Anda" required>

        <label>Jumlah Donasi (Rp)</label>
        <input type="number" name="amount" placeholder="Minimal 10000" required>

        <label><input type="checkbox" name="is_anonymous"> Donasi Anonim</label>

        <label>Metode Pembayaran</label>
        <select name="payment_method" required>
            <option value="">-- Pilih --</option>
            <option value="QRIS">QRIS </option>
            <option value="Transfer Bank">Transfer Bank </option>
        </select>

        <div class="qr-image">
            <img src="qr.png" alt="QRIS ">
        </div>

        <button type="submit">Bayar Sekarang </button>
    </form>

    <div class="back-link">
        <a href="index.php">← Kembali ke Beranda</a>
    </div>
</div>
</body>
</html>
