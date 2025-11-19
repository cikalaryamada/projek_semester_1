<?php
session_start();

// CEK SEMUA KONFIGURASI YANG MUNGKIN
$configs = [
    ['localhost', 'umkmk16', 'root', ''],
    ['127.0.0.1', 'umkmk16', 'root', ''],
    ['localhost:3306', 'umkmk16', 'root', ''],
    ['127.0.0.1:3306', 'umkmk16', 'root', '']
];

$pdo = null;
$error = "";

foreach ($configs as $config) {
    list($host, $dbname, $username, $password) = $config;
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        break; // Berhasil connect, keluar loop
    } catch(PDOException $e) {
        $error = $e->getMessage();
        continue; // Coba config berikutnya
    }
}

if (!$pdo) {
    die("<div style='padding: 20px; background: #ff4757; color: white;'>
        <h3>Error Koneksi Database</h3>
        <p><strong>Pesan Error:</strong> $error</p>
        <p><strong>Solusi:</strong></p>
        <ol>
            <li>Pastikan MySQL berjalan di XAMPP/Laragon</li>
            <li>Pastikan database 'umkmk16' sudah diimport</li>
            <li>Cek username/password MySQL</li>
        </ol>
    </div>");
}

// Informasi login admin
$admin_username = 'admin';
$admin_password = 'admin123';

if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $admin_username;
    $_SESSION['login_time'] = time();
    header('Location: admin_dashboard.php');
    exit;
} else {
    header('Location: admin.php?error=1');
    exit;
}
?>