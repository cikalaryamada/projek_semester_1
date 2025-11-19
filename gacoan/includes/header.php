<?php
// includes/header.php
// Page header component

$page_titles = [
    'admin_dashboard.php' => ['icon' => 'fa-tachometer-alt', 'title' => 'Dashboard Overview', 'subtitle' => 'Monitor and manage K SIXTEEN CAFE performance'],
    'admin_products.php' => ['icon' => 'fa-coffee', 'title' => 'Menu Management', 'subtitle' => 'Manage your cafe menu items'],
    'admin_transactions.php' => ['icon' => 'fa-receipt', 'title' => 'Transactions', 'subtitle' => 'View and manage all transactions'],
    'admin_customers.php' => ['icon' => 'fa-users', 'title' => 'Customers', 'subtitle' => 'Customer information and data'],
    'admin_suppliers.php' => ['icon' => 'fa-truck', 'title' => 'Suppliers', 'subtitle' => 'Manage your suppliers'],
    'admin_categories.php' => ['icon' => 'fa-tags', 'title' => 'Categories', 'subtitle' => 'Manage product categories'],
    'admin_reviews.php' => ['icon' => 'fa-star', 'title' => 'Reviews', 'subtitle' => 'Manage customer reviews']
];

$current_page = basename($_SERVER['PHP_SELF']);
$page_info = $page_titles[$current_page] ?? ['icon' => 'fa-cog', 'title' => 'Admin Panel', 'subtitle' => 'K SIXTEEN CAFE'];
?>

<div class="main-header">
    <div>
        <h1 class="page-title">
            <i class="fas <?php echo $page_info['icon']; ?>"></i> 
            <?php echo $page_info['title']; ?>
        </h1>
        <p class="page-subtitle"><?php echo $page_info['subtitle']; ?></p>
    </div>
</div>