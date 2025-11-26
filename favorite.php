<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db-connect.php';

echo '<div class="container py-4">';

if (!isset($_SESSION['customer'])) {
  echo '<div class="alert alert-warning">お気に入りを表示するには、ログインしてください。</div>';
  echo '</div>';
  return;
}

$pdo = new PDO($connect, USER, PASS, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$sql = $pdo->prepare(
  'SELECT p.id, p.name, p.price
     FROM favorite f
     JOIN product p ON p.id = f.product_id
    WHERE f.customer_id = ?
    ORDER BY p.id'
);
$sql->execute([$_SESSION['customer']['id']]);
$rows = $sql->fetchAll();

echo '<h1 class="h4 mb-3"><i class="bi bi-heart text-danger"></i> お気に入り</h1>';

if (!$rows) {
  echo '<div class="alert alert-info">お気に入りはまだありません。</div>';
  echo '</div>';
  return;
}
?>
<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>商品番号</th>
        <th>商品名</th>
        <th>価格</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): $id = (int)$r['id']; ?>
        <tr>
          <td><?= $id ?></td>
          <td><a href="detail.php?id=<?= $id ?>"><?= htmlspecialchars($r['name']) ?></a></td>
          <td>¥<?= number_format((int)$r['price']) ?></td>
          <td><a class="btn btn-sm btn-outline-danger" href="favorite-delete.php?id=<?= $id ?>"><i class="bi bi-trash"></i> 削除</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php echo '</div>'; ?>