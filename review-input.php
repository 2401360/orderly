<?php
require_once 'app.php';
$page_title = 'レビューを書く';
require_once 'header.php';

if (empty($_SESSION['customer'])) {
  header('Location: login-input.php');
  exit;
}
$customerId = (int)$_SESSION['customer']['id'];
$productId  = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

$pdo = db();

$sql = '
SELECT 1
FROM purchase_detail pd
JOIN purchase p ON p.id = pd.purchase_id
WHERE p.customer_id = ? AND pd.product_id = ?
LIMIT 1';
$st = $pdo->prepare($sql);
$st->execute([$customerId, $productId]);
$eligible = (bool)$st->fetchColumn();

if (!$eligible) {
  http_response_code(403);
  echo 'この商品は未購入のため、レビューできません。';
  exit;
}

$sql = 'SELECT rating, comment FROM review WHERE product_id = ? AND customer_id = ?';
$st  = $pdo->prepare($sql);
$st->execute([$productId, $customerId]);
$myReview = $st->fetch(PDO::FETCH_ASSOC);

$st = $pdo->prepare('SELECT name FROM product WHERE id = ?');
$st->execute([$productId]);
$product = $st->fetch(PDO::FETCH_ASSOC);
?>
<h1 class="mb-3">レビューを書く：<?= htmlspecialchars($product['name'] ?? '商品', ENT_QUOTES, 'UTF-8') ?></h1>

<form action="review-insert.php" method="post" class="vstack gap-3">
  <input type="hidden" name="product_id" value="<?= $productId ?>">

  <div>
    <label class="form-label">評価（1〜5）</label>
    <select name="rating" class="form-select" require_onced>
      <?php for ($i = 1; $i <= 5; $i++): ?>
        <option value="<?= $i ?>" <?= (isset($myReview['rating']) && (int)$myReview['rating'] === $i) ? 'selected' : ''; ?>>
          <?= $i ?>
        </option>
      <?php endfor; ?>
    </select>
  </div>

  <div>
    <label class="form-label">コメント</label>
    <textarea name="comment" rows="5" class="form-control" maxlength="1000"><?= htmlspecialchars($myReview['comment'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
  </div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary"><?= $myReview ? '更新する' : '投稿する' ?></button>
    <a class="btn btn-outline-secondary" href="detail.php?id=<?= $productId ?>">商品詳細へ戻る</a>
  </div>
</form>