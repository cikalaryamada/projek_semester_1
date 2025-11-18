<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Koneksi database
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
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

header('Content-Type: application/json');

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    try {
        // Query untuk mendapatkan detail order
        $order_query = "
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
            WHERE tp.ID_Transaksi_Penjualan = ?
            GROUP BY tp.ID_Transaksi_Penjualan
        ";
        
        $stmt = $pdo->prepare($order_query);
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order) {
            echo json_encode([
                'success' => true,
                'order' => $order
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Order not found'
            ]);
        }
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Order ID is required'
    ]);
}
?>