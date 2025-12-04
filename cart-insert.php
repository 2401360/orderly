<?php
require_once 'app.php';
require_once 'header.php';

// 商品情報を POST で受け取る
$id    = $_POST['product_id'] ?? null;
$name  = $_POST['name'] ?? '';
$price = (int)($_POST['price'] ?? 0);

// count が送られてこないときは 1 にする
$count = isset($_POST['count']) ? (int)$_POST['count'] : 1;

// 商品IDが無い → エラー
if (!$id) {
  echo '<div class="container pt-3"><div class="alert alert-danger">不正な商品です。</div></div>';
  require_once 'footer.php';
  exit;
}

// カート用のセッションが無ければ作成
if (!isset($_SESSION['product'])) {
  $_SESSION['product'] = [];
}

// 既存の個数（あれば取得）
$prevCount = $_SESSION['product'][$id]['count'] ?? 0;

// 新しい合計個数
$newCount = $prevCount + $count;

// セッションに保存
$_SESSION['product'][$id] = [
  'name'  => $name,
  'price' => $price,
  'count' => $newCount
];

// 完了メッセージ
echo '<div class="container pt-3">
        <div class="alert alert-success">
          カートに商品を追加しました。（個数: ' . $newCount . '）
        </div>
      </div>';

// カート画面を表示
require_once 'cart.php';
require_once 'footer.php';
