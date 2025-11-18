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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Product
    if (isset($_POST['add_product'])) {
        $nama_produk = trim($_POST['nama_produk']);
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        $id_kategori = $_POST['id_kategori'];
        $id_supplier = $_POST['id_supplier'];
        
        // Handle file upload
        $gambar_produk = '';
        if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] === 0) {
            $uploadDir = 'assets/images/menu/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($_FILES['gambar_produk']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_' . strtolower(str_replace(' ', '_', $nama_produk)) . '.' . $fileExtension;
            $uploadFile = $uploadDir . $fileName;
            
            // Check file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($fileExtension), $allowedTypes)) {
                if (move_uploaded_file($_FILES['gambar_produk']['tmp_name'], $uploadFile)) {
                    $gambar_produk = $fileName;
                }
            }
        }
        
        if (empty($nama_produk) || empty($harga) || empty($stok)) {
            $_SESSION['error_message'] = "Semua field harus diisi!";
        } else {
            try {
                if ($gambar_produk) {
                    $stmt = $pdo->prepare("INSERT INTO produk (Nama_Produk, Harga, Stok, ID_Kategori, ID_Supplier, Gambar) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nama_produk, $harga, $stok, $id_kategori, $id_supplier, $gambar_produk]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO produk (Nama_Produk, Harga, Stok, ID_Kategori, ID_Supplier) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$nama_produk, $harga, $stok, $id_kategori, $id_supplier]);
                }
                $_SESSION['success_message'] = "✅ Menu <strong>$nama_produk</strong> berhasil ditambahkan!";
            } catch(PDOException $e) {
                $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
            }
        }
    }
    
    // Update Product
    if (isset($_POST['update_product'])) {
        $id_produk = $_POST['id_produk'];
        $nama_produk = trim($_POST['nama_produk']);
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        $id_kategori = $_POST['id_kategori'];
        
        // Handle file upload for update
        $gambar_produk = $_POST['current_gambar'] ?? '';
        if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] === 0) {
            $uploadDir = 'assets/images/menu/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($_FILES['gambar_produk']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_' . strtolower(str_replace(' ', '_', $nama_produk)) . '.' . $fileExtension;
            $uploadFile = $uploadDir . $fileName;
            
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($fileExtension), $allowedTypes)) {
                if (move_uploaded_file($_FILES['gambar_produk']['tmp_name'], $uploadFile)) {
                    // Delete old image if exists
                    if ($gambar_produk && file_exists($uploadDir . $gambar_produk)) {
                        unlink($uploadDir . $gambar_produk);
                    }
                    $gambar_produk = $fileName;
                }
            }
        }
        
        if (empty($nama_produk) || empty($harga) || empty($stok)) {
            $_SESSION['error_message'] = "Semua field harus diisi!";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE produk SET Nama_Produk = ?, Harga = ?, Stok = ?, ID_Kategori = ?, Gambar = ? WHERE ID_Produk = ?");
                if ($stmt->execute([$nama_produk, $harga, $stok, $id_kategori, $gambar_produk, $id_produk])) {
                    $_SESSION['success_message'] = "✅ Menu <strong>$nama_produk</strong> berhasil diupdate!";
                }
            } catch(PDOException $e) {
                $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
            }
        }
    }
    
    // Delete Product
    if (isset($_POST['delete_product'])) {
        $id_produk = $_POST['id_produk'];
        $nama_produk = $_POST['nama_produk'];
        $gambar_produk = $_POST['gambar_produk'] ?? '';
        
        try {
            // Delete image file if exists
            if ($gambar_produk) {
                $imagePath = 'assets/images/menu/' . $gambar_produk;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $stmt = $pdo->prepare("DELETE FROM produk WHERE ID_Produk = ?");
            if ($stmt->execute([$id_produk])) {
                $_SESSION['success_message'] = "✅ Menu <strong>$nama_produk</strong> berhasil dihapus!";
            }
        } catch(PDOException $e) {
            $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
        }
    }
    
    // Add Category
    if (isset($_POST['add_category'])) {
        $nama_kategori = trim($_POST['nama_kategori']);
        
        if (empty($nama_kategori)) {
            $_SESSION['error_message'] = "Nama kategori harus diisi!";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO kategori (Nama_Kategori) VALUES (?)");
                if ($stmt->execute([$nama_kategori])) {
                    $_SESSION['success_message'] = "✅ Kategori <strong>$nama_kategori</strong> berhasil ditambahkan!";
                }
            } catch(PDOException $e) {
                $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
            }
        }
    }
    
    // Add Supplier
    if (isset($_POST['add_supplier'])) {
        $nama_supplier = trim($_POST['nama_supplier']);
        $alamat = trim($_POST['alamat']);
        
        if (empty($nama_supplier)) {
            $_SESSION['error_message'] = "Nama supplier harus diisi!";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO supplier (Nama_Supplier, Alamat) VALUES (?, ?)");
                if ($stmt->execute([$nama_supplier, $alamat])) {
                    $_SESSION['success_message'] = "✅ Supplier <strong>$nama_supplier</strong> berhasil ditambahkan!";
                }
            } catch(PDOException $e) {
                $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
            }
        }
    }
    
    // Handle Review Actions
    if (isset($_POST['approve_review'])) {
        $id_ulasan = $_POST['id_ulasan'];
        try {
            $stmt = $pdo->prepare("UPDATE ulasan SET Status = 'approved' WHERE ID_Ulasan = ?");
            if ($stmt->execute([$id_ulasan])) {
                $_SESSION['success_message'] = "✅ Ulasan berhasil disetujui!";
            }
        } catch(PDOException $e) {
            $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['reject_review'])) {
        $id_ulasan = $_POST['id_ulasan'];
        try {
            $stmt = $pdo->prepare("UPDATE ulasan SET Status = 'rejected' WHERE ID_Ulasan = ?");
            if ($stmt->execute([$id_ulasan])) {
                $_SESSION['success_message'] = "✅ Ulasan berhasil ditolak!";
            }
        } catch(PDOException $e) {
            $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['delete_review'])) {
        $id_ulasan = $_POST['id_ulasan'];
        $foto_ulasan = $_POST['foto_ulasan'] ?? '';
        
        try {
            // Hapus file foto jika ada
            if ($foto_ulasan && file_exists('assets/images/reviews/' . $foto_ulasan)) {
                unlink('assets/images/reviews/' . $foto_ulasan);
            }
            
            $stmt = $pdo->prepare("DELETE FROM ulasan WHERE ID_Ulasan = ?");
            if ($stmt->execute([$id_ulasan])) {
                $_SESSION['success_message'] = "✅ Ulasan berhasil dihapus!";
            }
        } catch(PDOException $e) {
            $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
        }
    }
    
    // ===== ORDER MANAGEMENT ACTIONS =====
    
    // Complete Order
    if (isset($_POST['complete_order'])) {
        $order_id = $_POST['order_id'];
        try {
            $stmt = $pdo->prepare("UPDATE transaksi_penjualan SET order_status = 'completed' WHERE ID_Transaksi_Penjualan = ?");
            if ($stmt->execute([$order_id])) {
                $_SESSION['success_message'] = "✅ Order #$order_id berhasil diselesaikan!";
            }
        } catch(PDOException $e) {
            $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
        }
    }
    
    // Cancel Order
    if (isset($_POST['cancel_order'])) {
        $order_id = $_POST['order_id'];
        try {
            $stmt = $pdo->prepare("UPDATE transaksi_penjualan SET order_status = 'cancelled' WHERE ID_Transaksi_Penjualan = ?");
            if ($stmt->execute([$order_id])) {
                $_SESSION['success_message'] = "✅ Order #$order_id berhasil dibatalkan!";
            }
        } catch(PDOException $e) {
            $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
        }
    }
    
    // Assign Seller to Order
    if (isset($_POST['assign_seller'])) {
        $order_id = $_POST['order_id'];
        $seller_id = $_POST['seller_id'];
        
        try {
            $stmt = $pdo->prepare("UPDATE transaksi_penjualan SET ID_Penjual = ?, order_status = 'processing' WHERE ID_Transaksi_Penjualan = ?");
            if ($stmt->execute([$seller_id, $order_id])) {
                // Get seller name for success message
                $seller_stmt = $pdo->prepare("SELECT Nama_Karyawan FROM penjual WHERE ID_Penjual = ?");
                $seller_stmt->execute([$seller_id]);
                $seller = $seller_stmt->fetch(PDO::FETCH_ASSOC);
                
                $_SESSION['success_message'] = "✅ Seller {$seller['Nama_Karyawan']} berhasil ditugaskan ke Order #$order_id!";
            }
        } catch(PDOException $e) {
            $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
        }
    }
    
    // Update Order Status
    if (isset($_POST['update_order_status'])) {
        $order_id = $_POST['order_id'];
        $new_status = $_POST['order_status'];
        
        try {
            $stmt = $pdo->prepare("UPDATE transaksi_penjualan SET order_status = ? WHERE ID_Transaksi_Penjualan = ?");
            if ($stmt->execute([$new_status, $order_id])) {
                $_SESSION['success_message'] = "✅ Status Order #$order_id berhasil diubah menjadi " . ucfirst($new_status) . "!";
            }
        } catch(PDOException $e) {
            $_SESSION['error_message'] = "❌ Error: " . $e->getMessage();
        }
    }
    
    header('Location: ' . $_SERVER['PHP_SELF'] . (isset($_GET['section']) ? '?section=' . $_GET['section'] : ''));
    exit;
}

