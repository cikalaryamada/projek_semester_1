<?php
require_once __DIR__ . '/db.php';

class OrderItem {
  public static function bulkCreate($order_id, $items) {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO order_items(order_id, menu_id, qty, price, subtotal) VALUES(?,?,?,?,?)");
    $total = 0;
    foreach ($items as $it) {
      $subtotal = intval($it['price']) * intval($it['qty']);
      $total += $subtotal;
      $menu_id = intval($it['menu_id']);
      $qty = intval($it['qty']);
      $price = intval($it['price']);
      $stmt->bind_param("iiiii", $order_id, $menu_id, $qty, $price, $subtotal);
      $stmt->execute();
    }
    return $total;
  }

  public static function byOrder($order_id) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM order_items WHERE order_id=?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_all(MYSQLI_ASSOC);
  }
}
