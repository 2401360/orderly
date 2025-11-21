<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db-connect.php';;
if (!isset($_SESSION['customer'])) {
    header('Location: login-input.php');
    exit;
}

$pdo = new PDO($connect, USER, PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$del = $pdo->prepare('DELETE FROM favorite WHERE customer_id=? AND product_id=?');
$del->execute([$_SESSION['customer']['id'], (int)($_REQUEST['id'] ?? 0)]);

header('Location: favorite-show.php');
exit;
