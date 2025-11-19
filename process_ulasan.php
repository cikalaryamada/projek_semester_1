<?php
// process_ulasan.php - VERSI LENGKAP DENGAN UPLOAD FOTO
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
        $foto_ulasan = '';
        
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
        
        // Handle file upload
        if (isset($_FILES['review_photo']) && $_FILES['review_photo']['error'] === 0) {
            $uploadDir = 'assets/images/reviews/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($_FILES['review_photo']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_review_' . time() . '.' . strtolower($fileExtension);
            $uploadFile = $uploadDir . $fileName;
            
            // Check file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($fileExtension), $allowedTypes)) {
                // Check file size (max 5MB)
                if ($_FILES['review_photo']['size'] > 5 * 1024 * 1024) {
                    throw new Exception('Ukuran file terlalu besar (maksimal 5MB)');
                }
                
                if (move_uploaded_file($_FILES['review_photo']['tmp_name'], $uploadFile)) {
                    // Pastikan permission agar dapat diakses oleh webserver/admin dashboard
                    @chmod($uploadFile, 0644);
                    $foto_ulasan = $fileName;
                } else {
                    throw new Exception('Gagal mengupload foto');
                }
            } else {
                throw new Exception('Format file tidak didukung. Gunakan JPG, PNG, atau GIF');
            }
        }
        
        // Cek dan buat tabel jika belum ada (dengan struktur baru)
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
                    Foto_Ulasan VARCHAR(255) NULL,
                    Tanggal_Ulasan DATETIME DEFAULT CURRENT_TIMESTAMP,
                    Status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        } else {
            // Cek jika kolom Foto_Ulasan belum ada
            $columnCheck = $pdo->query("SHOW COLUMNS FROM ulasan LIKE 'Foto_Ulasan'")->fetch();
            if (!$columnCheck) {
                $pdo->exec("ALTER TABLE ulasan ADD COLUMN Foto_Ulasan VARCHAR(255) NULL AFTER Rekomendasi");
            }
            
            // Hapus kolom ID_Pelanggan dan ID_Produk jika masih ada
            $columnCheckPelanggan = $pdo->query("SHOW COLUMNS FROM ulasan LIKE 'ID_Pelanggan'")->fetch();
            if ($columnCheckPelanggan) {
                // Hapus foreign key constraints terlebih dahulu
                try {
                    $pdo->exec("ALTER TABLE ulasan DROP FOREIGN KEY ulasan_ibfk_1");
                } catch (Exception $e) {
                    // Ignore error jika constraint tidak ada
                }
                $pdo->exec("ALTER TABLE ulasan DROP COLUMN ID_Pelanggan");
            }
            
            $columnCheckProduk = $pdo->query("SHOW COLUMNS FROM ulasan LIKE 'ID_Produk'")->fetch();
            if ($columnCheckProduk) {
                // Hapus foreign key constraints terlebih dahulu
                try {
                    $pdo->exec("ALTER TABLE ulasan DROP FOREIGN KEY ulasan_ibfk_2");
                } catch (Exception $e) {
                    // Ignore error jika constraint tidak ada
                }
                $pdo->exec("ALTER TABLE ulasan DROP COLUMN ID_Produk");
            }
        }
        
// Simpan ke database - set status default ke 'pending' untuk admin approval
if ($foto_ulasan) {
    $stmt = $pdo->prepare("
        INSERT INTO ulasan (Nama_Pelanggan, Rating, Judul_Ulasan, Isi_Ulasan, Rekomendasi, Foto_Ulasan, Status) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");
    $result = $stmt->execute([$nama, $rating, $judul, $ulasan, $rekomendasi, $foto_ulasan]);
} else {
    $stmt = $pdo->prepare("
        INSERT INTO ulasan (Nama_Pelanggan, Rating, Judul_Ulasan, Isi_Ulasan, Rekomendasi, Status) 
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");
    $result = $stmt->execute([$nama, $rating, $judul, $ulasan, $rekomendasi]);
}
        
        if ($result) {
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