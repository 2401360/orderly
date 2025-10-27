<?php $page_title='Products'; require 'header.php'; ?>
<?php require 'db-connect.php'; ?>
<div class="container py-4">
  <h1 class="h4 mb-3">商品一覧</h1>
  <form method="get" class="row g-2 mb-3 content-narrow">
    <div class="col-sm-9">
      <input class="form-control" type="search" name="keyword" placeholder="商品名で検索" value="<?= e($_GET['keyword'] ?? '') ?>">
    </div>
    <div class="col-sm-3 d-grid">
      <button class="btn btn-outline-secondary"><i class="bi bi-search"></i> 検索</button>
    </div>
  </form>

  <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
  <?php
    $pdo = new PDO($connect, USER, PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $kw = trim($_GET['keyword'] ?? '');
    if ($kw !== '') {
      $like = '%' . str_replace(['%', '_'], ['\%','\_'], $kw) . '%';
      $sql = $pdo->prepare("SELECT * FROM product WHERE name LIKE ? ESCAPE '\\' ORDER BY id");
      $sql->execute([$like]);
    } else {
      $sql = $pdo->query('SELECT * FROM product ORDER BY id');
    }
    foreach ($sql as $row):
      $id = (int)$row['id'];
  ?>
    <div class="col">
      <div class="card h-100 card-hover">
        <?php if (!empty($row['image_url'])): ?>
          <img src="<?= e($row['image_url']) ?>" class="card-img-top" alt="<?= e($row['name']) ?>">
        <?php endif; ?>
        <div class="card-body d-flex flex-column">
          <h3 class="h6 card-title mb-1"><?= e($row['name']) ?></h3>
          <div class="text-muted mb-3">¥<?= number_format((int)$row['price']) ?></div>
          <div class="mt-auto d-flex gap-2">
            <a class="btn btn-sm btn-outline-secondary" href="detail.php?id=<?= $id ?>"><i class="bi bi-info-circle"></i> Details</a>
            <a class="btn btn-sm btn-outline-danger" href="favorite-insert.php?id=<?= $id ?>"><i class="bi bi-heart"></i></a>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  </div>
</div>
<?php require 'footer.php'; ?>
