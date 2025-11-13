<?php
// Get all transactions dengan data meja
$transactions = $pdo->query("
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

$total_revenue = $pdo->query("SELECT SUM(Total_Harga) FROM transaksi_penjualan")->fetchColumn() ?? 0;
$total_orders = $pdo->query("SELECT COUNT(*) FROM transaksi_penjualan")->fetchColumn();
?>

<!-- Tampilkan Nomor Meja di tabel -->
<td>
    <?php if (!empty($transaction['Nomor_Meja'])): ?>
        <span class="table-badge"><?php echo $transaction['Nomor_Meja']; ?></span>
    <?php else: ?>
        <span style="color: var(--cafe-text-light);">-</span>
    <?php endif; ?>
</td>