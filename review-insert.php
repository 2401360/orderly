<?php
session_start();
require_once __DIR__ . '/db-connect.php';

if (empty($_SESSION['customer'])) {
    header('Location: login-input.php');
    exit;
}
$customerId = (int)$_SESSION['customer']['id'];
$productId  = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$rating     = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$comment    = isset($_POST['comment']) ? trim((string)$_POST['comment']) : '';

if ($productId <= 0 || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo '不正な入力です。';
    exit;
}

try {
    $pdo = new PDO('mysql:host=' . SERVER . ';dbname=' . DBNAME . ';charset=utf8mb4', USER, PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'DB接続に失敗しました。';
    exit;
}


$sql = '
SELECT 1
FROM purchase_detail pd
JOIN purchase p ON p.id = pd.purchase_id
WHERE p.customer_id = ? AND pd.product_id = ?
LIMIT 1';
$st = $pdo->prepare($sql);
$st->execute([$customerId, $productId]);
if (!$st->fetchColumn()) {
    http_response_code(403);
    echo 'この商品は未購入のため、レビューできません。';
    exit;
}


$sql = '
INSERT INTO review (product_id, customer_id, rating, comment, created_at)
VALUES (?, ?, ?, ?, NOW())
ON DUPLICATE KEY UPDATE
  rating = VALUES(rating),
  comment = VALUES(comment)';
$st = $pdo->prepare($sql);
$st->execute([$productId, $customerId, $rating, $comment]);

header('Location: detail.php?id=' . $productId);
exit;
