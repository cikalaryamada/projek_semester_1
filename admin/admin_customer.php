<?php
// Get customer data from transactions
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
?>

<style>
.badge.no-transactions { 
    background: var(--cafe-text-light); 
    color: var(--cafe-dark); 
}
.total-customers { 
    color: var(--cafe-text-light); 
    font-size: 0.9rem; 
}
</style>

<div class="recent-table">
    <div class="table-header">
        <h3 style="color: var(--cafe-main);">
            <i class="fas fa-users"></i> Data Pelanggan
        </h3>
        <div class="total-customers">
            Total: <?php echo count($customerTransactions); ?> pelanggan terdaftar
        </div>
    </div>
    
    <?php if (empty($customerTransactions)): ?>
        <div style="text-align: center; padding: 3rem; color: var(--cafe-text-light);">
            <i class="fas fa-users fa-3x" style="margin-bottom: 1rem;"></i>
            <h3>Belum ada data pelanggan</h3>
            <p>Data pelanggan akan muncul setelah ada transaksi</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Total Transaksi</th>
                    <th>Transaksi Terakhir</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customerTransactions as $customer): ?>
                <tr>
                    <td>#<?php echo $customer['ID_Pelanggan']; ?></td>
                    <td><strong><?php echo $customer['Nama_Pelanggan']; ?></strong></td>
                    <td>
                        <span class="badge <?php echo $customer['total_orders'] > 0 ? '' : 'no-transactions'; ?>">
                            <?php echo $customer['total_orders']; ?> transaksi
                        </span>
                    </td>
                    <td>
                        <?php echo $customer['last_order'] ? date('d M Y', strtotime($customer['last_order'])) : 'Belum ada transaksi'; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>