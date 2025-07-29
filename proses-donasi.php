<?php
include 'koneksi.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    $campaign_id = (int)$_POST['campaign_id'];
    $amount = (float)$_POST['amount'];
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $message = trim($_POST['message']);
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    if ($amount > 0 && $campaign_id > 0) {
        // 1. Insert ke tabel donations
        $stmt = mysqli_prepare($conn, "INSERT INTO donations (campaign_id, amount, message, is_anonymous, donation_date, user_id) VALUES (?, ?, ?, ?, NOW(), ?)");
        if (!$stmt) {
            die("Gagal prepare insert donation: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "idsii", $campaign_id, $amount, $message, $is_anonymous, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // 2. Update total donasi terkumpul di tabel campaigns
        $stmt2 = mysqli_prepare($conn, "UPDATE campaigns SET current_amount = current_amount + ? WHERE campaign_id = ?");
        if (!$stmt2) {
            die("Gagal prepare update campaign: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt2, "di", $amount, $campaign_id);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        // 3. Redirect ke halaman terima kasih
        header("Location: terimakasih.php?campaign_id=$campaign_id");
        exit;
    }
}

// Jika validasi gagal
header("Location: donasi.php?error=1");
exit;
