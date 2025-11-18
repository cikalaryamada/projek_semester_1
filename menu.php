<?php
// Koneksi database
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

// Ambil data minuman dari database
$minuman = $pdo->query("
    SELECT p.*, k.Nama_Kategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.ID_Kategori = k.ID_Kategori 
    WHERE k.Nama_Kategori = 'Minuman' 
    ORDER BY p.Nama_Produk
")->fetchAll(PDO::FETCH_ASSOC);

// Ambil data snack dari database
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
          <input type="text" id="table-number" placeholder="Contoh: A1, B5, dll" required>
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
            <label class="payment-option">
              <input type="radio" name="payment" value="transfer">
              <span class="payment-label">
                <i class="fas fa-university"></i>
                Transfer Bank
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

  <!-- Payment Modal -->
  <div class="modal" id="payment-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fas fa-university"></i> Pembayaran Transfer</h3>
        <button class="modal-close" onclick="closePaymentModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-body">
        <div id="payment-content">
          <!-- Transfer Payment -->
          <div id="transfer-payment" class="payment-method">
            <h4>Transfer Bank</h4>
            <div class="bank-accounts">
              <div class="bank-account">
                <div class="bank-icon">
                  <i class="fas fa-university"></i>
                </div>
                <div class="bank-info">
                  <h5>BCA - 923809042893</h5>
                  <p>a.n. K SIXTEEN CAFE</p>
                </div>
              </div>
              <div class="bank-account">
                <div class="bank-icon">
                  <i class="fas fa-university"></i>
                </div>
                <div class="bank-info">
                  <h5>BRI - 0987654321</h5>
                  <p>a.n. K SIXTEEN CAFE</p>
                </div>
              </div>
              <div class="bank-account">
                <div class="bank-icon">
                  <i class="fas fa-university"></i>
                </div>
                <div class="bank-info">
                  <h5>Mandiri - 1122334455</h5>
                  <p>a.n. K SIXTEEN CAFE</p>
                </div>
              </div>
            </div>
            <div class="transfer-instructions">
              <p><strong>Total Transfer: <span id="transfer-total">Rp 0</span></strong></p>
              <p><strong>Kode Unik: <span id="transfer-notes">ORDER#000</span></strong></p>
              <p><strong>Langkah-langkah:</strong></p>
              <ol>
                <li>Transfer ke salah satu rekening di atas</li>
                <li>Masukkan jumlah transfer sesuai total + kode unik</li>
                <li>Gunakan berita transfer: <strong id="transfer-message">ORDER#000</strong></li>
                <li>Simpan bukti transfer</li>
                <li>Klik tombol "Konfirmasi Pembayaran" setelah transfer</li>
              </ol>
            </div>
          </div>
        </div>
        
        <div class="payment-actions">
          <button class="btn-secondary" onclick="closePaymentModal()">
            <i class="fas fa-times"></i> Batal
          </button>
          <button class="btn-primary" id="verify-payment-btn" onclick="verifyPayment()">
            <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran
          </button>
        </div>
        
        <div id="payment-status" style="display: none;">
          <div class="payment-success">
            <i class="fas fa-check-circle"></i>
            <h4>Pembayaran Berhasil!</h4>
            <p>Pesanan Anda sedang diproses</p>
          </div>
        </div>
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

    // Function untuk proses order
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
      
      try {
        // Tampilkan loading
        const checkoutBtn = document.querySelector('.btn-primary');
        const originalText = checkoutBtn.innerHTML;
        checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        checkoutBtn.disabled = true;
        
        // Kirim data ke server untuk update database
        const response = await saveOrderToDatabase(orderId, customerName, tableNumber, paymentMethod);
        
        if (response.success) {
          if (paymentMethod === 'cash') {
            closeCheckoutModal();
            alert('✅ Order berhasil! Pesanan Anda sedang diproses.');
            clearCart();
          } else if (paymentMethod === 'transfer') {
            const totalAmount = response.total_amount || cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
            const uniqueCode = Math.floor(Math.random() * 900) + 100;
            openPaymentModal(orderId, paymentMethod, totalAmount, customerName, tableNumber, uniqueCode);
          }
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

    // Function untuk buka modal pembayaran transfer
    function openPaymentModal(orderId, paymentMethod, totalAmount, customerName, tableNumber, uniqueCode = 0) {
      currentOrderId = orderId;
      currentPaymentMethod = paymentMethod;
      currentCustomerName = customerName;
      currentTableNumber = tableNumber;
      
      const modal = document.getElementById('payment-modal');
      const transferTotal = document.getElementById('transfer-total');
      const transferNotes = document.getElementById('transfer-notes');
      const transferMessage = document.getElementById('transfer-message');
      
      // Hitung total akhir dengan kode unik
      const finalTotal = totalAmount + uniqueCode;
      
      // Set total amount
      transferTotal.textContent = formatPrice(finalTotal);
      transferNotes.textContent = `ORDER#${orderId}`;
      transferMessage.textContent = `ORDER#${orderId}`;
      
      // Tambahkan info kode unik untuk transfer
      const transferInfo = document.querySelector('.transfer-instructions');
      const existingUniqueCodeInfo = document.getElementById('unique-code-info');
      if (existingUniqueCodeInfo) {
        existingUniqueCodeInfo.remove();
      }
      
      const uniqueCodeHTML = `
        <div id="unique-code-info">
          <p><strong>Kode Unik: <span style="color: var(--cafe-main);">${uniqueCode}</span></strong></p>
          <p>Total transfer: Rp ${formatPrice(totalAmount)} + ${uniqueCode} = <strong>Rp ${formatPrice(finalTotal)}</strong></p>
        </div>
      `;
      transferInfo.insertAdjacentHTML('afterbegin', uniqueCodeHTML);
      
      // Reset status
      document.getElementById('payment-status').style.display = 'none';
      document.getElementById('verify-payment-btn').style.display = 'block';
      document.getElementById('payment-content').style.display = 'block';
      
      // Close checkout modal dan buka payment modal
      closeCheckoutModal();
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    // Function untuk verifikasi pembayaran
    function verifyPayment() {
      const verifyBtn = document.getElementById('verify-payment-btn');
      const originalText = verifyBtn.innerHTML;
      
      verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memverifikasi...';
      verifyBtn.disabled = true;
      
      // Simulasi proses verifikasi pembayaran
      setTimeout(() => {
        showPaymentSuccess();
        
        // Setelah berhasil, clear cart
        setTimeout(() => {
          closePaymentModal();
          clearCart();
        }, 2000);
        
      }, 3000);
    }

    // Function untuk tampilkan status sukses pembayaran
    function showPaymentSuccess() {
      document.getElementById('payment-content').style.display = 'none';
      document.getElementById('verify-payment-btn').style.display = 'none';
      document.getElementById('payment-status').style.display = 'block';
    }

    // Function untuk tutup modal pembayaran
    function closePaymentModal() {
      const modal = document.getElementById('payment-modal');
      modal.classList.remove('show');
      document.body.style.overflow = 'auto';
    }

    // Helper function untuk nama metode pembayaran
    function getPaymentMethodName(method) {
      const methods = {
        'cash': 'Tunai',
        'transfer': 'Transfer Bank'
      };
      return methods[method] || method;
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
      const checkoutModal = document.getElementById('checkout-modal');
      const paymentModal = document.getElementById('payment-modal');
      
      if (e.target === checkoutModal) {
        closeCheckoutModal();
      }
      if (e.target === paymentModal) {
        closePaymentModal();
      }
    });

    // Initialize menu when page loads
    document.addEventListener('DOMContentLoaded', initMenu);
  </script>
</body>
</html>