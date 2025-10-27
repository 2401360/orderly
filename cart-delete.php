<?php session_start(); ?>
<?php require 'header.php'; ?>
<?php
$id = $_REQUEST['id'] ?? null;
if ($id !== null && isset($_SESSION['product'][$id])) {
  unset($_SESSION['product'][$id]);
  echo '<div class="container pt-3"><div class="alert alert-danger">カートから商品を削除しました。</div></div>';
}
require 'cart.php';
?>
<?php require 'footer.php'; ?>
