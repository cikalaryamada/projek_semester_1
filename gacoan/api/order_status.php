<?php
require_once __DIR__ . '/../inc/Order.php';
$id = intval($_POST['id'] ?? 0);
$status = $_POST['status'] ?? 'baru';
$allowed = ['baru','diproses','siap','selesai','batal'];
if ($id && in_array($status, $allowed)) {
  Order::updateStatus($id, $status);
}
header('Location: ../public/admin.php');
