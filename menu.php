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
        <div class="logo-text">K SIXTEEN CAFE</div>
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
          Snack
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
              <input type="radio" name="payment" value="qris" checked>
              <span class="payment-label">
                <i class="fas fa-qrcode"></i>
                QRIS
              </span>
            </label>
            <label class="payment-option">
              <input type="radio" name="payment" value="bri">
              <span class="payment-label">
                <i class="fas fa-university"></i>
                Transfer Bank BRI
              </span>
            </label>
            <label class="payment-option">
              <input type="radio" name="payment" value="cash">
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
        <button class="btn-primary" onclick="processPayment()">
          <i class="fas fa-credit-card"></i> Lanjutkan Pembayaran
        </button>
      </div>
    </div>
  </div>

  <!-- Payment Modal -->
  <div class="modal" id="payment-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Pembayaran</h3>
        <button class="modal-close" onclick="closePaymentModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-body">
        <div class="payment-instructions" id="payment-instructions">
          <!-- Payment instructions will be loaded here -->
        </div>
        
        <div class="payment-status" id="payment-status">
          <div class="status-pending">
            <i class="fas fa-clock"></i>
            <h4>Menunggu Pembayaran</h4>
            <p>Silakan selesaikan pembayaran Anda</p>
          </div>
          
          <div class="status-success" style="display: none;">
            <i class="fas fa-check-circle"></i>
            <h4>Pembayaran Berhasil</h4>
            <p>Pesanan Anda sedang diproses</p>
          </div>
        </div>
        
        <div class="payment-timer" id="payment-timer">
          <p>Selesaikan dalam: <span id="countdown">15:00</span></p>
        </div>
      </div>
      
      <div class="modal-footer">
        <button class="btn-secondary" onclick="closePaymentModal()">Batalkan Pesanan</button>
        <button class="btn-primary" id="confirm-payment-btn" onclick="confirmPayment()" style="display: none;">
          <i class="fas fa-check"></i> Konfirmasi Pembayaran
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
          <a href="#" class="social-link">
            <i class="fab fa-facebook-f"></i>
          </a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Menu Data Lengkap
    const menuData = {
      makanan: [
        { 
          nama: "Ayam Penyet Sak Segone", 
          harga: 16000, 
          img: "https://images.unsplash.com/photo-1544025162-d76694265947?w=400", 
          desc: "Ayam goreng penyet + nasi, sambal, lalapan" 
        },
        { 
          nama: "Tempe Penyet Sak Segone", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400", 
          desc: "Tempe goreng penyet + nasi, sambal, lalapan" 
        },
        { 
          nama: "Telur Penyet Sak Segone", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400", 
          desc: "Telur dadar penyet + nasi, sambal, lalapan" 
        },
        { 
          nama: "Pentol Penyet Sak Segone", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400", 
          desc: "Pentol goreng penyet + nasi, sambal, lalapan" 
        },
        { 
          nama: "Ayam Geprek Sak Segone", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1562967914-608f82629710?w=400", 
          desc: "Ayam geprek + nasi, sambal, lalapan" 
        },
        { 
          nama: "Chicken Ricebowl (Blackpepper / Spicy Mayo)", 
          harga: 15000, 
          img: "https://images.unsplash.com/photo-1562967914-608f82629710?w=400", 
          desc: "Ricebowl ayam blackpepper/spicy mayo" 
        }
      ],
      minuman: [
        { 
          nama: "Kopi Susu", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400", 
          desc: "Signature kopi susu creamy" 
        },
        { 
          nama: "Cappucino", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1511920170033-f8396924c348?w=400", 
          desc: "Espresso + susu steamed" 
        },
        { 
          nama: "Mochachino", 
          harga: 14000, 
          img: "https://images.unsplash.com/photo-1511407397940-d57f68e81203?w=400", 
          desc: "Espresso + coklat + susu" 
        },
        { 
          nama: "Kopi Karamel", 
          harga: 14000, 
          img: "https://images.unsplash.com/photo-1511407397940-d57f68e81203?w=400", 
          desc: "Kopi susu karamel" 
        },
        { 
          nama: "Kopi Hazelnut", 
          harga: 14000, 
          img: "https://images.unsplash.com/photo-1465101046530-73398c7f28ca?w=400", 
          desc: "Kopi susu hazelnut" 
        },
        { 
          nama: "Kopi Aren", 
          harga: 14000, 
          img: "https://images.unsplash.com/photo-1511407397940-d57f68e81203?w=400", 
          desc: "Kopi susu gula aren" 
        },
        { 
          nama: "Kopi Vanilla", 
          harga: 14000, 
          img: "https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400", 
          desc: "Kopi susu vanilla" 
        },
        { 
          nama: "Kopi Pandan", 
          harga: 14000, 
          img: "https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400", 
          desc: "Kopi susu pandan" 
        },
        { 
          nama: "Americano", 
          harga: 10000, 
          img: "https://images.unsplash.com/photo-1525253086316-d0c936c814f8?w=400", 
          desc: "Espresso + air panas" 
        },
        { 
          nama: "Kopi Tubruk", 
          harga: 6000, 
          img: "https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=400", 
          desc: "Kopi tubruk klasik" 
        },
        { 
          nama: "Choco Almond", 
          harga: 14000, 
          img: "https://images.unsplash.com/photo-1511407397940-d57f68e81203?w=400", 
          desc: "Coklat, susu, almond" 
        },
        { 
          nama: "Milky Chocolate", 
          harga: 13000, 
          img: "https://images.unsplash.com/photo-1511407397940-d57f68e81203?w=400", 
          desc: "Coklat susu creamy" 
        },
        { 
          nama: "Melon Squash", 
          harga: 10000, 
          img: "https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?w=400", 
          desc: "Minuman melon soda segar" 
        },
        { 
          nama: "Lime Squash", 
          harga: 10000, 
          img: "https://images.unsplash.com/photo-1511920170033-f8396924c348?w=400", 
          desc: "Minuman lime soda segar" 
        },
        { 
          nama: "Mango Squash", 
          harga: 10000, 
          img: "https://images.unsplash.com/photo-1571934811356-5cc061b6821f?w=400", 
          desc: "Minuman mango soda segar" 
        },
        { 
          nama: "Grape Squash", 
          harga: 10000, 
          img: "https://images.unsplash.com/photo-1571934811356-5cc061b6821f?w=400", 
          desc: "Minuman grape soda segar" 
        },
        { 
          nama: "Red Velvet", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1571934811356-5cc061b6821f?w=400", 
          desc: "Red velvet creamy" 
        },
        { 
          nama: "Matcha", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1571934811356-5cc061b6821f?w=400", 
          desc: "Matcha latte" 
        },
        { 
          nama: "Taro", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1482192596544-9eb780fc7f66?w=400", 
          desc: "Taro latte manis & creamy" 
        },
        { 
          nama: "Lychee Tea", 
          harga: 8000, 
          img: "https://images.unsplash.com/photo-1482192596544-9eb780fc7f66?w=400", 
          desc: "Teh leci dingin, segar" 
        },
        { 
          nama: "Lemon Tea", 
          harga: 8000, 
          img: "https://images.unsplash.com/photo-1510627498534-cf7e9002facc?w=400", 
          desc: "Teh lemon segar dingin/panas" 
        },
        { 
          nama: "Jasmine Tea", 
          harga: 5000, 
          img: "https://images.unsplash.com/photo-1482192596544-9eb780fc7f66?w=400", 
          desc: "Teh melati hangat/segar" 
        }
      ],
      snack: [
        { 
          nama: "Mix Platter (Kentang, Sosis, Nugget)", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1551782450-a2132b4ba21d?w=400", 
          desc: "Kentang, sosis, nugget goreng" 
        },
        { 
          nama: "Otak-Otak", 
          harga: 10000, 
          img: "https://images.unsplash.com/photo-1551782450-a2132b4ba21d?w=400", 
          desc: "Otak-otak goreng" 
        },
        { 
          nama: "Kentang Goreng", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1551218808-94e220e084d2?w=400", 
          desc: "French fries" 
        },
        { 
          nama: "Sosis Goreng", 
          harga: 10000, 
          img: "https://images.unsplash.com/photo-1544025162-d76694265947?w=400", 
          desc: "Sosis goreng crispy" 
        },
        { 
          nama: "Nugget Goreng", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1550547660-d9450f859349?w=400", 
          desc: "Nugget ayam goreng" 
        },
        { 
          nama: "Cireng", 
          harga: 10000, 
          img: "https://images.unsplash.com/photo-1551782450-a2132b4ba21d?w=400", 
          desc: "Cireng crispy" 
        },
        { 
          nama: "Pentol Goreng", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1551782450-a2132b4ba21d?w=400", 
          desc: "Pentol daging goreng" 
        },
        { 
          nama: "Risol", 
          harga: 12000, 
          img: "https://images.unsplash.com/photo-1550547660-d9450f859349?w=400", 
          desc: "Risoles isi sayur & ayam" 
        },
        { 
          nama: "Roti Panggang (Coklat / Keju)", 
          harga: 8000, 
          img: "https://images.unsplash.com/photo-1550547660-d9450f859349?w=400", 
          desc: "Roti panggang coklat / keju. Mix +2K" 
        }
      ]
    };

    let cart = [];
    let currentCategory = 'makanan';
    let currentOrder = null;
    let paymentTimer = null;

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
      
      menuGrid.innerHTML = items.map(item => `
        <div class="menu-card">
          <img src="${item.img}" alt="${item.nama}" loading="lazy">
          <h3>${item.nama}</h3>
          <div class="desc">${item.desc}</div>
          <div class="price">${formatPrice(item.harga)}</div>
          <div class="quantity-controls">
            <button class="qty-btn" onclick="decreaseQuantity('${item.nama}')">-</button>
            <span class="qty-display" id="qty-${item.nama.replace(/\s+/g, '-')}">0</span>
            <button class="qty-btn" onclick="increaseQuantity('${item.nama}')">+</button>
          </div>
        </div>
      `).join('');
      
      // Update quantity displays for current category
      items.forEach(item => {
        updateQuantityDisplay(item.nama);
      });
    }

    // Format price to Indonesian Rupiah
    function formatPrice(price) {
      return 'Rp ' + price.toLocaleString('id-ID');
    }

    // Increase item quantity
    function increaseQuantity(itemName) {
      const item = findMenuItem(itemName);
      const existingItem = cart.find(cartItem => cartItem.nama === itemName);
      
      if (existingItem) {
        existingItem.quantity++;
      } else {
        cart.push({
          ...item,
          quantity: 1
        });
      }
      
      saveCartToStorage();
      updateCart();
      updateQuantityDisplay(itemName);
    }

    // Decrease item quantity
    function decreaseQuantity(itemName) {
      const existingItem = cart.find(cartItem => cartItem.nama === itemName);
      
      if (existingItem) {
        existingItem.quantity--;
        if (existingItem.quantity <= 0) {
          cart = cart.filter(item => item.nama !== itemName);
        }
      }
      
      saveCartToStorage();
      updateCart();
      updateQuantityDisplay(itemName);
    }

    // Update quantity display for an item
    function updateQuantityDisplay(itemName) {
      const display = document.getElementById(`qty-${itemName.replace(/\s+/g, '-')}`);
      if (display) {
        const cartItem = cart.find(item => item.nama === itemName);
        display.textContent = cartItem ? cartItem.quantity : 0;
      }
    }

    // Find menu item by name
    function findMenuItem(itemName) {
      for (const category in menuData) {
        const item = menuData[category].find(item => item.nama === itemName);
        if (item) return item;
      }
      return null;
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
              <button class="cart-qty-btn" onclick="decreaseQuantity('${item.nama}')">-</button>
              <span class="cart-item-qty">${item.quantity}</span>
              <button class="cart-qty-btn" onclick="increaseQuantity('${item.nama}')">+</button>
              <button class="cart-remove-btn" onclick="removeFromCart('${item.nama}')">
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
    function removeFromCart(itemName) {
      cart = cart.filter(item => item.nama !== itemName);
      saveCartToStorage();
      updateCart();
      updateQuantityDisplay(itemName);
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
            updateQuantityDisplay(item.nama);
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

    // Process payment based on selected method
    function processPayment() {
      const customerName = document.getElementById('customer-name').value.trim();
      const tableNumber = document.getElementById('table-number').value.trim();
      const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
      
      if (!customerName || !tableNumber) {
        alert('Harap isi nama pemesan dan nomor meja!');
        return;
      }
      
      // Create order object
      const total = cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
      currentOrder = {
        id: 'K16-' + Date.now(),
        customer_name: customerName,
        table_number: tableNumber,
        payment_method: paymentMethod,
        total: total,
        items: [...cart],
        status: 'pending',
        created_at: new Date().toISOString()
      };
      
      // Save order to localStorage
      saveOrder(currentOrder);
      
      // Close checkout modal and open payment modal
      closeCheckoutModal();
      openPaymentModal(paymentMethod, total);
    }

    // Open payment modal with instructions
    function openPaymentModal(method, total) {
      const modal = document.getElementById('payment-modal');
      const instructions = document.getElementById('payment-instructions');
      
      // Reset payment status
      document.querySelector('.status-pending').style.display = 'block';
      document.querySelector('.status-success').style.display = 'none';
      document.getElementById('confirm-payment-btn').style.display = 'none';
      
      // Set payment instructions based on method
      if (method === 'qris') {
        instructions.innerHTML = `
          <div class="payment-method-qris">
            <h4>Pembayaran QRIS</h4>
            <p>Total: <strong>${formatPrice(total)}</strong></p>
            <div class="qris-code">
              <!-- Replace with your actual QRIS image -->
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=K16CAFE-${currentOrder.id}" alt="QRIS Code">
            </div>
            <p>Scan QR code di atas menggunakan aplikasi e-wallet atau mobile banking Anda</p>
            <div class="payment-steps">
              <p><strong>Langkah-langkah:</strong></p>
              <ol>
                <li>Buka aplikasi e-wallet atau mobile banking</li>
                <li>Pilih fitur scan QRIS</li>
                <li>Arahkan kamera ke QR code di atas</li>
                <li>Konfirmasi pembayaran</li>
                <li>Tunggu konfirmasi otomatis</li>
              </ol>
            </div>
          </div>
        `;
        
        // Simulate automatic verification for QRIS
        setTimeout(() => {
          simulatePaymentVerification();
        }, 10000); // 10 seconds delay for simulation
      } 
      else if (method === 'bri') {
        instructions.innerHTML = `
          <div class="payment-method-bri">
            <h4>Transfer Bank BRI</h4>
            <p>Total: <strong>${formatPrice(total)}</strong></p>
            <div class="bank-details">
              <div class="bank-info">
                <p><strong>Nomor Rekening:</strong> 1234-5678-9012-3456</p>
                <p><strong>Atas Nama:</strong> K SIXTEEN CAFE</p>
                <p><strong>Bank:</strong> BRI (Bank Rakyat Indonesia)</p>
              </div>
            </div>
            <div class="payment-steps">
              <p><strong>Langkah-langkah:</strong></p>
              <ol>
                <li>Transfer tepat sejumlah <strong>${formatPrice(total)}</strong></li>
                <li>Ke rekening BRI di atas</li>
                <li>Gunakan kode unik: <strong>${currentOrder.id.slice(-4)}</strong></li>
                <li>Simpan bukti transfer</li>
                <li>Klik tombol "Konfirmasi Pembayaran" setelah transfer</li>
              </ol>
            </div>
          </div>
        `;
        
        // Show manual confirmation button for bank transfer
        document.getElementById('confirm-payment-btn').style.display = 'block';
      }
      else if (method === 'cash') {
        instructions.innerHTML = `
          <div class="payment-method-cash">
            <h4>Pembayaran Tunai</h4>
            <p>Total: <strong>${formatPrice(total)}</strong></p>
            <p>Silakan lakukan pembayaran tunai ke kasir saat pesanan Anda siap.</p>
            <p>Pesanan Anda akan segera diproses.</p>
          </div>
        `;
        
        // For cash payment, mark as paid immediately
        currentOrder.status = 'paid';
        saveOrder(currentOrder);
        document.querySelector('.status-pending').style.display = 'none';
        document.querySelector('.status-success').style.display = 'block';
      }
      
      // Start payment timer (15 minutes)
      startPaymentTimer();
      
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    // Close payment modal
    function closePaymentModal() {
      const modal = document.getElementById('payment-modal');
      modal.classList.remove('show');
      document.body.style.overflow = 'auto';
      
      // Clear timer
      if (paymentTimer) {
        clearInterval(paymentTimer);
        paymentTimer = null;
      }
    }

    // Start payment countdown timer
    function startPaymentTimer() {
      let timeLeft = 15 * 60; // 15 minutes in seconds
      const countdownElement = document.getElementById('countdown');
      
      paymentTimer = setInterval(() => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
          clearInterval(paymentTimer);
          // Handle expired payment
          alert('Waktu pembayaran telah habis. Silakan ulangi proses pemesanan.');
          closePaymentModal();
          clearCart();
        }
        
        timeLeft--;
      }, 1000);
    }

    // Simulate payment verification (for demo purposes)
    function simulatePaymentVerification() {
      // In a real implementation, this would be a webhook from payment gateway
      if (currentOrder && currentOrder.status === 'pending') {
        currentOrder.status = 'paid';
        saveOrder(currentOrder);
        
        // Update UI
        document.querySelector('.status-pending').style.display = 'none';
        document.querySelector('.status-success').style.display = 'block';
        
        // Clear timer
        if (paymentTimer) {
          clearInterval(paymentTimer);
          paymentTimer = null;
        }
        
        // Send WhatsApp notification
        sendWhatsAppNotification();
      }
    }

    // Manual confirmation for bank transfer
    function confirmPayment() {
      if (currentOrder) {
        currentOrder.status = 'paid';
        saveOrder(currentOrder);
        
        // Update UI
        document.querySelector('.status-pending').style.display = 'none';
        document.querySelector('.status-success').style.display = 'block';
        document.getElementById('confirm-payment-btn').style.display = 'none';
        
        // Clear timer
        if (paymentTimer) {
          clearInterval(paymentTimer);
          paymentTimer = null;
        }
        
        // Send WhatsApp notification
        sendWhatsAppNotification();
      }
    }

    // Save order to localStorage
    function saveOrder(order) {
      const orders = JSON.parse(localStorage.getItem('k16_orders') || '[]');
      orders.push(order);
      localStorage.setItem('k16_orders', JSON.stringify(orders));
    }

    // Send WhatsApp notification
    function sendWhatsAppNotification() {
      if (!currentOrder) return;
      
      let message = `Halo K SIXTEEN CAFE! Saya ingin memesan:\n\n`;
      
      currentOrder.items.forEach(item => {
        message += `• ${item.nama} x${item.quantity} = Rp ${formatPrice(item.harga * item.quantity)}\n`;
      });
      
      message += `\nTotal: Rp ${formatPrice(currentOrder.total)}`;
      message += `\n\nNama: ${currentOrder.customer_name}`;
      message += `\nMeja: ${currentOrder.table_number}`;
      message += `\nMetode Bayar: ${getPaymentMethodName(currentOrder.payment_method)}`;
      message += `\nOrder ID: ${currentOrder.id}`;
      message += `\nStatus: ${currentOrder.status === 'paid' ? 'Telah Dibayar' : 'Menunggu Pembayaran'}`;
      message += `\n\nTerima kasih!`;
      
      const whatsappUrl = `https://wa.me/6282132384305?text=${encodeURIComponent(message)}`;
      window.open(whatsappUrl, '_blank');
      
      // Clear cart after successful order
      setTimeout(() => {
        clearCart();
        closePaymentModal();
        alert('Pesanan berhasil! Terima kasih telah memesan di K SIXTEEN CAFE.');
      }, 2000);
    }

    // Get payment method name
    function getPaymentMethodName(method) {
      const methods = {
        'qris': 'QRIS',
        'bri': 'Transfer Bank BRI',
        'cash': 'Tunai'
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

  <style>
    /* Existing styles remain the same, adding new styles for payment modal */
    
    .payment-instructions {
      margin-bottom: 1.5rem;
    }
    
    .payment-instructions h4 {
      color: var(--cafe-main);
      margin-bottom: 1rem;
    }
    
    .qris-code {
      text-align: center;
      margin: 1rem 0;
      padding: 1rem;
      background: white;
      border-radius: 10px;
      display: inline-block;
    }
    
    .qris-code img {
      width: 200px;
      height: 200px;
    }
    
    .bank-details {
      background: rgba(255, 255, 255, 0.05);
      padding: 1rem;
      border-radius: 8px;
      margin: 1rem 0;
    }
    
    .bank-info p {
      margin: 0.5rem 0;
    }
    
    .payment-steps {
      margin-top: 1rem;
    }
    
    .payment-steps ol {
      padding-left: 1.5rem;
      margin: 0.5rem 0;
    }
    
    .payment-steps li {
      margin-bottom: 0.5rem;
    }
    
    .payment-status {
      text-align: center;
      padding: 1.5rem;
      border-radius: 10px;
      margin: 1rem 0;
    }
    
    .status-pending {
      background: rgba(255, 214, 0, 0.1);
      border: 1px solid var(--cafe-main);
    }
    
    .status-success {
      background: rgba(76, 175, 80, 0.1);
      border: 1px solid #4CAF50;
    }
    
    .payment-status i {
      font-size: 3rem;
      margin-bottom: 1rem;
    }
    
    .status-pending i {
      color: var(--cafe-main);
    }
    
    .status-success i {
      color: #4CAF50;
    }
    
    .payment-timer {
      text-align: center;
      font-weight: bold;
      margin: 1rem 0;
      padding: 0.5rem;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
    }
    
    #countdown {
      color: var(--cafe-main);
      font-size: 1.2rem;
    }
  </style>
</body>
</html>