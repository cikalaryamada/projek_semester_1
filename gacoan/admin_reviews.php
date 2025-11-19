<?php
session_start();

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin.php');
    exit;
}

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

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $reviewId = $_POST['review_id'] ?? 0;
    
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE ulasan SET Status = 'approved' WHERE ID_Ulasan = ?");
        $stmt->execute([$reviewId]);
        $message = "Ulasan berhasil disetujui";
        $messageType = "success";
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE ulasan SET Status = 'rejected' WHERE ID_Ulasan = ?");
        $stmt->execute([$reviewId]);
        $message = "Ulasan berhasil ditolak";
        $messageType = "success";
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM ulasan WHERE ID_Ulasan = ?");
        $stmt->execute([$reviewId]);
        $message = "Ulasan berhasil dihapus";
        $messageType = "success";
    }
}

// Get reviews
$filter = $_GET['filter'] ?? 'all';
$query = "SELECT * FROM ulasan ORDER BY Tanggal_Ulasan DESC";

if ($filter !== 'all') {
    $query = "SELECT * FROM ulasan WHERE Status = ? ORDER BY Tanggal_Ulasan DESC";
}

$stmt = $pdo->prepare($query);
if ($filter !== 'all') {
    $stmt->execute([$filter]);
} else {
    $stmt->execute();
}
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get stats
$stats = [
    'total' => 0,
    'approved' => 0,
    'pending' => 0,
    'rejected' => 0,
    'average_rating' => 0
];

$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN Status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN Status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN Status = 'rejected' THEN 1 ELSE 0 END) as rejected,
        AVG(CASE WHEN Status = 'approved' THEN Rating ELSE NULL END) as average_rating
    FROM ulasan
");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
$stats['average_rating'] = round($stats['average_rating'], 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ulasan - Admin K SIXTEEN CAFE</title>
    <link href="assets/css/admin_style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/images/logo.jpg" alt="Logo">
            <h3>K SIXTEEN CAFE</h3>
        </div>
        
        <nav class="sidebar-nav">
            <a href="admin_dashboard.php" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="admin_products.php" class="nav-item">
                <i class="fas fa-box"></i>
                <span>Produk</span>
            </a>
            <a href="admin_transactions.php" class="nav-item">
                <i class="fas fa-receipt"></i>
                <span>Transaksi</span>
            </a>
            <a href="admin_customers.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Pelanggan</span>
            </a>
            <a href="admin_reviews.php" class="nav-item active">
                <i class="fas fa-star"></i>
                <span>Ulasan</span>
            </a>
            <a href="admin_suppliers.php" class="nav-item">
                <i class="fas fa-truck"></i>
                <span>Supplier</span>
            </a>
            <a href="admin_categories.php" class="nav-item">
                <i class="fas fa-tags"></i>
                <span>Kategori</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="admin_logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <h1><i class="fas fa-star"></i> Kelola Ulasan</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            </div>
        </div>

        <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <i class="fas fa-check-circle"></i>
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #2196F3;">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total']); ?></h3>
                    <p>Total Ulasan</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #4CAF50;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['approved']); ?></h3>
                    <p>Disetujui</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #FF9800;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['pending']); ?></h3>
                    <p>Pending</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #9C27B0;">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['average_rating']; ?></h3>
                    <p>Rating Rata-rata</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="content-section">
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <a href="?filter=all" class="btn <?php echo $filter === 'all' ? 'btn-primary' : 'btn-info'; ?>">
                    <i class="fas fa-list"></i> Semua (<?php echo $stats['total']; ?>)
                </a>
                <a href="?filter=pending" class="btn <?php echo $filter === 'pending' ? 'btn-primary' : 'btn-info'; ?>">
                    <i class="fas fa-clock"></i> Pending (<?php echo $stats['pending']; ?>)
                </a>
                <a href="?filter=approved" class="btn <?php echo $filter === 'approved' ? 'btn-primary' : 'btn-info'; ?>">
                    <i class="fas fa-check"></i> Disetujui (<?php echo $stats['approved']; ?>)
                </a>
                <a href="?filter=rejected" class="btn <?php echo $filter === 'rejected' ? 'btn-primary' : 'btn-info'; ?>">
                    <i class="fas fa-times"></i> Ditolak (<?php echo $stats['rejected']; ?>)
                </a>
            </div>

            <?php if (empty($reviews)): ?>
                <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <h3>Tidak ada ulasan</h3>
                    <p>Belum ada ulasan yang masuk</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Rating</th>
                            <th>Judul</th>
                            <th>Ulasan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo $review['ID_Ulasan']; ?></td>
                            <td><?php echo htmlspecialchars($review['Nama_Pelanggan']); ?></td>
                            <td>
                                <?php 
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $review['Rating']) {
                                        echo '<i class="fas fa-star" style="color: var(--primary-color);"></i>';
                                    } else {
                                        echo '<i class="far fa-star" style="color: var(--text-secondary);"></i>';
                                    }
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($review['Judul_Ulasan']); ?></td>
                            <td><?php echo htmlspecialchars(substr($review['Isi_Ulasan'], 0, 50)) . '...'; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($review['Tanggal_Ulasan'])); ?></td>
                            <td>
                                <?php if ($review['Status'] === 'approved'): ?>
                                    <span class="badge badge-success">Disetujui</span>
                                <?php elseif ($review['Status'] === 'pending'): ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <?php if ($review['Status'] !== 'approved'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="review_id" value="<?php echo $review['ID_Ulasan']; ?>">
                                        <button type="submit" class="btn btn-success" style="padding: 0.5rem 0.75rem; font-size: 0.9rem;">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($review['Status'] !== 'rejected'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="review_id" value="<?php echo $review['ID_Ulasan']; ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 0.5rem 0.75rem; font-size: 0.9rem;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus ulasan ini?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="review_id" value="<?php echo $review['ID_Ulasan']; ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 0.5rem 0.75rem; font-size: 0.9rem;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>