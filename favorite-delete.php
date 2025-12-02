<?php
require_once 'app.php';
require_once 'header.php';
if (!isset($_SESSION['customer'])) {
    header('Location: login-input.php');
    exit;
}

$pdo = db();
$del = $pdo->prepare('DELETE FROM favorite WHERE customer_id=? AND product_id=?');
$del->execute([$_SESSION['customer']['id'], (int)($_REQUEST['id'] ?? 0)]);

header('Location: favorite-show.php');
exit;
