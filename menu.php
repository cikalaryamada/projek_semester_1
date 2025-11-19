<?php
// menu.php
session_start();

// Koneksi database untuk menu
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
    die("Koneksi database gagal. Pastikan MySQL berjalan dan database 'umkmk16' sudah diimport.");
}

// Ambil data menu dari database
$makanan = $pdo->query("
    SELECT p.*, k.Nama_Kategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.ID_Kategori = k.ID_Kategori 
    WHERE k.Nama_Kategori = 'Makanan' 
    ORDER BY p.Nama_Produk
")->fetchAll(PDO::FETCH_ASSOC);

$minuman = $pdo->query("
    SELECT p.*, k.Nama_Kategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.ID_Kategori = k.ID_Kategori 
    WHERE k.Nama_Kategori = 'Minuman' 
    ORDER BY p.Nama_Produk
")->fetchAll(PDO::FETCH_ASSOC);

$snack = $pdo->query("
    SELECT p.*, k.Nama_Kategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.ID_Kategori = k.ID_Kategori 
    WHERE k.Nama_Kategori NOT IN ('Makanan', 'Minuman') 
    ORDER BY p.Nama_Produk
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Menu | K SIXTEEN CAFE</title>
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    /* Simplified styles: transfer/upload removed (transfer payment method removed) */
    .file-upload-area { display: none; } /* removed */
    .file-preview { display: none; } /* removed */

    .payment-success {
      text-align: center;
      padding: 2rem;
      color: var(--success);
    }

    .status-badge {
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
    }

    .status-pending { background: #ffa502; color: white; }
    .status-processing { background: #3742fa; color: white; }
    .status-completed { background: #2ed573; color: white; }
    .status-cancelled { background: #ff4757; color: white; }

    /* Receipt Styles */
    .receipt {
      background: white;
      color: #333;
      padding: 2rem;
      border-radius: 8px;
      font-family: 'Courier New', monospace;
      max-width: 400px;
      margin: 0 auto;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .receipt-header {
      text-align: center;
      border-bottom: 2px dashed #333;
      padding-bottom: 1rem;
      margin-bottom: 1rem;
    }

    .receipt-header h2 {
      color: var(--cafe-main);
      margin-bottom: 0.5rem;
      font-size: 1.5rem;
    }

    .receipt-info {
      margin-bottom: 1.5rem;
    }

    .receipt-info p {
      margin: 0.25rem 0;
      font-size: 0.9rem;
    }

    .receipt-items {
      margin-bottom: 1.5rem;
    }

    .receipt-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.5rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px dashed #ddd;
    }

    .receipt-item:last-child {
      border-bottom: none;
    }

    .receipt-total {
      border-top: 2px solid #333;
      padding-top: 1rem;
      margin-top: 1rem;
      font-weight: bold;
      font-size: 1.1rem;
    }

    .receipt-footer {
      text-align: center;
      margin-top: 2rem;
      padding-top: 1rem;
      border-top: 2px dashed #333;
      font-size: 0.8rem;
      color: #666;
    }

    /* Print Styles */
    @media print {
      body * {
        visibility: hidden;
      }
      #receipt-content, #receipt-content * {
        visibility: visible;
      }
      #receipt-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
      }
      .modal-header, .modal-footer {
        display: none !important;
      }
    }

    .receipt-modal .modal-content {
      max-width: 500px;
    }

    /* Ubah background dropdown pilih nomor meja */
    #table-number {
      background-color: #000 !important;
      color: #fff !important;
    }

    #table-number option,
    #table-number optgroup {
      background-color: #000 !important;
      color: #fff !important;
    }

  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="logo-wrapper">
        <div class="logo-image">
          <img src="assets/images/logo.jpg" alt="K SIXTEEN CAFE">
        </div>
        <div class="logo-text">K-SIXTEEN CAFE</div>
      </div>
      
      <ul class="nav-menu">
        <li><a href="index.php" class="nav-link">Home</a></li>
        <li><a href="menu.php" class="nav-link active">Menu</a></li>
        <li><a href="about.php" class="nav-link">About</a></li>
        <li><a href="contact.php" class="nav-link">Contact</a></li>
        <li><a href="Ulasan.php" class="nav-link">Ulasan</a></li>
      </ul>
    </div>
  </nav>

  <!-- Menu Section -->
  <section class="menu-section">
    <div class="container">
      <h2 class="section-title">Our Menu</h2>
      <p class="section-subtitle">Pilihan terbaik kopi, minuman, dan makanan lezat</p>
      
      <!-- Menu Tabs -->
      <div class="menu-tabs">
        <button class="menu-tab active" data-category="makanan" onclick="switchCategory('makanan')">
          <i class="fas fa-utensils"></i>
          Makanan
        </button>
        <button class="menu-tab" data-category="minuman" onclick="switchCategory('minuman')">
          <i class="fas fa-coffee"></i>
          Minuman
        </button>
        <button class="menu-tab" data-category="snack" onclick="switchCategory('snack')">
          <i class="fas fa-cookie"></i>
          Camilan
        </button>
      </div>

      <!-- Menu Grid -->
      <div class="menu-grid" id="menu-grid">
        <!-- Menu items will be dynamically loaded here -->
      </div>

      <!-- Cart Section -->
      <div class="cart-section">
        <div class="cart-header">
          <h3><i class="fas fa-shopping-cart"></i> Keranjang Pesanan</h3>
          <button class="clear-cart-btn" onclick="clearCart()">
            <i class="fas fa-trash"></i> Kosongkan
          </button>
        </div>
        
        <div class="cart-items" id="cart-items">
          <div class="empty-cart">Keranjang masih kosong</div>
        </div>
        
        <div class="cart-summary">
          <div class="cart-total">
            <span>Total:</span>
            <span id="cart-total-amount">Rp 0</span>
          </div>
          <button class="checkout-btn" id="checkout-btn" onclick="openCheckoutModal()" disabled>
            <i class="fas fa-credit-card"></i> Checkout
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- Checkout Modal -->
  <div class="modal" id="checkout-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Konfirmasi Pesanan</h3>
        <button class="modal-close" onclick="closeCheckoutModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-body">
        <div class="order-summary" id="order-summary">
          <!-- Order summary will be loaded here -->
        </div>
        
        <div class="form-group">
          <label for="customer-name">Nama Pemesan:</label>
          <input type="text" id="customer-name" placeholder="Masukkan nama Anda" required>
        </div>
        
        <div class="form-group">
          <label for="table-number">Nomor Meja:</label>
          <select id="table-number" required>
            <option value="">Pilih Nomor Meja</option>
            <optgroup label="Regular">
              <option value="Regular 1">Regular 1</option>
              <option value="Regular 2">Regular 2</option>
              <option value="Regular 3">Regular 3</option>
              <option value="Regular 4">Regular 4</option>
              <option value="Regular 5">Regular 5</option>
              <option value="Regular 6">Regular 6</option>
              <option value="Regular 7">Regular 7</option>
              <option value="Regular 8">Regular 8</option>
              <option value="Regular 9">Regular 9</option>
            </optgroup>
            <optgroup label="VIP">
              <option value="VIP 1">VIP 1</option>
              <option value="VIP 2">VIP 2</option>
              <option value="VIP 3">VIP 3</option>
              <option value="VIP 4">VIP 4</option>
              <option value="VIP 5">VIP 5</option>
            </optgroup>
            <optgroup label="Luxury">
              <option value="Luxury 1">Luxury 1</option>
              <option value="Luxury 2">Luxury 2</option>
            </optgroup>
            <optgroup label="Premier">
              <option value="Premier 1">Premier 1</option>
            </optgroup>
          </select>
        </div>
        
        <div class="payment-methods">
          <h4>Metode Pembayaran:</h4>
          <div class="payment-options">
            <label class="payment-option">
              <input type="radio" name="payment" value="cash" checked>
              <span class="payment-label">
                <i class="fas fa-money-bill-wave"></i>
                Tunai
              </span>
            </label>
          </div>
        </div>
      </div>
      
      <div class="modal-footer">
        <button class="btn-secondary" onclick="closeCheckoutModal()">Batal</button>
        <button class="btn-primary" onclick="processOrder()">
          <i class="fas fa-check-circle"></i> Konfirmasi Pesanan
        </button>
      </div>
    </div>
  </div>

  <!-- Receipt Modal -->
  <div class="modal receipt-modal" id="receipt-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fas fa-receipt"></i> Kuitansi Pesanan</h3>
        <button class="modal-close" onclick="closeReceiptModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-body">
        <div id="receipt-content">
          <!-- Receipt content will be loaded here -->
        </div>
      </div>
      
      <div class="modal-footer">
        <button class="btn-secondary" onclick="closeReceiptModal()">Tutup</button>
        <button class="btn-primary" onclick="printReceipt()">
          <i class="fas fa-print"></i> Print Kuitansi
        </button>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <p>&copy; <?php echo date('Y'); ?> K SIXTEEN CAFE. All rights reserved.</p>
        <p>Jl. Imam Bonjol No.36, Payaman, Kec. Nganjuk, Jawa Timur</p>
        
        <div class="social-links">
          <a href="https://www.instagram.com/k16_playstation/" class="social-link" target="_blank">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="https://wa.me/6282132384305" class="social-link" target="_blank">
            <i class="fab fa-whatsapp"></i>
          </a>
          <a href="https://www.tiktok.com/@k16playstation" class="social-link" target="_blank">
            <i class="fab fa-tiktok"></i>
          </a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Menu Data dari PHP/Database
    const menuData = {
      makanan: [
        <?php 
        $makanan_items = [];
        foreach ($makanan as $item) {
            $makanan_items[] = "{
              id: {$item['ID_Produk']},
              nama: \"".addslashes($item['Nama_Produk'])."\",
              harga: {$item['Harga']},
              stok: {$item['Stok']},
              img: \"".($item['Gambar'] ? 'admin/assets/images/menu/' . $item['Gambar'] : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400')."\",
              desc: \"".addslashes($item['Nama_Produk'])." - Stok: {$item['Stok']}\"
            }";
        }
        echo implode(',', $makanan_items);
        ?>
      ],
      minuman: [
        <?php 
        $minuman_items = [];
        foreach ($minuman as $item) {
            $minuman_items[] = "{
              id: {$item['ID_Produk']},
              nama: \"".addslashes($item['Nama_Produk'])."\",
              harga: {$item['Harga']},
              stok: {$item['Stok']},
              img: \"".($item['Gambar'] ? 'admin/assets/images/menu/' . $item['Gambar'] : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400')."\",
              desc: \"".addslashes($item['Nama_Produk'])." - Stok: {$item['Stok']}\"
            }";
        }
        echo implode(',', $minuman_items);
        ?>
      ],
      snack: [
        <?php 
        $snack_items = [];
        foreach ($snack as $item) {
            $snack_items[] = "{
              id: {$item['ID_Produk']},
              nama: \"".addslashes($item['Nama_Produk'])."\",
              harga: {$item['Harga']},
              stok: {$item['Stok']},
              img: \"".($item['Gambar'] ? 'admin/assets/images/menu/' . $item['Gambar'] : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400')."\",
              desc: \"".addslashes($item['Nama_Produk'])." - Stok: {$item['Stok']}\"
            }";
        }
        echo implode(',', $snack_items);
        ?>
      ]
    };

    let cart = [];
    let currentCategory = 'makanan';
    let currentOrderId = null;
    let currentPaymentMethod = null;
    let currentCustomerName = null;
    let currentTableNumber = null;
    let currentOrderData = null;

    // Initialize the menu
    function initMenu() {
      switchCategory('makanan');
      loadCartFromStorage();
      updateCart();
    }

    // Switch between menu categories
    function switchCategory(category) {
      currentCategory = category;
      
      // Update active tab
      document.querySelectorAll('.menu-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.category === category);
      });
      
      // Render menu items
      renderMenuItems(category);
    }

    // Render menu items for a category
    function renderMenuItems(category) {
      const menuGrid = document.getElementById('menu-grid');
      const items = menuData[category];
      
      if (items.length === 0) {
        menuGrid.innerHTML = `
          <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--cafe-text-light);">
            <i class="fas fa-box-open fa-3x" style="margin-bottom: 1rem;"></i>
            <h3>Belum ada menu untuk kategori ini</h3>
            <p>Silakan hubungi admin untuk menambahkan menu</p>
          </div>
        `;
        return;
      }
      
      menuGrid.innerHTML = items.map(item => `
        <div class="menu-card">
          <img src="${item.img}" alt="${item.nama}" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400'">
          <h3>${item.nama}</h3>
          <div class="desc">${item.desc}</div>
          <div class="price">${formatPrice(item.harga)}</div>
          ${item.stok > 0 ? `
          <div class="quantity-controls">
            <button class="qty-btn" onclick="decreaseQuantity(${item.id})">-</button>
            <span class="qty-display" id="qty-${item.id}">0</span>
            <button class="qty-btn" onclick="increaseQuantity(${item.id})">+</button>
          </div>
          ` : `
          <div style="color: #ff4757; font-weight: 600; padding: 1rem;">
            <i class="fas fa-times-circle"></i> Stok Habis
          </div>
          `}
        </div>
      `).join('');
      
      // Update quantity displays for current category
      items.forEach(item => {
        updateQuantityDisplay(item.id);
      });
    }

    // Format price to Indonesian Rupiah
    function formatPrice(price) {
      return 'Rp ' + price.toLocaleString('id-ID');
    }

    // Find menu item by ID
    function findMenuItem(itemId) {
      for (const category in menuData) {
        const item = menuData[category].find(item => item.id == itemId);
        if (item) return item;
      }
      return null;
    }

    // Increase item quantity
    function increaseQuantity(itemId) {
      const item = findMenuItem(itemId);
      if (!item) return;
      
      const existingItem = cart.find(cartItem => cartItem.id == itemId);
      
      if (existingItem) {
        if (existingItem.quantity >= item.stok) {
          alert(`Stok ${item.nama} hanya tersedia ${item.stok} pcs`);
          return;
        }
        existingItem.quantity++;
      } else {
        if (item.stok < 1) {
          alert(`Stok ${item.nama} habis`);
          return;
        }
        cart.push({
          ...item,
          quantity: 1
        });
      }
      
      saveCartToStorage();
      updateCart();
      updateQuantityDisplay(itemId);
    }

    // Decrease item quantity
    function decreaseQuantity(itemId) {
      const existingItem = cart.find(cartItem => cartItem.id == itemId);
      
      if (existingItem) {
        existingItem.quantity--;
        if (existingItem.quantity <= 0) {
          cart = cart.filter(item => item.id != itemId);
        }
      }
      
      saveCartToStorage();
      updateCart();
      updateQuantityDisplay(itemId);
    }

    // Update quantity display for an item
    function updateQuantityDisplay(itemId) {
      const display = document.getElementById(`qty-${itemId}`);
      if (display) {
        const cartItem = cart.find(item => item.id == itemId);
        display.textContent = cartItem ? cartItem.quantity : 0;
      }
    }

    // Update cart display
    function updateCart() {
      const cartItems = document.getElementById('cart-items');
      const cartTotal = document.getElementById('cart-total-amount');
      const checkoutBtn = document.getElementById('checkout-btn');
      
      if (cart.length === 0) {
        cartItems.innerHTML = '<div class="empty-cart">Keranjang masih kosong</div>';
        cartTotal.textContent = formatPrice(0);
        checkoutBtn.disabled = true;
      } else {
        cartItems.innerHTML = cart.map(item => `
          <div class="cart-item">
            <div class="cart-item-info">
              <span class="cart-item-name">${item.nama}</span>
              <span class="cart-item-price">${formatPrice(item.harga)}</span>
            </div>
            <div class="cart-item-controls">
              <button class="cart-qty-btn" onclick="decreaseQuantity(${item.id})">-</button>
              <span class="cart-item-qty">${item.quantity}</span>
              <button class="cart-qty-btn" onclick="increaseQuantity(${item.id})">+</button>
              <button class="cart-remove-btn" onclick="removeFromCart(${item.id})">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        `).join('');
        
        const total = cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
        cartTotal.textContent = formatPrice(total);
        checkoutBtn.disabled = false;
      }
    }

    // Remove item from cart
    function removeFromCart(itemId) {
      cart = cart.filter(item => item.id != itemId);
      saveCartToStorage();
      updateCart();
      updateQuantityDisplay(itemId);
    }

    // Clear entire cart
    function clearCart() {
      if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
        cart = [];
        saveCartToStorage();
        updateCart();
        // Reset all quantity displays
        for (const category in menuData) {
          menuData[category].forEach(item => {
            updateQuantityDisplay(item.id);
          });
        }
      }
    }

    // Storage functions
    function saveCartToStorage() {
      localStorage.setItem('k16_cart', JSON.stringify(cart));
    }

    function loadCartFromStorage() {
      const savedCart = localStorage.getItem('k16_cart');
      if (savedCart) {
        cart = JSON.parse(savedCart);
      }
    }

    // Open checkout modal
    function openCheckoutModal() {
      const modal = document.getElementById('checkout-modal');
      const orderSummary = document.getElementById('order-summary');
      
      orderSummary.innerHTML = cart.map(item => `
        <div class="order-item">
          <span>${item.nama} x${item.quantity}</span>
          <span>${formatPrice(item.harga * item.quantity)}</span>
        </div>
      `).join('');
      
      const total = cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
      orderSummary.innerHTML += `
        <div class="order-total">
          <strong>Total: ${formatPrice(total)}</strong>
        </div>
      `;
      
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    // Close checkout modal
    function closeCheckoutModal() {
      const modal = document.getElementById('checkout-modal');
      modal.classList.remove('show');
      document.body.style.overflow = 'auto';
    }

    // Function untuk proses order (HANYA TUNAI sekarang — metode transfer dihapus)
    async function processOrder() {
      const customerName = document.getElementById('customer-name').value.trim();
      const tableNumber = document.getElementById('table-number').value.trim();
      const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
      
      if (!customerName || !tableNumber) {
        alert('Harap isi nama pemesan dan nomor meja!');
        return;
      }
      
      if (cart.length === 0) {
        alert('Keranjang masih kosong!');
        return;
      }
      
      const orderId = generateOrderId();
      const totalAmount = cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
      
      try {
        // Tampilkan loading
        const checkoutBtn = document.querySelector('.btn-primary');
        const originalText = checkoutBtn.innerHTML;
        checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        checkoutBtn.disabled = true;
        
        // Kirim data ke server untuk update database
        const response = await saveOrderToDatabase(orderId, customerName, tableNumber, paymentMethod);
        
        if (response.success) {
          // Simpan data order untuk kuitansi (unique_code tidak digunakan karena transfer dihapus)
          currentOrderData = {
            order_id: orderId,
            customer_name: customerName,
            table_number: tableNumber,
            payment_method: paymentMethod,
            items: [...cart],
            total: totalAmount,
            unique_code: 0,
            status: 'completed' // untuk pembayaran tunai langsung kita set completed/lunas
          };
          
          closeCheckoutModal();
          
          // Untuk metode tunai langsung munculkan kuitansi dan kosongkan keranjang
          openReceipt(currentOrderData);
          clearCart();
          
        } else {
          alert('❌ Error: ' + response.message);
          checkoutBtn.innerHTML = originalText;
          checkoutBtn.disabled = false;
        }
      } catch (error) {
        alert('❌ Terjadi kesalahan: ' + error.message);
        const checkoutBtn = document.querySelector('.btn-primary');
        checkoutBtn.innerHTML = '<i class="fas fa-check-circle"></i> Konfirmasi Pesanan';
        checkoutBtn.disabled = false;
      }
    }

    // Function untuk simpan order ke database
    async function saveOrderToDatabase(orderId, customerName, tableNumber, paymentMethod) {
      const formData = new FormData();
      formData.append('action', 'process_order');
      formData.append('order_id', orderId);
      formData.append('customer_name', customerName);
      formData.append('table_number', tableNumber);
      formData.append('payment_method', paymentMethod);
      formData.append('cart_items', JSON.stringify(cart));
      
      const response = await fetch('process_order.php', {
        method: 'POST',
        body: formData
      });
      
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      
      return await response.json();
    }

    // Generate random order ID
    function generateOrderId() {
      return 'ORD' + Date.now().toString().slice(-6);
    }

    // Function untuk buka kuitansi
    function openReceipt(orderData) {
      const modal = document.getElementById('receipt-modal');
      const receiptContent = document.getElementById('receipt-content');
      
      const receiptHTML = `
        <div class="receipt">
            <div class="receipt-header">
                <h2>K SIXTEEN CAFE</h2>
                <p>Jl. Imam Bonjol No.36, Payaman, Nganjuk</p>
                <p>Telp: 0821-3238-4305</p>
            </div>
            
            <div class="receipt-info">
                <p><strong>No. Order:</strong> ${orderData.order_id}</p>
                <p><strong>Pelanggan:</strong> ${orderData.customer_name}</p>
                <p><strong>Meja:</strong> ${orderData.table_number}</p>
                <p><strong>Tanggal:</strong> ${new Date().toLocaleString('id-ID')}</p>
                <p><strong>Metode Bayar:</strong> ${orderData.payment_method === 'cash' ? 'Tunai' : orderData.payment_method}</p>
            </div>
            
            <div class="receipt-items">
                ${orderData.items.map(item => `
                    <div class="receipt-item">
                        <span>${item.nama} x${item.quantity}</span>
                        <span>${formatPrice(item.harga * item.quantity)}</span>
                    </div>
                `).join('')}
            </div>
            
            <div class="receipt-total">
                <div class="receipt-item">
                    <span><strong>TOTAL</strong></span>
                    <span><strong>${formatPrice(orderData.total)}</strong></span>
                </div>
            </div>
            
            <div class="receipt-footer">
                <p>Terima kasih atas kunjungan Anda</p>
                <p>*** ${orderData.payment_method === 'cash' ? 'LUNAS' : 'SUKSES'} ***</p>
                <span class="receipt-status ${getStatusClass(orderData.status)}">${orderData.status.toUpperCase()}</span>
            </div>
        </div>
      `;
      
      receiptContent.innerHTML = receiptHTML;
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    // Function untuk tutup kuitansi
    function closeReceiptModal() {
      const modal = document.getElementById('receipt-modal');
      modal.classList.remove('show');
      document.body.style.overflow = 'auto';
    }

    // Function untuk print kuitansi
    function printReceipt() {
      window.print();
    }

    // Helper function untuk status class
    function getStatusClass(status) {
      const statusClasses = {
        'pending': 'status-pending',
        'processing': 'status-processing',
        'completed': 'status-completed',
        'cancelled': 'status-cancelled'
      };
      return statusClasses[status] || 'status-pending';
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
      const checkoutModal = document.getElementById('checkout-modal');
      const receiptModal = document.getElementById('receipt-modal');
      
      if (e.target === checkoutModal) {
        closeCheckoutModal();
      }
      if (e.target === receiptModal) {
        closeReceiptModal();
      }
    });

    // Initialize menu when page loads
    document.addEventListener('DOMContentLoaded', initMenu);
  </script>
</body>
</html>