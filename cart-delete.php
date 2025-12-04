<?php
session_start();
require_once 'app.php';

$pdo = db();
$id = $_REQUEST['id'] ?? null;
if ($id !== null && isset($_SESSION['product'][$id])) {
    unset($_SESSION['product'][$id]);
    echo '<div class="container pt-3"><div class="alert alert-danger">カートから商品を削除しました。</div></div>';
}
header("Location: cart-show.php");
exit;
