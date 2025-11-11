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
$host = 'localhost';
$dbname = 'umkmk16';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Ambil data statistik
$total_products = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$total_customers = $pdo->query("SELECT COUNT(*) FROM pelanggan")->fetchColumn();
$total_transactions = $pdo->query("SELECT COUNT(*) FROM transaksi_penjualan")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(Total_Harga) FROM transaksi_penjualan")->fetchColumn() ?? 0;

// Ambil data transaksi terbaru
$recent_transactions = $pdo->query("
    SELECT t.*, p.Nama_Pelanggan, pr.Nama_Produk, pen.Nama_Karyawan 
    FROM transaksi_penjualan t 
    LEFT JOIN pelanggan p ON t.ID_Pelanggan = p.ID_Pelanggan 
    LEFT JOIN produk pr ON t.ID_Produk = pr.ID_Produk 
    LEFT JOIN penjual pen ON t.ID_Penjual = pen.ID_Penjual 
    ORDER BY t.Tanggal_Transaksi DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Ambil produk terlaris
$best_sellers = $pdo->query("
    SELECT pr.Nama_Produk, SUM(t.Jumlah_Barang) as total_terjual
    FROM transaksi_penjualan t
    JOIN produk pr ON t.ID_Produk = pr.ID_Produk
    GROUP BY t.ID_Produk
    ORDER BY total_terjual DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard | K SIXTEEN CAFE</title>
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
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 15px;
    }

    /* ADMIN DASHBOARD STYLES */
    .admin-dashboard {
      padding: 120px 0 50px;
      background: var(--cafe-bg);
      min-height: 100vh;
    }

    .admin-header {
      background: var(--cafe-dark);
      padding: 1rem 0;
      border-bottom: 2px solid var(--cafe-main);
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
    }

    .admin-nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .admin-welcome {
      color: var(--cafe-main);
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .admin-menu {
      display: flex;
      gap: 1rem;
      list-style: none;
    }

    .admin-menu a {
      color: var(--cafe-text);
      text-decoration: none;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .admin-menu a:hover,
    .admin-menu a.active {
      background: var(--cafe-main);
      color: var(--cafe-dark);
    }

    .logout-btn {
      background: rgba(255, 71, 87, 0.1);
      color: #ff4757;
      border: 1px solid #ff4757;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .logout-btn:hover {
      background: #ff4757;
      color: white;
    }

    /* SECTION STYLES */
    .section-title {
      text-align: center;
      font-size: 2.5rem;
      font-weight: 800;
      margin-bottom: 1rem;
      color: var(--cafe-main);
    }

    .section-subtitle {
      text-align: center;
      color: var(--cafe-text-light);
      margin-bottom: 3rem;
      font-size: 1.1rem;
    }

    /* STATS GRID */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
    }

    .stat-card {
      background: var(--cafe-card);
      padding: 2rem;
      border-radius: 15px;
      text-align: center;
      border: 1px solid var(--cafe-border);
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      border-color: var(--cafe-main);
      box-shadow: var(--cafe-shadow);
    }

    .stat-icon {
      font-size: 2.5rem;
      color: var(--cafe-main);
      margin-bottom: 1rem;
    }

    .stat-number {
      font-size: 2rem;
      font-weight: 800;
      color: var(--cafe-main);
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: var(--cafe-text-light);
    }

    /* DASHBOARD GRID */
    .dashboard-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 2rem;
    }

    .recent-table, .best-sellers {
      background: var(--cafe-card);
      border-radius: 15px;
      padding: 2rem;
      border: 1px solid var(--cafe-border);
    }

    .recent-table h3, .best-sellers h3 {
      color: var(--cafe-main);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    /* TABLE STYLES */
    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid var(--cafe-border);
    }

    th {
      color: var(--cafe-main);
      font-weight: 600;
    }

    /* ADMIN SECTIONS */
    .admin-section {
      display: none;
    }

    .admin-section.active {
      display: block;
    }

    /* BADGE STYLES */
    .badge {
      background: var(--cafe-main);
      color: var(--cafe-dark);
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .dashboard-grid {
        grid-template-columns: 1fr;
      }
      
      .admin-nav {
        flex-direction: column;
        gap: 1rem;
      }
      
      .admin-menu {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      .section-title {
        font-size: 2rem;
      }
    }

    @media (max-width: 480px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }
      
      .recent-table, .best-sellers {
        padding: 1rem;
      }
      
      th, td {
        padding: 0.75rem 0.5rem;
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>
  <!-- Admin Header -->
  <div class="admin-header">
    <div class="admin-nav">
      <div class="admin-welcome">
        <i class="fas fa-user-shield"></i> 
        <span>Welcome, <?php echo $_SESSION['admin_username']; ?> | K SIXTEEN CAFE Admin</span>
      </div>
      <ul class="admin-menu">
        <li><a href="#" class="nav-link active" data-section="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="#" class="nav-link" data-section="products"><i class="fas fa-coffee"></i> Menu</a></li>
        <li><a href="#" class="nav-link" data-section="transactions"><i class="fas fa-shopping-cart"></i> Transaksi</a></li>
        <li><a href="#" class="nav-link" data-section="customers"><i class="fas fa-users"></i> Pelanggan</a></li>
        <li><a href="#" class="nav-link" data-section="suppliers"><i class="fas fa-truck"></i> Supplier</a></li>
      </ul>
      <form method="POST" action="admin_logout.php">
        <button type="submit" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </form>
    </div>
  </div>

  <!-- Main Content -->
  <section class="admin-dashboard">
    <div class="container">
      <!-- Dashboard Section -->
      <div id="dashboard" class="admin-section active">
        <h2 class="section-title">Dashboard Admin</h2>
        <p class="section-subtitle">Kelola data dan monitor performa K SIXTEEN CAFE</p>
        
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-mug-hot"></i>
            </div>
            <div class="stat-number"><?php echo $total_products; ?></div>
            <div class="stat-label">Total Menu</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-number"><?php echo $total_customers; ?></div>
            <div class="stat-label">Pelanggan</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-number"><?php echo $total_transactions; ?></div>
            <div class="stat-label">Total Transaksi</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-number">Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></div>
            <div class="stat-label">Total Pendapatan</div>
          </div>
        </div>
        
        <div class="dashboard-grid">
          <div class="recent-table">
            <h3><i class="fas fa-history"></i> Transaksi Terbaru</h3>
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Pelanggan</th>
                  <th>Produk</th>
                  <th>Tanggal</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recent_transactions as $transaction): ?>
                <tr>
                  <td>#<?php echo $transaction['ID_Transaksi_Penjualan']; ?></td>
                  <td><?php echo $transaction['Nama_Pelanggan'] ?? 'Guest'; ?></td>
                  <td><?php echo $transaction['Nama_Produk']; ?></td>
                  <td><?php echo date('d/m/Y', strtotime($transaction['Tanggal_Transaksi'])); ?></td>
                  <td>Rp <?php echo number_format($transaction['Total_Harga'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          
          <div class="best-sellers">
            <h3><i class="fas fa-star"></i> Menu Terlaris</h3>
            <table>
              <thead>
                <tr>
                  <th>Menu</th>
                  <th>Terjual</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($best_sellers as $item): ?>
                <tr>
                  <td><?php echo $item['Nama_Produk']; ?></td>
                  <td><span class="badge"><?php echo $item['total_terjual']; ?> pcs</span></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Products Section -->
      <div id="products" class="admin-section">
        <h2 class="section-title">Manajemen Menu</h2>
        <p class="section-subtitle">Kelola menu makanan dan minuman K SIXTEEN CAFE</p>
        <?php 
        // Include products management dengan CSS internal
        $include_products = true;
        include 'admin_products.php'; 
        ?>
      </div>

      <!-- Transactions Section -->
      <div id="transactions" class="admin-section">
        <h2 class="section-title">Manajemen Transaksi</h2>
        <p class="section-subtitle">Lihat dan kelola semua transaksi penjualan</p>
        <?php include 'admin_transaction.php'; ?>
      </div>

      <!-- Customers Section -->
      <div id="customers" class="admin-section">
        <h2 class="section-title">Manajemen Pelanggan</h2>
        <p class="section-subtitle">Data pelanggan K SIXTEEN CAFE</p>
        <?php include 'admin_customer.php'; ?>
      </div>

      <!-- Suppliers Section -->
      <div id="suppliers" class="admin-section">
        <h2 class="section-title">Manajemen Supplier</h2>
        <p class="section-subtitle">Data supplier K SIXTEEN CAFE</p>
        <?php include 'admin_suppliers.php'; ?>
      </div>
    </div>
  </section>

  <script>
    // Navigation between sections
    document.querySelectorAll('.admin-menu .nav-link').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all links and sections
        document.querySelectorAll('.admin-menu .nav-link').forEach(l => l.classList.remove('active'));
        document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
        
        // Add active class to clicked link and corresponding section
        this.classList.add('active');
        const sectionId = this.getAttribute('data-section');
        document.getElementById(sectionId).classList.add('active');
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });
  </script>
</body>
</html>