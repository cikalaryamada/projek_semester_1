<?php
// Ulasan.php - VERSI LENGKAP SIAP PAKAI
session_start();

// Koneksi database
$configs = [
    ['localhost', 'umkmk16', 'root', '']
];

$pdo = null;
$stats = [
    'total_reviews' => 0,
    'average_rating' => 0,
    'rating_5_percent' => 0,
    'rating_4_percent' => 0,
    'rating_3_percent' => 0,
    'rating_2_percent' => 0,
    'rating_1_percent' => 0
];

$reviews_from_db = [];

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

// Jika koneksi berhasil, ambil data dari database
if ($pdo) {
    try {
        // Cek apakah tabel ulasan exists, jika tidak buat
        $tableExists = $pdo->query("SHOW TABLES LIKE 'ulasan'")->rowCount() > 0;
        
        if (!$tableExists) {
            // Buat tabel ulasan jika belum ada
            $pdo->exec("
                CREATE TABLE ulasan (
                    ID_Ulasan INT AUTO_INCREMENT PRIMARY KEY,
                    Nama_Pelanggan VARCHAR(100) NOT NULL,
                    Rating INT NOT NULL,
                    Judul_Ulasan VARCHAR(200) NOT NULL,
                    Isi_Ulasan TEXT NOT NULL,
                    Rekomendasi ENUM('yes', 'no') NOT NULL DEFAULT 'yes',
                    Tanggal_Ulasan DATETIME DEFAULT CURRENT_TIMESTAMP,
                    Status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            // Insert sample data
            $sampleReviews = [
                [5, 'Budi Santoso', 'Tempat nongkrong favorit!', 'K SIXTEEN CAFE benar-benar menjadi tempat favorit saya untuk nongkrong. Kopinya enak, makanannya lezat, dan suasana nyaman. Staffnya juga ramah-ramah. Sangat recommended!', 'yes'],
                [5, 'Sari Dewi', 'Pelayanan luar biasa', 'Saya sering datang ke sini untuk meeting atau sekedar mengerjakan tugas. WiFi-nya cepat, tempatnya nyaman, dan yang paling penting kopinya selalu konsisten enaknya.', 'yes'],
                [4, 'Ahmad Rizki', 'Kopi dan makanan enak', 'Kopi susu di sini memang juara. Rasa kopinya kuat tapi tidak pahit. Makanannya juga enak, terutama ayam penyetnya.', 'yes'],
                [5, 'Diana Putri', 'Buka 24 jam, sangat membantu', 'Sebagai mahasiswa yang sering begadang, keberadaan K SIXTEEN yang buka 24 jam sangat membantu. Bisa nugas sampai pagi dengan ditemani kopi dan snack yang enak.', 'yes']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO ulasan (Rating, Nama_Pelanggan, Judul_Ulasan, Isi_Ulasan, Rekomendasi) VALUES (?, ?, ?, ?, ?)");
            foreach ($sampleReviews as $review) {
                $stmt->execute($review);
            }
        }
        
        // Ambil statistik ulasan
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total_reviews,
                AVG(Rating) as average_rating,
                COUNT(CASE WHEN Rating = 5 THEN 1 END) as rating_5,
                COUNT(CASE WHEN Rating = 4 THEN 1 END) as rating_4,
                COUNT(CASE WHEN Rating = 3 THEN 1 END) as rating_3,
                COUNT(CASE WHEN Rating = 2 THEN 1 END) as rating_2,
                COUNT(CASE WHEN Rating = 1 THEN 1 END) as rating_1
            FROM ulasan 
            WHERE Status = 'approved'
        ");
        
        $dbStats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($dbStats && $dbStats['total_reviews'] > 0) {
            $stats = [
                'total_reviews' => $dbStats['total_reviews'],
                'average_rating' => round($dbStats['average_rating'], 1),
                'rating_5_percent' => round(($dbStats['rating_5'] / $dbStats['total_reviews']) * 100),
                'rating_4_percent' => round(($dbStats['rating_4'] / $dbStats['total_reviews']) * 100),
                'rating_3_percent' => round(($dbStats['rating_3'] / $dbStats['total_reviews']) * 100),
                'rating_2_percent' => round(($dbStats['rating_2'] / $dbStats['total_reviews']) * 100),
                'rating_1_percent' => round(($dbStats['rating_1'] / $dbStats['total_reviews']) * 100)
            ];
        }
        
        // Ambil ulasan untuk ditampilkan di PHP (fallback)
        $stmt = $pdo->query("
            SELECT * FROM ulasan 
            WHERE Status = 'approved' 
            ORDER BY Tanggal_Ulasan DESC 
            LIMIT 12
        ");
        $reviews_from_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        // Tetap lanjut dengan default values
        error_log("Error loading review data: " . $e->getMessage());
    }
}

// Fungsi untuk format tanggal
function time_elapsed_string($datetime, $full = false) {
    if (empty($datetime)) return 'baru saja';
    
    try {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        // Hitung minggu dan sisa hari tanpa menulis properti dinamis pada DateInterval
        $weeks = floor($diff->d / 7);
        $days = $diff->d - ($weeks * 7);

        $units = array(
            'y' => 'tahun',
            'm' => 'bulan', 
            'w' => 'minggu',
            'd' => 'hari',
            'h' => 'jam',
            'i' => 'menit',
            's' => 'detik',
        );

        $diffValues = array(
            'y' => $diff->y,
            'm' => $diff->m,
            'w' => $weeks,
            'd' => $days,
            'h' => $diff->h,
            'i' => $diff->i,
            's' => $diff->s,
        );
        
        $string = array();
        foreach ($units as $k => $name) {
            if (!empty($diffValues[$k])) {
                $string[$k] = $diffValues[$k] . ' ' . $name;
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
    } catch (Exception $e) {
        return 'baru saja';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ulasan | K SIXTEEN CAFE</title>
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    /* VARIABLES */
    :root {
      --cafe-main: #FFD600;
      --cafe-dark: #111111;
      --cafe-bg: #1a1a1a;
      --cafe-card: #2d2d2d;
      --cafe-text: #ffffff;
      --cafe-text-light: #b0b0b0;
      --cafe-shadow: 0 4px 20px rgba(255, 214, 0, 0.15);
      --cafe-border: rgba(255, 214, 0, 0.2);
    }

    /* RESET & BASE STYLES */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: var(--cafe-bg);
      color: var(--cafe-text);
      line-height: 1.6;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    /* NAVIGATION STYLES */
    .navbar {
      background: rgba(17, 17, 17, 0.95);
      backdrop-filter: blur(10px);
      padding: 1rem 0;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      border-bottom: 2px solid var(--cafe-main);
      box-shadow: var(--cafe-shadow);
    }

    .nav-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .logo-wrapper {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo-image {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      overflow: hidden;
      border: 2px solid var(--cafe-main);
      background: var(--cafe-main);
    }

    .logo-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .logo-text {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--cafe-main);
      text-shadow: 0 2px 10px rgba(255, 214, 0, 0.3);
    }

    .nav-menu {
      display: flex;
      list-style: none;
      gap: 1.5rem;
      align-items: center;
    }

    .nav-link {
      color: var(--cafe-text);
      text-decoration: none;
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      transition: all 0.3s ease;
      font-size: 0.95rem;
    }

    .nav-link:hover {
      color: var(--cafe-main);
      background: rgba(255, 214, 0, 0.1);
    }

    .nav-link.active {
      color: var(--cafe-dark);
      background: var(--cafe-main);
      box-shadow: 0 2px 10px rgba(255, 214, 0, 0.4);
    }

    /* Reviews Hero Section */
    .reviews-hero {
      background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                  url('assets/images/tempat/suasana.jpg') center/cover;
      min-height: 40vh;
      display: flex;
      align-items: center;
      margin-top: 80px;
      border-radius: 0 0 20px 20px;
    }

    .reviews-hero-content {
      text-align: center;
      max-width: 800px;
      margin: 0 auto;
      padding: 2rem;
    }

    .reviews-hero h1 {
      font-size: 3rem;
      color: var(--cafe-main);
      margin-bottom: 1rem;
    }

    .reviews-hero p {
      font-size: 1.2rem;
      color: var(--cafe-text-light);
    }

    .rating-section,
    .add-review-section,
    .reviews-list-section {
      padding: 5rem 0;
    }

    .rating-section {
      background: var(--cafe-bg);
    }

    .add-review-section {
      background: var(--cafe-dark);
    }

    .reviews-list-section {
      background: var(--cafe-bg);
    }

    .section-title {
      text-align: center;
      font-size: 2.5rem;
      color: var(--cafe-main);
      margin-bottom: 1rem;
    }

    .section-subtitle {
      text-align: center;
      color: var(--cafe-text-light);
      font-size: 1.1rem;
      margin-bottom: 3rem;
    }

    .rating-summary {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      align-items: center;
    }

    .rating-overview {
      text-align: center;
    }

    .rating-score {
      margin-bottom: 2rem;
    }

    .score-number {
      font-size: 4rem;
      font-weight: 800;
      color: var(--cafe-main);
      line-height: 1;
    }

    .score-stars {
      font-size: 1.5rem;
      color: var(--cafe-main);
      margin: 0.5rem 0;
    }

    .score-count {
      color: var(--cafe-text-light);
    }

    .rating-item {
      display: flex;
      align-items: center;
      margin-bottom: 0.5rem;
      gap: 1rem;
    }

    .rating-bar {
      flex: 1;
      height: 8px;
      background: var(--cafe-card);
      border-radius: 4px;
      overflow: hidden;
    }

    .rating-fill {
      height: 100%;
      background: var(--cafe-main);
      border-radius: 4px;
    }

    .rating-features {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .feature-rating {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .feature-name {
      flex: 1;
      color: var(--cafe-text);
    }

    .feature-stars {
      color: var(--cafe-main);
    }

    .feature-score {
      font-weight: 700;
      color: var(--cafe-main);
      min-width: 40px;
      text-align: right;
    }

    .form-container {
      max-width: 700px;
      margin: 0 auto;
    }

    .review-form {
      background: var(--cafe-card);
      padding: 2.5rem;
      border-radius: 15px;
      border: 1px solid var(--cafe-border);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: var(--cafe-main);
      font-weight: 600;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 0.75rem 1rem;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid var(--cafe-border);
      border-radius: 8px;
      color: var(--cafe-text);
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--cafe-main);
      box-shadow: 0 0 0 3px rgba(255, 214, 0, 0.1);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 120px;
    }

    .star-rating {
      display: flex;
      flex-direction: row-reverse;
      justify-content: flex-end;
      gap: 0.2rem;
    }

    .star-rating input {
      display: none;
    }

    .star-rating label {
      cursor: pointer;
      font-size: 1.5rem;
      color: #ddd;
      transition: color 0.2s ease;
    }

    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
      color: var(--cafe-main);
    }

    .recommendation-options {
      display: flex;
      gap: 1rem;
    }

    .radio-option {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
    }

    .radio-label {
      color: var(--cafe-text);
    }

    .submit-btn {
      background: var(--cafe-main);
      color: var(--cafe-dark);
      border: none;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 700;
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      width: 100%;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 214, 0, 0.4);
    }

    .reviews-filter {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 2rem;
      flex-wrap: wrap;
      justify-content: center;
    }

    .filter-btn {
      background: var(--cafe-card);
      color: var(--cafe-text);
      border: 1px solid var(--cafe-border);
      padding: 0.75rem 1.5rem;
      border-radius: 50px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .filter-btn.active,
    .filter-btn:hover {
      background: var(--cafe-main);
      color: var(--cafe-dark);
    }

    .reviews-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
    }

    .review-card {
      background: var(--cafe-card);
      border-radius: 15px;
      padding: 2rem;
      border: 1px solid var(--cafe-border);
      transition: all 0.3s ease;
    }

    .review-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--cafe-shadow);
    }

    .review-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1.5rem;
    }

    .reviewer-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .reviewer-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: var(--cafe-main);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--cafe-dark);
      font-size: 1.2rem;
    }

    .reviewer-name {
      color: var(--cafe-text);
      margin-bottom: 0.25rem;
    }

    .review-date {
      color: var(--cafe-text-light);
      font-size: 0.9rem;
    }

    .review-rating {
      color: var(--cafe-main);
    }

    .review-content {
      margin-bottom: 1.5rem;
    }

    .review-title {
      color: var(--cafe-main);
      margin-bottom: 1rem;
      font-size: 1.2rem;
    }

    .review-text {
      color: var(--cafe-text-light);
      line-height: 1.6;
    }

    .review-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 1rem;
      border-top: 1px solid var(--cafe-border);
    }

    .recommendation {
      color: var(--cafe-text-light);
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .recommendation i {
      color: var(--cafe-main);
    }

    .load-more-container {
      text-align: center;
    }

    .load-more-btn {
      background: var(--cafe-main);
      color: var(--cafe-dark);
      border: none;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .load-more-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 214, 0, 0.4);
    }

    .no-reviews {
      text-align: center;
      padding: 3rem;
      grid-column: 1 / -1;
    }

    .no-reviews i {
      font-size: 4rem;
      color: var(--cafe-text-light);
      margin-bottom: 1rem;
    }

    .no-reviews h3 {
      color: var(--cafe-text);
      margin-bottom: 1rem;
    }

    .no-reviews p {
      color: var(--cafe-text-light);
    }

    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      z-index: 2000;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .modal.show {
      display: flex;
    }

    .modal-content {
      background: var(--cafe-card);
      border-radius: 15px;
      width: 100%;
      max-width: 500px;
      border: 2px solid var(--cafe-main);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem;
      border-bottom: 1px solid var(--cafe-border);
    }

    .modal-header h3 {
      color: var(--cafe-main);
      font-size: 1.3rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .modal-close {
      background: none;
      border: none;
      color: var(--cafe-text);
      font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .modal-close:hover {
      color: var(--cafe-main);
    }

    .modal-body {
      padding: 1.5rem;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      padding: 1.5rem;
      border-top: 1px solid var(--cafe-border);
    }

    .btn {
      padding: 0.75rem 1.5rem;
      border-radius: 50px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-primary {
      background: var(--cafe-main);
      color: var(--cafe-dark);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 214, 0, 0.4);
    }

    /* Footer */
    .footer {
      background: var(--cafe-dark);
      padding: 3rem 0;
      text-align: center;
      border-top: 2px solid var(--cafe-main);
    }

    .footer-content p {
      margin-bottom: 1rem;
      color: var(--cafe-text-light);
    }

    .social-links {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 1.5rem;
    }

    .social-link {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--cafe-card);
      color: var(--cafe-text);
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .social-link:hover {
      background: var(--cafe-main);
      color: var(--cafe-dark);
      transform: translateY(-2px);
    }

    /* Message styles */
    .alert {
      padding: 1rem 1.5rem;
      border-radius: 10px;
      margin-bottom: 2rem;
      border: 1px solid;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .alert-success {
      background: rgba(46, 213, 115, 0.1);
      color: #2ed573;
      border-color: rgba(46, 213, 115, 0.3);
    }

    .alert-error {
      background: rgba(255, 71, 87, 0.1);
      color: #ff4757;
      border-color: rgba(255, 71, 87, 0.3);
    }

    /* Responsive design */
    @media (max-width: 768px) {
      .rating-summary {
        grid-template-columns: 1fr;
        gap: 2rem;
      }
      
      .reviews-grid {
        grid-template-columns: 1fr;
      }
      
      .review-header {
        flex-direction: column;
        gap: 1rem;
      }
      
      .recommendation-options {
        flex-direction: column;
        gap: 0.5rem;
      }
      
      .review-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
      }
      
      .reviews-filter {
        justify-content: flex-start;
      }
      
      .nav-container {
        flex-direction: column;
        gap: 1rem;
      }
      
      .nav-menu {
        gap: 1rem;
      }
      
      .section-title {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="logo-wrapper">
        <div class="logo-image">
          <img src="assets/images/logo.jpg" alt="K SIXTEEN CAFE">
        </div>
        <div class="logo-text">K SIXTEEN CAFE</div>
      </div>
      
      <ul class="nav-menu">
        <li><a href="index.php" class="nav-link">Home</a></li>
        <li><a href="menu.php" class="nav-link">Menu</a></li>
        <li><a href="about.php" class="nav-link">About</a></li>
        <li><a href="contact.php" class="nav-link">Contact</a></li>
        <li><a href="Ulasan.php" class="nav-link active">Ulasan</a></li>
      </ul>
    </div>
  </nav>

  <!-- Reviews Hero Section -->
  <section class="reviews-hero">
    <div class="container">
      <div class="reviews-hero-content">
        <h1>Ulasan Pelanggan</h1>
        <p>Bagikan pengalaman Anda di K SIXTEEN CAFE dan lihat apa kata pelanggan lain tentang kami</p>
      </div>
    </div>
  </section>

  <!-- Overall Rating Section -->
  <section class="rating-section">
    <div class="container">
      <div class="rating-summary">
        <div class="rating-overview">
          <div class="rating-score">
            <div class="score-number"><?php echo $stats['average_rating']; ?></div>
            <div class="score-stars">
              <?php
              $fullStars = floor($stats['average_rating']);
              $hasHalfStar = ($stats['average_rating'] - $fullStars) >= 0.5;
              
              for ($i = 1; $i <= 5; $i++) {
                  if ($i <= $fullStars) {
                      echo '<i class="fas fa-star"></i>';
                  } else if ($i == $fullStars + 1 && $hasHalfStar) {
                      echo '<i class="fas fa-star-half-alt"></i>';
                  } else {
                      echo '<i class="far fa-star"></i>';
                  }
              }
              ?>
            </div>
            <div class="score-count">Berdasarkan <?php echo $stats['total_reviews']; ?> ulasan</div>
          </div>
          
          <div class="rating-breakdown">
            <div class="rating-item">
              <span>5 Bintang</span>
              <div class="rating-bar">
                <div class="rating-fill" style="width: <?php echo $stats['rating_5_percent']; ?>%"></div>
              </div>
              <span><?php echo $stats['rating_5_percent']; ?>%</span>
            </div>
            <div class="rating-item">
              <span>4 Bintang</span>
              <div class="rating-bar">
                <div class="rating-fill" style="width: <?php echo $stats['rating_4_percent']; ?>%"></div>
              </div>
              <span><?php echo $stats['rating_4_percent']; ?>%</span>
            </div>
            <div class="rating-item">
              <span>3 Bintang</span>
              <div class="rating-bar">
                <div class="rating-fill" style="width: <?php echo $stats['rating_3_percent']; ?>%"></div>
              </div>
              <span><?php echo $stats['rating_3_percent']; ?>%</span>
            </div>
            <div class="rating-item">
              <span>2 Bintang</span>
              <div class="rating-bar">
                <div class="rating-fill" style="width: <?php echo $stats['rating_2_percent']; ?>%"></div>
              </div>
              <span><?php echo $stats['rating_2_percent']; ?>%</span>
            </div>
            <div class="rating-item">
              <span>1 Bintang</span>
              <div class="rating-bar">
                <div class="rating-fill" style="width: <?php echo $stats['rating_1_percent']; ?>%"></div>
              </div>
              <span><?php echo $stats['rating_1_percent']; ?>%</span>
            </div>
          </div>
        </div>
        
        <div class="rating-features">
          <div class="feature-rating">
            <div class="feature-name">Kualitas Kopi</div>
            <div class="feature-stars">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <div class="feature-score">4.9</div>
          </div>
          <div class="feature-rating">
            <div class="feature-name">Rasa Makanan</div>
            <div class="feature-stars">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star-half-alt"></i>
            </div>
            <div class="feature-score">4.7</div>
          </div>
          <div class="feature-rating">
            <div class="feature-name">Pelayanan</div>
            <div class="feature-stars">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <div class="feature-score">4.9</div>
          </div>
          <div class="feature-rating">
            <div class="feature-name">Suasana</div>
            <div class="feature-stars">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star-half-alt"></i>
            </div>
            <div class="feature-score">4.7</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Add Review Section -->
  <section class="add-review-section">
    <div class="container">
      <div class="form-container">
        <h2 class="section-title">Bagikan Pengalaman Anda</h2>
        <p class="section-subtitle">Ceritakan pengalaman Anda di K SIXTEEN CAFE</p>
        
        <?php if (isset($_SESSION['review_message'])): ?>
          <div class="alert <?php echo $_SESSION['review_success'] ? 'alert-success' : 'alert-error'; ?>">
            <i class="fas <?php echo $_SESSION['review_success'] ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo $_SESSION['review_message']; ?>
          </div>
          <?php 
          unset($_SESSION['review_message']); 
          unset($_SESSION['review_success']);
          ?>
        <?php endif; ?>
        
        <form class="review-form" action="process_ulasan.php" method="POST">
          <div class="form-group">
            <label for="reviewer-name">Nama Anda *</label>
            <input type="text" id="reviewer-name" name="reviewer_name" required placeholder="Masukkan nama Anda" value="<?php echo $_POST['reviewer_name'] ?? ''; ?>">
          </div>
          
          <div class="form-group">
            <label>Rating Keseluruhan *</label>
            <div class="star-rating">
              <input type="radio" id="star5" name="rating" value="5" required <?php echo ($_POST['rating'] ?? '') == '5' ? 'checked' : ''; ?>>
              <label for="star5"><i class="fas fa-star"></i></label>
              <input type="radio" id="star4" name="rating" value="4" <?php echo ($_POST['rating'] ?? '') == '4' ? 'checked' : ''; ?>>
              <label for="star4"><i class="fas fa-star"></i></label>
              <input type="radio" id="star3" name="rating" value="3" <?php echo ($_POST['rating'] ?? '') == '3' ? 'checked' : ''; ?>>
              <label for="star3"><i class="fas fa-star"></i></label>
              <input type="radio" id="star2" name="rating" value="2" <?php echo ($_POST['rating'] ?? '') == '2' ? 'checked' : ''; ?>>
              <label for="star2"><i class="fas fa-star"></i></label>
              <input type="radio" id="star1" name="rating" value="1" <?php echo ($_POST['rating'] ?? '') == '1' ? 'checked' : ''; ?>>
              <label for="star1"><i class="fas fa-star"></i></label>
            </div>
          </div>
          
          <div class="form-group">
            <label for="review-title">Judul Ulasan *</label>
            <input type="text" id="review-title" name="review_title" required placeholder="Contoh: Tempat nongkrong terbaik di Nganjuk" value="<?php echo $_POST['review_title'] ?? ''; ?>">
          </div>
          
          <div class="form-group">
            <label for="review-text">Ulasan Anda *</label>
            <textarea id="review-text" name="review_text" rows="5" required placeholder="Ceritakan pengalaman Anda di K SIXTEEN CAFE..."><?php echo $_POST['review_text'] ?? ''; ?></textarea>
          </div>
          
          <div class="form-group">
            <label>Rekomendasikan ke teman?</label>
            <div class="recommendation-options">
              <label class="radio-option">
                <input type="radio" name="recommend" value="yes" <?php echo ($_POST['recommend'] ?? 'yes') == 'yes' ? 'checked' : ''; ?>>
                <span class="radio-label">Ya, sangat merekomendasikan</span>
              </label>
              <label class="radio-option">
                <input type="radio" name="recommend" value="no" <?php echo ($_POST['recommend'] ?? '') == 'no' ? 'checked' : ''; ?>>
                <span class="radio-label">Tidak merekomendasikan</span>
              </label>
            </div>
          </div>
          
          <button type="submit" class="submit-btn">
            <i class="fas fa-paper-plane"></i> Kirim Ulasan
          </button>
        </form>
      </div>
    </div>
  </section>

  <!-- Reviews List Section -->
  <section class="reviews-list-section">
    <div class="container">
      <h2 class="section-title">Ulasan Pelanggan</h2>
      <p class="section-subtitle">Apa kata pelanggan tentang pengalaman mereka di K SIXTEEN</p>
      
      <div class="reviews-filter">
        <button class="filter-btn active" data-filter="all">Semua Ulasan</button>
        <button class="filter-btn" data-filter="5">5 Bintang</button>
        <button class="filter-btn" data-filter="4">4 Bintang</button>
        <button class="filter-btn" data-filter="3">3 Bintang</button>
        <button class="filter-btn" data-filter="2">2 Bintang</button>
        <button class="filter-btn" data-filter="1">1 Bintang</button>
      </div>
      
      <div class="reviews-grid" id="reviewsGrid">
        <?php if (!empty($reviews_from_db)): ?>
          <?php foreach ($reviews_from_db as $review): ?>
            <div class="review-card" data-rating="<?php echo $review['Rating']; ?>">
              <div class="review-header">
                <div class="reviewer-info">
                  <div class="reviewer-avatar">
                    <i class="fas fa-user"></i>
                  </div>
                  <div class="reviewer-details">
                    <h4 class="reviewer-name"><?php echo htmlspecialchars($review['Nama_Pelanggan']); ?></h4>
                    <div class="review-date"><?php echo time_elapsed_string($review['Tanggal_Ulasan']); ?></div>
                  </div>
                </div>
                <div class="review-rating">
                  <?php
                  for ($i = 1; $i <= 5; $i++) {
                      echo $i <= $review['Rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                  }
                  ?>
                </div>
              </div>
              
              <div class="review-content">
                <h3 class="review-title"><?php echo htmlspecialchars($review['Judul_Ulasan']); ?></h3>
                <p class="review-text"><?php echo nl2br(htmlspecialchars($review['Isi_Ulasan'])); ?></p>
              </div>
              
              <div class="review-footer">
                <div class="recommendation">
                  <?php if ($review['Rekomendasi'] === 'yes'): ?>
                    <i class="fas fa-thumbs-up"></i> Merekomendasikan tempat ini
                  <?php else: ?>
                    <i class="fas fa-thumbs-down"></i> Tidak merekomendasikan
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="no-reviews">
            <i class="fas fa-comment-slash"></i>
            <h3>Belum ada ulasan</h3>
            <p>Jadilah yang pertama memberikan ulasan untuk K SIXTEEN CAFE!</p>
          </div>
        <?php endif; ?>
      </div>
      
      <?php if (count($reviews_from_db) > 6): ?>
        <div class="load-more-container">
          <button class="load-more-btn" id="loadMoreBtn">
            <i class="fas fa-redo"></i> Muat Lebih Banyak Ulasan
          </button>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <p>&copy; <?php echo date('Y'); ?> K SIXTEEN CAFE. All rights reserved.</p>
        <p>Jl. Imam Bonjol No.36, Payaman, Kec. Nganjuk, Jawa Timur</p>
        
        <div class="social-links">
          <a href="https://www.instagram.com/k16_playstation/" class="social-link" target="_blank">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="https://wa.me/6282132384305" class="social-link" target="_blank">
            <i class="fab fa-whatsapp"></i>
          </a>
          <a href="https://www.tiktok.com/@k16playstation" class="social-link" target="_blank">
            <i class="fab fa-tiktok"></i>
          </a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // JavaScript untuk filter dan interaksi
    document.addEventListener('DOMContentLoaded', function() {
      const filterBtns = document.querySelectorAll('.filter-btn');
      const reviewCards = document.querySelectorAll('.review-card');
      const loadMoreBtn = document.getElementById('loadMoreBtn');
      let visibleReviews = 6;
      
      // Filter functionality
      filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const filter = this.getAttribute('data-filter');
          
          // Update active button
          filterBtns.forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          
          // Filter reviews
          reviewCards.forEach(card => {
            if (filter === 'all' || card.getAttribute('data-rating') === filter) {
              card.style.display = 'block';
            } else {
              card.style.display = 'none';
            }
          });
          
          // Reset visible reviews count
          visibleReviews = 6;
          updateLoadMoreButton();
        });
      });
      
      // Load more functionality
      if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
          visibleReviews += 6;
          const allReviews = document.querySelectorAll('.review-card[style="display: block"], .review-card:not([style])');
          
          allReviews.forEach((card, index) => {
            if (index < visibleReviews) {
              card.style.display = 'block';
            }
          });
          
          updateLoadMoreButton();
        });
        
        function updateLoadMoreButton() {
          const visibleCards = document.querySelectorAll('.review-card[style="display: block"], .review-card:not([style])');
          const totalCards = document.querySelectorAll('.review-card');
          
          if (visibleReviews >= totalCards.length) {
            loadMoreBtn.style.display = 'none';
          } else {
            loadMoreBtn.style.display = 'block';
          }
        }
        
        // Initial load more setup
        updateLoadMoreButton();
      }
      
      // Star rating interaction
      const starInputs = document.querySelectorAll('.star-rating input');
      starInputs.forEach(input => {
        input.addEventListener('change', function() {
          const rating = this.value;
          const stars = this.parentElement.querySelectorAll('label i');
          
          stars.forEach((star, index) => {
            if (index < rating) {
              star.className = 'fas fa-star';
            } else {
              star.className = 'far fa-star';
            }
          });
        });
      });
    });
  </script>
</body>
</html>