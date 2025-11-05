<?php $page_title='Confirm Purchase'; require 'header.php'; ?>
<div class="container py-4 content-narrow">
  <style>
.customer-info {
  background: #f8f9fa; /* màu nền nhẹ */
  border: 1px solid #dee2e6;
  border-radius: 10px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.customer-info h1 {
  font-size: 1.2rem;
  font-weight: 600;
  color: #333;
  border-left: 4px solid #0d6efd;
  padding-left: 10px;
  margin-bottom: 15px;
}

.customer-info p {
  margin-bottom: 8px;
  font-size: 1rem;
}

.customer-info p span {
  font-weight: bold;
  color: #555;
}
</style>

<?php
if (!isset($_SESSION['customer'])) {
  echo '<div class="alert alert-warning">購入手続きを行うにはログインしてください。</div>';
  require 'footer.php'; exit;
}
if (empty($_SESSION['product'])) {
  echo '<div class="alert alert-info">カートが空です。</div>';
  require 'footer.php'; exit;
}

if(!empty($_SESSION['product']) && isset($_SESSION['customer'])){
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
<?php require 'footer.php'; ?>
