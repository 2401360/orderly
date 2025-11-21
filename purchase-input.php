<?php $page_title='Confirm Purchase'; require 'header.php'; ?>
<div class="container py-4 content-narrow">
<?php
if (!isset($_SESSION['customer'])) {
  echo '<div class="alert alert-warning">購入手続きを行うにはログインしてください。</div>';
  require 'footer.php'; exit;
}
if (empty($_SESSION['product'])) {
  echo '<div class="alert alert-info">カートが空です。</div>';
  require 'footer.php'; exit;
}
$total = 0;
foreach ($_SESSION['product'] as $it) {
  $total += (int)$it['price'] * (int)$it['count'];
}
?>
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="h5 mb-3">購入の確認</h1>
      <p class="mb-3">合計金額：<strong>¥<?= number_format($total) ?></strong></p>
      <form action="purchase-output.php" method="post" class="vstack gap-3">
        <?= csrf_field() ?>
        <button class="btn btn-primary w-100"><i class="bi bi-credit-card"></i> 購入を確定する</button>
      </form>
    </div>
  </div>
</div>
<?php require 'footer.php'; ?>
