<?php
session_start();

// Koneksi database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=umkmk16", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $_SESSION['review_message'] = "❌ Gagal koneksi database!";
    $_SESSION['review_success'] = false;
    header("Location: Ulasan.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil data form
    $reviewer_name = trim($_POST['reviewer_name']);
    $rating        = intval($_POST['rating']);
    $review_title  = trim($_POST['review_title']);
    $review_text   = trim($_POST['review_text']);
    $recommend     = $_POST['recommend'] ?? 'yes';

    // Validasi
    if (
        empty($reviewer_name) ||
        empty($review_title) ||
        empty($review_text) ||
        $rating < 1 || $rating > 5
    ) {
        $_SESSION['review_message'] = "❌ Semua field wajib diisi!";
        $_SESSION['review_success'] = false;
        header("Location: Ulasan.php");
        exit;
    }

    // ================
    // UPLOAD FOTO
    // ================
    $foto_ulasan = NULL;

    // Folder ABSOLUTE path (pasti benar)
    $upload_dir = __DIR__ . "/assets/images/reviews/";

    // Pastikan folder ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Jika ada foto diupload
    if (isset($_FILES['review_photo']) && $_FILES['review_photo']['error'] === UPLOAD_ERR_OK) {

        // Cek ekstensi file
        $ext_allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['review_photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $ext_allowed)) {
            $_SESSION['review_message'] = "❌ Format foto harus JPG/PNG/GIF!";
            $_SESSION['review_success'] = false;
            header("Location: Ulasan.php");
            exit;
        }

        // Cek ukuran (maks 5MB)
        if ($_FILES['review_photo']['size'] > 5 * 1024 * 1024) {
            $_SESSION['review_message'] = "❌ Ukuran foto maximal 5MB!";
            $_SESSION['review_success'] = false;
            header("Location: Ulasan.php");
            exit;
        }

        // Nama file baru
        $new_filename = "review_" . time() . "_" . uniqid() . "." . $ext;
        $upload_path = $upload_dir . $new_filename;

        // Upload file
        if (move_uploaded_file($_FILES['review_photo']['tmp_name'], $upload_path)) {
            // Simpan path RELATIF ke database
            $foto_ulasan = "assets/images/reviews/" . $new_filename;
        } else {
            $_SESSION['review_message'] = "❌ Foto gagal diupload!";
            $_SESSION['review_success'] = false;
            header("Location: Ulasan.php");
            exit;
        }
    }

    // ================
    // SIMPAN DATABASE
    // ================
    try {
        $stmt = $pdo->prepare("
            INSERT INTO ulasan 
            (Nama_Pelanggan, Rating, Judul_Ulasan, Isi_Ulasan, Rekomendasi, Foto_Ulasan, Tanggal_Ulasan, Status)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 'approved')
        ");

        $stmt->execute([
            $reviewer_name,
            $rating,
            $review_title,
            $review_text,
            $recommend,
            $foto_ulasan
        ]);

        $_SESSION['review_message'] = "✅ Ulasan berhasil dikirim!";
        $_SESSION['review_success'] = true;

    } catch (PDOException $e) {

        // Hapus foto jika query gagal
        if ($foto_ulasan && file_exists($upload_dir . basename($foto_ulasan))) {
            unlink($upload_dir . basename($foto_ulasan));
        }

        $_SESSION['review_message'] = "❌ Gagal menyimpan ulasan!";
        $_SESSION['review_success'] = false;
    }
}

header("Location: Ulasan.php");
exit;

?>