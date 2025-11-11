<?php
// Get all customers
$customers = $pdo->query("SELECT * FROM pelanggan ORDER BY ID_Pelanggan")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.badge.no-transactions { background: var(--cafe-text-light); color: var(--cafe-dark); }
.total-customers { color: var(--cafe-text-light); font-size: 0.9rem; }
</style>

<div class="recent-table">
    <div class="table-header">
        <h3 style="color: var(--cafe-main);">
            <i class="fas fa-users"></i> Data Pelanggan
        </h3>
        <div class="total-customers">Total: <?php echo count($customers); ?> pelanggan</div>
    </div>
    
    <?php if (empty($customers)): ?>
        <div style="text-align: center; padding: 3rem; color: var(--cafe-text-light);">
            <i class="fas fa-users fa-3x" style="margin-bottom: 1rem;"></i>
            <h3>Belum ada data pelanggan</h3>
            <p>Data pelanggan akan muncul di sini</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Total Transaksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): 
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM transaksi_penjualan WHERE ID_Pelanggan = ?");
                    $stmt->execute([$customer['ID_Pelanggan']]);
                    $total_transactions = $stmt->fetchColumn();
                ?>
                <tr>
                    <td>#<?php echo $customer['ID_Pelanggan']; ?></td>
                    <td><strong><?php echo $customer['Nama_Pelanggan']; ?></strong></td>
                    <td><?php echo $customer['Alamat'] ?? 'Tidak tercatat'; ?></td>
                    <td><?php echo $customer['No_Telp']; ?></td>
                    <td>
                        <span class="badge <?php echo $total_transactions > 0 ? '' : 'no-transactions'; ?>">
                            <?php echo $total_transactions; ?> transaksi
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>