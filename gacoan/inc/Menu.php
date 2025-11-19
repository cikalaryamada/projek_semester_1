<?php
require_once __DIR__ . '/db.php';

class Menu {
  public static function allPublic() {
    global $mysqli;
    $res = $mysqli->query("SELECT * FROM menus WHERE is_available=1 ORDER BY id DESC");
    return $res->fetch_all(MYSQLI_ASSOC);
  }

  public static function allAdmin() {
    global $mysqli;
    $res = $mysqli->query("SELECT * FROM menus ORDER BY id DESC");
    return $res->fetch_all(MYSQLI_ASSOC);
  }

  public static function create($name, $category, $price, $is_available) {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO menus(name, category, price, is_available) VALUES(?,?,?,?)");
    $stmt->bind_param("ssii", $name, $category, $price, $is_available);
    return $stmt->execute();
  }

  public static function update($id, $name, $category, $price, $is_available) {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE menus SET name=?, category=?, price=?, is_available=? WHERE id=?");
    $stmt->bind_param("ssiii", $name, $category, $price, $is_available, $id);
    return $stmt->execute();
  }

  public static function remove($id) {
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM menus WHERE id=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
  }
}
