<?php
// test_db.php
echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Database Connection</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 Test Database Connection - K SIXTEEN CAFE</h1>";

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "<p class='success'>✅ Database connected successfully!</p>";
    
    // Test: Count orders
    try {
        $stmt = $db->query("SELECT COUNT(*) as total_orders FROM orders");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<div class='info'>📊 Total orders in database: <strong>" . $result['total_orders'] . "</strong></div>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error counting orders: " . $e->getMessage() . "</p>";
    }
    
    // Test: Get admin users
    try {
        $stmt = $db->query("SELECT username, name, role FROM admin_users");
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>👥 Admin Users</h3>";
        if (count($admins) > 0) {
            echo "<table>";
            echo "<tr><th>Username</th><th>Name</th><th>Role</th></tr>";
            foreach ($admins as $admin) {
                echo "<tr>";
                echo "<td>" . $admin['username'] . "</td>";
                echo "<td>" . $admin['name'] . "</td>";
                echo "<td>" . $admin['role'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>❌ No admin users found!</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error fetching admin users: " . $e->getMessage() . "</p>";
    }
    
    // Test: Get recent orders
    try {
        $stmt = $db->query("SELECT order_code, customer_name, table_number, total_amount, status, created_at 
                           FROM orders ORDER BY created_at DESC LIMIT 5");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>📋 Recent Orders</h3>";
        if (count($orders) > 0) {
            echo "<table>";
            echo "<tr><th>Order Code</th><th>Customer</th><th>Table</th><th>Amount</th><th>Status</th><th>Date</th></tr>";
            foreach ($orders as $order) {
                echo "<tr>";
                echo "<td>" . $order['order_code'] . "</td>";
                echo "<td>" . $order['customer_name'] . "</td>";
                echo "<td>" . $order['table_number'] . "</td>";
                echo "<td>Rp " . number_format($order['total_amount']) . "</td>";
                echo "<td><span style='color: " . getStatusColor($order['status']) . "'>" . ucfirst($order['status']) . "</span></td>";
                echo "<td>" . $order['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No orders found in database.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error fetching orders: " . $e->getMessage() . "</p>";
    }
    
    // Test: Database tables
    try {
        $stmt = $db->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>🗃️ Database Tables</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table . "</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error fetching tables: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p class='error'>❌ Database connection failed!</p>";
    echo "<div class='info'>";
    echo "<p><strong>Troubleshooting tips:</strong></p>";
    echo "<ul>";
    echo "<li>Check if MySQL is running in XAMPP</li>";
    echo "<li>Verify database name in config/database.php</li>";
    echo "<li>Check username and password in config/database.php</li>";
    echo "<li>Make sure database 'ksixteen_cafe' exists</li>";
    echo "</ul>";
    echo "</div>";
}

echo "</div></body></html>";

function getStatusColor($status) {
    switch ($status) {
        case 'pending': return '#ffc107';
        case 'confirmed': return '#17a2b8';
        case 'preparing': return '#fd7e14';
        case 'ready': return '#28a745';
        case 'completed': return '#6c757d';
        default: return '#dc3545';
    }
}
?>