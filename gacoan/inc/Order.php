<?php
require_once __DIR__ . '/db.php';

class Order {
  public static function create($customer_name, $table_no) {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO orders(customer_name, table_no, status, total) VALUES(?,?, 'baru', 0)");
    $stmt->bind_param("ss", $customer_name, $table_no);
    if (!$stmt->execute()) return false;
    return $mysqli->insert_id;
  }

  public static function setTotal($order_id, $total) {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE orders SET total=? WHERE id=?");
    $stmt->bind_param("ii", $total, $order_id);
    return $stmt->execute();
  }

  public static function list($status = null) {
    global $mysqli;
    if ($status) {
      $stmt = $mysqli->prepare("SELECT * FROM orders WHERE status=? ORDER BY id DESC");
      $stmt->bind_param("s", $status);
      $stmt->execute();
      $res = $stmt->get_result();
    } else {
      $res = $mysqli->query("SELECT * FROM orders ORDER BY id DESC");
    }
    return $res->fetch_all(MYSQLI_ASSOC);
  }

  public static function updateStatus($order_id, $status) {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $order_id);
    return $stmt->execute();
  }
}
