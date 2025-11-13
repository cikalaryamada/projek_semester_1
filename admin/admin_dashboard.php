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
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Ambil data statistik
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard | K SIXTEEN CAFE</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    /* CSS SAMA SEPERTI SEBELUMNYA - TIDAK DIUBAH */
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
      max-width: 1200px;
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
      .admin-sidebar {
        width: 100%;
        height: auto;
        position: relative;
        transform: translateX(-100%);
      }
      
      .admin-sidebar.active {
        transform: translateX(0);
      }
      
      .admin-main {
        margin-left: 0;
        padding: 1rem;
      }
      
      .menu-toggle {
        display: block;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: var(--cafe-main);
        color: var(--cafe-dark);
        border: none;
        padding: 0.5rem;
        border-radius: 5px;
        cursor: pointer;
      }
      
      .form-row {
        grid-template-columns: 1fr;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
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
          <img src="assets/images/logo.jpg" alt="K SIXTEEN CAFE">
        </div>
        <div class="sidebar-logo-text">K SIXTEEN CAFE</div>
      </div>
      <div class="admin-welcome">
        Welcome, <strong><?php echo $_SESSION['admin_username']; ?></strong>
      </div>
    </div>

    <div class="sidebar-menu">
      <a href="#" class="menu-item active" data-section="dashboard">
        <i class="fas fa-tachometer-alt"></i>
        <span class="menu-text">Dashboard</span>
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
                        <img src="assets/images/menu/<?php echo $item['Gambar']; ?>" alt="<?php echo $item['Nama_Produk']; ?>" class="product-image-small" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100'">
                      <?php else: ?>
                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100" alt="Default" class="product-image-small">
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

      <!-- Products Section -->
      <div id="products" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">☕ Menu Management</h1>
            <p class="page-subtitle">Manage food and beverage menu items</p>
          </div>
        </div>
        
        <div class="admin-actions">
          <button class="btn btn-primary" onclick="showAddForm()">
            <i class="fas fa-plus"></i> Add New Menu Item
          </button>
        </div>

        <!-- Add Product Form -->
        <div id="addProductForm" class="form-container" style="display: none;">
          <h3 style="color: var(--cafe-main); margin-bottom: 1.5rem;">
            <i class="fas fa-plus-circle"></i> Add New Menu Item
          </h3>
          <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
              <div class="form-group">
                <label><i class="fas fa-utensils"></i> Menu Name *</label>
                <input type="text" name="nama_produk" required placeholder="e.g., Kopi Susu, Ayam Penyet">
              </div>
              <div class="form-group">
                <label><i class="fas fa-tag"></i> Price *</label>
                <input type="number" name="harga" step="100" min="0" required placeholder="Price in IDR">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label><i class="fas fa-boxes"></i> Stock *</label>
                <input type="number" name="stok" min="0" required placeholder="Available stock">
              </div>
              <div class="form-group">
                <label><i class="fas fa-tags"></i> Category *</label>
                <select name="id_kategori" required>
                  <option value="">Select Category</option>
                  <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['ID_Kategori']; ?>"><?php echo $category['Nama_Kategori']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label><i class="fas fa-truck"></i> Supplier *</label>
              <select name="id_supplier" required>
                <option value="">Select Supplier</option>
                <?php foreach ($suppliers as $supplier): ?>
                  <option value="<?php echo $supplier['ID_Supplier']; ?>"><?php echo $supplier['Nama_Supplier']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label><i class="fas fa-image"></i> Product Image</label>
              <div class="file-upload" onclick="document.getElementById('addImageInput').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Click to upload product image</p>
                <p class="text-muted">Recommended: 500x500px, JPG/PNG/WEBP</p>
                <input type="file" id="addImageInput" name="gambar_produk" accept="image/*" onchange="previewAddImage(this)">
              </div>
              <div class="image-preview" id="addImagePreview"></div>
            </div>
            <div class="form-actions">
              <button type="submit" name="add_product" class="btn btn-success">
                <i class="fas fa-save"></i> Save Menu Item
              </button>
              <button type="button" class="btn btn-secondary" onclick="hideAddForm()">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </form>
        </div>

        <!-- Products Table -->
        <div class="recent-table">
          <div class="table-header">
            <h3 style="color: var(--cafe-main);">
              <i class="fas fa-list"></i> Menu Items (<?php echo count($products); ?> items)
            </h3>
            <div style="color: var(--cafe-text-light); font-size: 0.9rem;">
              <i class="fas fa-info-circle"></i> Click edit/delete to manage items
            </div>
          </div>
          <table>
            <thead>
              <tr>
                <th>Image</th>
                <th>ID</th>
                <th>Menu Name</th>
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
                  <td colspan="8" style="text-align: center; color: var(--cafe-text-light); padding: 3rem;">
                    <i class="fas fa-box-open fa-2x" style="margin-bottom: 1rem; display: block;"></i>
                    No menu items found
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                  <td>
                    <?php if ($product['Gambar']): ?>
                      <img src="assets/images/menu/<?php echo $product['Gambar']; ?>" alt="<?php echo $product['Nama_Produk']; ?>" class="product-image" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100'">
                    <?php else: ?>
                      <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100" alt="Default" class="product-image">
                    <?php endif; ?>
                  </td>
                  <td><strong>#<?php echo $product['ID_Produk']; ?></strong></td>
                  <td><strong><?php echo $product['Nama_Produk']; ?></strong></td>
                  <td>Rp <?php echo number_format($product['Harga'], 0, ',', '.'); ?></td>
                  <td>
                    <?php
                    $stock_class = '';
                    if ($product['Stok'] == 0) {
                        $stock_class = 'danger';
                    } elseif ($product['Stok'] < 10) {
                        $stock_class = 'warning';
                    } else {
                        $stock_class = 'success';
                    }
                    ?>
                    <span class="badge <?php echo $stock_class; ?>">
                      <i class="fas fa-box"></i> <?php echo $product['Stok']; ?> pcs
                    </span>
                  </td>
                  <td>
                    <span class="badge info"><?php echo $product['Nama_Kategori']; ?></span>
                  </td>
                  <td><?php echo $product['Nama_Supplier']; ?></td>
                  <td>
                    <div class="action-buttons">
                      <button class="action-btn edit" onclick="editProduct(<?php echo $product['ID_Produk']; ?>, '<?php echo addslashes($product['Nama_Produk']); ?>', <?php echo $product['Harga']; ?>, <?php echo $product['Stok']; ?>, <?php echo $product['ID_Kategori']; ?>, '<?php echo $product['Gambar'] ?? ''; ?>')">
                        <i class="fas fa-edit"></i>
                      </button>
                      <form method="POST" style="display: inline;">
                        <input type="hidden" name="id_produk" value="<?php echo $product['ID_Produk']; ?>">
                        <input type="hidden" name="nama_produk" value="<?php echo $product['Nama_Produk']; ?>">
                        <input type="hidden" name="gambar_produk" value="<?php echo $product['Gambar'] ?? ''; ?>">
                        <button type="submit" name="delete_product" class="action-btn delete" onclick="return confirm('Delete <?php echo addslashes($product['Nama_Produk']); ?>? This action cannot be undone.')">
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

        <!-- Edit Product Form -->
        <div id="editProductForm" class="form-container" style="display: none;">
          <h3 style="color: var(--cafe-main); margin-bottom: 1.5rem;">
            <i class="fas fa-edit"></i> Edit Menu Item
          </h3>
          <form method="POST" id="editForm" enctype="multipart/form-data">
            <input type="hidden" name="id_produk" id="edit_id_produk">
            <input type="hidden" name="current_gambar" id="edit_current_gambar">
            <div class="form-row">
              <div class="form-group">
                <label><i class="fas fa-utensils"></i> Menu Name *</label>
                <input type="text" name="nama_produk" id="edit_nama_produk" required>
              </div>
              <div class="form-group">
                <label><i class="fas fa-tag"></i> Price *</label>
                <input type="number" name="harga" id="edit_harga" step="100" min="0" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label><i class="fas fa-boxes"></i> Stock *</label>
                <input type="number" name="stok" id="edit_stok" min="0" required>
              </div>
              <div class="form-group">
                <label><i class="fas fa-tags"></i> Category *</label>
                <select name="id_kategori" id="edit_id_kategori" required>
                  <option value="">Select Category</option>
                  <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['ID_Kategori']; ?>"><?php echo $category['Nama_Kategori']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label><i class="fas fa-image"></i> Product Image</label>
              <div id="editCurrentImage" class="current-image"></div>
              <div class="file-upload" onclick="document.getElementById('editImageInput').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Click to change product image</p>
                <p class="text-muted">Recommended: 500x500px, JPG/PNG/WEBP</p>
                <input type="file" id="editImageInput" name="gambar_produk" accept="image/*" onchange="previewEditImage(this)">
              </div>
              <div class="image-preview" id="editImagePreview"></div>
            </div>
            <div class="form-actions">
              <button type="submit" name="update_product" class="btn btn-success">
                <i class="fas fa-save"></i> Update Menu Item
              </button>
              <button type="button" class="btn btn-secondary" onclick="hideEditForm()">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Transactions Section -->
      <div id="transactions" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">💰 Transactions</h1>
            <p class="page-subtitle">View and manage all sales transactions</p>
          </div>
        </div>
        
        <div class="recent-table">
          <div class="table-header">
            <h3 style="color: var(--cafe-main);">
              <i class="fas fa-receipt"></i> All Transactions
            </h3>
            <div style="color: var(--cafe-text-light); font-size: 0.9rem;">
              Total: <?php echo count($all_transactions); ?> transactions | Revenue: Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?>
            </div>
          </div>
          
          <?php if (empty($all_transactions)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--cafe-text-light);">
              <i class="fas fa-receipt fa-3x" style="margin-bottom: 1rem;"></i>
              <h3>No transactions found</h3>
              <p>Transaction data will appear here after orders are placed</p>
            </div>
          <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Date</th>
                  <th>Customer</th>
                  <th>Product</th>
                  <th>Category</th>
                  <th>Price</th>
                  <th>Qty</th>
                  <th>Total</th>
                  <th>Payment</th>
                  <th>Cashier</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($all_transactions as $transaction): ?>
                <tr>
                  <td><strong>#<?php echo $transaction['ID_Transaksi_Penjualan']; ?></strong></td>
                  <td>
                    <small><?php echo date('d/m/Y', strtotime($transaction['Tanggal_Transaksi'])); ?></small><br>
                    <small style="color: var(--cafe-text-light);"><?php echo date('H:i', strtotime($transaction['Tanggal_Transaksi'])); ?></small>
                  </td>
                  <td><?php echo $transaction['Nama_Pelanggan'] ?? 'Guest'; ?></td>
                  <td><?php echo $transaction['Nama_Produk']; ?></td>
                  <td>
                    <span class="badge"><?php echo $transaction['Nama_Kategori']; ?></span>
                  </td>
                  <td>Rp <?php echo number_format($transaction['Harga_Satuan'], 0, ',', '.'); ?></td>
                  <td><?php echo $transaction['Jumlah_Barang']; ?>x</td>
                  <td><strong>Rp <?php echo number_format($transaction['Total_Harga'], 0, ',', '.'); ?></strong></td>
                  <td>
                    <span class="badge payment-<?php echo strtolower($transaction['Metode_Pembayaran']); ?>">
                      <?php echo $transaction['Metode_Pembayaran']; ?>
                    </span>
                  </td>
                  <td><?php echo $transaction['Nama_Karyawan'] ?? 'System'; ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>

      <!-- Customers Section -->
      <div id="customers" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">👥 Customers</h1>
            <p class="page-subtitle">Manage customer data for K SIXTEEN CAFE</p>
          </div>
        </div>
        
        <div class="recent-table">
          <div class="table-header">
            <h3 style="color: var(--cafe-main);">
              <i class="fas fa-users"></i> Customer Data
            </h3>
            <div style="color: var(--cafe-text-light); font-size: 0.9rem;">
              Total: <?php echo count($customers); ?> customers
            </div>
          </div>
          
          <?php if (empty($customers)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--cafe-text-light);">
              <i class="fas fa-users fa-3x" style="margin-bottom: 1rem;"></i>
              <h3>No customers found</h3>
              <p>Customer data will appear here</p>
            </div>
          <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer Name</th>
                  <th>Address</th>
                  <th>Phone</th>
                  <th>Total Transactions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($customers as $customer): 
                  $stmt = $pdo->prepare("SELECT COUNT(*) FROM transaksi_penjualan WHERE ID_Pelanggan = ?");
                  $stmt->execute([$customer['ID_Pelanggan']]);
                  $total_transactions = $stmt->fetchColumn();
                ?>
                <tr>
                  <td><strong>#<?php echo $customer['ID_Pelanggan']; ?></strong></td>
                  <td><strong><?php echo $customer['Nama_Pelanggan']; ?></strong></td>
                  <td><?php echo $customer['Alamat'] ?? 'Not recorded'; ?></td>
                  <td><?php echo $customer['No_Telp']; ?></td>
                  <td>
                    <span class="badge <?php echo $total_transactions > 0 ? 'success' : ''; ?>">
                      <?php echo $total_transactions; ?> transactions
                    </span>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>

      <!-- Suppliers Section -->
      <div id="suppliers" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">🚚 Suppliers</h1>
            <p class="page-subtitle">Manage supplier data for K SIXTEEN CAFE</p>
          </div>
        </div>
        
        <div class="admin-actions">
          <button class="btn btn-primary" onclick="showAddSupplierForm()">
            <i class="fas fa-plus"></i> Add New Supplier
          </button>
        </div>

        <!-- Add Supplier Form -->
        <div id="addSupplierForm" class="form-container" style="display: none;">
          <h3 style="color: var(--cafe-main); margin-bottom: 1.5rem;">
            <i class="fas fa-plus-circle"></i> Add New Supplier
          </h3>
          <form method="POST">
            <div class="form-row">
              <div class="form-group">
                <label><i class="fas fa-truck"></i> Supplier Name *</label>
                <input type="text" name="nama_supplier" required placeholder="Supplier name">
              </div>
              <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Address</label>
                <input type="text" name="alamat" placeholder="Supplier address">
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

        <div class="recent-table">
          <div class="table-header">
            <h3 style="color: var(--cafe-main);">
              <i class="fas fa-truck"></i> Supplier Data
            </h3>
            <div style="color: var(--cafe-text-light); font-size: 0.9rem;">
              Total: <?php echo count($suppliers); ?> suppliers
            </div>
          </div>
          
          <?php if (empty($suppliers)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--cafe-text-light);">
              <i class="fas fa-truck fa-3x" style="margin-bottom: 1rem;"></i>
              <h3>No suppliers found</h3>
              <p>Supplier data will appear here</p>
            </div>
          <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Supplier Name</th>
                  <th>Address</th>
                  <th>Products Count</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($suppliers as $supplier): 
                  $stmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE ID_Supplier = ?");
                  $stmt->execute([$supplier['ID_Supplier']]);
                  $total_products = $stmt->fetchColumn();
                ?>
                <tr>
                  <td><strong>#<?php echo $supplier['ID_Supplier']; ?></strong></td>
                  <td><strong><?php echo $supplier['Nama_Supplier']; ?></strong></td>
                  <td><?php echo $supplier['Alamat'] ?? 'Not recorded'; ?></td>
                  <td>
                    <span class="badge">
                      <?php echo $total_products; ?> products
                    </span>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>

      <!-- Categories Section -->
      <div id="categories" class="admin-section">
        <div class="main-header">
          <div>
            <h1 class="page-title">🏷️ Categories</h1>
            <p class="page-subtitle">Manage menu categories for K SIXTEEN CAFE</p>
          </div>
        </div>
        
        <div class="admin-actions">
          <button class="btn btn-primary" onclick="showAddCategoryForm()">
            <i class="fas fa-plus"></i> Add New Category
          </button>
        </div>

        <!-- Add Category Form -->
        <div id="addCategoryForm" class="form-container" style="display: none;">
          <h3 style="color: var(--cafe-main); margin-bottom: 1.5rem;">
            <i class="fas fa-plus-circle"></i> Add New Category
          </h3>
          <form method="POST">
            <div class="form-group">
              <label><i class="fas fa-tag"></i> Category Name *</label>
              <input type="text" name="nama_kategori" required placeholder="Category name (e.g., Beverages, Food, Snacks)">
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

        <div class="recent-table">
          <div class="table-header">
            <h3 style="color: var(--cafe-main);">
              <i class="fas fa-tags"></i> Category Data
            </h3>
            <div style="color: var(--cafe-text-light); font-size: 0.9rem;">
              Total: <?php echo count($categories); ?> categories
            </div>
          </div>
          
          <?php if (empty($categories)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--cafe-text-light);">
              <i class="fas fa-tags fa-3x" style="margin-bottom: 1rem;"></i>
              <h3>No categories found</h3>
              <p>Category data will appear here</p>
            </div>
          <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Category Name</th>
                  <th>Products Count</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($categories as $category): 
                  $stmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE ID_Kategori = ?");
                  $stmt->execute([$category['ID_Kategori']]);
                  $total_products = $stmt->fetchColumn();
                ?>
                <tr>
                  <td><strong>#<?php echo $category['ID_Kategori']; ?></strong></td>
                  <td><strong><?php echo $category['Nama_Kategori']; ?></strong></td>
                  <td>
                    <span class="badge">
                      <?php echo $total_products; ?> products
                    </span>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Navigation between sections
    document.querySelectorAll('.menu-item').forEach(link => {
      if (link.getAttribute('data-section')) {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Remove active class from all links and sections
          document.querySelectorAll('.menu-item').forEach(l => l.classList.remove('active'));
          document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
          
          // Add active class to clicked link and corresponding section
          this.classList.add('active');
          const sectionId = this.getAttribute('data-section');
          document.getElementById(sectionId).classList.add('active');
          
          // Update page title based on section
          updatePageTitle(sectionId);
          
          // Close sidebar on mobile
          if (window.innerWidth <= 768) {
            toggleSidebar();
          }
        });
      }
    });

    // Update page title based on active section
    function updatePageTitle(section) {
      const titles = {
        'dashboard': '📊 Dashboard Overview',
        'products': '☕ Menu Management',
        'transactions': '💰 Transactions',
        'customers': '👥 Customers',
        'suppliers': '🚚 Suppliers',
        'categories': '🏷️ Categories'
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
      // Reset form
      document.getElementById('addImagePreview').innerHTML = '';
    }

    function showEditForm() {
      document.getElementById('editProductForm').style.display = 'block';
      document.getElementById('addProductForm').style.display = 'none';
      window.scrollTo({ top: document.getElementById('editProductForm').offsetTop - 100, behavior: 'smooth' });
    }

    function hideEditForm() {
      document.getElementById('editProductForm').style.display = 'none';
      // Reset preview
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
  </script>
</body>
</html>