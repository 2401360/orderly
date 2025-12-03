<?php
require_once 'app.php';
$page_title = 'Confirm Purchase';
require_once 'header.php'; ?>

<div class="container py-4 content-narrow">
  <?php
  if (!isset($_SESSION['customer'])) {
    echo '<div class="alert alert-warning">購入手続きを行うにはログインしてください。</div>';
    require_once 'footer.php';
    exit;
  }
  if (empty($_SESSION['product'])) {
    echo '<div class="alert alert-info">カートが空です。</div>';
    require_once 'footer.php';
    exit;
  }
  if (!empty($_SESSION['product']) && isset($_SESSION['customer'])) {
    echo '<div class="customer-info">';
    echo '<h1>注文者情報</h1>';
    echo '<p><span>お名前：</span>', htmlspecialchars($_SESSION['customer']['name']), '</p>';
    echo '<p><span>ご住所：</span>', htmlspecialchars($_SESSION['customer']['address']), '</p>';
    echo '</div>';
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
<?php require_once 'footer.php'; ?>