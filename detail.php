<?php
require_once 'app.php';
$page_title = '商品詳細';
require_once 'header.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo = db();

$cid = !empty($_SESSION['customer']['id']) ? (int)$_SESSION['customer']['id'] : 0;
$isFav = false;

/* 商品情報 */
$st = $pdo->prepare('SELECT id, name, price, image_url, description FROM product WHERE id = ?');
$st->execute([$productId]);
$product = $st->fetch();

if ($cid > 0 && $product) {
  $stFav = $pdo->prepare('SELECT 1 FROM favorite WHERE customer_id = ? AND product_id = ? LIMIT 1');
  $stFav->execute([$cid, $productId]);
  $isFav = (bool)$stFav->fetchColumn();
}

if (!$product) {
  echo '<div class="container py-5"><div class="alert alert-danger">商品が見つかりません。</div></div>';
  exit;
}

/* レビュー平均 */
$st = $pdo->prepare('SELECT ROUND(AVG(rating),1) AS avg_rating, COUNT(*) AS cnt FROM review WHERE product_id = ?');
$st->execute([$productId]);
$stat = $st->fetch();

/* レビュー一覧 */
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

/* レビュー投稿権限 */
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

/* おすすめ商品 16件 */
$sqlReco = "
 SELECT p.*,
        CASE WHEN :cid1 > 0 AND EXISTS(
            SELECT 1 FROM favorite f WHERE f.customer_id = :cid2 AND f.product_id = p.id
        ) THEN 1 ELSE 0 END AS is_fav
   FROM product p
 ORDER BY RAND()
 LIMIT 16
";
$stReco = $pdo->prepare($sqlReco);
$stReco->bindValue(':cid1', $cid, PDO::PARAM_INT);
$stReco->bindValue(':cid2', $cid, PDO::PARAM_INT);
$stReco->execute();
$recommended = $stReco->fetchAll();
?>

<style>
  /* ===== your CSS (unchanged) ===== */
  .product-hero {
    display: flex;
    align-items: center;
    background: #FFF6EE;
    padding: 40px;
    margin-bottom: 40px;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
    gap: 40px;
    border: 1px solid #f0f0f0;
    transition: .25s;
  }

  .product-hero:hover {
    box-shadow: 0 10px 28px rgba(0, 0, 0, .12);
  }

  .product-hero .hero-image {
    flex: 1 1 40%;
    max-width: 500px;
  }

  .product-hero .hero-image img {
    width: 100%;
    border-radius: 12px;
  }

  .product-hero .hero-text {
    flex: 1 1 50%;
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding-left: 50px;
  }

  .spec-container {
    background: #FFF;
    display: flex;
    justify-content: space-between;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
    border: 1px solid #f0f0f0;
    gap: 40px;
  }

  .spec-images {
    display: flex;
    flex-direction: row;
    gap: 20px;
  }

  .spec-img-box img {
    width: 130px;
    border-radius: 10px;
  }

  .table-spec {
    width: 100%;
    max-width: 600px;
    border-collapse: collapse;
    font-size: .95rem;
    color: #444;
    margin-bottom: 40px;
    border: 1px solid #ddd;
  }

  .table-spec td {
    padding: 10px 20px;
    border-bottom: 1px solid #e0dcd3;
  }

  .table-spec tr:last-child td {
    border-bottom: none;
  }

  .table-spec .td-key {
    width: 40%;
    background: #F9F6ED;
    font-weight: 600;
  }

  .review {
    background: #EFE8E4;
    padding: 40px;
    border-radius: 16px;
    margin-top: 50px;
  }

  .reco-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #5a4332;
    text-align: center;
    margin-bottom: 1rem;
    letter-spacing: 0.5px;
  }
</style>

