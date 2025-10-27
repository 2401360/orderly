<div class="container py-4">
  <h1 class="h4 mb-3"><i class="bi bi-cart"></i> カート</h1>
  <?php if (empty($_SESSION['product'])): ?>
    <div class="alert alert-info">カートは空です。</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead><tr><th>商品名</th><th>価格</th><th>個数</th><th>小計</th><th></th></tr></thead>
        <tbody>
        <?php $total=0; foreach ($_SESSION['product'] as $id => $item):
          $subtotal = (int)$item['price'] * (int)$item['count']; $total += $subtotal; ?>
          <tr>
            <td><a href="detail.php?id=<?= (int)$id ?>"><?= htmlspecialchars($item['name']) ?></a></td>
            <td>¥<?= number_format($item['price']) ?></td>
            <td><?= (int)$item['count'] ?></td>
            <td>¥<?= number_format($subtotal) ?></td>
            <td><a class="btn btn-sm btn-outline-danger" href="cart-delete.php?id=<?= (int)$id ?>"><i class="bi bi-trash"></i> 削除</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr><th>合計</th><td></td><td></td><th>¥<?= number_format($total) ?></th><td></td></tr>
        </tfoot>
      </table>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="product.php">買い物を続ける</a>
      <a class="btn btn-primary" href="purchase-input.php"><i class="bi bi-credit-card"></i> 購入へ進む</a>
    </div>
  <?php endif; ?>
</div>
