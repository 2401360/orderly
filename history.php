<?php
require_once 'app.php';
$page_title = 'Purchase History';
require_once 'header.php';
?>

<div class="container py-4">

  <?php
  /* ログイン確認 */
  if (!isset($_SESSION['customer'])) {
    echo '<div class="alert alert-warning">購入履歴を表示するには、ログインしてください。</div>';
    require_once 'footer.php';
    exit;
  }

  $pdo = db();

  /* ================================
   Pagination: 5 orders per page
================================ */
  $perPage = 5;
  $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
  $offset = ($page - 1) * $perPage;

  /* ===== 総注文数 ===== */
  $sqlCount = $pdo->prepare(
    'SELECT COUNT(*) FROM purchase WHERE customer_id = ?'
  );
  $sqlCount->execute([$_SESSION['customer']['id']]);
  $totalOrders = (int)$sqlCount->fetchColumn();
  $totalPages = max(1, (int)ceil($totalOrders / $perPage));

  /* ===== このページの注文ID一覧 ===== */
  $sql = $pdo->prepare(
    'SELECT id, created_at
     FROM purchase
     WHERE customer_id = ?
     ORDER BY id DESC
     LIMIT ? OFFSET ?'
  );
  $sql->bindValue(1, $_SESSION['customer']['id'], PDO::PARAM_INT);
  $sql->bindValue(2, $perPage, PDO::PARAM_INT);
  $sql->bindValue(3, $offset, PDO::PARAM_INT);
  $sql->execute();
  $purchases = $sql->fetchAll(PDO::FETCH_ASSOC);

  /* ===== 注文が0件 ===== */
  if (empty($purchases)) {
    echo '<div class="alert alert-info">購入履歴はありません。</div>';
    require_once 'footer.php';
    exit;
  }

  /* ================================================
   For each order, get purchase_detail + product
================================================ */
  foreach ($purchases as $purchase):
    $pid = (int)$purchase['id'];

    $sql2 = $pdo->prepare(
      'SELECT 
            product.id,
            product.name,
            product.price,
            purchase_detail.count
         FROM purchase_detail
         JOIN product ON product.id = purchase_detail.product_id
         WHERE purchase_detail.purchase_id = ?'
    );
    $sql2->execute([$pid]);

    $total = 0;
  ?>
    <div class="card mb-4">
      <div class="card-header bg-light">
        注文番号 #<?= $pid ?>　
        <span class="text-muted"><?= htmlspecialchars($purchase['created_at']) ?></span>
      </div>

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
                $total += $subtotal;
              ?>
                <tr>
                  <td><?= (int)$row['id'] ?></td>
                  <td>
                    <a href="detail.php?id=<?= (int)$row['id'] ?>">
                      <?= htmlspecialchars($row['name']) ?>
                    </a>
                  </td>
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

  <!-- ==========================
          PAGINATION
=========================== -->
  <nav>
    <ul class="pagination justify-content-center">

      <!-- 前へ -->
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page - 1 ?>">« 前へ</a>
      </li>

      <!-- ページ番号 -->
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <!-- 次へ -->
      <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page + 1 ?>">次へ »</a>
      </li>

    </ul>
  </nav>

</div>

<?php require_once 'footer.php'; ?>