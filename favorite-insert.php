<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require 'db-connect.php';
require_once __DIR__ . '/app.php';

if (!isset($_SESSION['customer'])) {
    flash('warning', 'お気に入りに追加するにはログインしてください。');
    header('Location: login-input.php');
    exit;
}

$productId = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($productId <= 0) {
    flash('danger', '不正な商品です。');
    header('Location: product.php');
    exit;
}

$pdo = db();
$stm = $pdo->prepare('INSERT IGNORE INTO favorite (customer_id, product_id) VALUES (?, ?)');
$stm->execute([$_SESSION['customer']['id'], $productId]);

if ($stm->rowCount() === 0) {
    flash('info', 'この商品はすでにお気に入りに入っています。');
} else {
    flash('success', 'お気に入りに追加しました。');
}

header('Location: favorite-show.php');
exit;
