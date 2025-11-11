<?php
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $nama_produk = $_POST['nama_produk'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        $id_kategori = $_POST['id_kategori'];
        $id_supplier = $_POST['id_supplier'];
        
        $stmt = $pdo->prepare("INSERT INTO produk (Nama_Produk, Harga, Stok, ID_Kategori, ID_Supplier) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$nama_produk, $harga, $stok, $id_kategori, $id_supplier])) {
            $success_message = "Menu berhasil ditambahkan!";
        } else {
            $error_message = "Gagal menambahkan menu!";
        }
    }
    
    if (isset($_POST['update_product'])) {
        $id_produk = $_POST['id_produk'];
        $nama_produk = $_POST['nama_produk'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        $id_kategori = $_POST['id_kategori'];
        
        $stmt = $pdo->prepare("UPDATE produk SET Nama_Produk = ?, Harga = ?, Stok = ?, ID_Kategori = ? WHERE ID_Produk = ?");
        if ($stmt->execute([$nama_produk, $harga, $stok, $id_kategori, $id_produk])) {
            $success_message = "Menu berhasil diupdate!";
        } else {
            $error_message = "Gagal mengupdate menu!";
        }
    }
    
    if (isset($_POST['delete_product'])) {
        $id_produk = $_POST['id_produk'];
        $stmt = $pdo->prepare("DELETE FROM produk WHERE ID_Produk = ?");
        if ($stmt->execute([$id_produk])) {
            $success_message = "Menu berhasil dihapus!";
        } else {
            $error_message = "Gagal menghapus menu!";
        }
    }
}

// Get all products
$products = $pdo->query("
    SELECT p.*, k.Nama_Kategori, s.Nama_Supplier 
    FROM produk p 
    LEFT JOIN kategori k ON p.ID_Kategori = k.ID_Kategori 
    LEFT JOIN supplier s ON p.ID_Supplier = s.ID_Supplier 
    ORDER BY p.ID_Kategori, p.Nama_Produk
")->fetchAll(PDO::FETCH_ASSOC);

// Get categories and suppliers
$categories = $pdo->query("SELECT * FROM kategori")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT * FROM supplier")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* PRODUCTS MANAGEMENT STYLES */
.admin-actions {
    margin-bottom: 2rem;
}

.cta-button {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--cafe-main);
    color: var(--cafe-dark);
    padding: 1rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(255, 214, 0, 0.4);
}

.form-container {
    background: var(--cafe-card);
    padding: 2rem;
    border-radius: 15px;
    border: 1px solid var(--cafe-border);
    margin-bottom: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--cafe-main);
    font-weight: 600;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--cafe-border);
    border-radius: 8px;
    color: var(--cafe-text);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--cafe-main);
    box-shadow: 0 0 0 3px rgba(255, 214, 0, 0.1);
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.btn-primary {
    background: var(--cafe-main);
    color: var(--cafe-dark);
    border: none;
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 214, 0, 0.4);
}

