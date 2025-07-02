<?php
// Konfigurasi database
$host = 'localhost';
$dbname = 'villa_booking';
$user = 'root';
$pass = '';

// Konfigurasi situs
$site_name = 'Luxury Villas';
$site_email = 'info@luxuryvillas.com';
$site_phone = '+62 123 4567 890';
$site_address = 'Jl. Villa Indah No. 123, Bali';

// Inisialisasi koneksi database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Fungsi helper
function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function format_rupiah($amount) {
    return 'Rp' . number_format($amount, 0, ',', '.');
}

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>