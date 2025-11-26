<?php session_start(); ?>
<?php require_once 'header.php'; ?>
<?php
$id = $_REQUEST['id'] ?? null;
$name = $_REQUEST['name'] ?? '';
$price = (int)($_REQUEST['price'] ?? 0);
$count = (int)($_REQUEST['count'] ?? 1);
if (!$id) {
  echo '<div class="container pt-3"><div class="alert alert-danger">不正な商品です。</div></div>';
  require_once 'footer.php';
  exit;
}
if (!isset($_SESSION['product'])) $_SESSION['product'] = [];
$prev = $_SESSION['product'][$id]['count'] ?? 0;
$_SESSION['product'][$id] = [
  'name'  => $name,
  'price' => $price,
  'count' => $prev + $count
];
echo '<div class="container pt-3"><div class="alert alert-success">カートに商品を追加しました。</div></div>';
require_once 'cart.php';
?>
<?php require_once 'footer.php'; ?>