.btn-secondary {
    background: transparent;
    color: var(--cafe-text);
    border: 2px solid var(--cafe-border);
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    border-color: var(--cafe-main);
    color: var(--cafe-main);
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    background: var(--cafe-main);
    color: var(--cafe-dark);
    border: none;
    padding: 0.5rem;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.action-btn.edit {
    background: #2ed573;
    color: white;
}

.action-btn.delete {
    background: #ff4757;
    color: white;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.badge.warning-stock {
    background: #ffa502;
    color: white;
}

.badge.out-of-stock {
    background: #ff4757;
    color: white;
}

.success-message {
    background: rgba(46, 213, 115, 0.1);
    color: #2ed573;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    border: 1px solid rgba(46, 213, 115, 0.3);
}

.error-message {
    background: rgba(255, 71, 87, 0.1);
    color: #ff4757;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    border: 1px solid rgba(255, 71, 87, 0.3);
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<div class="admin-actions">
    <button class="cta-button" onclick="showAddForm()">
        <i class="fas fa-plus"></i> Tambah Menu Baru
    </button>
</div>

<!-- Messages -->
<?php if (isset($success_message)): ?>
    <div class="success-message">
        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="error-message">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<!-- Add Product Form -->
<div id="addProductForm" class="form-container" style="display: none;">
    <h3 style="color: var(--cafe-main); margin-bottom: 1.5rem;">
        <i class="fas fa-plus-circle"></i> Tambah Menu Baru
    </h3>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Nama Menu *</label>
                <input type="text" name="nama_produk" required placeholder="Contoh: Kopi Susu, Ayam Penyet, dll">
            </div>
            <div class="form-group">
                <label>Harga *</label>
                <input type="number" name="harga" step="100" min="0" required placeholder="Harga dalam Rupiah">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Stok *</label>
                <input type="number" name="stok" min="0" required placeholder="Jumlah stok tersedia">
            </div>
            <div class="form-group">
                <label>Kategori *</label>
                <select name="id_kategori" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['ID_Kategori']; ?>"><?php echo $category['Nama_Kategori']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Supplier *</label>
            <select name="id_supplier" required>
                <option value="">Pilih Supplier</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo $supplier['ID_Supplier']; ?>"><?php echo $supplier['Nama_Supplier']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" name="add_product" class="btn-primary">
                <i class="fas fa-save"></i> Simpan Menu
            </button>
            <button type="button" class="btn-secondary" onclick="hideAddForm()">
                <i class="fas fa-times"></i> Batal
            </button>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="recent-table">
    <div class="table-header">
        <h3 style="color: var(--cafe-main);">
            <i class="fas fa-list"></i> Daftar Menu (<?php echo count($products); ?> items)
        </h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Menu</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Kategori</th>
                <th>Supplier</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--cafe-text-light); padding: 2rem;">
                        <i class="fas fa-box-open"></i> Belum ada data menu
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td>#<?php echo $product['ID_Produk']; ?></td>
                    <td><strong><?php echo $product['Nama_Produk']; ?></strong></td>
                    <td>Rp <?php echo number_format($product['Harga'], 0, ',', '.'); ?></td>
                    <td>
                        <span class="badge <?php echo $product['Stok'] > 10 ? '' : ($product['Stok'] > 0 ? 'warning-stock' : 'out-of-stock'); ?>">
                            <?php echo $product['Stok']; ?> pcs
                        </span>
                    </td>
                    <td><?php echo $product['Nama_Kategori']; ?></td>
                    <td><?php echo $product['Nama_Supplier']; ?></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn edit" onclick="editProduct(<?php echo $product['ID_Produk']; ?>, '<?php echo $product['Nama_Produk']; ?>', <?php echo $product['Harga']; ?>, <?php echo $product['Stok']; ?>, <?php echo $product['ID_Kategori']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id_produk" value="<?php echo $product['ID_Produk']; ?>">
                                <button type="submit" name="delete_product" class="action-btn delete" onclick="return confirm('Hapus menu <?php echo addslashes($product['Nama_Produk']); ?>?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Edit Product Form -->
<div id="editProductForm" class="form-container" style="display: none;">
    <h3 style="color: var(--cafe-main); margin-bottom: 1.5rem;">
        <i class="fas fa-edit"></i> Edit Menu
    </h3>
    <form method="POST" id="editForm">
        <input type="hidden" name="id_produk" id="edit_id_produk">
        <div class="form-row">
            <div class="form-group">
                <label>Nama Menu *</label>
                <input type="text" name="nama_produk" id="edit_nama_produk" required>
            </div>
            <div class="form-group">
                <label>Harga *</label>
                <input type="number" name="harga" id="edit_harga" step="100" min="0" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Stok *</label>
                <input type="number" name="stok" id="edit_stok" min="0" required>
            </div>
            <div class="form-group">
                <label>Kategori *</label>
                <select name="id_kategori" id="edit_id_kategori" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['ID_Kategori']; ?>"><?php echo $category['Nama_Kategori']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="update_product" class="btn-primary">
                <i class="fas fa-save"></i> Update Menu
            </button>
            <button type="button" class="btn-secondary" onclick="hideEditForm()">
                <i class="fas fa-times"></i> Batal
            </button>
        </div>
    </form>
</div>

<script>
function showAddForm() {
    document.getElementById('addProductForm').style.display = 'block';
    document.getElementById('editProductForm').style.display = 'none';
    window.scrollTo({ top: document.getElementById('addProductForm').offsetTop - 100, behavior: 'smooth' });
}

function hideAddForm() {
    document.getElementById('addProductForm').style.display = 'none';
}

function showEditForm() {
    document.getElementById('editProductForm').style.display = 'block';
    document.getElementById('addProductForm').style.display = 'none';
    window.scrollTo({ top: document.getElementById('editProductForm').offsetTop - 100, behavior: 'smooth' });
}

function hideEditForm() {
    document.getElementById('editProductForm').style.display = 'none';
}

function editProduct(id, nama, harga, stok, kategori) {
    document.getElementById('edit_id_produk').value = id;
    document.getElementById('edit_nama_produk').value = nama;
    document.getElementById('edit_harga').value = harga;
    document.getElementById('edit_stok').value = stok;
    document.getElementById('edit_id_kategori').value = kategori;
    
    showEditForm();
}
</script>