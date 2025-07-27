<?php

// --- Parameter Koneksi Database ---
$servername = "localhost"; // Alamat server database (biasanya localhost jika XAMPP)
$username = "root";        // Username database (default XAMPP)
$password = "";            // Password database (default XAMPP, kosong)
$dbname = "charity";       // Nama database yang sudah Anda buat
$port = 3307;              // Port MySQL yang Anda gunakan di XAMPP (umumnya 3306, bukan 3307)

// --- Membuat Koneksi ---
// mysqli_connect() adalah fungsi untuk membuat koneksi ke MySQL
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// --- Mengecek Koneksi ---
// Jika koneksi gagal, hentikan script dan tampilkan pesan error
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mengatur charset koneksi untuk mendukung karakter UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Anda bisa menambahkan ini untuk debugging, tapi sebaiknya dihapus di produksi
// echo "Koneksi database berhasil!";

?>
