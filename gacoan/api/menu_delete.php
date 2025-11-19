<?php
require_once __DIR__ . '/../inc/Menu.php';
$id = intval($_POST['id'] ?? 0);
if ($id) { Menu::remove($id); }
header('Location: ../public/admin.php');