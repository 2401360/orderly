<?php $page_title = 'Product Detail'; require 'header.php'; ?>
<?php require 'db-connect.php'; ?>
<div class="container py-4 content-narrow">
<?php
$pdo = new PDO($connect, USER, PASS);
$sql = $pdo->prepare('select * from product where id=?');
$sql->execute([$_REQUEST['id'] ?? 0]);
foreach ($sql as $row): ?>
  <div class="card shadow-sm">
    <div class="row g-0">
      <div class="col-md-5">
        <?php if (!empty($row['image_url'])): ?>
          <img src="<?= htmlspecialchars($row['image_url']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($row['name']) ?>">
        <?php endif; ?>
      </div>
      <div class="col-md-7">
        <div class="card-body">
          <h1 class="h4 mb-1"><?= htmlspecialchars($row['name']) ?></h1>
          <div class="text-muted mb-3">¥<?= number_format($row['price']) ?></div>
          <?php if (!empty($row['description'])): ?>
            <p class="mb-4"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
          <?php endif; ?>
          <form action="cart-insert.php" method="post" class="d-flex flex-wrap gap-2 align-items-end">
            <div>
              <label class="form-label">数量</label>
              <select name="count" class="form-select">
                <?php for ($i=1; $i<=10; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
            <input type="hidden" name="price" value="<?= (int)$row['price'] ?>">
            <button class="btn btn-primary"><i class="bi bi-bag-plus"></i> カートに追加</button>
            <a href="favorite-insert.php?id=<?= (int)$row['id'] ?>" class="btn btn-outline-danger"><i class="bi bi-heart"></i> お気に入り</a>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php require 'footer.php'; ?>
