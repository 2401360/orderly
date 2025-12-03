<?php
require_once 'app.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$pdo = db();

$cid = $_SESSION['customer']['id'] ?? 0;
$pid = $_POST['product_id'] ?? 0;

if ($cid <= 0 || $pid <= 0) {
    http_response_code(400);
    echo "invalid";
    exit;
}

// すでにあるかチェック
$st = $pdo->prepare("SELECT 1 FROM favorite WHERE customer_id=? AND product_id=? LIMIT 1");
$st->execute([$cid, $pid]);

if (!$st->fetchColumn()) {
    $ins = $pdo->prepare("INSERT INTO favorite (customer_id, product_id) VALUES (?, ?)");
    $ins->execute([$cid, $pid]);
}

echo "ok";
exit;
