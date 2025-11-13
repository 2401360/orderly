<?php $page_title='Products'; require 'header.php'; ?>
<?php require 'db-connect.php'; ?>
<link href="css/style.css" rel="stylesheet"><div class="container py-4">
  <h1 class="h4 mb-3">商品一覧</h1>
  <form method="get" class="row g-2 mb-3 content-narrow">
    <div class="col-sm-9">
      <input class="form-control" type="search" name="keyword" placeholder="商品名で検索" value="<?= e($_GET['keyword'] ?? '') ?>">
    </div>
    <div class="col-sm-3 d-grid">
      <button class="btn btn-outline-secondary"><i class="bi bi-search"></i> 検索</button>
    </div>
  </form>

  <div class="showcase-container">
  <!-- 背景 -->
  <img src="image/background4.png" alt="ショーケース背景" class="showcase-bg">
  <div class="cake-area">
  <?php
    $pdo = new PDO($connect, USER, PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $kw = trim($_GET['keyword'] ?? '');
    if ($kw !== '') {
      //  検索モード
      $like = '%' . str_replace(['%', '_'], ['\%','\_'], $kw) . '%';
      $sql = $pdo->prepare("SELECT * FROM product WHERE name LIKE ? ESCAPE '\\' ORDER BY id");
      $sql->execute([$like]);

    } else {
      $sql = $pdo->query('SELECT * FROM product ORDER BY id');
    }
    // 最大12件（3段×4個）まで表示
        $tiers = [
            array_slice($sql, 0, 4),
            array_slice($sql, 4, 4),
            array_slice($sql, 8, 4),
        ];

      $tiers_y = [200, 360, 520]; // 各段のbottom位置
  foreach ($tiers as $tier_index => $cakes):
      $tier_y = $tiers_y[$tier_index] ?? null;
  ?>
    <div class="showcase-tier" style="bottom: <?= $tier_y ?>px;">
      <img src="image/single_showcase.png" alt="ショーケース棚" class="shelf">
        <?php foreach ($cakes as $i => $cake): ?>
          <div class="cake-item" >
            <img src="image/plate.png" class="plate" alt="皿">
            <img src="<?= e($cake['image_url']) ?>" class="cake-img" alt="<?= e($cake['name']) ?>">
          </div>
        <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
  </div>
</div>
</div>
<?php require 'footer.php'; ?>
