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
  <!-- Navigation -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="logo-wrapper">
        <div class="logo-image">
          <img src="aset beranda/logo.jpg" alt="K SIXTEEN CAFE">
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
            <div class="score-number">4.8</div>
            <div class="score-stars">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star-half-alt"></i>
            </div>
            <div class="score-count">Berdasarkan 124 ulasan</div>
          </div>
          
          <div class="rating-breakdown">
            <div class="rating-item">
              <span>5 Bintang</span>
              <div class="rating-bar">
                <div class="rating-fill" style="width: 75%"></div>
              </div>
              <span>75%</span>
            </div>
            <div class="rating-item">
              <span>4 Bintang</span>
              <div class="rating-bar">
                <div class="rating-fill" style="width: 15%"></div>
              </div>
              <span>15%</span>
            </div>
            <div class="rating-item">
              <span>3 Bintang</span>
              <div class="rating-bar">
                <div class="rating-fill" style="width: 7%"></div>
              </div>
              <span>7%</span>
            </div>
            <div class="rating-item">
              <span>2 Bintang</span>
              <div class="rating-bar">
                <div class="rating-fill" style="width: 2%"></div>
              </div>
              <span>2%</span>
            </div>
            <div class="rating-item">
              <span>1 Bintang</span>
              <div class="rating-bar">
                <div class="rating-fill" style="width: 1%"></div>
              </div>
              <span>1%</span>
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
        
        <form class="review-form" id="reviewForm">
          <div class="form-group">
            <label for="reviewer-name">Nama Anda *</label>
            <input type="text" id="reviewer-name" name="reviewer-name" required placeholder="Masukkan nama Anda">
          </div>
          
          <div class="form-group">
            <label>Rating Keseluruhan *</label>
            <div class="star-rating">
              <input type="radio" id="star5" name="rating" value="5">
              <label for="star5"><i class="fas fa-star"></i></label>
              <input type="radio" id="star4" name="rating" value="4">
              <label for="star4"><i class="fas fa-star"></i></label>
              <input type="radio" id="star3" name="rating" value="3">
              <label for="star3"><i class="fas fa-star"></i></label>
              <input type="radio" id="star2" name="rating" value="2">
              <label for="star2"><i class="fas fa-star"></i></label>
              <input type="radio" id="star1" name="rating" value="1">
              <label for="star1"><i class="fas fa-star"></i></label>
            </div>
          </div>
          
          <div class="form-group">
            <label for="review-title">Judul Ulasan *</label>
            <input type="text" id="review-title" name="review-title" required placeholder="Contoh: Tempat nongkrong terbaik di Nganjuk">
          </div>
          
          <div class="form-group">
            <label for="review-text">Ulasan Anda *</label>
            <textarea id="review-text" name="review-text" rows="5" required placeholder="Ceritakan pengalaman Anda di K SIXTEEN CAFE..."></textarea>
          </div>
          
          <div class="form-group">
            <label>Rekomendasikan ke teman?</label>
            <div class="recommendation-options">
              <label class="radio-option">
                <input type="radio" name="recommend" value="yes" checked>
                <span class="radio-label">Ya, sangat merekomendasikan</span>
              </label>
              <label class="radio-option">
                <input type="radio" name="recommend" value="no">
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
        <!-- Reviews will be loaded here dynamically -->
      </div>
      
      <div class="load-more-container">
        <button class="load-more-btn" id="loadMoreBtn">
          <i class="fas fa-redo"></i> Muat Lebih Banyak Ulasan
        </button>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <p>&copy; 2025 K SIXTEEN CAFE. All rights reserved.</p>
        <p>Jl. Imam Bonjol No.36, Payaman, Kec. Nganjuk, Jawa Timur</p>
        
        <div class="social-links">
          <a href="https://www.instagram.com/k16_playstation/" class="social-link" target="_blank">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="https://wa.me/6282132384305" class="social-link" target="_blank">
            <i class="fab fa-whatsapp"></i>
          </a>
          <a href="#" class="social-link">
            <i class="fab fa-facebook-f"></i>
          </a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Success Modal -->
  <div class="modal" id="successModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fas fa-check-circle"></i> Ulasan Terkirim!</h3>
        <button class="modal-close" onclick="closeSuccessModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-body">
        <p>Terima kasih telah berbagi pengalaman Anda di K SIXTEEN CAFE. Ulasan Anda telah berhasil dikirim dan akan ditampilkan setelah proses moderasi.</p>
      </div>
      
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="closeSuccessModal()">Tutup</button>
      </div>
    </div>
  </div>

  <script>
    // Sample reviews data
    const sampleReviews = [

      {
        id: 1,
        name: "Budi Santoso",
        rating: 5,
        title: "Tempat nongkrong favorit!",
        text: "K SIXTEEN CAFE benar-benar menjadi tempat favorit saya untuk nongkrong. Kopinya enak, makanannya lezat, dan suasana nyaman. Staffnya juga ramah-ramah. Sangat recommended!",
        date: "2 hari yang lalu",
        recommend: true
      },
      {
        id: 2,
        name: "Sari Dewi",
        rating: 5, 
        title: "Pelayanan luar biasa",
        text: "Saya sering datang ke sini untuk meeting atau sekedar mengerjakan tugas. WiFi-nya cepat, tempatnya nyaman, dan yang paling penting kopinya selalu konsisten enaknya. Pelayanan staff juga sangat baik.",
        date: "1 minggu yang lalu",
        recommend: true
      },
      {
        id: 3,
        name: "Ahmad Rizki",
        rating: 4,
        title: "Kopi dan makanan enak",
        text: "Kopi susu di sini memang juara. Rasa kopinya kuat tapi tidak pahit. Makanannya juga enak, terutama ayam penyetnya. Hanya saja kadang agak ramai di jam-jam tertentu.",
        date: "2 minggu yang lalu",
        recommend: true
      },
      {
        id: 4,
        name: "Diana Putri",
        rating: 5,
        title: "Buka 24 jam, sangat membantu",
        text: "Sebagai mahasiswa yang sering begadang, keberadaan K SIXTEEN yang buka 24 jam sangat membantu. Bisa nugas sampai pagi dengan ditemani kopi dan snack yang enak. Suasana di malam hari juga lebih tenang.",
        date: "3 minggu yang lalu",
        recommend: true
      },
      {
        id: 5,
        name: "Rudi Hartono",
        rating: 4,
        title: "Tempat yang nyaman",
        text: "Saya suka datang ke sini untuk meeting kecil-kecilan dengan klien. Tempatnya nyaman, tidak terlalu berisik, dan harganya terjangkau. Parkirannya juga luas.",
        date: "1 bulan yang lalu",
        recommend: true
      },
      {
        id: 6,
        name: "Maya Sari",
        rating: 3,
        title: "Cukup bagus",
        text: "Secara keseluruhan cukup bagus. Kopinya enak, tapi menurut saya harganya agak mahal untuk ukuran Nganjuk. Tempatnya nyaman tapi kadang musiknya agak keras.",
        date: "1 bulan yang lalu",
        recommend: false
      }
    ];

    let reviews = [...sampleReviews];
    let currentFilter = 'all';
    let displayedReviews = 4;

    // Initialize the reviews page
    function initReviews() {
      renderReviews();
      setupEventListeners();
    }

    // Render reviews based on current filter
    function renderReviews() {
      const reviewsGrid = document.getElementById('reviewsGrid');
      const filteredReviews = currentFilter === 'all' 
        ? reviews 
        : reviews.filter(review => review.rating == currentFilter);
      
      const reviewsToShow = filteredReviews.slice(0, displayedReviews);
      
      if (reviewsToShow.length === 0) {
        reviewsGrid.innerHTML = `
          <div class="no-reviews">
            <i class="fas fa-comment-slash"></i>
            <h3>Tidak ada ulasan untuk filter ini</h3>
            <p>Coba filter lain atau tambahkan ulasan pertama Anda!</p>
          </div>
        `;
        return;
      }
      
      reviewsGrid.innerHTML = reviewsToShow.map(review => `
        <div class="review-card">
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar">
                <i class="fas fa-user"></i>
              </div>
              <div class="reviewer-details">
                <h4 class="reviewer-name">${review.name}</h4>
                <div class="review-date">${review.date}</div>
              </div>
            </div>
            <div class="review-rating">
              ${generateStars(review.rating)}
            </div>
          </div>
          
          <div class="review-content">
            <h3 class="review-title">${review.title}</h3>
            <p class="review-text">${review.text}</p>
          </div>
          
          <div class="review-footer">
            <div class="recommendation">
              ${review.recommend 
                ? '<i class="fas fa-thumbs-up"></i> Merekomendasikan tempat ini' 
                : '<i class="fas fa-thumbs-down"></i> Tidak merekomendasikan'}
            </div>
            <div class="review-actions">
              <button class="action-btn" onclick="likeReview(${review.id})">
                <i class="far fa-thumbs-up"></i> <span id="like-count-${review.id}">0</span>
              </button>
            </div>
          </div>
        </div>
      `).join('');
      
      // Show/hide load more button
      const loadMoreBtn = document.getElementById('loadMoreBtn');
      if (displayedReviews >= filteredReviews.length) {
        loadMoreBtn.style.display = 'none';
      } else {
        loadMoreBtn.style.display = 'block';
      }
    }

    // Generate star rating HTML
    function generateStars(rating) {
      let stars = '';
      for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
          stars += '<i class="fas fa-star"></i>';
        } else {
          stars += '<i class="far fa-star"></i>';
        }
      }
      return stars;
    }

    // Setup event listeners
    function setupEventListeners() {
      // Filter buttons
      document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          currentFilter = this.dataset.filter;
          displayedReviews = 4;
          renderReviews();
        });
      });
      
      // Load more button
      document.getElementById('loadMoreBtn').addEventListener('click', function() {
        displayedReviews += 4;
        renderReviews();
      });
      
      // Review form submission
      document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitReview();
      });
      
      // Star rating interaction
      document.querySelectorAll('.star-rating input').forEach(star => {
        star.addEventListener('change', function() {
          const rating = this.value;
          updateStarDisplay(rating);
        });
      });
    }

    // Update star display when user selects a rating
    function updateStarDisplay(rating) {
      const stars = document.querySelectorAll('.star-rating label i');
      stars.forEach((star, index) => {
        if (index < rating) {
          star.className = 'fas fa-star';
        } else {
          star.className = 'far fa-star';
        }
      });
    }

    // Submit a new review
    function submitReview() {
      const form = document.getElementById('reviewForm');
      const formData = new FormData(form);
      
      const name = formData.get('reviewer-name');
      const rating = formData.get('rating');
      const title = formData.get('review-title');
      const text = formData.get('review-text');
      const recommend = formData.get('recommend') === 'yes';
      
      if (!name || !rating || !title || !text) {
        alert('Harap isi semua field yang wajib diisi!');
        return;
      }
      
      const newReview = {
        id: reviews.length + 1,
        name: name,
        rating: parseInt(rating),
        title: title,
        text: text,
        date: 'Baru saja',
        recommend: recommend
      };
      
      reviews.unshift(newReview);
      form.reset();
      resetStarDisplay();
      openSuccessModal();
      renderReviews();
    }

    // Reset star display after form submission
    function resetStarDisplay() {
      const stars = document.querySelectorAll('.star-rating label i');
      stars.forEach(star => {
        star.className = 'fas fa-star';
      });
    }

    // Like a review (demo functionality)
    function likeReview(reviewId) {
      const likeCount = document.getElementById(`like-count-${reviewId}`);
      let count = parseInt(likeCount.textContent) || 0;
      likeCount.textContent = count + 1;
      
      // Change icon to filled
      const btn = likeCount.closest('.action-btn');
      const icon = btn.querySelector('i');
      icon.className = 'fas fa-thumbs-up';
      
      // Visual feedback
      btn.style.color = 'var(--cafe-main)';
    }

    // Modal functions
    function openSuccessModal() {
      document.getElementById('successModal').classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function closeSuccessModal() {
      document.getElementById('successModal').classList.remove('show');
      document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
      const modal = document.getElementById('successModal');
      if (e.target === modal) {
        closeSuccessModal();
      }
    });

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', initReviews);
  </script>

  <style>
    /* Additional CSS for Reviews Page */
    .reviews-hero {
      background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                  url('https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=1200') center/cover;
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

    .action-btn {
      background: none;
      border: none;
      color: var(--cafe-text-light);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.3s ease;
    }

    .action-btn:hover {
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
    }
  </style>
</body>
</html>
