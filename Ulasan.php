<?php
// Ulasan.php - Halaman tampil + form ulasan (disesuaikan dengan assets/css/style.css)
session_start();

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// DB configs (sesuaikan jika perlu)
$configs = [
    ['localhost', 'umkmk16', 'root', '']
];

$pdo = null;
foreach ($configs as $config) {
    list($host, $dbname, $username, $password) = $config;
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        break;
    } catch (PDOException $e) {
        continue;
    }
}

// buat tabel jika belum ada
if ($pdo) {
    $stmt = $pdo->query("SHOW TABLES LIKE 'ulasan'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("
            CREATE TABLE ulasan (
                ID_Ulasan INT AUTO_INCREMENT PRIMARY KEY,
                Nama_Pelanggan VARCHAR(100) NOT NULL DEFAULT 'Pelanggan',
                Rating TINYINT NOT NULL,
                Judul_Ulasan VARCHAR(200) NOT NULL,
                Isi_Ulasan TEXT NOT NULL,
                Rekomendasi ENUM('yes','no') NOT NULL DEFAULT 'yes',
                Foto_Ulasan VARCHAR(255) NULL,
                Tanggal_Ulasan DATETIME DEFAULT CURRENT_TIMESTAMP,
                Status ENUM('pending','approved','rejected') DEFAULT 'pending'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } else {
        $c = $pdo->query("SHOW COLUMNS FROM ulasan LIKE 'Foto_Ulasan'")->fetch();
        if (!$c) {
            $pdo->exec("ALTER TABLE ulasan ADD COLUMN Foto_Ulasan VARCHAR(255) NULL AFTER Rekomendasi");
        }
    }
}

// helper waktu
function time_elapsed_string($datetime, $full = false) {
    if (empty($datetime)) return 'baru saja';
    try {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $weeks = floor($diff->d / 7);
        $days = $diff->d - ($weeks * 7);

        $units = ['y'=>'tahun','m'=>'bulan','w'=>'minggu','d'=>'hari','h'=>'jam','i'=>'menit','s'=>'detik'];
        $diffValues = ['y'=>$diff->y,'m'=>$diff->m,'w'=>$weeks,'d'=>$days,'h'=>$diff->h,'i'=>$diff->i,'s'=>$diff->s];

        $string = [];
        foreach ($units as $k => $name) {
            if (!empty($diffValues[$k])) {
                $string[] = $diffValues[$k] . ' ' . $name;
            }
        }
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
    } catch (Exception $e) {
        return 'baru saja';
    }
}

// ambil statistik dan ulasan approved
$stats = [
    'total_reviews'=>0,
    'average_rating'=>0,
    'rating_5_percent'=>0,'rating_4_percent'=>0,'rating_3_percent'=>0,'rating_2_percent'=>0,'rating_1_percent'=>0
];
$reviews_from_db = [];

if ($pdo) {
    try {
        $s = $pdo->query("
            SELECT 
                COUNT(*) as total_reviews,
                AVG(Rating) as average_rating,
                SUM(Rating = 5) as rating_5,
                SUM(Rating = 4) as rating_4,
                SUM(Rating = 3) as rating_3,
                SUM(Rating = 2) as rating_2,
                SUM(Rating = 1) as rating_1
            FROM ulasan
            WHERE Status = 'approved'
        ")->fetch(PDO::FETCH_ASSOC);

        if ($s && $s['total_reviews'] > 0) {
            $stats['total_reviews'] = (int)$s['total_reviews'];
            $stats['average_rating'] = round((float)$s['average_rating'], 1);
            $stats['rating_5_percent'] = round(($s['rating_5'] / $s['total_reviews']) * 100);
            $stats['rating_4_percent'] = round(($s['rating_4'] / $s['total_reviews']) * 100);
            $stats['rating_3_percent'] = round(($s['rating_3'] / $s['total_reviews']) * 100);
            $stats['rating_2_percent'] = round(($s['rating_2'] / $s['total_reviews']) * 100);
            $stats['rating_1_percent'] = round(($s['rating_1'] / $s['total_reviews']) * 100);
        }

        $q = $pdo->prepare("SELECT * FROM ulasan WHERE Status = 'approved' ORDER BY Tanggal_Ulasan DESC LIMIT 12");
        $q->execute();
        $reviews_from_db = $q->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // ignore
    }
}

// ambil form data yang disimpan session jika ada (prefill saat error)
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

// pesan hasil submit
$flash = $_SESSION['review_message'] ?? null;
$flash_success = $_SESSION['review_success'] ?? false;
unset($_SESSION['review_message'], $_SESSION['review_success']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ulasan | K SIXTEEN CAFE</title>
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <!-- NAV -->
  <nav class="navbar">
    <div class="nav-container container">
      <div class="logo-wrapper">
        <div class="logo-image"><img src="assets/images/logo.jpg" alt="K SIXTEEN"></div>
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

  <!-- HERO -->
  <section class="reviews-hero">
    <div class="container reviews-hero-content">
      <h1>Ulasan Pelanggan</h1>
      <p>Bagikan pengalaman Anda di K SIXTEEN CAFE dan lihat apa kata pelanggan lain</p>
    </div>
  </section>

  <!-- RATING SUMMARY -->
  <section class="rating-section">
    <div class="container">
      <div class="rating-summary">
        <div class="rating-overview">
          <div class="rating-score">
            <div class="score-number"><?php echo htmlspecialchars($stats['average_rating']); ?></div>
            <div class="score-stars" aria-hidden="true">
              <?php
              $fullStars = floor($stats['average_rating']);
              $hasHalfStar = ($stats['average_rating'] - $fullStars) >= 0.5;
              for ($i = 1; $i <= 5; $i++) {
                  if ($i <= $fullStars) echo '<i class="fas fa-star"></i>';
                  else if ($i == $fullStars + 1 && $hasHalfStar) echo '<i class="fas fa-star-half-stroke"></i>';
                  else echo '<i class="far fa-star"></i>';
              }
              ?>
            </div>
            <div class="score-count">Berdasarkan <?php echo (int)$stats['total_reviews']; ?> ulasan</div>
          </div>

          <div class="rating-breakdown">
            <div class="rating-item"><span>5 Bintang</span><div class="rating-bar"><div class="rating-fill" style="width: <?php echo $stats['rating_5_percent']; ?>%"></div></div><span><?php echo $stats['rating_5_percent']; ?>%</span></div>
            <div class="rating-item"><span>4 Bintang</span><div class="rating-bar"><div class="rating-fill" style="width: <?php echo $stats['rating_4_percent']; ?>%"></div></div><span><?php echo $stats['rating_4_percent']; ?>%</span></div>
            <div class="rating-item"><span>3 Bintang</span><div class="rating-bar"><div class="rating-fill" style="width: <?php echo $stats['rating_3_percent']; ?>%"></div></div><span><?php echo $stats['rating_3_percent']; ?>%</span></div>
            <div class="rating-item"><span>2 Bintang</span><div class="rating-bar"><div class="rating-fill" style="width: <?php echo $stats['rating_2_percent']; ?>%"></div></div><span><?php echo $stats['rating_2_percent']; ?>%</span></div>
            <div class="rating-item"><span>1 Bintang</span><div class="rating-bar"><div class="rating-fill" style="width: <?php echo $stats['rating_1_percent']; ?>%"></div></div><span><?php echo $stats['rating_1_percent']; ?>%</span></div>
          </div>
        </div>

        <div class="rating-features">
          <div class="feature-rating"><div class="feature-name">Kualitas Kopi</div><div class="feature-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div><div class="feature-score">4.9</div></div>
          <div class="feature-rating"><div class="feature-name">Rasa Makanan</div><div class="feature-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-stroke"></i></div><div class="feature-score">4.7</div></div>
          <div class="feature-rating"><div class="feature-name">Pelayanan</div><div class="feature-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div><div class="feature-score">4.9</div></div>
          <div class="feature-rating"><div class="feature-name">Suasana</div><div class="feature-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-stroke"></i></div><div class="feature-score">4.7</div></div>
        </div>
      </div>
    </div>
  </section>

  <!-- FORM -->
  <section class="add-review-section">
    <div class="container">
      <div class="form-container">
        <h2 class="section-title">Bagikan Pengalaman Anda</h2>
        <p class="section-subtitle">Ceritakan pengalaman Anda di K SIXTEEN CAFE</p>

        <?php if ($flash): ?>
          <div class="alert <?php echo $flash_success ? 'alert-success' : 'alert-error'; ?>">
            <i class="fas <?php echo $flash_success ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($flash); ?>
          </div>
        <?php endif; ?>

        <form class="review-form" action="process_ulasan.php" method="POST" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <div class="form-group">
            <label for="reviewer-name">Nama Anda *</label>
            <input type="text" id="reviewer-name" name="reviewer_name" required placeholder="Masukkan nama Anda" value="<?php echo htmlspecialchars($form_data['reviewer_name'] ?? ''); ?>">
          </div>

          <div class="form-group">
            <label>Rating Keseluruhan *</label>
            <div class="star-rating" role="radiogroup" aria-label="Rating">
              <?php for ($r=5;$r>=1;$r--): ?>
                <input type="radio" id="star<?php echo $r; ?>" name="rating" value="<?php echo $r; ?>" <?php echo (string)($form_data['rating'] ?? '') === (string)$r ? 'checked' : ''; ?> required>
                <label for="star<?php echo $r; ?>"><i class="fas fa-star"></i></label>
              <?php endfor; ?>
            </div>
          </div>

          <div class="form-group">
            <label for="review-title">Judul Ulasan *</label>
            <input type="text" id="review-title" name="review_title" required placeholder="Judul singkat ulasan" value="<?php echo htmlspecialchars($form_data['review_title'] ?? ''); ?>">
          </div>

          <div class="form-group">
            <label for="review-text">Ulasan Anda *</label>
            <textarea id="review-text" name="review_text" rows="5" required placeholder="Ceritakan pengalaman Anda..."><?php echo htmlspecialchars($form_data['review_text'] ?? ''); ?></textarea>
          </div>

          <div class="form-group">
            <label for="review-photo">Foto Ulasan (Opsional)</label>
            <div class="file-upload-area" id="photo-upload-area" onclick="document.getElementById('review-photo').click()">
              <i class="fas fa-cloud-upload-alt"></i>
              <p>Klik untuk upload foto</p>
              <span>Format: JPG, PNG, GIF, WEBP (Maks. 5MB)</span>
              <input type="file" id="review-photo" name="review_photo" accept="image/*" onchange="previewPhoto(this)">
            </div>
            <div id="photo-preview" class="photo-preview"></div>
          </div>

          <div class="form-group">
            <label>Rekomendasikan ke teman?</label>
            <div class="recommendation-options">
              <label class="radio-option"><input type="radio" name="recommend" value="yes" <?php echo (($form_data['recommend'] ?? 'yes') === 'yes') ? 'checked' : ''; ?>> <span class="radio-label">Ya</span></label>
              <label class="radio-option"><input type="radio" name="recommend" value="no" <?php echo (($form_data['recommend'] ?? '') === 'no') ? 'checked' : ''; ?>> <span class="radio-label">Tidak</span></label>
            </div>
          </div>

          <button type="submit" class="submit-btn"><i class="fas fa-paper-plane"></i> Kirim Ulasan</button>
        </form>
      </div>
    </div>
  </section>

  <!-- LIST ULASAN -->
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
            <div class="review-card" data-rating="<?php echo (int)$review['Rating']; ?>">
              <div class="review-header">
                <div class="reviewer-info">
                  <div class="reviewer-avatar"><i class="fas fa-user"></i></div>
                  <div class="reviewer-details">
                    <h4 class="reviewer-name"><?php echo htmlspecialchars($review['Nama_Pelanggan']); ?></h4>
                    <div class="review-date"><?php echo time_elapsed_string($review['Tanggal_Ulasan']); ?></div>
                  </div>
                </div>
                <div class="review-rating" aria-hidden="true">
                  <?php for ($i=1;$i<=5;$i++) echo $i <= $review['Rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                </div>
              </div>

              <?php if (!empty($review['Foto_Ulasan'])): ?>
                <div class="review-photo-container">
                  <img src="assets/images/reviews/<?php echo htmlspecialchars($review['Foto_Ulasan']); ?>" alt="Foto Ulasan" class="review-photo" onerror="this.style.display='none'">
                </div>
              <?php endif; ?>

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
    </div>
  </section>

  <footer class="footer">
    <div class="container footer-content">
      <p>&copy; <?php echo date('Y'); ?> K SIXTEEN CAFE. All rights reserved.</p>
      <p>Jl. Imam Bonjol No.36, Payaman, Kec. Nganjuk, Jawa Timur</p>
      <div class="social-links">
        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
        <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
      </div>
    </div>
  </footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // filter
  const filterBtns = document.querySelectorAll('.filter-btn');
  const reviewCards = document.querySelectorAll('.review-card');
  filterBtns.forEach(btn => btn.addEventListener('click', function(){
    filterBtns.forEach(b=>b.classList.remove('active')); this.classList.add('active');
    const f = this.getAttribute('data-filter');
    reviewCards.forEach(card => {
      if (f === 'all' || card.getAttribute('data-rating') === f) card.style.display = 'block';
      else card.style.display = 'none';
    });
  }));

  // star visual fill (inputs in reverse order)
  document.querySelectorAll('.star-rating').forEach(container=>{
    const inputs = Array.from(container.querySelectorAll('input[type=radio]'));
    const labels = Array.from(container.querySelectorAll('label'));
    function updateVisual(rating) {
      const vis = labels.slice().reverse();
      vis.forEach((lab, idx) => {
        if (idx < rating) lab.classList.add('filled'); else lab.classList.remove('filled');
      });
    }
    inputs.forEach(i=>i.addEventListener('change', ()=> updateVisual(parseInt(i.value))));
    const checked = container.querySelector('input[type=radio]:checked');
    if (checked) updateVisual(parseInt(checked.value));
  });
});

// preview foto
function previewPhoto(input) {
  const preview = document.getElementById('photo-preview');
  preview.innerHTML = '';
  if (input.files && input.files[0]) {
    const f = input.files[0];
    if (f.size > 5 * 1024 * 1024) { alert('Maks 5MB'); input.value = ''; return; }
    const reader = new FileReader();
    reader.onload = e => {
      preview.innerHTML = '<img src="'+e.target.result+'" alt="Preview">';
    };
    reader.readAsDataURL(f);
  }
}
</script>
</body>
</html>