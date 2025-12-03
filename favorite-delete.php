<?php
require_once 'app.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$pdo = db();

$cid = $_SESSION['customer']['id'] ?? 0;
$pid = $_POST['product_id'] ?? 0;

if ($cid <= 0 || $pid <= 0) {
    http_response_code(400);
    exit("invalid");
}

$del = $pdo->prepare("DELETE FROM favorite WHERE customer_id=? AND product_id=?");
$del->execute([$cid, $pid]);

echo "ok";
exit;
