<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../inc/OrderItem.php';

$order_id = intval($_GET['order_id'] ?? 0);
if (!$order_id) { http_response_code(400); echo json_encode(['error'=>'order_id diperlukan']); exit; }

$items = OrderItem::byOrder($order_id);
echo json_encode(['order_id'=>$order_id, 'items'=>$items]);
