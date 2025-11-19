<?php
// includes/sidebar.php
// Reusable sidebar component for all admin pages

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Admin Sidebar -->
<div class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="sidebar-logo-image">
                <img src="assets/images/logo.jpg" alt="K SIXTEEN CAFE" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiBmaWxsPSIjRkZENjAwIi8+Cjx0ZXh0IHg9IjI1IiB5PSIzMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE4IiBmb250LXdlaWdodD0iYm9sZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iIzExMTExMSI+S1M8L3RleHQ+Cjwvc3ZnPgo='">
            </div>
            <div class="sidebar-logo-text">K SIXTEEN CAFE</div>
        </div>
        <div class="admin-welcome">
            Welcome, <strong><?php echo $_SESSION['admin_username'] ?? 'Admin'; ?></strong>
        </div>
    </div>

    <div class="sidebar-menu">
        <a href="admin_dashboard.php" class="menu-item <?php echo $current_page == 'admin_dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span class="menu-text">Dashboard</span>
        </a>
        
        <a href="admin_products.php" class="menu-item <?php echo $current_page == 'admin_products.php' ? 'active' : ''; ?>">
            <i class="fas fa-coffee"></i>
            <span class="menu-text">Menu Management</span>
        </a>
        
        <a href="admin_transactions.php" class="menu-item <?php echo $current_page == 'admin_transactions.php' ? 'active' : ''; ?>">
            <i class="fas fa-receipt"></i>
            <span class="menu-text">Transactions</span>
        </a>
        
        <a href="admin_customers.php" class="menu-item <?php echo $current_page == 'admin_customers.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span class="menu-text">Customers</span>
        </a>
        
        <a href="admin_suppliers.php" class="menu-item <?php echo $current_page == 'admin_suppliers.php' ? 'active' : ''; ?>">
            <i class="fas fa-truck"></i>
            <span class="menu-text">Suppliers</span>
        </a>
        
        <a href="admin_categories.php" class="menu-item <?php echo $current_page == 'admin_categories.php' ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i>
            <span class="menu-text">Categories</span>
        </a>
        
        <a href="admin_reviews.php" class="menu-item <?php echo $current_page == 'admin_reviews.php' ? 'active' : ''; ?>">
            <i class="fas fa-star"></i>
            <span class="menu-text">Reviews</span>
        </a>
        
        <a href="../menu.php" class="menu-item" target="_blank">
            <i class="fas fa-external-link-alt"></i>
            <span class="menu-text">View Frontend</span>
        </a>
    </div>

    <div class="logout-section">
        <form method="POST" action="admin_logout.php">
            <button type="submit" class="btn btn-danger" style="width: 100%;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- Mobile Menu Toggle -->
<button class="menu-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>