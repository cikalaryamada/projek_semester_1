<?php
// process_review.php - VERSI LENGKAP FIXED
session_start();

// Koneksi database
$configs = [
    ['localhost', 'umkmk16', 'root', '']
];

$pdo = null;
foreach ($configs as $config) {
    list($host, $dbname, $username, $password) = $config;
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        break;
    } catch(PDOException $e) {
        continue;
    }
}

$success = false;
$message = '';

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nama = trim($_POST['reviewer_name'] ?? '');
        $rating = intval($_POST['rating'] ?? 0);
        $judul = trim($_POST['review_title'] ?? '');
        $ulasan = trim($_POST['review_text'] ?? '');
        $rekomendasi = $_POST['recommend'] ?? 'yes';
        
        // Validasi data
        if (empty($nama) || empty($rating) || empty($judul) || empty($ulasan)) {
            throw new Exception('Semua field harus diisi!');
        }
        
        if ($rating < 1 || $rating > 5) {
            throw new Exception('Rating harus antara 1-5!');
        }
        
        if (strlen($judul) > 200) {
            throw new Exception('Judul ulasan terlalu panjang (maksimal 200 karakter)');
        }
        
        if (strlen($ulasan) < 10) {
            throw new Exception('Ulasan terlalu pendek (minimal 10 karakter)');
        }
        
        // Cek dan buat tabel jika belum ada
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'ulasan'")->fetch();
        if (!$tableCheck) {
            $pdo->exec("
                CREATE TABLE ulasan (
                    ID_Ulasan INT AUTO_INCREMENT PRIMARY KEY,
                    Nama_Pelanggan VARCHAR(100) NOT NULL DEFAULT 'Pelanggan',
                    Rating INT NOT NULL,
                    Judul_Ulasan VARCHAR(200) NOT NULL,
                    Isi_Ulasan TEXT NOT NULL,
                    Rekomendasi ENUM('yes', 'no') NOT NULL DEFAULT 'yes',
                    Tanggal_Ulasan DATETIME DEFAULT CURRENT_TIMESTAMP,
                    Status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }
        
        // Simpan ke database
        $stmt = $pdo->prepare("
            INSERT INTO ulasan (Nama_Pelanggan, Rating, Judul_Ulasan, Isi_Ulasan, Rekomendasi) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$nama, $rating, $judul, $ulasan, $rekomendasi])) {
            $success = true;
            $message = 'Ulasan berhasil dikirim! Terima kasih atas feedback Anda.';
        } else {
            throw new Exception('Gagal menyimpan ulasan ke database.');
        }
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        
        // Simpan data form untuk prefill
        $_SESSION['form_data'] = [
            'reviewer_name' => $_POST['reviewer_name'] ?? '',
            'rating' => $_POST['rating'] ?? '',
            'review_title' => $_POST['review_title'] ?? '',
            'review_text' => $_POST['review_text'] ?? '',
            'recommend' => $_POST['recommend'] ?? 'yes'
        ];
    }
} else {
    $message = 'Koneksi database gagal atau method tidak valid.';
}

// Simpan status ke session
$_SESSION['review_message'] = $message;
$_SESSION['review_success'] = $success;

// Redirect kembali ke halaman ulasan
header('Location: Ulasan.php');
exit;
?>