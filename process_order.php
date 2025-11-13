<?php
// proses_order.php
session_start();

// Koneksi database
$host = 'localhost';
$dbname = 'umkmk16';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Koneksi database gagal: ' . $e->getMessage()]));
}

// Ambil data dari POST
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    die(json_encode(['success' => false, 'message' => 'Data tidak valid']));
}

$customerName = $input['customer_name'];
$tableNumber = $input['table_number'];
$paymentMethod = $input['payment_method'];
$cartItems = $input['cart_items'];

try {
    $pdo->beginTransaction();

    // 1. Cari atau buat pelanggan baru
    $stmt = $pdo->prepare("SELECT ID_Pelanggan FROM pelanggan WHERE Nama_Pelanggan = ? LIMIT 1");
    $stmt->execute([$customerName]);
    $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

    $idPelanggan = null;
    if ($pelanggan) {
        $idPelanggan = $pelanggan['ID_Pelanggan'];
    } else {
        // Buat pelanggan baru
        $stmt = $pdo->prepare("INSERT INTO pelanggan (Nama_Pelanggan, No_Telp) VALUES (?, '')");
        $stmt->execute([$customerName]);
        $idPelanggan = $pdo->lastInsertId();
    }

    // 2. Ambil ID penjual (kasir) pertama
    $stmt = $pdo->query("SELECT ID_Penjual FROM penjual LIMIT 1");
    $penjual = $stmt->fetch(PDO::FETCH_ASSOC);
    $idPenjual = $penjual ? $penjual['ID_Penjual'] : 1;

    // 3. Simpan setiap item di cart sebagai transaksi terpisah
    $orderId = 'ORD' . date('YmdHis');
    $savedTransactions = [];

    foreach ($cartItems as $item) {
        // Cek stok tersedia
        $stmt = $pdo->prepare("SELECT Stok FROM produk WHERE ID_Produk = ?");
        $stmt->execute([$item['id']]);
        $produk = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produk) {
            throw new Exception("Produk tidak ditemukan: " . $item['nama']);
        }

        if ($produk['Stok'] < $item['quantity']) {
            throw new Exception("Stok " . $item['nama'] . " tidak mencukupi. Stok tersedia: " . $produk['Stok']);
        }

        // Simpan transaksi
        $stmt = $pdo->prepare("
            INSERT INTO transaksi_penjualan 
            (ID_Penjual, ID_Pelanggan, ID_Produk, Tanggal_Transaksi, Metode_Pembayaran, Jumlah_Barang, Total_Harga, Nomor_Meja) 
            VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)
        ");

        $totalHarga = $item['harga'] * $item['quantity'];
        
        $stmt->execute([
            $idPenjual,
            $idPelanggan,
            $item['id'],
            $paymentMethod,
            $item['quantity'],
            $totalHarga,
            $tableNumber
        ]);

        $savedTransactions[] = [
            'id_transaksi' => $pdo->lastInsertId(),
            'produk' => $item['nama'],
            'qty' => $item['quantity'],
            'total' => $totalHarga
        ];

        // Stok akan otomatis berkurang oleh TRIGGER di database
    }

    $pdo->commit();

    // Response sukses
    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'message' => 'Transaksi berhasil disimpan',
        'transactions' => $savedTransactions,
        'total_items' => count($cartItems)
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>