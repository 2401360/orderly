<?php
session_start();
require_once 'app.php';

if (!isset($_SESSION['customer'])) {
    flash('warning', '購入手続きを行うにはログインしてください。');
    header('Location: login-input.php');
    exit;
}
if (empty($_SESSION['product'])) {
    flash('info', 'カートが空です。');
    header('Location: cart-show.php');
    exit;
}

$pdo = db();
try {
    $pdo->beginTransaction();
    $ins_p = $pdo->prepare('INSERT INTO purchase (customer_id) VALUES (?)');
    $ins_p->execute([$_SESSION['customer']['id']]);
    $pid = (int)$pdo->lastInsertId();

    $ins_d = $pdo->prepare('INSERT INTO purchase_detail (purchase_id, product_id, count) VALUES (?, ?, ?)');

    foreach ($_SESSION['product'] as $id => $item) {
        $id = (int)$id;
        $count = max(1, (int)($item['count'] ?? 1));
        $ins_d->execute([$pid, $id, $count]);
    }
    $pdo->commit();
    $_SESSION['product'] = [];
    flash('success', 'ご購入ありがとうございました。注文番号 #' . $pid . ' を作成しました。');
    header('Location: history.php');
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('danger', '購入処理でエラーが発生しました。');
    header('Location: cart-show.php');
    exit;
}
