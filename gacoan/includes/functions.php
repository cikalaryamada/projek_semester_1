<?php
// includes/functions.php
// Helper functions

/**
 * Format currency to Rupiah
 */
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date to Indonesian format
 */
function formatDate($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}

/**
 * Format datetime to Indonesian format
 */
function formatDateTime($datetime, $format = 'd M Y H:i') {
    return date($format, strtotime($datetime));
}

/**
 * Get stock badge class based on quantity
 */
function getStockBadgeClass($stock) {
    if ($stock > 10) return 'success';
    if ($stock > 0) return 'warning';
    return 'danger';
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Upload image file
 */
function uploadImage($file, $productName, $uploadDir = 'assets/images/menu/') {
    if (!isset($file) || $file['error'] !== 0) {
        return '';
    }
    
    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedTypes)) {
        return '';
    }
    
    // Generate unique filename
    $fileName = uniqid() . '_' . strtolower(str_replace(' ', '_', $productName)) . '.' . $fileExtension;
    $uploadFile = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        return $fileName;
    }
    
    return '';
}

/**
 * Delete image file
 */
function deleteImage($imageName, $uploadDir = 'assets/images/menu/') {
    if ($imageName && file_exists($uploadDir . $imageName)) {
        unlink($uploadDir . $imageName);
        return true;
    }
    return false;
}

/**
 * Get placeholder image
 */
function getPlaceholderImage($size = 60) {
    return "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iJHNpemUiIGhlaWdodD0iJHNpemUiIHZpZXdCb3g9IjAgMCAkc2l6ZSAkc2l6ZSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IiRzaXplIiBoZWlnaHQ9IiRzaXplIiBmaWxsPSIjMmQyZDJkIi8+Cjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZpbGw9IiNiMGIwYjAiPkltYWdlPC90ZXh0Pgo8L3N2Zz4K";
}

/**
 * Redirect with message
 */
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION[$type . '_message'] = $message;
    header('Location: ' . $url);
    exit;
}
?>