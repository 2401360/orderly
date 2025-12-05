<?php
require_once 'app.php';

$id    = $_POST['product_id'] ?? null;
$name  = $_POST['name'] ?? '';
$price = (int)($_POST['price'] ?? 0);
$count = isset($_POST['count']) ? (int)$_POST['count'] : 1;

if (!$id) {
  echo '<div class="container pt-3"><div class="alert alert-danger">不正な商品です。</div></div>';
  require_once 'footer.php';
  exit;
}

if (!isset($_SESSION['product'])) {
  $_SESSION['product'] = [];
}

$prevCount = $_SESSION['product'][$id]['count'] ?? 0;
$newCount = $prevCount + $count;

$_SESSION['product'][$id] = [
  'name'  => $name,
  'price' => $price,
  'count' => $newCount
];

header("Location: cart-show.php");
exit;
