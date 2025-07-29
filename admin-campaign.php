<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$success = $error = '';
$campaign_name = $description = $target_amount = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campaign_name = trim($_POST['campaign_name']);
    $description = trim($_POST['description']);
    $target_amount = (float)$_POST['target_amount'];

    // Proses upload gambar
    $image_url = '';
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "gambar/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $filename = basename($_FILES["image_file"]["name"]);
        $target_file = $target_dir . time() . "_" . preg_replace('/[^a-zA-Z0-9.\-_]/', '', $filename);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
                $image_url = $target_file;
            } else {
                $error = "Gagal meng-upload gambar.";
            }
        } else {
            $error = "Format gambar harus JPG, JPEG, PNG, atau GIF.";
        }
    }

    if ($campaign_name && $description && $target_amount > 0 && !$error) {
        $stmt = mysqli_prepare($conn, "INSERT INTO campaigns (campaign_name, description, target_amount, current_amount, image_url) VALUES (?, ?, ?, 0, ?)");
        mysqli_stmt_bind_param($stmt, "ssds", $campaign_name, $description, $target_amount, $image_url);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Kampanye berhasil ditambahkan!";
            $campaign_name = $description = $target_amount = '';
        } else {
            $error = "Gagal menambah kampanye: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } elseif (!$error) {
        $error = "Semua field wajib diisi dan target harus lebih dari 0.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kampanye Donasi - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container { max-width: 600px; margin: 40px auto; background: #fff; padding: 32px; border-radius: 8px; box-shadow: 0 2px 8px #eee; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; }
        .form-group input, .form-group textarea { width: 100%; padding: 8px; }
        .btn-submit { padding: 10px 24px; background: #ff6b35; color: #fff; border: none; border-radius: 4px; font-weight: bold; }
        .success-msg { color: green; margin-bottom: 12px; }
        .error-msg { color: red; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Tambah Kampanye Donasi Baru</h2>
        <?php if ($success): ?><div class="success-msg"><?php echo $success; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST" action="admin-campaign.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="campaign_name">Nama Kampanye</label>
                <input type="text" name="campaign_name" id="campaign_name" value="<?php echo htmlspecialchars($campaign_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" rows="6" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="form-group">
                <label for="target_amount">Target Donasi (Rp)</label>
                <input type="number" name="target_amount" id="target_amount" min="1000" value="<?php echo htmlspecialchars($target_amount); ?>" required>
            </div>
            <div class="form-group">
                <label for="image_file">Upload Gambar (JPG, PNG, GIF)</label>
                <input type="file" name="image_file" id="image_file" accept=".jpg,.jpeg,.png,.gif">
            </div>
            <button type="submit" class="btn-submit">Tambah Kampanye</button>
        </form>
        <br>
        <a href="donasi.php">Kembali ke Daftar Donasi</a>
    </div>
</body>
</html>