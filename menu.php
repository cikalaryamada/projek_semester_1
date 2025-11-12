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
              <input type="radio" name="payment" value="cash">
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
        <button class="btn-primary" onclick="sendOrder()">
          <i class="fab fa-whatsapp"></i> Kirim via WhatsApp
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

    // Send order via WhatsApp
 // GANTI function sendOrder() yang lama dengan ini:

function sendOrder() {
    const customerName = document.getElementById('customer-name').value.trim();
    const tableNumber = document.getElementById('table-number').value.trim();
    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
    
    if (!customerName || !tableNumber) {
        alert('Harap isi nama pemesan dan nomor meja!');
        return;
    }
    
    // Siapkan data untuk dikirim
    const orderData = {
        customer_name: customerName,
        customer_phone: '', // Bisa ditambahkan field nomor telepon
        table_number: tableNumber,
        payment_method: paymentMethod,
        cart_items: cart.map(item => ({
            nama: item.nama,
            quantity: item.quantity,
            harga: item.harga
        }))
    };
    
    // Tampilkan loading
    const submitBtn = document.querySelector('.btn-primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    submitBtn.disabled = true;
    
    // Kirim ke server
    fetch('process_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Buat pesan untuk WhatsApp
            let message = `Halo K SIXTEEN CAFE! Saya ingin memesan:\n\n`;
            
            cart.forEach(item => {
                message += `• ${item.nama} x${item.quantity} = Rp ${formatPrice(item.harga * item.quantity)}\n`;
            });
            
            const total = cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
            message += `\nTotal: Rp ${formatPrice(total)}`;
            message += `\n\nNama: ${customerName}`;
            message += `\nMeja: ${tableNumber}`;
            message += `\nMetode Bayar: ${getPaymentMethodName(paymentMethod)}`;
            message += `\n\nOrder ID: #${data.order_id}`;
            message += `\n\nTerima kasih!`;
            
            const whatsappUrl = `https://wa.me/6282132384305?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
            
            // Reset dan tutup
            closeCheckoutModal();
            clearCart();
            
            alert('Pesanan berhasil! Silakan lanjutkan konfirmasi via WhatsApp.');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses pesanan.');
    })
    .finally(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Tambahkan function ini di menu.php
function getPaymentMethodName(method) {
    const methods = {
        'qris': 'QRIS',
        'cash': 'Tunai',
        'transfer': 'Transfer Bank'
    };
    return methods[method] || method;
}

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
      const modal = document.getElementById('checkout-modal');
      if (e.target === modal) {
        closeCheckoutModal();
      }
    });

    // Initialize menu when page loads
    document.addEventListener('DOMContentLoaded', initMenu);

// Debug function
function debugOrder() {
    console.log('=== DEBUG ORDER ===');
    console.log('Cart:', cart);
    console.log('Customer Name:', document.getElementById('customer-name').value);
    console.log('Table Number:', document.getElementById('table-number').value);
    console.log('Payment Method:', document.querySelector('input[name="payment"]:checked').value);
    
    // Test langsung
    const testData = {
        customer_name: 'Test Customer',
        table_number: 'A1', 
        payment_method: 'cash',
        cart_items: [
            { nama: 'Kopi Susu', harga: 12000, quantity: 1 },
            { nama: 'Kentang Goreng', harga: 12000, quantity: 1 }
        ]
    };
    
    console.log('Test Data:', testData);
    
    // Test fetch
    fetch('process_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(testData)
    })
    .then(response => response.json())
    .then(data => console.log('Test Response:', data))
    .catch(error => console.error('Test Error:', error));
}

// Panggil function debug dari console browser
// Ketik: debugOrder()
  </script>


  <style>
    .menu-section {
      padding: 120px 0 80px;
      background: var(--cafe-bg);
    }

    .menu-tabs {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-bottom: 3rem;
      flex-wrap: wrap;
    }

    .menu-tab {
      background: var(--cafe-card);
      color: var(--cafe-text);
      border: 2px solid transparent;
      padding: 1rem 2rem;
      border-radius: 50px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .menu-tab.active,
    .menu-tab:hover {
      background: var(--cafe-main);
      color: var(--cafe-dark);
      border-color: var(--cafe-main);
    }

    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
    }

    .menu-card {
      background: var(--cafe-card);
      border-radius: 15px;
      padding: 1.5rem;
      text-align: center;
      border: 1px solid var(--cafe-border);
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .menu-card:hover {
      transform: translateY(-5px);
      border-color: var(--cafe-main);
      box-shadow: 0 8px 25px rgba(255, 214, 0, 0.15);
    }

    .menu-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 1rem;
    }

    .menu-card h3 {
      color: var(--cafe-main);
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
      font-weight: 700;
    }

    .menu-card .desc {
      color: var(--cafe-text-light);
      margin-bottom: 1rem;
      font-size: 0.9rem;
      line-height: 1.5;
    }

    .menu-card .price {
      color: var(--cafe-main);
      font-size: 1.3rem;
      font-weight: 800;
      margin-bottom: 1rem;
    }

    .quantity-controls {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .qty-btn {
      background: var(--cafe-main);
      color: var(--cafe-dark);
      border: none;
      width: 35px;
      height: 35px;
      border-radius: 50%;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .qty-btn:hover {
      background: var(--cafe-dark);
      color: var(--cafe-main);
      transform: scale(1.1);
    }

    .qty-display {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--cafe-main);
      min-width: 40px;
      text-align: center;
    }

    /* Cart Section */
    .cart-section {
      background: var(--cafe-card);
      border-radius: 15px;
      padding: 2rem;
      margin-top: 3rem;
      border: 1px solid var(--cafe-border);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .cart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 2px solid var(--cafe-border);
    }

    .cart-header h3 {
      color: var(--cafe-main);
      font-size: 1.3rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .clear-cart-btn {
      background: rgba(255, 214, 0, 0.1);
      color: var(--cafe-main);
      border: 1px solid var(--cafe-main);
      padding: 0.5rem 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .clear-cart-btn:hover {
      background: var(--cafe-main);
      color: var(--cafe-dark);
    }

    .cart-items {
      min-height: 100px;
      margin-bottom: 1.5rem;
    }

    .empty-cart {
      text-align: center;
      color: var(--cafe-text-light);
      font-style: italic;
      padding: 2rem;
    }

    .cart-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
      margin-bottom: 0.5rem;
    }

    .cart-item-info {
      flex: 1;
    }

    .cart-item-name {
      display: block;
      font-weight: 600;
      margin-bottom: 0.25rem;
    }

    .cart-item-price {
      color: var(--cafe-main);
      font-size: 0.9rem;
    }

    .cart-item-controls {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .cart-qty-btn {
      background: var(--cafe-main);
      color: var(--cafe-dark);
      border: none;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      cursor: pointer;
      font-weight: 700;
    }

    .cart-item-qty {
      min-width: 30px;
      text-align: center;
      font-weight: 600;
    }

    .cart-remove-btn {
      background: #ff4757;
      color: white;
      border: none;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      cursor: pointer;
      margin-left: 0.5rem;
    }

    .cart-summary {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 1.5rem;
      border-top: 2px solid var(--cafe-border);
    }

    .cart-total {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--cafe-main);
    }

    .checkout-btn {
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

    .checkout-btn:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 214, 0, 0.4);
    }

    .checkout-btn:disabled {
      background: #666;
      cursor: not-allowed;
      transform: none;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      z-index: 2000;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .modal.show {
      display: flex;
    }

    .modal-content {
      background: var(--cafe-card);
      border-radius: 15px;
      width: 100%;
      max-width: 500px;
      max-height: 90vh;
      overflow-y: auto;
      border: 2px solid var(--cafe-main);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem;
      border-bottom: 1px solid var(--cafe-border);
    }

    .modal-header h3 {
      color: var(--cafe-main);
      font-size: 1.3rem;
    }

    .modal-close {
      background: none;
      border: none;
      color: var(--cafe-text);
      font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .modal-close:hover {
      color: var(--cafe-main);
    }

    .modal-body {
      padding: 1.5rem;
    }

    /* Form Styles */
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
    .form-group textarea,
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
    .form-group textarea:focus,
    .form-group select:focus {
      outline: none;
      border-color: var(--cafe-main);
      box-shadow: 0 0 0 3px rgba(255, 214, 0, 0.1);
    }

    /* Payment Methods */
    .payment-methods {
      margin: 1.5rem 0;
    }

    .payment-methods h4 {
      color: var(--cafe-main);
      margin-bottom: 1rem;
    }

    .payment-options {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .payment-option {
      display: flex;
      align-items: center;
      padding: 1rem;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 1px solid transparent;
    }

    .payment-option:hover {
      border-color: var(--cafe-main);
    }

    .payment-option input[type="radio"] {
      margin-right: 0.75rem;
    }

    .payment-label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
    }

    /* Order Summary */
    .order-summary {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1.5rem;
    }

    .order-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0;
      border-bottom: 1px solid var(--cafe-border);
    }

    .order-item:last-child {
      border-bottom: none;
    }

    .order-total {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 1rem;
      margin-top: 1rem;
      border-top: 2px solid var(--cafe-main);
      font-size: 1.1rem;
    }

    /* Button Styles */
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
      width: 100%;
      justify-content: center;
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
      width: 100%;
    }

    .btn-secondary:hover {
      border-color: var(--cafe-main);
      color: var(--cafe-main);
    }

    .modal-footer {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      padding: 1.5rem;
      border-top: 1px solid var(--cafe-border);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .menu-tabs {
        flex-direction: column;
        align-items: center;
      }
      
      .menu-tab {
        width: 200px;
      }
      
      .cart-summary {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
      }
      
      .modal-footer {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 480px) {
      .menu-grid {
        grid-template-columns: 1fr;
      }
      
      .modal-content {
        margin: 1rem;
      }
      
      .modal-body {
        padding: 1rem;
      }
    }
  </style>
</body>
</html>