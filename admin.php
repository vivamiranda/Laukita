<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$title = $content = '';
$success = $error = '';

// Hapus berita jika ada request GET hapus
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $hapus_id = (int)$_GET['hapus'];
    // Ambil nama file gambar untuk dihapus dari folder
    $q = mysqli_query($conn, "SELECT image_url FROM news WHERE news_id=$hapus_id");
    $img = mysqli_fetch_assoc($q);
    if ($img && !empty($img['image_url']) && file_exists($img['image_url'])) {
        unlink($img['image_url']);
    }
    mysqli_query($conn, "DELETE FROM news WHERE news_id=$hapus_id");
    $success = "Berita berhasil dihapus.";
}

// Proses tambah berita
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

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

// Ambil semua berita untuk ditampilkan
$newsList = [];
$result = mysqli_query($conn, "SELECT news_id, title, content, image_url, publish_date FROM news ORDER BY publish_date DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $newsList[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah & Kelola Berita - Admin</title>
    <link rel="stylesheet" href="admin.css">
    
</head>
<body>
    <div class="form-container">
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