<?php
// proses_order.php
header('Content-Type: application/json');

// Koneksi database
$host = 'localhost';
$dbname = 'umkmk16';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal: ' . $e->getMessage()
    ]);
    exit;
}

// Ambil data dari POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode([
        'success' => false, 
        'message' => 'Tidak ada data yang diterima'
    ]);
    exit;
}

// Data dari frontend
$customer_name = $data['customer_name'] ?? '';
$table_number = $data['table_number'] ?? '';
$payment_method = $data['payment_method'] ?? 'cash';
$cart_items = $data['cart_items'] ?? [];

if (empty($customer_name) || empty($table_number) || empty($cart_items)) {
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak lengkap'
    ]);
    exit;
}

try {
    // Mulai transaction
    $pdo->beginTransaction();

    // 1. Cari atau buat pelanggan baru
    $stmt = $pdo->prepare("SELECT ID_Pelanggan FROM pelanggan WHERE Nama_Pelanggan = ? LIMIT 1");
    $stmt->execute([$customer_name]);
    $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pelanggan) {
        $id_pelanggan = $pelanggan['ID_Pelanggan'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO pelanggan (Nama_Pelanggan, No_Telp) VALUES (?, '')");
        $stmt->execute([$customer_name]);
        $id_pelanggan = $pdo->lastInsertId();
    }

    // 2. Ambil penjual pertama sebagai kasir
    $stmt = $pdo->query("SELECT ID_Penjual FROM penjual LIMIT 1");
    $penjual = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_penjual = $penjual ? $penjual['ID_Penjual'] : 1;

    // 3. Generate order ID
    $order_id = 'ORD' . date('YmdHis') . rand(100, 999);

    // 4. Simpan setiap item transaksi
    foreach ($cart_items as $item) {
        // Cek stok tersedia
        $stmt = $pdo->prepare("SELECT Stok, Nama_Produk FROM produk WHERE ID_Produk = ?");
        $stmt->execute([$item['id']]);
        $produk = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produk) {
            throw new Exception("Produk tidak ditemukan: ID " . $item['id']);
        }

        if ($produk['Stok'] < $item['quantity']) {
            throw new Exception("Stok " . $produk['Nama_Produk'] . " tidak cukup. Tersedia: " . $produk['Stok']);
        }

        // Hitung total harga
        $total_harga = $item['harga'] * $item['quantity'];

        // Simpan transaksi
        $stmt = $pdo->prepare("
            INSERT INTO transaksi_penjualan 
            (ID_Penjual, ID_Pelanggan, ID_Produk, Tanggal_Transaksi, Metode_Pembayaran, Jumlah_Barang, Total_Harga, Nomor_Meja) 
            VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)
        ");

        $stmt->execute([
            $id_penjual,
            $id_pelanggan,
            $item['id'],
            $payment_method,
            $item['quantity'],
            $total_harga,
            $table_number
        ]);

        // Stok akan berkurang otomatis oleh trigger
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Pesanan berhasil disimpan! Stok telah diperbarui.',
        'total_items' => count($cart_items)
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>