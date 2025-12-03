<?php
require_once 'app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['customer']);

try {
    $pdo = db();
    $sql = $pdo->prepare('SELECT * FROM customer WHERE login = ? LIMIT 1');
    $sql->execute([$_POST['login'] ?? '']);
    $user = $sql->fetch();
    $ok = $user && password_verify($_POST['password'] ?? '', $user['password']);

    if (!$ok) {
        usleep(250000); // 遅延
    }

    if ($ok) {
        session_regenerate_id(true); // HTML 出力より前なのでOK
        $_SESSION['customer'] = [
            'id'      => (int)$user['id'],
            'name'    => $user['name'],
            'address' => $user['address'],
            'login'   => $user['login'],
            'role'    => $user['role'],
        ];
        $login_success = true;
    } else {
        $login_success = false;
    }
} catch (Throwable $e) {
    $login_success = null; // エラー扱い
}

// ここから画面出力開始
$page_title = 'Login result';
require_once 'header.php';
?>

<div class="container pt-3">
    <?php if ($login_success === true): ?>
        <div class="alert alert-success">
            いらっしゃいませ、<?php echo e($_SESSION['customer']['name']); ?> さん。
        </div>
        <a class="btn btn-primary" href="index.php">トップへ</a>

    <?php elseif ($login_success === false): ?>
        <div class="alert alert-danger">
            ログイン名またはパスワードが違います。
        </div>

    <?php else: ?>
        <div class="alert alert-danger">
            ログイン処理でエラーが発生しました。
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>