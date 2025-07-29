<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campaign_id = (int)$_POST['campaign_id'];
    $amount = (float)$_POST['amount'];
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $message = trim($_POST['message']);

    // Cek apakah user login (jika ada sistem login)
    session_start();
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    if ($amount > 0 && $campaign_id > 0) {
        $stmt = mysqli_prepare($conn, "INSERT INTO donations (campaign_id, amount, message, is_anonymous, donation_date, user_id) VALUES (?, ?, ?, ?, NOW(), ?)");
        mysqli_stmt_bind_param($stmt, "idsii", $campaign_id, $amount, $message, $is_anonymous, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Update current_amount di tabel campaigns
        mysqli_query($conn, "UPDATE campaigns SET current_amount = current_amount + $amount WHERE campaign_id = $campaign_id");

        header("Location: detail-donasi.php?id=$campaign_id&donasi=success");
        exit;
    }
}

header("Location: donasi.php?error=1");
exit;
