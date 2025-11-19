<?php
// process_order.php
session_start();

// NOTE: Transfer payment method removed. Backend now validates and accepts only allowed payment methods (cash, qris).
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

if (!$pdo) {
    die(json_encode(['success' => false, 'message' => 'Koneksi database gagal']));
}

// Enable CORS untuk development
header('Content-Type: application/json');

if (isset($_POST['action']) && $_POST['action'] == 'process_order') {
    try {
        $pdo->beginTransaction();
        
        // Data dari AJAX
        $customerName = $_POST['customer_name'] ?? '';
        $tableNumber = $_POST['table_number'] ?? '';
        $paymentMethod = $_POST['payment_method'] ?? 'cash';
        $orderId = $_POST['order_id'] ?? '';
        $cartItems = json_decode($_POST['cart_items'] ?? '[]', true);
        
        // Normalisasi dan validasi metode pembayaran: hanya 'cash' dan 'qris' diperbolehkan.
        $paymentMethod = strtolower(trim($paymentMethod));
        if ($paymentMethod !== 'cash' && $paymentMethod !== 'qris') {
            // Default ke cash jika ada nilai tidak dikenal
            $paymentMethod = 'cash';
        }
        $paymentMethodDb = $paymentMethod === 'cash' ? 'Cash' : 'QRIS';
        // Untuk pembayaran tunai, kita bisa set status completed langsung; untuk lainnya pending.
        $order_status = $paymentMethod === 'cash' ? 'completed' : 'pending';
        
        // Validasi data
        if (empty($customerName) || empty($tableNumber) || empty($cartItems)) {
            throw new Exception('Data tidak lengkap');
        }
        
        // 1. Cari atau buat pelanggan (hanya nama)
        $customerId = null;
        $stmt = $pdo->prepare("SELECT ID_Pelanggan FROM pelanggan WHERE Nama_Pelanggan = ? LIMIT 1");
        $stmt->execute([$customerName]);
        $existingCustomer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingCustomer) {
          $customerId = $existingCustomer['ID_Pelanggan'];
        } else {
          // Buat pelanggan baru (hanya nama)
          $stmt = $pdo->prepare("INSERT INTO pelanggan (Nama_Pelanggan) VALUES (?)");
          $stmt->execute([$customerName]);
          $customerId = $pdo->lastInsertId();
        }
        
        // 2. Gunakan penjual default (ID 1)
        $penjualId = 1;
        
        $totalAmount = 0;
        
        // 3. Insert setiap item ke transaksi_penjualan dan update stok
        foreach ($cartItems as $item) {
            // Cek stok tersedia
            $checkStmt = $pdo->prepare("SELECT Stok FROM produk WHERE ID_Produk = ?");
            $checkStmt->execute([$item['id']]);
            $currentStock = $checkStmt->fetchColumn();
            
            if ($currentStock < $item['quantity']) {
                throw new Exception("Stok {$item['nama']} tidak cukup. Stok tersedia: $currentStock");
            }
            
            // Hitung total harga untuk item ini
            $itemTotal = $item['harga'] * $item['quantity'];
            $totalAmount += $itemTotal;
            
            // Insert ke transaksi_penjualan
            $stmt = $pdo->prepare("
                INSERT INTO transaksi_penjualan 
                (ID_Penjual, ID_Pelanggan, ID_Produk, Tanggal_Transaksi, Metode_Pembayaran, Jumlah_Barang, Total_Harga, Nomor_Meja, order_status) 
                VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $penjualId,
                $customerId,
                $item['id'],
                $paymentMethodDb,
                $item['quantity'],
                $itemTotal,
                $tableNumber,
                $order_status
            ]);
            
            // Update stok produk
            $stmt = $pdo->prepare("
                UPDATE produk SET Stok = Stok - ? WHERE ID_Produk = ?
            ");
            $stmt->execute([$item['quantity'], $item['id']]);
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Order berhasil diproses dan stok diperbarui',
            'order_id' => $orderId,
            'total_amount' => $totalAmount,
            'customer_id' => $customerId
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}
?>