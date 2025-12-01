<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'header.php';
require_once 'app.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$pdo = db();

$cid = !empty($_SESSION['customer']['id']) ? (int)$_SESSION['customer']['id'] : 0;
$isFav = false;

// 商品情報
$st = $pdo->prepare('SELECT id, name, price, image_url, description FROM product WHERE id = ?');
$st->execute([$productId]);
$product = $st->fetch();

if ($cid > 0 && $product) {
  $stFav = $pdo->prepare('SELECT 1 FROM favorite WHERE customer_id = ? AND product_id = ? LIMIT 1');
  $stFav->execute([$cid, $productId]);
  $isFav = (bool)$stFav->fetchColumn();
}

if (!$product) {
  http_response_code(404);
  echo '<div class="container py-5"><div class="alert alert-danger">商品が見つかりません。</div></div>';
  exit;
}

// レビュー平均
$st = $pdo->prepare('SELECT ROUND(AVG(rating),1) AS avg_rating, COUNT(*) AS cnt FROM review WHERE product_id = ?');
$st->execute([$productId]);
$stat = $st->fetch();

// レビュー一覧
$st = $pdo->prepare('
  SELECT r.rating, r.comment, r.created_at, c.name
  FROM review r
  JOIN customer c ON c.id = r.customer_id
  WHERE r.product_id = ?
  ORDER BY r.created_at DESC
  LIMIT 100
');
$st->execute([$productId]);
$reviews = $st->fetchAll();

// レビュー投稿権限
$eligible = false;
if ($cid > 0) {
  $st = $pdo->prepare('
    SELECT 1
    FROM purchase_detail pd
    JOIN purchase p ON p.id = pd.purchase_id
    WHERE p.customer_id = ? AND pd.product_id = ?
    LIMIT 1
  ');
  $st->execute([$cid, $productId]);
  $eligible = (bool)$st->fetchColumn();
}

/* ------------------------------
   おすすめ商品
--------------------------------*/
$sqlReco = "
SELECT p.*,
       CASE WHEN :cid1 > 0 AND EXISTS(
            SELECT 1
              FROM favorite f
             WHERE f.customer_id = :cid2
               AND f.product_id = p.id)
            THEN 1 ELSE 0 END AS is_fav
  FROM product p
 ORDER BY RAND()
 LIMIT 16";
$stReco = $pdo->prepare($sqlReco);
$stReco->bindValue(':cid1', $cid, PDO::PARAM_INT);
$stReco->bindValue(':cid2', $cid, PDO::PARAM_INT);
$stReco->execute();
$recommended = $stReco->fetchAll();
?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?> | 商品詳細</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
  <div class="container py-4">
    <nav class="mb-3">
      <a class="btn btn-outline-secondary btn-sm" href="index.php">← 一覧へ戻る</a>
    </nav>

    <style>
      .container {
        margin: 0 20px;
        max-width: 96vw;
      }

      .product-hero {
        padding: 40px 20px;
        margin-bottom: 40px;
        width: 96%;
        display: flex;
        align-items: center;
        background: #FFF6EE;
        box-shadow: none;
        margin-left: 21px;
      }

      .product-hero .hero-image {
        flex: 1 1 40%;
        max-width: 500px;
        order: 0;
      }

      .product-hero .hero-image img {
        width: 100%;
        border-radius: 12px;
      }

      .product-hero .hero-text {
        flex: 1 1 50%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 20px;
        padding-left: 50px;
        order: 1;
        text-align: left;
      }

      .product-hero .btn-review:hover {
        background-color: #e6a800;
      }

      .product-hero .btn-buy,
      .product-hero .btn-fav {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        font-weight: bold;
        border: none;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
      }

      .product-hero .btn-buy {
        background-color: #693529;
        color: #fff;
      }

      .product-hero .btn-buy:hover {
        background-color: #e6a800;
      }

      .product-hero .btn-fav {
        background-color: #ff4d4d;
        color: #fff;
        text-align: center;
      }

      .product-hero .btn-fav:hover {
        background-color: #e60000;
      }

      .product-hero .action-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
      }

      .product-hero .action-row>* {
        width: 100%;
      }

      @media (max-width: 768px) {
        .product-hero {
          flex-direction: column;
          padding: 20px;
        }

        .product-hero .hero-image,
        .product-hero .hero-text {
          flex: 1 1 100%;
          max-width: 100%;
          order: initial;
        }

        .product-hero .hero-text {
          padding-left: 0;
        }

        .form-label {
          display: block;
          margin-bottom: 10px;
        }
      }
    </style>

    <div class="product-hero">
      <div class="hero-text">
        <div>
          <h1 class="h3"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>

          <!-- ⭐ 平均評価（Bootstrapの黄色い星付き） -->
          <p class="text-muted mb-3">
            平均評価：
            <?php if ($stat['avg_rating']): ?>
              <?php
              $avg = (float)$stat['avg_rating'];
              $fullStars = floor($avg);
              $halfStar = ($avg - $fullStars >= 0.5);
              ?>
              <span class="text-warning">
                <?php for ($i = 0; $i < $fullStars; $i++): ?>
                  <i class="bi bi-star-fill"></i>
                <?php endfor; ?>
                <?php if ($halfStar): ?>
                  <i class="bi bi-star-half"></i>
                <?php endif; ?>
                <?php for ($i = $fullStars + ($halfStar ? 1 : 0); $i < 5; $i++): ?>
                  <i class="bi bi-star"></i>
                <?php endfor; ?>
              </span>
              <?= htmlspecialchars($stat['avg_rating']) ?> / 5（<?= (int)$stat['cnt'] ?>件）
            <?php else: ?>
              —（0件）
            <?php endif; ?>
          </p>
          <a href="#review-section" class="btn-review">レビューを見る</a>
          <p class="fs-4 fw-bold mt-2">¥<?= number_format((int)$product['price']) ?></p>
        </div>

        <!-- Dòng số lượng + yêu thích -->
        <div class="action-row">
          <div>
            <p class="form-label"><strong>数量</strong>
              <select name="count" class="form-select">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </p>
          </div>

          <!-- Nút giỏ hàng -->
          <form action="cart-insert.php" method="get">
            <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="price" value="<?= (int)$product['price'] ?>">
            <button type="submit" class="btn-buy">
              <i class="bi bi-bag-plus"></i> カートに入れる
            </button>
          </form>


          <?php if ($cid > 0): ?>
            <button type="button"
              class="btn-fav"
              data-id="<?= (int)$product['id'] ?>"
              data-fav="<?= $isFav ? '1' : '0' ?>"
              aria-pressed="<?= $isFav ? 'true' : 'false' ?>"
              title="<?= $isFav ? 'お気に入り解除' : 'お気に入りに追加' ?>">
              <i class="bi <?= $isFav ? 'bi-heart-fill' : 'bi-heart' ?>"></i> お気に入り
            </button>
          <?php else: ?>
            <a class="btn-fav" href="login-input.php" title="ログインしてお気に入りに追加">
              <i class="bi bi-heart"></i> お気に入り
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="hero-image">
        <?php if (!empty($product['image_url'])): ?>
          <img src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
      </div>
    </div>
    <?php
    // ===== STYLE =====
    ?>
    <style>
      .spec-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-left: 33px;
        padding-right: 9px;
        margin: 10px 20px;
        box-sizing: border-box;
        margin-left: -10px;
      }

      .spec-images {
        display: flex;
        flex-direction: row;
        gap: 20px;
        flex-wrap: nowrap;
      }

      .spec-images img {
        width: 120px;
        height: auto;
        border-radius: 8px;
      }

      .spec-img-box {
        text-align: center;
      }

      .spec-img-box img {
        width: 130px;
        height: auto;
        border-radius: 10px;
        display: block;
        margin: 0 auto 8px;
      }

      .spec-caption {
        font-size: 0.9rem;
        color: #555;
      }

      .table-spec {
        width: 100%;
        max-width: 600px;
        border-collapse: collapse;
        font-size: 0.95rem;
        color: #444;
        margin-bottom: 40px;
      }

      .table-spec td {
        padding: 10px 20px;
        border-bottom: 1px solid #e0dcd3;
        vertical-align: top;
      }

      .table-spec tr:last-child td {
        border-bottom: none;
      }

      .table-spec .td-key {
        width: 40%;
        background-color: #F9F6ED;
        font-weight: 600;
      }

      .table-spec .td-value {
        width: 65%;
      }

      .related-products {
        background-color: #F9F6ED;
        padding: 20px 20px;
      }

      .review {
        background-color: #EFE8E4;
        padding: 30px 30px;
      }
    </style>

    <?php
    // ===== PROCESS DESCRIPTION =====
    $desc = $product['description'] ?? '';

    $desc = preg_replace('/(kcal)(?!）)/i', "$1\n", $desc);
    $desc = preg_replace('/(\dg)(?!）)/i', "$1\n", $desc);

    $lines = preg_split('/\r\n|\r|\n/', trim($desc));

    function splitLine($line)
    {
      $line = trim($line);

      if (strpos($line, ':') !== false) {
        $parts = explode(':', $line, 2);
        return [trim($parts[0]), trim($parts[1])];
      }
      if (strpos($line, '）') !== false) {
        $parts = explode('）', $line, 2);
        return [trim($parts[0] . '）'), trim($parts[1])];
      }
      if (strpos($line, '：') !== false) {
        $parts = explode('：', $line, 2);
        return [trim($parts[0]), trim($parts[1])];
      }
      return [$line, ""];
    }
    ?>

    <!-- ===== TABLE OUTPUT ===== -->
    <div class="spec-container">

      <!-- TABLE -->
      <div>
        <table class="table-spec">
          <tbody>
            <?php foreach ($lines as $line): ?>
              <?php
              $line = trim($line);
              if ($line === '') continue;
              list($left, $right) = splitLine($line);
              ?>
              <tr>
                <td class="td-key"><?= htmlspecialchars($left, ENT_QUOTES, 'UTF-8') ?></td>
                <td class="td-value"><?= htmlspecialchars($right, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <!-- IMAGES -->
      <div class="spec-images">
        <div class="spec-img-box">
          <img src="./image/noshi.jpg">

        </div>

        <div class="spec-img-box">
          <img src="./image/wrap.jpg">

        </div>

        <div class="spec-img-box">
          <img src="./image/mcard.jpg">

        </div>

        <div class="spec-img-box">
          <img src="./image/bag.jpg">
        </div>
      </div>
    </div>
    <div class="mt-25px mt-md-55px pt-25px pt-md-35px" style="border-top: 2px solid #230e02;"></div>
    <?php
    if (!function_exists('e')) {
      function e($s)
      {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
      }
    }
    // Lấy sản phẩm liên quan (cùng category, không trùng id hiện tại)
    $related = [];
    if (!empty($product['category'])) {
      $sql = "
        SELECT p.*, " . ($cid > 0 ? "EXISTS(SELECT 1 FROM favorite f WHERE f.customer_id = :cid AND f.product_id = p.id) AS is_fav" : "0 AS is_fav") . "
        FROM product p
        WHERE p.category = :cat AND p.id != :id
        ORDER BY p.created_at DESC
        LIMIT 8
      ";
      $stmt = $pdo->prepare($sql);
      $params = [
        ':cat' => $product['category'],
        ':id'  => $product['id']
      ];
      if ($cid > 0) $params[':cid'] = $cid;
      $stmt->execute($params);
      $related = $stmt->fetchAll();
    }
    ?>
    <!-- ===== RELATED PRODUCTS SLIDER ===== -->
    <?php
    $related = [];
    $uploadDir = __DIR__ . '/uploads/products/';
    $category = $product['category'] ?? '';

    if ($category && is_dir($uploadDir)) {
      $files = glob($uploadDir . '*');

      foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) continue;
        $filename = basename($file);
        if (stripos($filename, $category) !== false || stripos($filename, 'product') !== false) {
          $related[] = [
            'id' => rand(1000, 9999),
            'name' => pathinfo($filename, PATHINFO_FILENAME),
            'price' => rand(500, 5000),
            'image_url' => $filename
          ];
        }
      }
      $related = array_slice($related, 0, 8);
    }
    ?>

    <!-- RELATED PRODUCTS SLIDER -->
    <section class="related-products mt-5">
      <h2 class="mb-5 text-center">こちらの商品もおすすめです</h2>
      <?php if ($recommended): ?>
        <div id="recoCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php
            $chunks = array_chunk($recommended, 4);
            foreach ($chunks as $i => $chunk):
            ?>
              <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                <div class="row row-cols-2 row-cols-md-4 g-2 px-3">
                  <?php foreach ($chunk as $p): ?>
                    <div class="col">
                      <div class="card h-100 shadow-sm border-0">
                        <!-- 画像を小さく -->
                        <img src="<?= e($p['image_url']) ?>"
                          alt="<?= e($p['name']) ?>"
                          class="card-img-top"
                          style="width: 100%; height: 100%; object-fit: cover;">

                        <div class="card-body p-2">
                          <h6 class="card-title mb-1 text-truncate" style="font-size: 0.85rem;">
                            <?= e($p['name']) ?>
                          </h6>
                          <p class="text-muted small mb-2">
                            ¥<?= number_format($p['price']) ?>
                          </p>

                          <a href="detail.php?id=<?= (int)$p['id'] ?>"
                            class="btn btn-sm btn-outline-primary w-100"
                            style="font-size: 0.75rem;">
                            詳細
                          </a>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>

                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#recoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>

          <button class="carousel-control-next" type="button" data-bs-target="#recoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      <?php endif; ?>
    </section>
    <div class="mt-25px mt-md-55px pt-25px pt-md-35px" style="border-top: 2px solid #230e02; margin-top: 50px;"></div>
    <!-- レビュー表示 -->
    <section id="review-section" class="review mt-5">
      <h2 class="mb-5 text-center">レビュー</h2>
      <!-- ⭐ 平均評価（Bootstrapの黄色い星付き） -->
      <p class="text-muted mb-3">
        平均評価：
        <?php if ($stat['avg_rating']): ?>
          <?php
          $avg = (float)$stat['avg_rating'];
          $fullStars = floor($avg);
          $halfStar = ($avg - $fullStars >= 0.5);
          ?>
          <span class="text-warning">
            <?php for ($i = 0; $i < $fullStars; $i++): ?>
              <i class="bi bi-star-fill"></i>
            <?php endfor; ?>
            <?php if ($halfStar): ?>
              <i class="bi bi-star-half"></i>
            <?php endif; ?>
            <?php for ($i = $fullStars + ($halfStar ? 1 : 0); $i < 5; $i++): ?>
              <i class="bi bi-star"></i>
            <?php endfor; ?>
          </span>
          <?= htmlspecialchars($stat['avg_rating']) ?> / 5（<?= (int)$stat['cnt'] ?>件）
        <?php else: ?>
          —（0件）
        <?php endif; ?>
      </p>
      <?php if ($eligible): ?>
        <p>
          <a class="btn btn-sm btn-primary" href="review-input.php?product_id=<?= $productId ?>">
            レビューを書く / 編集する
          </a>
        </p>
      <?php else: ?>
        <p class="text-muted">※レビュー投稿は購入者のみ可能です。</p>
      <?php endif; ?>

      <?php if ($reviews): ?>
        <ul class="list-unstyled vstack gap-3">
          <?php foreach ($reviews as $r): ?>
            <li class="border rounded p-3" style="background-color: #c3d2c2ff;">
              <div class="d-flex justify-content-between align-items-center">
                <strong><?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                <span class="text-warning">
                  <?php for ($i = 0; $i < (int)$r['rating']; $i++): ?>
                    <i class="bi bi-star-fill"></i>
                  <?php endfor; ?>
                  <?php for ($i = (int)$r['rating']; $i < 5; $i++): ?>
                    <i class="bi bi-star"></i>
                  <?php endfor; ?>
                </span>
              </div>
              <div class="mt-2"><?= nl2br(htmlspecialchars($r['comment'], ENT_QUOTES, 'UTF-8')) ?></div>
              <div class="text-end">
                <small class="text-muted"><?= htmlspecialchars($r['created_at'], ENT_QUOTES, 'UTF-8') ?></small>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>まだレビューはありません。</p>
      <?php endif; ?>
    </section>
    <!-- トースト通知 -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080;">
      <div id="favToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body small" id="favToastMsg">更新しました</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="閉じる"></button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (function() {
      const LOGGED_IN = <?= $cid > 0 ? 'true' : 'false' ?>;

      async function postFav(url, productId) {
        const body = new URLSearchParams();
        body.append('product_id', String(productId));
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body
        });
        return res.ok;
      }

      function toggleBtn(btn, isFav) {
        btn.dataset.fav = isFav ? '1' : '0';
        btn.setAttribute('aria-pressed', isFav ? 'true' : 'false');
        btn.classList.toggle('btn-danger', isFav);
        btn.classList.toggle('btn-outline-danger', !isFav);
        const icon = btn.querySelector('i');
        if (icon) {
          icon.classList.toggle('bi-heart-fill', isFav);
          icon.classList.toggle('bi-heart', !isFav);
        }
        btn.title = isFav ? 'お気に入り解除' : 'お気に入りに追加';
      }

      function showToast(msg) {
        const el = document.getElementById('favToast');
        const msgEl = document.getElementById('favToastMsg');
        if (!el || !window.bootstrap) return;
        msgEl.textContent = msg;
        const t = bootstrap.Toast.getOrCreateInstance(el, {
          delay: 1600
        });
        t.show();
      }

      document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.fav-btn');
        if (!btn) return;

        if (!LOGGED_IN) {
          window.location.href = 'login-input.php';
          return;
        }

        const productId = btn.dataset.id;
        const isFav = btn.dataset.fav === '1';
        btn.disabled = true;
        try {
          const ok = await postFav(isFav ? 'favorite-delete.php' : 'favorite-insert.php', productId);
          if (ok) {
            toggleBtn(btn, !isFav);
            showToast(!isFav ? 'お気に入りに追加しました' : 'お気に入りを解除しました');
          }
        } finally {
          btn.disabled = false;
        }
      });
    })();
  </script>
</body>

</html>