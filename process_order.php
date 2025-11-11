<?php
// process_order_working.php - VERSI PASTI WORK
$host = 'localhost';
$dbname = 'umkmk16';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]));
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    die(json_encode(['success' => false, 'message' => 'No data received']));
}

// DEBUG: Log data yang diterima
error_log("=== ORDER DATA ===");
error_log(print_r($data, true));

try {
    $customer_name = $data['customer_name'] ?? 'Guest';
    $table_number = $data['table_number'] ?? '';
    $payment_method = $data['payment_method'] ?? 'cash';
    
    // 1. Handle pelanggan - PASTIKAN TIDAK NULL
    if (empty($customer_name) || $customer_name === 'Guest') {
        $customer_id = 1; // Pastikan ID 1 ada di database
    } else {
        $stmt = $pdo->prepare("SELECT ID_Pelanggan FROM pelanggan WHERE Nama_Pelanggan = ? LIMIT 1");
        $stmt->execute([$customer_name]);
        $customer = $stmt->fetch();
        
        if ($customer) {
            $customer_id = $customer['ID_Pelanggan'];
        } else {
            // Insert dengan nilai eksplisit
            $stmt = $pdo->prepare("INSERT INTO pelanggan (Nama_Pelanggan, Alamat, No_Telp) VALUES (?, '', '')");
            $stmt->execute([$customer_name]);
            $customer_id = $pdo->lastInsertId();
        }
    }
    
    // 2. Handle penjual - PASTIKAN TIDAK NULL
    $stmt = $pdo->query("SELECT ID_Penjual FROM penjual WHERE ID_Penjual IS NOT NULL LIMIT 1");
    $seller = $stmt->fetch();
    $seller_id = $seller['ID_Penjual'] ?? 1; // Fallback ke 1
    
    // 3. Simpan pesanan item per item
    $total_amount = 0;
    $saved_items = [];
    
    foreach ($data['cart_items'] as $item) {
        // Cari produk
        $stmt = $pdo->prepare("SELECT ID_Produk, Harga, Stok FROM produk WHERE Nama_Produk = ? LIMIT 1");
        $stmt->execute([$item['nama']]);
        $product = $stmt->fetch();
        
        if (!$product) {
            throw new Exception("Produk '{$item['nama']}' tidak ditemukan");
        }
        
        // Cek stok
        if ($product['Stok'] < $item['quantity']) {
            throw new Exception("Stok {$item['nama']} tidak cukup. Tersedia: {$product['Stok']}");
        }
        
        $subtotal = $product['Harga'] * $item['quantity'];
        $total_amount += $subtotal;
        
        // INSERT TRANSAKSI - TANPA MENYEBUTKAN ID (biarkan auto increment)
        $stmt = $pdo->prepare("
            INSERT INTO transaksi_penjualan 
            (ID_Penjual, ID_Pelanggan, ID_Produk, Nomor_Meja, Tanggal_Transaksi, Metode_Pembayaran, Jumlah_Barang, Total_Harga) 
            VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $seller_id,
            $customer_id,
            $product['ID_Produk'],
            $table_number, // Nomor Meja
            $payment_method,
            $item['quantity'],
            $subtotal
        ]);
        
        if (!$result) {
            $error_info = $stmt->errorInfo();
            throw new Exception("Gagal insert transaksi: " . $error_info[2]);
        }
        
        $saved_items[] = $item['nama'];
        
        // Update stok
        $update_stmt = $pdo->prepare("UPDATE produk SET Stok = Stok - ? WHERE ID_Produk = ?");
        $update_stmt->execute([$item['quantity'], $product['ID_Produk']]);
    }
    
    // Ambil ID transaksi terakhir yang berhasil
    $last_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Pesanan berhasil disimpan! 🎉',
        'order_id' => $last_id,
        'table_number' => $table_number,
        'total_amount' => $total_amount,
        'items_count' => count($saved_items),
        'saved_items' => $saved_items
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage(),
        'debug_info' => [
            'customer_name' => $customer_name ?? 'unknown',
            'table_number' => $table_number ?? 'unknown',
            'cart_count' => count($data['cart_items'] ?? [])
        ]
    ]);
}
?>