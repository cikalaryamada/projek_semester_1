<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../inc/Order.php';
require_once __DIR__ . '/../inc/OrderItem.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
  // fallback form-encoded
  $input = $_POST;
  $input['items'] = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];
}

$customer_name = trim($input['customer_name'] ?? '');
$table_no = trim($input['table_no'] ?? '');
$items = $input['items'] ?? [];

if (!$customer_name || !$table_no || !is_array($items) || count($items)===0) {
  http_response_code(400);
  echo json_encode(['error' => 'Data tidak lengkap']);
  exit;
}

$order_id = Order::create($customer_name, $table_no);
if (!$order_id) {
  http_response_code(500);
  echo json_encode(['error' => 'Gagal membuat pesanan']);
  exit;
}

$total = OrderItem::bulkCreate($order_id, $items);
Order::setTotal($order_id, $total);

echo json_encode(['order_id' => $order_id, 'total' => $total]);
