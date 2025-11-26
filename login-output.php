<?php
session_start();
require_once 'app.php';

unset($_SESSION['customer']);
try {
    $pdo = db();
    $sql = $pdo->prepare('SELECT * FROM customer WHERE login = ? LIMIT 1');
    $sql->execute([$_POST['login'] ?? '']);
    $user = $sql->fetch();
    $ok = $user && password_verify($_POST['password'] ?? '', $user['password']);

    if (!$ok) {
        usleep(250000);
    }
    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['customer'] = [
            'id'      => (int)$user['id'],
            'name'    => $user['name'],
            'address' => $user['address'],
            'login'   => $user['login'],
            'role'   => $user['role'],

        ];
        require_once 'header.php';
        echo '<div class="container pt-3"><div class="alert alert-success">いらっしゃいませ、' . e($_SESSION['customer']['name']) . ' さん。</div></div>';
        echo '<div class="container pb-3"><a class="btn btn-primary" href="index.php">トップへ</a></div>';
    } else {
        require_once 'header.php';
        echo '<div class="container pt-3"><div class="alert alert-danger">ログイン名またはパスワードが違います。</div></div>';
    }
} catch (Throwable $e) {
    echo '<div class="container pt-3"><div class="alert alert-danger">ログイン処理でエラーが発生しました。</div></div>';
}
require_once 'footer.php';
