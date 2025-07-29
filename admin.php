<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// --- Tambah Kampanye Donasi ---
$success_campaign = $error_campaign = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_campaign'])) {
    $campaign_name = trim($_POST['campaign_name']);
    $description = trim($_POST['description']);
    $target_amount = (float)$_POST['target_amount'];
    $image_url = '';

    // Proses upload gambar
    if (isset($_FILES['campaign_image']) && $_FILES['campaign_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "gambar/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $filename = basename($_FILES["campaign_image"]["name"]);
        $target_file = $target_dir . time() . "_" . preg_replace('/[^a-zA-Z0-9.\-_]/', '', $filename);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["campaign_image"]["tmp_name"], $target_file)) {
                $image_url = $target_file;
            } else {
                $error_campaign = "Gagal meng-upload gambar.";
            }
        } else {
            $error_campaign = "Format gambar harus JPG, JPEG, PNG, atau GIF.";
        }
    }

    if ($campaign_name && $description && $target_amount > 0 && !$error_campaign) {
        $stmt = mysqli_prepare($conn, "INSERT INTO campaigns (campaign_name, description, target_amount, current_amount, image_url) VALUES (?, ?, ?, 0, ?)");
        mysqli_stmt_bind_param($stmt, "ssds", $campaign_name, $description, $target_amount, $image_url);
        if (mysqli_stmt_execute($stmt)) {
            $success_campaign = "Kampanye berhasil ditambahkan!";
        } else {
            $error_campaign = "Gagal menambah kampanye: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } elseif (!$error_campaign) {
        $error_campaign = "Semua field wajib diisi dan target harus lebih dari 0.";
    }
}

// --- Tambah & Hapus Berita ---
$title = $content = '';
$success = $error = '';
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $hapus_id = (int)$_GET['hapus'];
    $q = mysqli_query($conn, "SELECT image_url FROM news WHERE news_id=$hapus_id");
    $img = mysqli_fetch_assoc($q);
    if ($img && !empty($img['image_url']) && file_exists($img['image_url'])) {
        unlink($img['image_url']);
    }
    mysqli_query($conn, "DELETE FROM news WHERE news_id=$hapus_id");
    $success = "Berita berhasil dihapus.";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['add_campaign'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
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
    if ($title && $content && !$error) {
        $stmt = mysqli_prepare($conn, "INSERT INTO news (title, content, image_url) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $title, $content, $image_url);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Berita berhasil ditambahkan!";
            $title = $content = '';
        } else {
            $error = "Gagal menambah berita: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } elseif (!$error) {
        $error = "Judul dan isi berita wajib diisi.";
    }
}

// --- Ambil data berita & kampanye ---
$newsList = [];
$result = mysqli_query($conn, "SELECT news_id, title, content, image_url, publish_date FROM news ORDER BY publish_date DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $newsList[] = $row;
}
$campaignList = [];
$result2 = mysqli_query($conn, "SELECT campaign_id, campaign_name, target_amount, current_amount, image_url FROM campaigns ORDER BY campaign_id DESC");
while ($row = mysqli_fetch_assoc($result2)) {
    $campaignList[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - Tambah & Kelola Berita/Kampanye</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-container { max-width: 700px; margin: 40px auto; background: #fff; padding: 32px; border-radius: 8px; box-shadow: 0 2px 8px #eee; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; }
        .form-group input, .form-group textarea { width: 100%; padding: 8px; }
        .btn-submit { padding: 10px 24px; background: #ff6b35; color: #fff; border: none; border-radius: 4px; font-weight: bold; }
        .success-msg { color: green; margin-bottom: 12px; }
        .error-msg { color: red; margin-bottom: 12px; }
        .news-table, .campaign-table { width: 100%; border-collapse: collapse; margin-top: 40px; }
        .news-table th, .news-table td, .campaign-table th, .campaign-table td { border: 1px solid #eee; padding: 8px; text-align: left; }
        .news-table th, .campaign-table th { background: #fafafa; }
        .news-thumb, .campaign-thumb { max-width: 80px; max-height: 60px; }
        .btn-hapus { background: #e74c3c; color: #fff; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Tambah Kampanye Donasi Baru</h2>
        <?php if ($success_campaign): ?><div class="success-msg"><?php echo $success_campaign; ?></div><?php endif; ?>
        <?php if ($error_campaign): ?><div class="error-msg"><?php echo $error_campaign; ?></div><?php endif; ?>
        <form method="POST" action="admin.php" enctype="multipart/form-data">
            <input type="hidden" name="add_campaign" value="1">
            <div class="form-group">
                <label for="campaign_name">Nama Kampanye</label>
                <input type="text" name="campaign_name" id="campaign_name" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="target_amount">Target Donasi (Rp)</label>
                <input type="number" name="target_amount" id="target_amount" min="1000" required>
            </div>
            <div class="form-group">
                <label for="campaign_image">Upload Gambar (JPG, PNG, GIF)</label>
                <input type="file" name="campaign_image" id="campaign_image" accept=".jpg,.jpeg,.png,.gif">
            </div>
            <button type="submit" class="btn-submit">Tambah Kampanye</button>
        </form>

        <h2 style="margin-top:40px;">Daftar Kampanye Donasi</h2>
        <table class="campaign-table">
            <tr>
                <th>No</th>
                <th>Nama Kampanye</th>
                <th>Target</th>
                <th>Terkumpul</th>
                <th>Gambar</th>
            </tr>
            <?php if (empty($campaignList)): ?>
                <tr><td colspan="5" style="text-align:center;">Belum ada kampanye.</td></tr>
            <?php else: ?>
                <?php $no=1; foreach ($campaignList as $c): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($c['campaign_name']); ?></td>
                    <td>Rp <?php echo number_format($c['target_amount'],0,',','.'); ?></td>
                    <td>Rp <?php echo number_format($c['current_amount'],0,',','.'); ?></td>
                    <td>
                        <?php if ($c['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($c['image_url']); ?>" class="campaign-thumb" alt="">
                        <?php else: ?>
                            <span style="color:#aaa;">(Tidak ada gambar)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>

        <hr style="margin:48px 0;">

        <h2>Tambah Berita Baru</h2>
        <?php if ($success): ?><div class="success-msg"><?php echo $success; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST" action="admin.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Judul Berita</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Isi Berita</label>
                <textarea name="content" id="content" rows="6" required><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            <div class="form-group">
                <label for="image_file">Upload Gambar (JPG, PNG, GIF)</label>
                <input type="file" name="image_file" id="image_file" accept=".jpg,.jpeg,.png,.gif">
            </div>
            <button type="submit" class="btn-submit">Tambah Berita</button>
        </form>
        <br>
        <a href="index.php">Kembali ke Beranda</a>

        <h2 style="margin-top:40px;">Daftar Berita Donasi</h2>
        <table class="news-table">
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Gambar</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
            <?php if (empty($newsList)): ?>
                <tr><td colspan="5" style="text-align:center;">Belum ada berita.</td></tr>
            <?php else: ?>
                <?php $no=1; foreach ($newsList as $news): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($news['title']); ?></td>
                    <td>
                        <?php if ($news['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($news['image_url']); ?>" class="news-thumb" alt="">
                        <?php else: ?>
                            <span style="color:#aaa;">(Tidak ada gambar)</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d M Y', strtotime($news['publish_date'])); ?></td>
                    <td>
                        <a href="admin.php?hapus=<?php echo $news['news_id']; ?>" class="btn-hapus" onclick="return confirm('Yakin hapus berita ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>