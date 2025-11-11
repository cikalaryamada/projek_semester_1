<?php
// Get all suppliers
$suppliers = $pdo->query("SELECT * FROM supplier ORDER BY ID_Supplier")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="recent-table">
    <div class="table-header">
        <h3 style="color: var(--cafe-main);">
            <i class="fas fa-truck"></i> Data Supplier
        </h3>
        <div style="color: var(--cafe-text-light); font-size: 0.9rem;">
            Total: <?php echo count($suppliers); ?> supplier
        </div>
    </div>
    
    <?php if (empty($suppliers)): ?>
        <div style="text-align: center; padding: 3rem; color: var(--cafe-text-light);">
            <i class="fas fa-truck fa-3x" style="margin-bottom: 1rem;"></i>
            <h3>Belum ada data supplier</h3>
            <p>Data supplier akan muncul di sini</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Supplier</th>
                    <th>Alamat</th>
                    <th>Jumlah Produk</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $supplier): 
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE ID_Supplier = ?");
                    $stmt->execute([$supplier['ID_Supplier']]);
                    $total_products = $stmt->fetchColumn();
                ?>
                <tr>
                    <td>#<?php echo $supplier['ID_Supplier']; ?></td>
                    <td><strong><?php echo $supplier['Nama_Supplier']; ?></strong></td>
                    <td><?php echo $supplier['Alamat'] ?? 'Tidak tercatat'; ?></td>
                    <td>
                        <span class="badge">
                            <?php echo $total_products; ?> produk
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>