<?php
session_start();
require 'db-connect.php';
require 'header.php';

unset($_SESSION['customer']);
try {
    $pdo = new PDO($connect, USER, PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $sql = $pdo->prepare('SELECT * FROM customer WHERE login = ? LIMIT 1');
    $sql->execute([$_POST['login'] ?? '']);
    $user = $sql->fetch();
    $ok = $user && password_verify($_POST['password'] ?? '', $user['password']);
    // Small delay to make brute force slightly harder (adjust or replace with proper throttling)
    if (!$ok) { usleep(250000); }
    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['customer'] = [
            'id'      => (int)$user['id'],
            'name'    => $user['name'],
            'address' => $user['address'],
            'login'   => $user['login'],
        ];
        echo '<div class="container pt-3"><div class="alert alert-success">いらっしゃいませ、' . e($_SESSION['customer']['name']) . ' さん。</div></div>';
    } else {
        echo '<div class="container pt-3"><div class="alert alert-danger">ログイン名またはパスワードが違います。</div></div>';
    }
} catch (Throwable $e) {
    echo '<div class="container pt-3"><div class="alert alert-danger">ログイン処理でエラーが発生しました。</div></div>';
}
require 'footer.php';
