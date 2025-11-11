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

<style>
.payment-cash { background: #2ed573; color: white; }
.payment-qris { background: #3742fa; color: white; }
.payment-transfer { background: #ffa502; color: white; }
.total-transactions { color: var(--cafe-text-light); font-size: 0.9rem; }
.table-badge { 
    background: #a55eea; 
    color: white; 
    padding: 0.3rem 0.7rem; 
    border-radius: 15px; 
    font-size: 0.8rem; 
    font-weight: bold;
}
</style>

<div class="recent-table">
    <div class="table-header">
        <h3 style="color: var(--cafe-main);">
            <i class="fas fa-receipt"></i> Semua Transaksi
        </h3>
        <div class="total-transactions">
            Total: <?php echo $total_orders; ?> transaksi | Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?>
        </div>
    </div>
    
    <?php if (empty($transactions)): ?>
        <div style="text-align: center; padding: 3rem; color: var(--cafe-text-light);">
            <i class="fas fa-receipt fa-3x" style="margin-bottom: 1rem;"></i>
            <h3>Belum ada transaksi</h3>
            <p>Data transaksi akan muncul di sini setelah ada pesanan</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Meja</th>
                    <th>Pelanggan</th>
                    <th>Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Kasir</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td>#<?php echo $transaction['ID_Transaksi_Penjualan']; ?></td>
                    <td>
                        <small><?php echo date('d/m/Y', strtotime($transaction['Tanggal_Transaksi'])); ?></small><br>
                        <small style="color: var(--cafe-text-light);"><?php echo date('H:i', strtotime($transaction['Tanggal_Transaksi'])); ?></small>
                    </td>
                    <td>
                        <?php if (!empty($transaction['Nomor_Meja'])): ?>
                            <span class="table-badge"><?php echo $transaction['Nomor_Meja']; ?></span>
                        <?php else: ?>
                            <span style="color: var(--cafe-text-light);">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $transaction['Nama_Pelanggan'] ?? 'Guest'; ?></td>
                    <td><?php echo $transaction['Nama_Produk']; ?></td>
                    <td>
                        <span class="category-badge"><?php echo $transaction['Nama_Kategori']; ?></span>
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