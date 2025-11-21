<?php $page_title = 'Purchase History';
require 'header.php'; ?>
<?php require_once 'db-connect.php'; ?>
<div class="container py-4">
  <?php
  if (!isset($_SESSION['customer'])) {
    echo '<div class="alert alert-warning">購入履歴を表示するには、ログインしてください。</div>';
    require 'footer.php';
    exit;
  }
  $pdo = new PDO($connect, USER, PASS);
  $sql = $pdo->prepare('select id from purchase where customer_id=? order by id desc');
  $sql->execute([$_SESSION['customer']['id']]);
  $purchases = $sql->fetchAll(PDO::FETCH_COLUMN);
  if (empty($purchases)) {
    echo '<div class="alert alert-info">購入履歴はありません。</div>';
    require 'footer.php';
    exit;
  }
  foreach ($purchases as $pid):
    $sql2 = $pdo->prepare(
      'select product.id, product.name, product.price, purchase_detail.count
       from purchase_detail
       join product on product.id = purchase_detail.product_id
      where purchase_detail.purchase_id = ?'
    );
    $sql2->execute([$pid]);
    $total = 0;
  ?>
    <div class="card mb-4">
      <div class="card-header bg-light">注文番号 #<?= (int)$pid ?></div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>商品番号</th>
                <th>商品名</th>
                <th>価格</th>
                <th>個数</th>
                <th>小計</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($sql2 as $row):
                $subtotal = $row['price'] * $row['count'];
                $total += $subtotal; ?>
                <tr>
                  <td><?= (int)$row['id'] ?></td>
                  <td><a href="detail.php?id=<?= (int)$row['id'] ?>"><?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?></a></td>
                  <td>¥<?= number_format($row['price']) ?></td>
                  <td><?= (int)$row['count'] ?></td>
                  <td>¥<?= number_format($subtotal) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th>合計</th>
                <td></td>
                <td></td>
                <td></td>
                <th>¥<?= number_format($total) ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php require 'footer.php'; ?>