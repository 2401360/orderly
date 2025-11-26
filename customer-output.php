<?php
session_start();
require_once 'app.php';
require_once 'header.php';

function post($key)
{
    return trim($_POST[$key] ?? '');
}

try {
    $pdo = db();

    $name     = post('name');
    $address  = post('address');
    $login    = post('login');
    $password = post('password');


    $errors = [];
    if ($name === '')     $errors[] = '名前を入力してください。';
    if ($address === '')  $errors[] = '住所を入力してください。';
    if ($login === '')    $errors[] = 'ログイン名を入力してください。';
    if (strlen($password) < 6) $errors[] = 'パスワードは6文字以上にしてください。';

    if ($errors) {
        echo '<div class="container pt-3"><div class="alert alert-danger"><ul class="mb-0">';
        foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>';
        echo '</ul></div></div>';
        require_once 'footer.php';
        exit;
    }


    if (isset($_SESSION['customer'])) {
        $id  = (int)$_SESSION['customer']['id'];
        $chk = $pdo->prepare('SELECT id FROM customer WHERE id != ? AND login = ? LIMIT 1');
        $chk->execute([$id, $login]);
    } else {
        $chk = $pdo->prepare('SELECT id FROM customer WHERE login = ? LIMIT 1');
        $chk->execute([$login]);
    }
    if ($chk->fetch()) {
        echo '<div class="container pt-3"><div class="alert alert-danger">ログイン名がすでに使用されています。変更してください。</div></div>';
        require_once 'footer.php';
        exit;
    }


    $hash = password_hash($password, PASSWORD_DEFAULT);

    if (isset($_SESSION['customer'])) {

        $id  = (int)$_SESSION['customer']['id'];
        $upd = $pdo->prepare('UPDATE customer SET name=?, address=?, login=?, password=? WHERE id=?');
        $upd->execute([$name, $address, $login, $hash, $id]);


        $_SESSION['customer'] = [
            'id'      => $id,
            'name'    => $name,
            'address' => $address,
            'login'   => $login,
        ];
        echo '<div class="container pt-3"><div class="alert alert-success">お客様情報を更新しました。</div></div>';
    } else {

        $ins = $pdo->prepare('INSERT INTO customer (name, address, login, password) VALUES (?, ?, ?, ?)');
        $ins->execute([$name, $address, $login, $hash]);


        $newId = (int)$pdo->lastInsertId();
        $_SESSION['customer'] = [
            'id'      => $newId,
            'name'    => $name,
            'address' => $address,
            'login'   => $login,
        ];
        echo '<div class="container pt-3"><div class="alert alert-success">お客様情報を登録しました。ログイン済みです。</div></div>';
    }

    echo '<div class="container pb-3"><a class="btn btn-primary" href="index.php">トップへ</a></div>';
} catch (Throwable $e) {

    echo '<div class="container pt-3"><div class="alert alert-danger">登録処理でエラーが発生しました。</div></div>';
}
require_once 'footer.php';
