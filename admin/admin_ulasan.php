<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin.php');
    exit;
}

// Session timeout (8 jam)
if (time() - $_SESSION['login_time'] > 28800) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Konfigurasi database
$configs = [
    ['localhost', 'umkmk16', 'root', ''],
    ['127.0.0.1', 'umkmk16', 'root', ''],
    ['localhost:3306', 'umkmk16', 'root', ''],
    ['127.0.0.1:3306', 'umkmk16', 'root', '']
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

if (!$pdo) {
    die("<div style='padding: 20px; background: #ff4757; color: white; text-align: center;'>
        <h3>❌ Database Connection Error</h3>
        <p>Pastikan MySQL berjalan dan database 'umkmk16' sudah diimport.</p>
    </div>");
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_review'])) {
        $id_ulasan = $_POST['id_ulasan'];
        $stmt = $pdo->prepare("UPDATE ulasan SET Status = 'approved' WHERE ID_Ulasan = ?");
        if ($stmt->execute([$id_ulasan])) {
            $_SESSION['success_message'] = "Ulasan berhasil disetujui!";
        } else {
            $_SESSION['error_message'] = "Gagal menyetujui ulasan.";
        }
    }
    
    if (isset($_POST['reject_review'])) {
        $id_ulasan = $_POST['id_ulasan'];
        $stmt = $pdo->prepare("UPDATE ulasan SET Status = 'rejected' WHERE ID_Ulasan = ?");
        if ($stmt->execute([$id_ulasan])) {
            $_SESSION['success_message'] = "Ulasan berhasil ditolak!";
        } else {
            $_SESSION['error_message'] = "Gagal menolak ulasan.";
        }
    }
    
    if (isset($_POST['delete_review'])) {
        $id_ulasan = $_POST['id_ulasan'];
        $foto_ulasan = $_POST['foto_ulasan'] ?? '';
        
        // Hapus file foto jika ada
        if ($foto_ulasan && file_exists('assets/images/reviews/' . $foto_ulasan)) {
            unlink('assets/images/reviews/' . $foto_ulasan);
        }
        
        $stmt = $pdo->prepare("DELETE FROM ulasan WHERE ID_Ulasan = ?");
        if ($stmt->execute([$id_ulasan])) {
            $_SESSION['success_message'] = "Ulasan berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus ulasan.";
        }
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Ambil data ulasan berdasarkan status
$status = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

$whereConditions = [];
$params = [];

if ($status !== 'all') {
    $whereConditions[] = "Status = ?";
    $params[] = $status;
}

if (!empty($search)) {
    $whereConditions[] = "(Nama_Pelanggan LIKE ? OR Judul_Ulasan LIKE ? OR Isi_Ulasan LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = $whereConditions ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Hitung statistik
$stats = $pdo->query("
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN Status = 'pending' THEN 1 END) as pending,
        COUNT(CASE WHEN Status = 'approved' THEN 1 END) as approved,
        COUNT(CASE WHEN Status = 'rejected' THEN 1 END) as rejected,
        AVG(Rating) as avg_rating
    FROM ulasan
")->fetch(PDO::FETCH_ASSOC);

// Ambil ulasan
$stmt = $pdo->prepare("
    SELECT * FROM ulasan 
    $whereClause 
    ORDER BY Tanggal_Ulasan DESC
");
$stmt->execute($params);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Ulasan | K SIXTEEN CAFE</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    /* ===== VARIABLES ===== */
    :root {
      --cafe-main: #FFD600;
      --cafe-dark: #111111;
      --cafe-bg: #1a1a1a;
      --cafe-card: #2d2d2d;
      --cafe-text: #ffffff;
      --cafe-text-light: #b0b0b0;
      --cafe-sidebar: #222222;
      --cafe-shadow: 0 4px 20px rgba(255, 214, 0, 0.15);
      --cafe-border: rgba(255, 214, 0, 0.2);
      --success: #2ed573;
      --warning: #ffa502;
      --danger: #ff4757;
      --info: #3742fa;
    }

    /* ===== RESET & BASE STYLES ===== */
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
      display: flex;
      min-height: 100vh;
    }

    .admin-sidebar {
      width: 280px;
      background: var(--cafe-sidebar);
      border-right: 2px solid var(--cafe-main);
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      z-index: 1000;
      transition: all 0.3s ease;
    }

    .sidebar-header {
      padding: 2rem 1.5rem;
      border-bottom: 1px solid var(--cafe-border);
      text-align: center;
    }

    .sidebar-logo {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-bottom: 1rem;
    }

    .sidebar-logo-image {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      overflow: hidden;
      border: 2px solid var(--cafe-main);
    }

    .sidebar-logo-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .sidebar-logo-text {
      font-size: 1.3rem;
      font-weight: 800;
      color: var(--cafe-main);
    }

    .admin-welcome {
      color: var(--cafe-text-light);
      font-size: 0.9rem;
    }

    .admin-welcome strong {
      color: var(--cafe-main);
    }

    .sidebar-menu {
      padding: 1.5rem 0;
    }

    .menu-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem 1.5rem;
      color: var(--cafe-text);
      text-decoration: none;
      transition: all 0.3s ease;
      border-left: 4px solid transparent;
    }

    .menu-item:hover {
      background: rgba(255, 214, 0, 0.1);
      color: var(--cafe-main);
      border-left-color: var(--cafe-main);
    }

    .menu-item.active {
      background: rgba(255, 214, 0, 0.15);
      color: var(--cafe-main);
      border-left-color: var(--cafe-main);
    }

    .menu-item i {
      width: 20px;
      text-align: center;
      font-size: 1.1rem;
    }

    .menu-text {
      font-weight: 500;
    }

    .logout-section {
      padding: 1.5rem;
      border-top: 1px solid var(--cafe-border);
      margin-top: auto;
    }

    .admin-main {
      flex: 1;
      margin-left: 280px;
      padding: 2rem;
      min-height: 100vh;
    }

    .main-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--cafe-border);
    }

    .page-title {
      color: var(--cafe-main);
      font-size: 2rem;
      font-weight: 700;
    }

    .page-subtitle {
      color: var(--cafe-text-light);
      font-size: 1.1rem;
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    .stat-card {
      background: var(--cafe-card);
      padding: 1.5rem;
      border-radius: 15px;
      text-align: center;
      border: 1px solid var(--cafe-border);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
    }

    .stat-card.total::before { background: var(--cafe-main); }
    .stat-card.pending::before { background: var(--warning); }
    .stat-card.approved::before { background: var(--success); }
    .stat-card.rejected::before { background: var(--danger); }
    .stat-card.rating::before { background: var(--info); }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--cafe-shadow);
    }

    .stat-icon {
      font-size: 2rem;
      margin-bottom: 1rem;
    }

    .stat-card.total .stat-icon { color: var(--cafe-main); }
    .stat-card.pending .stat-icon { color: var(--warning); }
    .stat-card.approved .stat-icon { color: var(--success); }
    .stat-card.rejected .stat-icon { color: var(--danger); }
    .stat-card.rating .stat-icon { color: var(--info); }

    .stat-number {
      font-size: 2rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
    }

    .stat-card.total .stat-number { color: var(--cafe-main); }
    .stat-card.pending .stat-number { color: var(--warning); }
    .stat-card.approved .stat-number { color: var(--success); }
    .stat-card.rejected .stat-number { color: var(--danger); }
    .stat-card.rating .stat-number { color: var(--info); }

    .stat-label {
      color: var(--cafe-text-light);
      font-size: 0.9rem;
    }

    .filters-section {
      background: var(--cafe-card);
      padding: 1.5rem;
      border-radius: 15px;
      margin-bottom: 2rem;
      border: 1px solid var(--cafe-border);
    }

    .filter-row {
      display: flex;
      gap: 1rem;
      align-items: center;
      flex-wrap: wrap;
    }

    .filter-group {
      display: flex;
      gap: 0.5rem;
      align-items: center;
    }

    .status-filter {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }

    .status-btn {
      padding: 0.5rem 1rem;
      border: 1px solid var(--cafe-border);
      background: var(--cafe-bg);
      color: var(--cafe-text);
      border-radius: 50px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .status-btn.active,
    .status-btn:hover {
      background: var(--cafe-main);
      color: var(--cafe-dark);
    }

    .search-box {
      display: flex;
      gap: 0.5rem;
      flex: 1;
      max-width: 400px;
    }

    .search-input {
      flex: 1;
      padding: 0.75rem 1rem;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid var(--cafe-border);
      border-radius: 8px;
      color: var(--cafe-text);
      font-size: 1rem;
    }

    .search-input:focus {
      outline: none;
      border-color: var(--cafe-main);
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
      font-size: 0.9rem;
    }

    .btn-primary {
      background: var(--cafe-main);
      color: var(--cafe-dark);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 214, 0, 0.4);
    }

    .btn-secondary {
      background: transparent;
      color: var(--cafe-text);
      border: 1px solid var(--cafe-border);
    }

    .btn-secondary:hover {
      border-color: var(--cafe-main);
      color: var(--cafe-main);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--cafe-card);
      border-radius: 15px;
      overflow: hidden;
      margin-bottom: 2rem;
    }

    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid var(--cafe-border);
    }

    th {
      color: var(--cafe-main);
      font-weight: 600;
      background: rgba(255, 214, 0, 0.1);
    }

    tr:hover {
      background: rgba(255, 255, 255, 0.05);
    }

    .review-photo {
      width: 60px;
      height: 60px;
      border-radius: 8px;
      object-fit: cover;
      border: 2px solid var(--cafe-border);
    }

    .review-photo-placeholder {
      width: 60px;
      height: 60px;
      border-radius: 8px;
      background: var(--cafe-bg);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--cafe-text-light);
      border: 2px dashed var(--cafe-border);
    }

    .rating-stars {
      color: var(--cafe-main);
    }

    .badge {
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
    }

    .badge.pending {
      background: var(--warning);
      color: white;
    }

    .badge.approved {
      background: var(--success);
      color: white;
    }

    .badge.rejected {
      background: var(--danger);
      color: white;
    }

    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }

    .action-btn {
      padding: 0.5rem;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
    }

    .action-btn.approve {
      background: var(--success);
    }

    .action-btn.reject {
      background: var(--danger);
    }

    .action-btn.delete {
      background: #2d2d2d;
      border: 1px solid var(--cafe-border);
      color: var(--cafe-text);
    }

    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }

    .review-content-preview {
      max-width: 300px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .no-reviews {
      text-align: center;
      padding: 3rem;
      color: var(--cafe-text-light);
    }

    .no-reviews i {
      font-size: 4rem;
      margin-bottom: 1rem;
    }

    .alert {
      padding: 1rem 1.5rem;
      border-radius: 10px;
      margin-bottom: 2rem;
      border: 1px solid;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
      from { transform: translateX(-100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    .alert-success {
      background: rgba(46, 213, 115, 0.1);
      color: var(--success);
      border-color: rgba(46, 213, 115, 0.3);
    }

    .alert-error {
      background: rgba(255, 71, 87, 0.1);
      color: var(--danger);
      border-color: rgba(255, 71, 87, 0.3);
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
      max-width: 600px;
      border: 2px solid var(--cafe-main);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
      max-height: 90vh;
      overflow-y: auto;
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

    .review-detail {
      display: grid;
      gap: 1.5rem;
    }

    .review-detail-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .reviewer-info h4 {
      color: var(--cafe-main);
      margin-bottom: 0.5rem;
    }

    .review-date {
      color: var(--cafe-text-light);
      font-size: 0.9rem;
    }

    .review-rating {
      color: var(--cafe-main);
      font-size: 1.2rem;
    }

    .review-photo-large {
      max-width: 100%;
      max-height: 300px;
      border-radius: 8px;
      border: 2px solid var(--cafe-border);
    }

    .review-text-full {
      color: var(--cafe-text-light);
      line-height: 1.6;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      padding: 1.5rem;
      border-top: 1px solid var(--cafe-border);
      gap: 1rem;
    }

    /* Mobile Menu Toggle */
    .menu-toggle {
      display: none;
      position: fixed;
      top: 1rem;
      left: 1rem;
      z-index: 1001;
      background: var(--cafe-main);
      color: var(--cafe-dark);
      border: none;
      padding: 0.75rem;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1.2rem;
    }

    @media (max-width: 1024px) {
      .admin-sidebar {
        width: 250px;
      }
      
      .admin-main {
        margin-left: 250px;
      }
    }

    @media (max-width: 768px) {
      .menu-toggle {
        display: block;
      }
      
      .admin-sidebar {
        width: 280px;
        transform: translateX(-100%);
      }
      
      .admin-sidebar.active {
        transform: translateX(0);
      }
      
      .admin-main {
        margin-left: 0;
        padding: 1rem;
      }
      
      .filter-row {
        flex-direction: column;
        align-items: stretch;
      }
      
      .search-box {
        max-width: none;
      }
      
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      table {
        display: block;
        overflow-x: auto;
      }
      
      .action-buttons {
        flex-direction: column;
      }
    }

    @media (max-width: 480px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }
      
      .status-filter {
        justify-content: center;
      }
      
      .stat-card {
        padding: 1rem;
      }
      
      .stat-number {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar Toggle for Mobile -->
  <button class="menu-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Admin Sidebar -->
  <div class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <div class="sidebar-logo-image">
          <img src="../assets/images/logo.jpg" alt="K SIXTEEN CAFE" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiBmaWxsPSIjRkZENjAwIi8+Cjx0ZXh0IHg9IjI1IiB5PSIzMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE4IiBmb250LXdlaWdodD0iYm9sZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iIzExMTExMSI+S1M8L3RleHQ+Cjwvc3ZnPgo='">
        </div>
        <div class="sidebar-logo-text">K SIXTEEN CAFE</div>
      </div>
      <div class="admin-welcome">
        Welcome, <strong><?php echo $_SESSION['admin_username'] ?? 'Admin'; ?></strong>
      </div>
    </div>

    <div class="sidebar-menu">
      <a href="admin_dashboard.php" class="menu-item">
        <i class="fas fa-tachometer-alt"></i>
        <span class="menu-text">Dashboard</span>
      </a>
      <a href="admin_dashboard.php#products" class="menu-item">
        <i class="fas fa-coffee"></i>
        <span class="menu-text">Menu Management</span>
      </a>
      <a href="admin_dashboard.php#transactions" class="menu-item">
        <i class="fas fa-receipt"></i>
        <span class="menu-text">Transactions</span>
      </a>
      <a href="admin_dashboard.php#customers" class="menu-item">
        <i class="fas fa-users"></i>
        <span class="menu-text">Customers</span>
      </a>
            <a href="admin_dashboard.php#supplier" class="menu-item" data-section="suppliers">
        <i class="fas fa-truck"></i>
        <span class="menu-text">Suppliers</span>
      </a>
      <a href="admin_dashboard.php#categories" class="menu-item" data-section="categories">
        <i class="fas fa-tags"></i>
        <span class="menu-text">Categories</span>
      </a>
      <a href="admin_ulasan.php" class="menu-item active">
        <i class="fas fa-star"></i>
        <span class="menu-text">Reviews Management</span>
      </a>
      <a href="../menu.php" class="menu-item" target="_blank">
        <i class="fas fa-external-link-alt"></i>
        <span class="menu-text">View Frontend</span>
      </a>
    </div>

    <div class="logout-section">
      <form method="POST" action="admin_logout.php">
        <button type="submit" class="btn btn-primary" style="width: 100%;">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </form>
    </div>
  </div>

  <!-- Main Content -->
  <div class="admin-main">
    <div class="container">
      <!-- Messages -->
      <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i> 
          <div><?php echo $_SESSION['success_message']; ?></div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i> 
          <div><?php echo $_SESSION['error_message']; ?></div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
      <?php endif; ?>

      <!-- Header -->
      <div class="main-header">
        <div>
          <h1 class="page-title">⭐ Reviews Management</h1>
          <p class="page-subtitle">Kelola ulasan dan testimoni pelanggan</p>
        </div>
      </div>

      <!-- Statistics -->
      <div class="stats-grid">
        <div class="stat-card total">
          <div class="stat-icon">
            <i class="fas fa-comments"></i>
          </div>
          <div class="stat-number"><?php echo $stats['total']; ?></div>
          <div class="stat-label">Total Reviews</div>
        </div>
        
        <div class="stat-card pending">
          <div class="stat-icon">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-number"><?php echo $stats['pending']; ?></div>
          <div class="stat-label">Pending Review</div>
        </div>
        
        <div class="stat-card approved">
          <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-number"><?php echo $stats['approved']; ?></div>
          <div class="stat-label">Approved</div>
        </div>
        
        <div class="stat-card rejected">
          <div class="stat-icon">
            <i class="fas fa-times-circle"></i>
          </div>
          <div class="stat-number"><?php echo $stats['rejected']; ?></div>
          <div class="stat-label">Rejected</div>
        </div>
        
        <div class="stat-card rating">
          <div class="stat-icon">
            <i class="fas fa-star"></i>
          </div>
          <div class="stat-number"><?php echo round($stats['avg_rating'], 1); ?></div>
          <div class="stat-label">Average Rating</div>
        </div>
      </div>

      <!-- Filters -->
      <div class="filters-section">
        <div class="filter-row">
          <div class="filter-group">
            <span>Status:</span>
            <div class="status-filter">
              <a href="?status=all" class="status-btn <?php echo $status === 'all' ? 'active' : ''; ?>">All</a>
              <a href="?status=pending" class="status-btn <?php echo $status === 'pending' ? 'active' : ''; ?>">Pending</a>
              <a href="?status=approved" class="status-btn <?php echo $status === 'approved' ? 'active' : ''; ?>">Approved</a>
              <a href="?status=rejected" class="status-btn <?php echo $status === 'rejected' ? 'active' : ''; ?>">Rejected</a>
            </div>
          </div>
          
          <form method="GET" class="search-box">
            <input type="hidden" name="status" value="<?php echo $status; ?>">
            <input type="text" name="search" class="search-input" placeholder="Cari nama, judul, atau isi ulasan..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-search"></i> Search
            </button>
            <?php if (!empty($search)): ?>
              <a href="?status=<?php echo $status; ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Clear
              </a>
            <?php endif; ?>
          </form>
        </div>
      </div>

      <!-- Reviews Table -->
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Rating</th>
            <th>Title</th>
            <th>Review</th>
            <th>Photo</th>
            <th>Recommend</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reviews)): ?>
            <tr>
              <td colspan="10" class="no-reviews">
                <i class="fas fa-comment-slash"></i>
                <h3>No reviews found</h3>
                <p><?php echo empty($search) ? 'No reviews available' : 'No reviews match your search criteria'; ?></p>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($reviews as $review): ?>
            <tr>
              <td><strong>#<?php echo $review['ID_Ulasan']; ?></strong></td>
              <td><?php echo htmlspecialchars($review['Nama_Pelanggan']); ?></td>
              <td>
                <div class="rating-stars">
                  <?php
                  for ($i = 1; $i <= 5; $i++) {
                      echo $i <= $review['Rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                  }
                  ?>
                  <br>
                  <small>(<?php echo $review['Rating']; ?>/5)</small>
                </div>
              </td>
              <td><?php echo htmlspecialchars($review['Judul_Ulasan']); ?></td>
              <td class="review-content-preview" title="<?php echo htmlspecialchars($review['Isi_Ulasan']); ?>">
                <?php echo htmlspecialchars(substr($review['Isi_Ulasan'], 0, 50)); ?>...
              </td>
              <td>
                <?php if (!empty($review['Foto_Ulasan'])): ?>
                  <img src="../assets/images/reviews/<?php echo $review['Foto_Ulasan']; ?>" 
                       alt="Review Photo" 
                       class="review-photo"
                       onclick="showReviewDetail(<?php echo $review['ID_Ulasan']; ?>)">
                <?php else: ?>
                  <div class="review-photo-placeholder">
                    <i class="fas fa-camera"></i>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($review['Rekomendasi'] === 'yes'): ?>
                  <span class="badge approved"><i class="fas fa-thumbs-up"></i> Yes</span>
                <?php else: ?>
                  <span class="badge rejected"><i class="fas fa-thumbs-down"></i> No</span>
                <?php endif; ?>
              </td>
              <td><?php echo date('M d, Y H:i', strtotime($review['Tanggal_Ulasan'])); ?></td>
              <td>
                <span class="badge <?php echo $review['Status']; ?>">
                  <?php echo ucfirst($review['Status']); ?>
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <button class="action-btn view" onclick="showReviewDetail(<?php echo $review['ID_Ulasan']; ?>)"
                         title="View Details">
                    <i class="fas fa-eye"></i>
                  </button>
                  
                  <?php if ($review['Status'] !== 'approved'): ?>
                  <form method="POST" style="display: inline;">
                    <input type="hidden" name="id_ulasan" value="<?php echo $review['ID_Ulasan']; ?>">
                    <button type="submit" name="approve_review" class="action-btn approve" title="Approve Review">
                      <i class="fas fa-check"></i>
                    </button>
                  </form>
                  <?php endif; ?>
                  
                  <?php if ($review['Status'] !== 'rejected'): ?>
                  <form method="POST" style="display: inline;">
                    <input type="hidden" name="id_ulasan" value="<?php echo $review['ID_Ulasan']; ?>">
                    <button type="submit" name="reject_review" class="action-btn reject" title="Reject Review">
                      <i class="fas fa-times"></i>
                    </button>
                  </form>
                  <?php endif; ?>
                  
                  <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this review?');">
                    <input type="hidden" name="id_ulasan" value="<?php echo $review['ID_Ulasan']; ?>">
                    <input type="hidden" name="foto_ulasan" value="<?php echo $review['Foto_Ulasan']; ?>">
                    <button type="submit" name="delete_review" class="action-btn delete" title="Delete Review">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Review Detail Modal -->
  <div class="modal" id="reviewModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fas fa-star"></i> Review Details</h3>
        <button class="modal-close" onclick="closeReviewModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <div id="reviewDetailContent">
          <!-- Content will be loaded by JavaScript -->
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeReviewModal()">Close</button>
      </div>
    </div>
  </div>

  <script>
    // Toggle sidebar for mobile
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('active');
    }

    // Review detail modal functions
    function showReviewDetail(reviewId) {
      // In a real application, you would fetch the review details via AJAX
      // For now, we'll show a message
      const modal = document.getElementById('reviewModal');
      const content = document.getElementById('reviewDetailContent');
      
      // Simulate loading content
      content.innerHTML = `
        <div class="review-detail">
          <div class="review-detail-header">
            <div class="reviewer-info">
              <h4>Loading...</h4>
              <div class="review-date">Please wait</div>
            </div>
            <div class="review-rating">
              <i class="fas fa-spinner fa-spin"></i>
            </div>
          </div>
          <div class="review-text-full">
            Loading review details...
          </div>
        </div>
      `;
      
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
      
      // Simulate AJAX call
      setTimeout(() => {
        // This would be replaced with actual AJAX call to get review details
        const reviewRow = document.querySelector(`tr td:first-child strong:contains("#${reviewId}")`)?.closest('tr');
        if (reviewRow) {
          const cells = reviewRow.querySelectorAll('td');
          const photoSrc = cells[5].querySelector('img')?.src || '';
          const hasPhoto = cells[5].querySelector('img');
          
          content.innerHTML = `
            <div class="review-detail">
              <div class="review-detail-header">
                <div class="reviewer-info">
                  <h4>${cells[1].textContent}</h4>
                  <div class="review-date">${cells[7].textContent}</div>
                </div>
                <div class="review-rating">
                  ${cells[2].innerHTML}
                </div>
              </div>
              
              <div>
                <strong>Title:</strong>
                <p>${cells[3].textContent}</p>
              </div>
              
              <div>
                <strong>Review:</strong>
                <p class="review-text-full">${cells[4].getAttribute('title')}</p>
              </div>
              
              <div>
                <strong>Recommendation:</strong>
                <p>${cells[6].textContent.includes('Yes') ? '👍 Recommends this place' : '👎 Does not recommend'}</p>
              </div>
              
              <div>
                <strong>Status:</strong>
                <span class="badge ${cells[8].querySelector('.badge').className.split(' ')[1]}">
                  ${cells[8].querySelector('.badge').textContent}
                </span>
              </div>
              
              ${hasPhoto ? `
              <div>
                <strong>Photo:</strong>
                <div style="text-align: center; margin-top: 1rem;">
                  <img src="${photoSrc}" alt="Review Photo" class="review-photo-large">
                </div>
              </div>
              ` : ''}
            </div>
          `;
        }
      }, 500);
    }

    function closeReviewModal() {
      const modal = document.getElementById('reviewModal');
      modal.classList.remove('show');
      document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
      const modal = document.getElementById('reviewModal');
      if (e.target === modal) {
        closeReviewModal();
      }
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
      if (window.innerWidth <= 768) {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.querySelector('.menu-toggle');
        if (!sidebar.contains(e.target) && !toggle.contains(e.target) && sidebar.classList.contains('active')) {
          toggleSidebar();
        }
      }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth > 768) {
        document.getElementById('sidebar').classList.remove('active');
      }
    });

    // Auto-hide messages after 5 seconds
    setTimeout(() => {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        alert.style.display = 'none';
      });
    }, 5000);
  </script>
</body>
</html>