<div class="container py-4">

  <!-- 商品ヘッダー -->
  <div class="product-hero">
    <div class="hero-text">
      <div>
        <h1 class="h3"><?= e($product['name']) ?></h1>

        <!-- 平均評価 -->
        <p class="text-muted mb-3">
          平均評価：
          <?php if ($stat['avg_rating']): ?>
            <?php
            $avg = (float)$stat['avg_rating'];
            $fullStars = floor($avg);
            $half = ($avg - $fullStars >= 0.5);
            ?>
            <span class="text-warning">
              <?php for ($i = 0; $i < $fullStars; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
              <?php if ($half): ?><i class="bi bi-star-half"></i><?php endif; ?>
              <?php for ($i = $fullStars + ($half ? 1 : 0); $i < 5; $i++): ?><i class="bi bi-star"></i><?php endfor; ?>
            </span>
            <?= $stat['avg_rating'] ?> / 5（<?= (int)$stat['cnt'] ?>件）
          <?php else: ?>
            —（0件）
          <?php endif; ?>
        </p>

        <a href="#review-section" class="btn-review">レビューを見る</a>

        <p class="fs-4 fw-bold mt-2">¥<?= number_format((int)$product['price']) ?></p>
      </div>

      <div class="action-row">
        <p class="form-label"><strong>数量</strong>
          <select name="count" class="form-select">
            <?php for ($i = 1; $i <= 10; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
          </select>
        </p>

        <form action="cart-insert.php" method="get">
          <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
          <input type="hidden" name="name" value="<?= e($product['name']) ?>">
          <input type="hidden" name="price" value="<?= (int)$product['price'] ?>">
          <button class="btn-buy"><i class="bi bi-bag-plus"></i> カートに入れる</button>
        </form>

        <?php if ($cid > 0): ?>
          <button class="btn-fav" data-id="<?= (int)$product['id'] ?>" data-fav="<?= $isFav ? 1 : 0 ?>">
            <i class="bi <?= $isFav ? 'bi-heart-fill' : 'bi-heart' ?>"></i> お気に入り
          </button>
        <?php else: ?>
          <a class="btn-fav" href="login-input.php"><i class="bi bi-heart"></i> お気に入り</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="hero-image">
      <img src="<?= e($product['image_url']) ?>">
    </div>
  </div>

  <?php
  /* 説明 → テーブル化 */
  $desc = $product['description'] ?? '';
  $desc = preg_replace('/(kcal)/i', "$1\n", $desc);
  $desc = preg_replace('/(\dg)/i', "$1\n", $desc);
  $lines = preg_split('/\r\n|\r|\n/', trim($desc));

  function splitLine($line)
  {
    $line = trim($line);
    if (strpos($line, ':') !== false) return explode(':', $line, 2);
    if (strpos($line, '：') !== false) return explode('：', $line, 2);
    return [$line, ""];
  }
  ?>

  <!-- TABLE + IMAGES + おすすめスライダー -->
  <div class="spec-container">

    <!-- LEFT (テーブル) -->
    <div style="flex:1;">
      <table class="table-spec">
        <tbody>
          <?php foreach ($lines as $line): ?>
            <?php if (trim($line) === '') continue;
            list($left, $right) = splitLine($line); ?>
            <tr>
              <td class="td-key"><?= e($left) ?></td>
              <td class="td-value"><?= e($right) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- RIGHT (画像 + おすすめスライダー) -->
    <div style="flex:1;">

      <!-- 画像 -->
      <div class="spec-images mb-4">
        <div class="spec-img-box"><img src="./image/noshi.jpg"></div>
        <div class="spec-img-box"><img src="./image/wrap.jpg"></div>
        <div class="spec-img-box"><img src="./image/mcard.jpg"></div>
        <div class="spec-img-box"><img src="./image/bag.jpg"></div>
      </div>

      <!-- ===== おすすめスライダー-->
      <?php if ($recommended): ?>
        <h3 class="reco-title my-5">こちらもおすすめ</h3>

        <div id="recoCarousel" class="carousel slide my-5" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php
            $chunks = array_chunk($recommended, 2);
            foreach ($chunks as $i => $chunk):
            ?>
              <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                <div class="row row-cols-2 g-2 px-3">
                  <?php foreach ($chunk as $p): ?>
                    <div class="col">
                      <div class="card h-100 shadow-sm border-0">
                        <img src="<?= e($p['image_url']) ?>"
                          class="card-img-top"
                          style="height:130px; object-fit:cover;">
                        <div class="card-body p-2">
                          <h6 class="card-title mb-1 text-truncate" style="font-size:0.85rem;">
                            <?= e($p['name']) ?>
                          </h6>
                          <p class="text-muted small mb-2">¥<?= number_format($p['price']) ?></p>
                          <a href="detail.php?id=<?= (int)$p['id'] ?>"
                            class="btn btn-sm btn-outline-primary w-100"
                            style="font-size:0.75rem;">詳細</a>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <button class="carousel-control-prev" type="button"
            data-bs-target="#recoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>

          <button class="carousel-control-next" type="button"
            data-bs-target="#recoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>

        </div>
      <?php endif; ?>

    </div>
  </div>

  <!-- ===== レビュー ===== -->
  <section id="review-section" class="review">
    <h2 class="mb-4 text-center">レビュー</h2>

    <p class="text-muted mb-3">
      平均評価：
      <?php if ($stat['avg_rating']): ?>
        <?= $stat['avg_rating'] ?> / 5（<?= (int)$stat['cnt'] ?>件）
      <?php else: ?>
        —（0件）
      <?php endif; ?>
    </p>

    <?php if ($eligible): ?>
      <p><a class="btn btn-primary btn-sm" href="review-input.php?product_id=<?= $productId ?>">レビューを書く</a></p>
    <?php else: ?>
      <p class="text-muted">※購入者のみレビューできます</p>
    <?php endif; ?>

    <?php if ($reviews): ?>
      <ul class="list-unstyled vstack gap-3">
        <?php foreach ($reviews as $r): ?>
          <li class="border rounded p-3" style="background:#c3d2c2;">
            <strong><?= e($r['name']) ?></strong>
            <div class="mt-2"><?= nl2br(e($r['comment'])) ?></div>
            <div class="text-end text-muted"><small><?= e($r['created_at']) ?></small></div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>まだレビューはありません。</p>
    <?php endif; ?>
  </section>

  <?php require_once 'footer.php'; ?>