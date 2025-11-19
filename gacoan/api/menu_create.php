<?php
require_once __DIR__ . '/../inc/Menu.php';

$name = trim($_POST['name'] ?? '');
$category = trim($_POST['category'] ?? '');
$price = intval($_POST['price'] ?? 0);
$is_available = isset($_POST['is_available']) ? 1 : 0;

if ($name && $category && $price > 0) {
  Menu::create($name, $category, $price, $is_available);
}
header('Location: ../public/admin.php');