// Ambil data statistik dashboard
$total_products = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$total_customers = $pdo->query("SELECT COUNT(*) FROM pelanggan")->fetchColumn();
$total_transactions = $pdo->query("SELECT COUNT(*) FROM transaksi_penjualan")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(Total_Harga) FROM transaksi_penjualan")->fetchColumn() ?? 0;

// Revenue bulan ini
$current_month_revenue = $pdo->query("
    SELECT SUM(Total_Harga) 
    FROM transaksi_penjualan 
    WHERE MONTH(Tanggal_Transaksi) = MONTH(CURRENT_DATE()) 
    AND YEAR(Tanggal_Transaksi) = YEAR(CURRENT_DATE())
")->fetchColumn() ?? 0;

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
    SELECT pr.Nama_Produk, pr.Gambar, SUM(t.Jumlah_Barang) as total_terjual
    FROM transaksi_penjualan t
    JOIN produk pr ON t.ID_Produk = pr.ID_Produk
    GROUP BY t.ID_Produk
    ORDER BY total_terjual DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua produk untuk management
$products = $pdo->query("
    SELECT p.*, k.Nama_Kategori, s.Nama_Supplier 
    FROM produk p 
    LEFT JOIN kategori k ON p.ID_Kategori = k.ID_Kategori 
    LEFT JOIN supplier s ON p.ID_Supplier = s.ID_Supplier 
    ORDER BY p.ID_Kategori, p.Nama_Produk
")->fetchAll(PDO::FETCH_ASSOC);

// Ambil categories dan suppliers
$categories = $pdo->query("SELECT * FROM kategori")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT * FROM supplier")->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua transaksi
$all_transactions = $pdo->query("
    SELECT 
        t.*, 
        p.Nama_Pelanggan, 
        pr.Nama_Produk, 
        pr.Harga as Harga_Satuan,
        pen.Nama_Karyawan,
        k.Nama_Kategori
    FROM transaksi_penjualan t 
    LEFT JOIN pelanggan p ON t.ID_Pelanggan = p.ID_Pelanggan 
    LEFT JOIN produk pr ON t.ID_Produk = pr.ID_Produk 
    LEFT JOIN penjual pen ON t.ID_Penjual = pen.ID_Penjual 
    LEFT JOIN kategori k ON pr.ID_Kategori = k.ID_Kategori
    ORDER BY t.Tanggal_Transaksi DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua pelanggan
$customers = $pdo->query("SELECT * FROM pelanggan ORDER BY ID_Pelanggan")->fetchAll(PDO::FETCH_ASSOC);

// ===== REVIEWS MANAGEMENT DATA =====
// Ambil data statistik ulasan
$review_stats = $pdo->query("
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN Status = 'pending' THEN 1 END) as pending,
        COUNT(CASE WHEN Status = 'approved' THEN 1 END) as approved,
        COUNT(CASE WHEN Status = 'rejected' THEN 1 END) as rejected,
        AVG(Rating) as avg_rating
    FROM ulasan
")->fetch(PDO::FETCH_ASSOC);

// Ambil data ulasan berdasarkan filter
$review_status = $_GET['review_status'] ?? 'all';
$review_search = $_GET['review_search'] ?? '';

$review_whereConditions = [];
$review_params = [];

if ($review_status !== 'all') {
    $review_whereConditions[] = "Status = ?";
    $review_params[] = $review_status;
}

if (!empty($review_search)) {
    $review_whereConditions[] = "(Nama_Pelanggan LIKE ? OR Judul_Ulasan LIKE ? OR Isi_Ulasan LIKE ?)";
    $review_params[] = "%$review_search%";
    $review_params[] = "%$review_search%";
    $review_params[] = "%$review_search%";
}

$review_whereClause = $review_whereConditions ? "WHERE " . implode(" AND ", $review_whereConditions) : "";

$review_stmt = $pdo->prepare("
    SELECT * FROM ulasan 
    $review_whereClause 
    ORDER BY Tanggal_Ulasan DESC
");
$review_stmt->execute($review_params);
$reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);

// ===== ORDER MANAGEMENT DATA =====
$order_status = $_GET['order_status'] ?? 'all';
$order_where = "";
$order_params = [];

if ($order_status !== 'all') {
    $order_where = "WHERE tp.order_status = ?";
    $order_params[] = $order_status;
}

// Query untuk mendapatkan data orders yang dikelompokkan
$orders_query = "
    SELECT 
        tp.ID_Transaksi_Penjualan as order_id,
        p.Nama_Pelanggan as customer_name,
        tp.Nomor_Meja as table_number,
        COUNT(DISTINCT tp2.ID_Produk) as item_count,
        SUM(tp.Total_Harga) as total_amount,
        tp.Metode_Pembayaran as payment_method,
        tp.order_status,
        pen.Nama_Karyawan as seller_name,
        tp.Tanggal_Transaksi as order_date,
        GROUP_CONCAT(DISTINCT pr.Nama_Produk SEPARATOR ', ') as items,
        tp.transfer_proof
    FROM transaksi_penjualan tp
    LEFT JOIN pelanggan p ON tp.ID_Pelanggan = p.ID_Pelanggan
    LEFT JOIN penjual pen ON tp.ID_Penjual = pen.ID_Penjual
    LEFT JOIN produk pr ON tp.ID_Produk = pr.ID_Produk
    LEFT JOIN transaksi_penjualan tp2 ON tp.ID_Transaksi_Penjualan = tp2.ID_Transaksi_Penjualan
    $order_where
    GROUP BY tp.ID_Transaksi_Penjualan
    ORDER BY tp.Tanggal_Transaksi DESC
";

$orders_stmt = $pdo->prepare($orders_query);
$orders_stmt->execute($order_params);
$orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua seller
$sellers = $pdo->query("SELECT * FROM penjual")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard | K SIXTEEN CAFE</title>
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
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    .stat-card {
      background: var(--cafe-card);
      padding: 2rem;
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
      background: var(--cafe-main);
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
      font-size: 2.5rem;
      font-weight: 800;
      color: var(--cafe-main);
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: var(--cafe-text-light);
      font-size: 1rem;
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 2rem;
      margin-bottom: 3rem;
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
      font-size: 1.3rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--cafe-card);
      border-radius: 10px;
      overflow: hidden;
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

    .admin-section {
      display: none;
      animation: fadeIn 0.5s ease-in;
    }

    .admin-section.active {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .badge {
      background: var(--cafe-main);
      color: var(--cafe-dark);
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
    }

    .badge.warning {
      background: var(--warning);
      color: white;
    }

    .badge.danger {
      background: var(--danger);
      color: white;
    }

    .badge.success {
      background: var(--success);
      color: white;
    }

    .badge.info {
      background: var(--info);
      color: white;
    }

    .form-container {
      background: var(--cafe-card);
      padding: 2rem;
      border-radius: 15px;
      border: 1px solid var(--cafe-border);
      margin-bottom: 2rem;
      box-shadow: var(--cafe-shadow);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: var(--cafe-main);
      font-weight: 600;
      font-size: 0.95rem;
    }

    .form-group input,
    .form-group select,
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
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--cafe-main);
      box-shadow: 0 0 0 3px rgba(255, 214, 0, 0.1);
      background: rgba(255, 255, 255, 0.15);
    }

    .file-upload {
      border: 2px dashed var(--cafe-border);
      border-radius: 8px;
      padding: 2rem;
      text-align: center;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .file-upload:hover {
      border-color: var(--cafe-main);
      background: rgba(255, 214, 0, 0.05);
    }

    .file-upload i {
      font-size: 2rem;
      color: var(--cafe-main);
      margin-bottom: 1rem;
    }

    .file-upload input {
      display: none;
    }

    .image-preview {
      margin-top: 1rem;
      text-align: center;
    }

    .image-preview img {
      max-width: 200px;
      max-height: 150px;
      border-radius: 8px;
      border: 2px solid var(--cafe-border);
    }

    .current-image {
      margin-top: 1rem;
      text-align: center;
    }

    .current-image img {
      max-width: 150px;
      max-height: 100px;
      border-radius: 8px;
      border: 2px solid var(--cafe-border);
    }

    .product-image {
      width: 60px;
      height: 60px;
      border-radius: 8px;
      object-fit: cover;
      border: 2px solid var(--cafe-border);
    }

    .product-image-small {
      width: 40px;
      height: 40px;
      border-radius: 6px;
      object-fit: cover;
      border: 1px solid var(--cafe-border);
    }

    .form-actions {
      display: flex;
      gap: 1rem;
      margin-top: 1.5rem;
      flex-wrap: wrap;
    }

    .btn {
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      border: none;
      text-decoration: none;
      font-size: 1rem;
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
      border: 2px solid var(--cafe-border);
    }

    .btn-secondary:hover {
      border-color: var(--cafe-main);
      color: var(--cafe-main);
    }

    .btn-danger {
      background: var(--danger);
      color: white;
    }

    .btn-danger:hover {
      background: #ff3742;
      transform: translateY(-2px);
    }

    .btn-success {
      background: var(--success);
      color: white;
    }

    .btn-success:hover {
      background: #26c46a;
      transform: translateY(-2px);
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

    .action-btn.edit {
      background: var(--success);
    }

    .action-btn.delete {
      background: var(--danger);
    }

    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
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

    .table-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .admin-actions {
      margin-bottom: 2rem;
    }

    .payment-cash { background: var(--success); color: white; }
    .payment-qris { background: var(--info); color: white; }
    .payment-transfer { background: var(--warning); color: white; }

    /* Review Management Styles */
    .status-filter-btn {
      padding: 0.5rem 1rem;
      border: 1px solid var(--cafe-border);
      background: var(--cafe-bg);
      color: var(--cafe-text);
      border-radius: 50px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 0.9rem;
    }

    .status-filter-btn.active,
    .status-filter-btn:hover {
      background: var(--cafe-main);
      color: var(--cafe-dark);
    }

    .review-modal {
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

    .review-modal.show {
      display: flex;
    }

    .review-modal-content {
      background: var(--cafe-card);
      border-radius: 15px;
      width: 100%;
      max-width: 600px;
      border: 2px solid var(--cafe-main);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
      max-height: 90vh;
      overflow-y: auto;
    }

    .review-modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem;
      border-bottom: 1px solid var(--cafe-border);
    }

    .review-modal-header h3 {
      color: var(--cafe-main);
      font-size: 1.3rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .review-modal-close {
      background: none;
      border: none;
      color: var(--cafe-text);
      font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .review-modal-close:hover {
      color: var(--cafe-main);
    }

    .review-modal-body {
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
      object-fit: cover;
    }

    .review-text-full {
      color: var(--cafe-text-light);
      line-height: 1.6;
      white-space: pre-wrap;
    }

    .review-modal-footer {
      display: flex;
      justify-content: flex-end;
      padding: 1.5rem;
      border-top: 1px solid var(--cafe-border);
      gap: 1rem;
    }

    /* Order Management Styles */
    .order-preview {
      background: rgba(255, 255, 255, 0.05);
      padding: 1rem;
      border-radius: 8px;
      margin-top: 1rem;
      border: 1px solid var(--cafe-border);
    }

    .order-preview h4 {
      color: var(--cafe-main);
      margin-bottom: 0.5rem;
    }

    .order-preview p {
      margin: 0.25rem 0;
      color: var(--cafe-text-light);
    }

    .transfer-proof {
      max-width: 200px;
      max-height: 150px;
      border-radius: 8px;
      border: 2px solid var(--cafe-border);
      cursor: pointer;
    }

    .transfer-proof-large {
      max-width: 100%;
      max-height: 400px;
      border-radius: 8px;
      border: 2px solid var(--cafe-main);
    }

    /* Modal Styles */
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

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      padding: 1.5rem;
      border-top: 1px solid var(--cafe-border);
      gap: 1rem;
    }
    /* === BASE STYLES === */
.order-table-container {
    background: var(--cafe-secondary);
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
    overflow-x: auto;
}

.order-table {
    width: 100%;
    border-collapse: collapse;
    color: var(--cafe-text);
}

.order-table th {
    background: var(--cafe-primary);
    padding: 12px;
    text-align: left;
    color: white;
}

.order-table td {
    padding: 10px;
    border-bottom: 1px solid var(--cafe-accent);
}

.action-btn {
    padding: 8px 12px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    border-radius: 6px;
}

.assign-btn {
    background: var(--cafe-primary);
    color: white;
}
.assign-btn:hover {
    background: #b38600;
}

.delete-btn {
    background: #b30000;
    color: white;
}
.delete-btn:hover {
    background: #800000;
}

.details-btn {
    background: var(--cafe-accent);
    color: black;
}

.status-paid {
    color: #00cc44;
    font-weight: bold;
}

.status-unpaid {
    color: #ff3333;
    font-weight: bold;
}

/* === MODAL === */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
}

/* === FIX: Modal tidak transparan === */
.modal-content {
    background: #000 !important;     /* HITAM SOLID */
    margin: 100px auto;
    padding: 20px;
    width: 50%;
    border-radius: 12px;
    color: white;

    opacity: 1 !important;           /* Pastikan tidak tembus */
    backdrop-filter: none !important;
}

/* Tombol close */
.close-modal {
    float: right;
    font-size: 24px;
    cursor: pointer;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: var(--cafe-text);
}

/* Dropdown biar tidak transparan */
.form-group select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid var(--cafe-accent);
    background: #fff !important;     /* FIX */
    color: #000 !important;
}

.form-group select option {
    background: #fff !important;     /* FIX */
    color: #000 !important;
}

.save-btn {
    width: 100%;
    padding: 10px;
    background: var(--cafe-primary);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}
.save-btn:hover {
    background: #b38600;
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
      .dashboard-grid {
        grid-template-columns: 1fr;
      }
      
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
      
      .form-row {
        grid-template-columns: 1fr;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
      }
      
      .table-header {
        flex-direction: column;
        align-items: flex-start;
      }
    }

    @media (max-width: 480px) {
      .recent-table, .best-sellers {
        padding: 1rem;
      }
      
      th, td {
        padding: 0.75rem 0.5rem;
        font-size: 0.9rem;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .stat-card {
        padding: 1.5rem;
      }
      
      .stat-number {
        font-size: 2rem;
      }
      
      .btn {
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
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
          <img src="assets/images/logo.jpg" alt="K SIXTEEN CAFE" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiBmaWxsPSIjRkZENjAwIi8+Cjx0ZXh0IHg9IjI1IiB5PSIzMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE4IiBmb250LXdlaWdodD0iYm9sZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iIzExMTExMSI+S1M8L3RleHQ+Cjwvc3ZnPgo='">
        </div>
        <div class="sidebar-logo-text">K SIXTEEN CAFE</div>
      </div>
      <div class="admin-welcome">
        Welcome, <strong><?php echo $_SESSION['admin_username'] ?? 'Admin'; ?></strong>
      </div>
    </div>

    <div class="sidebar-menu">
      <a href="#" class="menu-item active" data-section="dashboard">
        <i class="fas fa-tachometer-alt"></i>
        <span class="menu-text">Dashboard</span>
      </a>
      <a href="#" class="menu-item" data-section="orders">
        <i class="fas fa-shopping-bag"></i>
        <span class="menu-text">Order Management</span>
      </a>
      <a href="#" class="menu-item" data-section="products">
        <i class="fas fa-coffee"></i>
        <span class="menu-text">Menu Management</span>
      </a>
      <a href="#" class="menu-item" data-section="transactions">
        <i class="fas fa-receipt"></i>
        <span class="menu-text">Transactions</span>
      </a>
      <a href="#" class="menu-item" data-section="customers">
        <i class="fas fa-users"></i>
        <span class="menu-text">Customers</span>
      </a>
      <a href="#" class="menu-item" data-section="suppliers">
        <i class="fas fa-truck"></i>
        <span class="menu-text">Suppliers</span>
      </a>
      <a href="#" class="menu-item" data-section="categories">
        <i class="fas fa-tags"></i>
        <span class="menu-text">Categories</span>
      </a>
      <a href="#" class="menu-item" data-section="reviews">
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
        <button type="submit" class="btn btn-danger" style="width: 100%;">
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

      <!-- Dashboard Section -->
      <div id="dashboard" class="admin-section active">
        <div class="main-header">
          <div>
            <h1 class="page-title">📊 Dashboard Overview</h1>
            <p class="page-subtitle">Monitor and manage K SIXTEEN CAFE performance</p>
          </div>
        </div>
        
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-mug-hot"></i>
            </div>
            <div class="stat-number"><?php echo $total_products; ?></div>
            <div class="stat-label">Total Menu Items</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-number"><?php echo $total_customers; ?></div>
            <div class="stat-label">Registered Customers</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-number"><?php echo $total_transactions; ?></div>
            <div class="stat-label">Total Transactions</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-number">Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></div>
            <div class="stat-label">Total Revenue</div>
          </div>
        </div>
        
        <div class="dashboard-grid">
          <div class="recent-table">
            <h3><i class="fas fa-history"></i> Recent Transactions</h3>
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer</th>
                  <th>Product</th>
                  <th>Date</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recent_transactions)): ?>
                  <tr>
                    <td colspan="5" style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                      <i class="fas fa-receipt"></i> No transactions yet
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($recent_transactions as $transaction): ?>
                  <tr>
                    <td><strong>#<?php echo $transaction['ID_Transaksi_Penjualan']; ?></strong></td>
                    <td><?php echo $transaction['Nama_Pelanggan'] ?? 'Guest'; ?></td>
                    <td><?php echo $transaction['Nama_Produk']; ?></td>
                    <td><?php echo date('M d, Y', strtotime($transaction['Tanggal_Transaksi'])); ?></td>
                    <td><span class="badge success">Rp <?php echo number_format($transaction['Total_Harga'], 0, ',', '.'); ?></span></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          
          <div class="best-sellers">
            <h3><i class="fas fa-star"></i> Best Sellers</h3>
            <table>
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Image</th>
                  <th>Sold</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($best_sellers)): ?>
                  <tr>
                    <td colspan="3" style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                      <i class="fas fa-chart-bar"></i> No sales data
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($best_sellers as $item): ?>
                  <tr>
                    <td><?php echo $item['Nama_Produk']; ?></td>
                    <td>
                      <?php if ($item['Gambar']): ?>
                        <img src="assets/images/menu/<?php echo $item['Gambar']; ?>" alt="<?php echo $item['Nama_Produk']; ?>" class="product-image-small" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjMmQyZDJkIi8+Cjx0ZXh0IHg9IjIwIiB5PSIyNCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjYjBiMGIwIj5JbWFnZTwvdGV4dD4KPC9zdmc+Cg=='">
                      <?php else: ?>
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjMmQyZDJkIi8+Cjx0ZXh0IHg9IjIwIiB5PSIyNCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjYjBiMGIwIj5JbWFnZTwvdGV4dD4KPC9zdmc+Cg==" alt="Default" class="product-image-small">
                      <?php endif; ?>
                    </td>
                    <td><span class="badge"><?php echo $item['total_terjual']; ?> pcs</span></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Order Management Section -->
      <div id="orders" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">📦 Order Management</h1>
            <p class="page-subtitle">Kelola status pesanan dan tentukan seller yang melayani</p>
          </div>
        </div>

        <!-- Order Status Filter -->
        <div class="form-container">
            <form method="GET" id="orderFilterForm">
                <input type="hidden" name="section" value="orders">
                <div class="form-row">
                    <div class="form-group">
                        <label>Filter Status Order</label>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <button type="button" class="status-filter-btn <?php echo $order_status === 'all' ? 'active' : ''; ?>" data-status="all">All Orders</button>
                            <button type="button" class="status-filter-btn <?php echo $order_status === 'pending' ? 'active' : ''; ?>" data-status="pending">Pending</button>
                            <button type="button" class="status-filter-btn <?php echo $order_status === 'processing' ? 'active' : ''; ?>" data-status="processing">Processing</button>
                            <button type="button" class="status-filter-btn <?php echo $order_status === 'completed' ? 'active' : ''; ?>" data-status="completed">Completed</button>
                            <button type="button" class="status-filter-btn <?php echo $order_status === 'cancelled' ? 'active' : ''; ?>" data-status="cancelled">Cancelled</button>
                        </div>
                        <input type="hidden" name="order_status" id="orderStatus" value="<?php echo $order_status; ?>">
                    </div>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="recent-table">
            <h3><i class="fas fa-list"></i> Daftar Pesanan</h3>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Meja</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th>Seller</th>
                        <th>Tanggal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                                <i class="fas fa-shopping-bag"></i> No orders found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?></td>
                            <td><?php echo htmlspecialchars($order['table_number'] ?? '-'); ?></td>
                            <td>
                                <span title="<?php echo htmlspecialchars($order['items']); ?>">
                                    <?php echo $order['item_count']; ?> items
                                </span>
                            </td>
                            <td>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="badge <?php 
                                    echo $order['payment_method'] === 'Cash' ? 'success' : 
                                         ($order['payment_method'] === 'Transfer' ? 'warning' : 'info'); 
                                ?>">
                                    <?php echo $order['payment_method']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php 
                                    echo $order['order_status'] === 'completed' ? 'success' : 
                                         ($order['order_status'] === 'processing' ? 'warning' : 
                                         ($order['order_status'] === 'cancelled' ? 'danger' : 'info')); 
                                ?>">
                                    <?php echo ucfirst($order['order_status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($order['seller_name']): ?>
                                    <?php echo htmlspecialchars($order['seller_name']); ?>
                                <?php else: ?>
                                    <span class="badge warning">Belum ditugaskan</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit" onclick="showOrderDetails(<?php echo $order['order_id']; ?>)" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn edit" onclick="assignSeller(<?php echo $order['order_id']; ?>)" title="Assign Seller" style="background: var(--info);">
                                        <i class="fas fa-user-tag"></i>
                                    </button>
                                    <?php if ($order['order_status'] === 'pending' || $order['order_status'] === 'processing'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" name="complete_order" class="action-btn edit" title="Complete Order" style="background: var(--success);">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($order['order_status'] === 'pending'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" name="cancel_order" class="action-btn delete" title="Cancel Order">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
      </div>

      <!-- Products Section -->
      <div id="products" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">☕ Menu Management</h1>
            <p class="page-subtitle">Manage your cafe menu items</p>
          </div>
          <button class="btn btn-primary" onclick="showAddForm()">
            <i class="fas fa-plus"></i> Add New Menu
          </button>
        </div>

        <!-- Add Product Form -->
        <div id="addProductForm" class="form-container" style="display: none;">
          <h3 style="color: var(--cafe-main); margin-bottom: 1.5rem;">➕ Add New Menu Item</h3>
          <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
              <div class="form-group">
                <label for="nama_produk">Product Name *</label>
                <input type="text" id="nama_produk" name="nama_produk" required>
              </div>
              <div class="form-group">
                <label for="harga">Price (Rp) *</label>
                <input type="number" id="harga" name="harga" min="0" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="stok">Stock *</label>
                <input type="number" id="stok" name="stok" min="0" required>
              </div>
              <div class="form-group">
                <label for="id_kategori">Category *</label>
                <select id="id_kategori" name="id_kategori" required>
                  <option value="">Select Category</option>
                  <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['ID_Kategori']; ?>"><?php echo $category['Nama_Kategori']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="id_supplier">Supplier</label>
                <select id="id_supplier" name="id_supplier">
                  <option value="">Select Supplier</option>
                  <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo $supplier['ID_Supplier']; ?>"><?php echo $supplier['Nama_Supplier']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Product Image</label>
                <div class="file-upload" onclick="document.getElementById('add_gambar_produk').click()">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Click to upload image</p>
                  <input type="file" id="add_gambar_produk" name="gambar_produk" accept="image/*" onchange="previewAddImage(this)">
                </div>
                <div id="addImagePreview" class="image-preview"></div>
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" name="add_product" class="btn btn-success">
                <i class="fas fa-save"></i> Save Product
              </button>
              <button type="button" class="btn btn-secondary" onclick="hideAddForm()">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </form>
        </div>

        <!-- Edit Product Form -->
        <div id="editProductForm" class="form-container" style="display: none;">
          <h3 style="color: var(--cafe-main); margin-bottom: 1.5rem;">✏️ Edit Menu Item</h3>
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" id="edit_id_produk" name="id_produk">
            <input type="hidden" id="edit_current_gambar" name="current_gambar">
            <div class="form-row">
              <div class="form-group">
                <label for="edit_nama_produk">Product Name *</label>
                <input type="text" id="edit_nama_produk" name="nama_produk" required>
              </div>
              <div class="form-group">
                <label for="edit_harga">Price (Rp) *</label>
                <input type="number" id="edit_harga" name="harga" min="0" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="edit_stok">Stock *</label>
                <input type="number" id="edit_stok" name="stok" min="0" required>
              </div>
              <div class="form-group">
                <label for="edit_id_kategori">Category *</label>
                <select id="edit_id_kategori" name="id_kategori" required>
                  <option value="">Select Category</option>
                  <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['ID_Kategori']; ?>"><?php echo $category['Nama_Kategori']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label>Product Image</label>
              <div id="editCurrentImage" class="current-image"></div>
              <div class="file-upload" onclick="document.getElementById('edit_gambar_produk').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Click to upload new image</p>
                <input type="file" id="edit_gambar_produk" name="gambar_produk" accept="image/*" onchange="previewEditImage(this)">
              </div>
              <div id="editImagePreview" class="image-preview"></div>
            </div>
            <div class="form-actions">
              <button type="submit" name="update_product" class="btn btn-success">
                <i class="fas fa-save"></i> Update Product
              </button>
              <button type="button" class="btn btn-secondary" onclick="hideEditForm()">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </form>
        </div>

        <!-- Products Table -->
        <div class="recent-table">
          <h3><i class="fas fa-list"></i> All Menu Items</h3>
          <table>
            <thead>
              <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($products)): ?>
                <tr>
                  <td colspan="7" style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                    <i class="fas fa-coffee"></i> No products found
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                  <td>
                    <?php if ($product['Gambar']): ?>
                      <img src="assets/images/menu/<?php echo $product['Gambar']; ?>" alt="<?php echo $product['Nama_Produk']; ?>" class="product-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjMmQyZDJkIi8+Cjx0ZXh0IHg9IjMwIiB5PSIzNSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjYjBiMGIwIj5JbWFnZTwvdGV4dD4KPC9zdmc+Cg=='">
                    <?php else: ?>
                      <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjMmQyZDJkIi8+Cjx0ZXh0IHg9IjMwIiB5PSIzNSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjYjBiMGIwIj5JbWFnZTwvdGV4dD4KPC9zdmc+Cg==" alt="Default" class="product-image">
                    <?php endif; ?>
                  </td>
                  <td><strong><?php echo $product['Nama_Produk']; ?></strong></td>
                  <td>Rp <?php echo number_format($product['Harga'], 0, ',', '.'); ?></td>
                  <td>
                    <span class="badge <?php echo $product['Stok'] > 10 ? 'success' : ($product['Stok'] > 0 ? 'warning' : 'danger'); ?>">
                      <?php echo $product['Stok']; ?> pcs
                    </span>
                  </td>
                  <td><?php echo $product['Nama_Kategori']; ?></td>
                  <td><?php echo $product['Nama_Supplier'] ?? '-'; ?></td>
                  <td>
                    <div class="action-buttons">
                      <button class="action-btn edit" onclick="editProduct(<?php echo $product['ID_Produk']; ?>, '<?php echo addslashes($product['Nama_Produk']); ?>', <?php echo $product['Harga']; ?>, <?php echo $product['Stok']; ?>, <?php echo $product['ID_Kategori']; ?>, '<?php echo $product['Gambar']; ?>')">
                        <i class="fas fa-edit"></i>
                      </button>
                      <form method="POST" style="display: inline;">
                        <input type="hidden" name="id_produk" value="<?php echo $product['ID_Produk']; ?>">
                        <input type="hidden" name="nama_produk" value="<?php echo $product['Nama_Produk']; ?>">
                        <input type="hidden" name="gambar_produk" value="<?php echo $product['Gambar']; ?>">
                        <button type="submit" name="delete_product" class="action-btn delete" onclick="return confirm('Are you sure you want to delete <?php echo addslashes($product['Nama_Produk']); ?>?')">
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

      <!-- Transactions Section -->
      <div id="transactions" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">💰 Transactions</h1>
            <p class="page-subtitle">View and manage all transactions</p>
          </div>
        </div>

        <div class="recent-table">
          <h3><i class="fas fa-receipt"></i> All Transactions</h3>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Date</th>
                <th>Seller</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($all_transactions)): ?>
                <tr>
                  <td colspan="8" style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                    <i class="fas fa-receipt"></i> No transactions found
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($all_transactions as $transaction): ?>
                <tr>
                  <td><strong>#<?php echo $transaction['ID_Transaksi_Penjualan']; ?></strong></td>
                  <td><?php echo $transaction['Nama_Pelanggan'] ?? 'Guest'; ?></td>
                  <td><?php echo $transaction['Nama_Produk']; ?></td>
                  <td><?php echo $transaction['Nama_Kategori']; ?></td>
                  <td><?php echo $transaction['Jumlah_Barang']; ?> pcs</td>
                  <td><span class="badge success">Rp <?php echo number_format($transaction['Total_Harga'], 0, ',', '.'); ?></span></td>
                  <td><?php echo date('M d, Y H:i', strtotime($transaction['Tanggal_Transaksi'])); ?></td>
                  <td><?php echo $transaction['Nama_Karyawan'] ?? '-'; ?></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Customers Section -->
      <div id="customers" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">👥 Customers</h1>
            <p class="page-subtitle">Customer information from transactions</p>
          </div>
        </div>

        <div class="recent-table">
          <h3><i class="fas fa-users"></i> Customer Data from Transactions</h3>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Total Transactions</th>
                <th>Last Order Date</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // Get unique customers from transactions
              $customerTransactions = $pdo->query("
                  SELECT 
                      p.ID_Pelanggan,
                      p.Nama_Pelanggan,
                      COUNT(t.ID_Transaksi_Penjualan) as total_orders,
                      MAX(t.Tanggal_Transaksi) as last_order
                  FROM pelanggan p 
                  LEFT JOIN transaksi_penjualan t ON p.ID_Pelanggan = t.ID_Pelanggan 
                  GROUP BY p.ID_Pelanggan, p.Nama_Pelanggan
                  ORDER BY total_orders DESC
              ")->fetchAll(PDO::FETCH_ASSOC);
              
              if (empty($customerTransactions)): ?>
                  <tr>
                      <td colspan="4" style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                          <i class="fas fa-users"></i> No customer data found
                      </td>
                  </tr>
              <?php else: ?>
                  <?php foreach ($customerTransactions as $customer): ?>
                  <tr>
                      <td><strong>#<?php echo $customer['ID_Pelanggan']; ?></strong></td>
                      <td><?php echo $customer['Nama_Pelanggan']; ?></td>
                      <td>
                          <span class="badge <?php echo $customer['total_orders'] > 0 ? 'success' : 'no-transactions'; ?>">
                              <?php echo $customer['total_orders']; ?> orders
                          </span>
                      </td>
                      <td>
                          <?php echo $customer['last_order'] ? date('M d, Y', strtotime($customer['last_order'])) : 'No orders'; ?>
                      </td>
                  </tr>
                  <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Suppliers Section -->
      <div id="suppliers" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">🚚 Suppliers</h1>
            <p class="page-subtitle">Manage your suppliers</p>
          </div>
          <button class="btn btn-primary" onclick="showAddSupplierForm()">
            <i class="fas fa-plus"></i> Add Supplier
          </button>
        </div>

        <!-- Add Supplier Form -->
        <div id="addSupplierForm" class="form-container" style="display: none;">
          <h3 style="color: var(--cafe-main); margin-bottom: 1.5rem;">➕ Add New Supplier</h3>
          <form method="POST">
            <div class="form-row">
              <div class="form-group">
                <label for="nama_supplier">Supplier Name *</label>
                <input type="text" id="nama_supplier" name="nama_supplier" required>
              </div>
              <div class="form-group">
                <label for="alamat">Address</label>
                <textarea id="alamat" name="alamat" rows="3"></textarea>
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" name="add_supplier" class="btn btn-success">
                <i class="fas fa-save"></i> Save Supplier
              </button>
              <button type="button" class="btn btn-secondary" onclick="hideAddSupplierForm()">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </form>
        </div>

        <!-- Suppliers Table -->
        <div class="recent-table">
          <h3><i class="fas fa-truck"></i> All Suppliers</h3>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($suppliers)): ?>
                <tr>
                  <td colspan="4" style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                    <i class="fas fa-truck"></i> No suppliers found
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($suppliers as $supplier): ?>
                <tr>
                  <td><strong>#<?php echo $supplier['ID_Supplier']; ?></strong></td>
                  <td><?php echo $supplier['Nama_Supplier']; ?></td>
                  <td><?php echo $supplier['Alamat'] ?? '-'; ?></td>
                  <td>
                    <div class="action-buttons">
                      <button class="action-btn delete" onclick="if(confirm('Delete <?php echo addslashes($supplier['Nama_Supplier']); ?>?')) { /* Add delete functionality here */ }">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Categories Section -->
      <div id="categories" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">🏷️ Categories</h1>
            <p class="page-subtitle">Manage product categories</p>
          </div>
          <button class="btn btn-primary" onclick="showAddCategoryForm()">
            <i class="fas fa-plus"></i> Add Category
          </button>
        </div>

        <!-- Add Category Form -->
        <div id="addCategoryForm" class="form-container" style="display: none;">
          <h3 style="color: var(--cafe-main); margin-bottom: 1.5rem;">➕ Add New Category</h3>
          <form method="POST">
            <div class="form-group">
              <label for="nama_kategori">Category Name *</label>
              <input type="text" id="nama_kategori" name="nama_kategori" required>
            </div>
            <div class="form-actions">
              <button type="submit" name="add_category" class="btn btn-success">
                <i class="fas fa-save"></i> Save Category
              </button>
              <button type="button" class="btn btn-secondary" onclick="hideAddCategoryForm()">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </form>
        </div>

        <!-- Categories Table -->
        <div class="recent-table">
          <h3><i class="fas fa-tags"></i> All Categories</h3>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($categories)): ?>
                <tr>
                  <td colspan="3" style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                    <i class="fas fa-tags"></i> No categories found
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($categories as $category): ?>
                <tr>
                  <td><strong>#<?php echo $category['ID_Kategori']; ?></strong></td>
                  <td><?php echo $category['Nama_Kategori']; ?></td>
                  <td>
                    <div class="action-buttons">
                      <button class="action-btn delete" onclick="if(confirm('Delete <?php echo addslashes($category['Nama_Kategori']); ?>?')) { /* Add delete functionality here */ }">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Reviews Section -->
      <div id="reviews" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">⭐ Reviews Management</h1>
            <p class="page-subtitle">Kelola ulasan dan testimoni pelanggan</p>
          </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-comments"></i>
            </div>
            <div class="stat-number"><?php echo $review_stats['total']; ?></div>
            <div class="stat-label">Total Reviews</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number"><?php echo $review_stats['pending']; ?></div>
            <div class="stat-label">Pending Review</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number"><?php echo $review_stats['approved']; ?></div>
            <div class="stat-label">Approved</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-number"><?php echo $review_stats['rejected']; ?></div>
            <div class="stat-label">Rejected</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-star"></i>
            </div>
            <div class="stat-number"><?php echo round($review_stats['avg_rating'], 1); ?></div>
            <div class="stat-label">Average Rating</div>
          </div>
        </div>

        <!-- Filters -->
        <div class="form-container">
          <form method="GET" id="reviewFilterForm">
            <input type="hidden" name="section" value="reviews">
            <div class="form-row">
              <div class="form-group">
                <label>Status Filter</label>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                  <button type="button" class="status-filter-btn <?php echo $review_status === 'all' ? 'active' : ''; ?>" data-status="all">All</button>
                  <button type="button" class="status-filter-btn <?php echo $review_status === 'pending' ? 'active' : ''; ?>" data-status="pending">Pending</button>
                  <button type="button" class="status-filter-btn <?php echo $review_status === 'approved' ? 'active' : ''; ?>" data-status="approved">Approved</button>
                  <button type="button" class="status-filter-btn <?php echo $review_status === 'rejected' ? 'active' : ''; ?>" data-status="rejected">Rejected</button>
                </div>
                <input type="hidden" name="review_status" id="reviewStatus" value="<?php echo $review_status; ?>">
              </div>
              
              <div class="form-group">
                <label for="review_search">Search Reviews</label>
                <div style="display: flex; gap: 0.5rem;">
                  <input type="text" id="review_search" name="review_search" class="form-control" placeholder="Cari nama, judul, atau isi ulasan..." value="<?php echo htmlspecialchars($review_search); ?>" style="flex: 1;">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                  </button>
                  <?php if (!empty($review_search)): ?>
                    <a href="?section=reviews&review_status=<?php echo $review_status; ?>" class="btn btn-secondary">
                      <i class="fas fa-times"></i> Clear
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Reviews Table -->
        <div class="recent-table">
          <h3><i class="fas fa-list"></i> Customer Reviews</h3>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Rating</th>
                <th>Title</th>
                <th>Review Preview</th>
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
                  <td colspan="10" style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                    <i class="fas fa-comment-slash"></i> No reviews found
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                <tr>
                  <td><strong>#<?php echo $review['ID_Ulasan']; ?></strong></td>
                  <td><?php echo htmlspecialchars($review['Nama_Pelanggan']); ?></td>
                  <td>
                    <div style="color: var(--cafe-main);">
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
                  <td title="<?php echo htmlspecialchars($review['Isi_Ulasan']); ?>">
                    <?php echo htmlspecialchars(substr($review['Isi_Ulasan'], 0, 50)); ?>...
                  </td>
                  <td>
                    <?php if (!empty($review['Foto_Ulasan'])): ?>
                      <img src="assets/images/reviews/<?php echo $review['Foto_Ulasan']; ?>" 
                           alt="Review Photo" 
                           class="product-image"
                           onclick="showReviewDetail(<?php echo $review['ID_Ulasan']; ?>)"
                           style="cursor: pointer;">
                    <?php else: ?>
                      <div style="width: 60px; height: 60px; border-radius: 8px; background: var(--cafe-bg); display: flex; align-items: center; justify-content: center; color: var(--cafe-text-light); border: 2px dashed var(--cafe-border);">
                        <i class="fas fa-camera"></i>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($review['Rekomendasi'] === 'yes'): ?>
                      <span class="badge success"><i class="fas fa-thumbs-up"></i> Yes</span>
                    <?php else: ?>
                      <span class="badge danger"><i class="fas fa-thumbs-down"></i> No</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo date('M d, Y H:i', strtotime($review['Tanggal_Ulasan'])); ?></td>
                  <td>
                    <span class="badge <?php 
                      echo $review['Status'] === 'pending' ? 'warning' : 
                           ($review['Status'] === 'approved' ? 'success' : 'danger'); 
                    ?>">
                      <?php echo ucfirst($review['Status']); ?>
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button class="action-btn edit" onclick="showReviewDetail(<?php echo $review['ID_Ulasan']; ?>)" title="View Details">
                        <i class="fas fa-eye"></i>
                      </button>
                      
                      <?php if ($review['Status'] !== 'approved'): ?>
                      <form method="POST" style="display: inline;">
                        <input type="hidden" name="id_ulasan" value="<?php echo $review['ID_Ulasan']; ?>">
                        <button type="submit" name="approve_review" class="action-btn edit" title="Approve Review" style="background: var(--success);">
                          <i class="fas fa-check"></i>
                        </button>
                      </form>
                      <?php endif; ?>
                      
                      <?php if ($review['Status'] !== 'rejected'): ?>
                      <form method="POST" style="display: inline;">
                        <input type="hidden" name="id_ulasan" value="<?php echo $review['ID_Ulasan']; ?>">
                        <button type="submit" name="reject_review" class="action-btn delete" title="Reject Review">
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
    </div>
  </div>

  <!-- Review Detail Modal -->
  <div class="review-modal" id="reviewModal">
    <div class="review-modal-content">
      <div class="review-modal-header">
        <h3><i class="fas fa-star"></i> Review Details</h3>
        <button class="review-modal-close" onclick="closeReviewModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="review-modal-body">
        <div id="reviewModalBody">
          <!-- Content will be loaded by JavaScript -->
        </div>
      </div>
      <div class="review-modal-footer">
        <button class="btn btn-secondary" onclick="closeReviewModal()">Close</button>
      </div>
    </div>
  </div>

  <!-- Assign Seller Modal -->
  <div class="modal" id="assignSellerModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fas fa-user-tag"></i> Assign Seller to Order</h3>
        <button class="modal-close" onclick="closeAssignSellerModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <form method="POST" id="assignSellerForm">
        <input type="hidden" name="order_id" id="assign_order_id">
        <div class="modal-body">
          <div class="form-group">
            <label for="seller_id">Pilih Seller:</label>
            <select id="seller_id" name="seller_id" required>
              <option value="">Pilih Seller</option>
              <?php foreach ($sellers as $seller): ?>
                <option value="<?php echo $seller['ID_Penjual']; ?>">
                  <?php echo htmlspecialchars($seller['Nama_Karyawan']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="order-preview" id="orderPreview">
            <!-- Order preview will be loaded here -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeAssignSellerModal()">Batal</button>
          <button type="submit" name="assign_seller" class="btn btn-primary">
            <i class="fas fa-user-tag"></i> Assign Seller
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Order Details Modal -->
  <div class="modal" id="orderDetailsModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fas fa-info-circle"></i> Order Details</h3>
        <button class="modal-close" onclick="closeOrderDetailsModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <div id="orderDetailsBody">
          <!-- Order details will be loaded here -->
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeOrderDetailsModal()">Close</button>
      </div>
    </div>
  </div>

  <script>
    // Navigation between sections
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize first section
        showSection('dashboard');
        
        // Add click events to all menu items
        document.querySelectorAll('.menu-item[data-section]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const sectionId = this.getAttribute('data-section');
                showSection(sectionId);
                
                // Close sidebar on mobile
                if (window.innerWidth <= 768) {
                    toggleSidebar();
                }
            });
        });

        // Review filter functionality
        const statusFilterBtns = document.querySelectorAll('.status-filter-btn');
        const reviewStatusInput = document.getElementById('reviewStatus');
        const reviewFilterForm = document.getElementById('reviewFilterForm');
        
        if (statusFilterBtns && reviewStatusInput && reviewFilterForm) {
            statusFilterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const status = this.getAttribute('data-status');
                    
                    // Update active button
                    statusFilterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update hidden input
                    reviewStatusInput.value = status;
                    
                    // Submit form
                    reviewFilterForm.submit();
                });
            });
        }

        // Order filter functionality
        const orderFilterBtns = document.querySelectorAll('#orders .status-filter-btn');
        const orderStatusInput = document.getElementById('orderStatus');
        const orderFilterForm = document.getElementById('orderFilterForm');
        
        if (orderFilterBtns && orderStatusInput && orderFilterForm) {
            orderFilterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const status = this.getAttribute('data-status');
                    
                    // Update active button
                    orderFilterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update hidden input
                    orderStatusInput.value = status;
                    
                    // Submit form
                    orderFilterForm.submit();
                });
            });
        }
    });

    function showSection(sectionId) {
        console.log('Showing section:', sectionId);
        
        // Remove active class from all links and sections
        document.querySelectorAll('.menu-item').forEach(l => l.classList.remove('active'));
        document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
        
        // Add active class to clicked link and corresponding section
        const activeLink = document.querySelector(`.menu-item[data-section="${sectionId}"]`);
        const activeSection = document.getElementById(sectionId);
        
        if (activeLink) activeLink.classList.add('active');
        if (activeSection) activeSection.classList.add('active');
        
        // Update page title based on section
        updatePageTitle(sectionId);
    }

    function updatePageTitle(section) {
        const titles = {
            'dashboard': '📊 Dashboard Overview',
            'orders': '📦 Order Management',
            'products': '☕ Menu Management',
            'transactions': '💰 Transactions',
            'customers': '👥 Customers',
            'suppliers': '🚚 Suppliers',
            'categories': '🏷️ Categories',
            'reviews': '⭐ Reviews Management'
        };
        
        const pageTitle = document.querySelector('.page-title');
        if (pageTitle && titles[section]) {
            pageTitle.textContent = titles[section];
        }
    }

    // Toggle sidebar for mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }

    // Product Management Functions
    function showAddForm() {
        document.getElementById('addProductForm').style.display = 'block';
        document.getElementById('editProductForm').style.display = 'none';
        window.scrollTo({ top: document.getElementById('addProductForm').offsetTop - 100, behavior: 'smooth' });
    }

    function hideAddForm() {
        document.getElementById('addProductForm').style.display = 'none';
        document.getElementById('addImagePreview').innerHTML = '';
    }

    function showEditForm() {
        document.getElementById('editProductForm').style.display = 'block';
        document.getElementById('addProductForm').style.display = 'none';
        window.scrollTo({ top: document.getElementById('editProductForm').offsetTop - 100, behavior: 'smooth' });
    }

    function hideEditForm() {
        document.getElementById('editProductForm').style.display = 'none';
        document.getElementById('editImagePreview').innerHTML = '';
    }

    function editProduct(id, nama, harga, stok, kategori, gambar) {
        document.getElementById('edit_id_produk').value = id;
        document.getElementById('edit_nama_produk').value = nama;
        document.getElementById('edit_harga').value = harga;
        document.getElementById('edit_stok').value = stok;
        document.getElementById('edit_id_kategori').value = kategori;
        document.getElementById('edit_current_gambar').value = gambar;
        
        // Show current image
        const currentImageDiv = document.getElementById('editCurrentImage');
        if (gambar) {
            currentImageDiv.innerHTML = `
                <p>Current Image:</p>
                <img src="assets/images/menu/${gambar}" alt="${nama}" onerror="this.style.display='none'">
            `;
        } else {
            currentImageDiv.innerHTML = '<p>No image uploaded</p>';
        }
        
        showEditForm();
    }

    // Image preview functions
    function previewAddImage(input) {
        const preview = document.getElementById('addImagePreview');
        preview.innerHTML = '';
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewEditImage(input) {
        const preview = document.getElementById('editImagePreview');
        preview.innerHTML = '';
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Supplier Management Functions
    function showAddSupplierForm() {
        document.getElementById('addSupplierForm').style.display = 'block';
        window.scrollTo({ top: document.getElementById('addSupplierForm').offsetTop - 100, behavior: 'smooth' });
    }

    function hideAddSupplierForm() {
        document.getElementById('addSupplierForm').style.display = 'none';
    }

    // Category Management Functions
    function showAddCategoryForm() {
        document.getElementById('addCategoryForm').style.display = 'block';
        window.scrollTo({ top: document.getElementById('addCategoryForm').offsetTop - 100, behavior: 'smooth' });
    }

    function hideAddCategoryForm() {
        document.getElementById('addCategoryForm').style.display = 'none';
    }

    // Review Management Functions
    function showReviewDetail(reviewId) {
        // Find the review in the table and display its details
        const reviewRows = document.querySelectorAll('tbody tr');
        let reviewRow = null;
        
        for (let row of reviewRows) {
            const firstCell = row.querySelector('td:first-child strong');
            if (firstCell && firstCell.textContent.includes('#' + reviewId)) {
                reviewRow = row;
                break;
            }
        }
        
        if (reviewRow) {
            const cells = reviewRow.querySelectorAll('td');
            const photoSrc = cells[5].querySelector('img')?.src || '';
            const hasPhoto = cells[5].querySelector('img');
            
            const modalContent = `
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
                        <p style="color: var(--cafe-text); font-weight: 600;">${cells[3].textContent}</p>
                    </div>
                    
                    <div>
                        <strong>Review:</strong>
                        <p class="review-text-full">${cells[4].getAttribute('title')}</p>
                    </div>
                    
                    <div>
                        <strong>Recommendation:</strong>
                        <p>${cells[6].textContent.includes('Yes') ? '✅ Recommends this place' : '❌ Does not recommend'}</p>
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
            
            document.getElementById('reviewModalBody').innerHTML = modalContent;
            document.getElementById('reviewModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    // Order Management Functions
    function assignSeller(orderId) {
        document.getElementById('assign_order_id').value = orderId;
        
        // Load order details for preview
        fetch(`get_order_details.php?order_id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const orderPreview = document.getElementById('orderPreview');
                    orderPreview.innerHTML = `
                        <h4>Order Details:</h4>
                        <p><strong>Customer:</strong> ${data.order.customer_name}</p>
                        <p><strong>Table:</strong> ${data.order.table_number}</p>
                        <p><strong>Items:</strong> ${data.order.items}</p>
                        <p><strong>Total:</strong> Rp ${data.order.total_amount.toLocaleString('id-ID')}</p>
                        <p><strong>Status:</strong> <span class="badge ${data.order.order_status}">${data.order.order_status}</span></p>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading order details:', error);
            });
        
        document.getElementById('assignSellerModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeAssignSellerModal() {
        document.getElementById('assignSellerModal').classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    function showOrderDetails(orderId) {
        fetch(`get_order_details.php?order_id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const orderDetails = document.getElementById('orderDetailsBody');
                    let transferProofHtml = '';
                    
                    if (data.order.transfer_proof) {
                        transferProofHtml = `
                            <div>
                                <strong>Bukti Transfer:</strong>
                                <div style="margin-top: 0.5rem;">
                                    <img src="assets/images/transfer_proofs/${data.order.transfer_proof}" 
                                         alt="Bukti Transfer" 
                                         class="transfer-proof"
                                         onclick="this.classList.toggle('transfer-proof-large')"
                                         style="cursor: pointer;">
                                </div>
                            </div>
                        `;
                    }
                    
                    orderDetails.innerHTML = `
                        <div class="order-detail">
                            <div class="form-row">
                                <div class="form-group">
                                    <strong>Order ID:</strong>
                                    <p>#${data.order.order_id}</p>
                                </div>
                                <div class="form-group">
                                    <strong>Tanggal:</strong>
                                    <p>${new Date(data.order.order_date).toLocaleString('id-ID')}</p>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <strong>Customer:</strong>
                                    <p>${data.order.customer_name}</p>
                                </div>
                                <div class="form-group">
                                    <strong>Meja:</strong>
                                    <p>${data.order.table_number}</p>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <strong>Metode Pembayaran:</strong>
                                    <p><span class="badge ${data.order.payment_method === 'Cash' ? 'success' : 'warning'}">${data.order.payment_method}</span></p>
                                </div>
                                <div class="form-group">
                                    <strong>Status:</strong>
                                    <p><span class="badge ${data.order.order_status}">${data.order.order_status}</span></p>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <strong>Seller:</strong>
                                <p>${data.order.seller_name || '<span class="badge warning">Belum ditugaskan</span>'}</p>
                            </div>
                            
                            <div class="form-group">
                                <strong>Items:</strong>
                                <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; margin-top: 0.5rem;">
                                    ${data.order.items.split(', ').map(item => `<div style="padding: 0.25rem 0;">• ${item}</div>`).join('')}
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <strong>Total Amount:</strong>
                                <p style="font-size: 1.2rem; font-weight: bold; color: var(--cafe-main);">
                                    Rp ${data.order.total_amount.toLocaleString('id-ID')}
                                </p>
                            </div>
                            
                            ${transferProofHtml}
                        </div>
                    `;
                } else {
                    document.getElementById('orderDetailsBody').innerHTML = `
                        <div style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                            <i class="fas fa-exclamation-circle"></i> Error loading order details
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading order details:', error);
                document.getElementById('orderDetailsBody').innerHTML = `
                    <div style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                        <i class="fas fa-exclamation-circle"></i> Error loading order details
                    </div>
                `;
            });
        
        document.getElementById('orderDetailsModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeOrderDetailsModal() {
        document.getElementById('orderDetailsModal').classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        const reviewModal = document.getElementById('reviewModal');
        const assignSellerModal = document.getElementById('assignSellerModal');
        const orderDetailsModal = document.getElementById('orderDetailsModal');
        
        if (e.target === reviewModal) {
            closeReviewModal();
        }
        if (e.target === assignSellerModal) {
            closeAssignSellerModal();
        }
        if (e.target === orderDetailsModal) {
            closeOrderDetailsModal();
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

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let valid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        valid = false;
                        field.style.borderColor = '#ff4757';
                    } else {
                        field.style.borderColor = '';
                    }
                });
                
                if (!valid) {
                    e.preventDefault();
                    alert('Please fill in all required fields!');
                }
            });
        });
    });
  </script>
</body>
</html>