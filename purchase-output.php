<?php
session_start();
require 'db-connect.php';
require_once __DIR__ . '/app.php';

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

if (!empty($_SESSION['product']) && isset($_SESSION['customer'])) {
    echo '<div class="customer-info">';
    echo '<h1>注文者情報</h1>';
    echo '<p><span>お名前：</span>', htmlspecialchars($_SESSION['customer']['name']), '</p>';
    echo '<p><span>ご住所：</span>', htmlspecialchars($_SESSION['customer']['address']), '</p>';
    echo '</div>';
}
?>
<style>
    .customer-info {
        background: #f8f9fa;
        /* màu nền nhẹ */
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .customer-info h1 {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
        border-left: 4px solid #0d6efd;
        padding-left: 10px;
        margin-bottom: 15px;
    }

    .customer-info p {
        margin-bottom: 8px;
        font-size: 1rem;
    }

    .customer-info p span {
        font-weight: bold;
        color: #555;
    }
</style>