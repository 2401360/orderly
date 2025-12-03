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
  /* ====================================
   GLOBAL LAYOUT
==================================== */
  .section-block {
    width: 100%;
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    box-sizing: border-box;
  }

  /* ====================================
   PRODUCT HERO
==================================== */
  .product-hero {
    display: flex;
    align-items: center;
    background: #FFF6EE;
    padding: 40px 20px;
    border-radius: 16px;
    border: 1px solid #f0f0f0;
    gap: 40px;
    transition: .25s;
    box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
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
    justify-content: space-between;
    gap: 20px;
    padding-left: 40px;
  }

  /* Buttons */
  .product-hero .btn-buy,
  .product-hero .btn-fav {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    border: none;
    margin-bottom: 10px;
    cursor: pointer;
    transition: 0.3s;
  }

  .product-hero .btn-buy {
    background-color: #693529;
    color: #fff;
  }

  .product-hero .btn-buy:hover {
    background-color: #e6a800;
  }

  .product-hero .btn-fav {
    background-color: #222222;
    color: #fff;
  }

  .product-hero .btn-fav:hover {
    background-color: #2B2B2B;
  }

  .btn-fav i.bi-heart-fill {
    color: #DA3E50;
  }

  @media (max-width: 768px) {
    .product-hero {
      flex-direction: column;
      padding: 20px;
    }

    .product-hero .hero-text {
      padding-left: 0;
    }
  }

  /* ====================================
   SPEC SECTION
==================================== */
  .spec-container {
    background: #FFF;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 40px 20px;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
    border: 1px solid #f0f0f0;
    gap: 40px;
  }

  /* images */
  .spec-images {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    gap: 20px;
  }

  .spec-images img {
    width: 120px;
    height: auto;
    border-radius: 8px;
  }

  .spec-img-box img {
    width: 130px;
    border-radius: 10px;
    margin: 0 auto 8px;
  }

  /* ====================================
   SPEC TABLE
==================================== */
  .table-spec {
    width: 100%;
    max-width: 600px;
    border-collapse: collapse;
    font-size: 0.95rem;
    margin-bottom: 40px;
    border: 1px solid #ddd;
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

  /* ====================================
   REVIEW Section
==================================== */
  .review {
    background-color: #EFE8E4;
    padding: 30px 30px;
    border-radius: 16px;
    margin-top: 50px;
  }

  .reco-title {
    font-size: 1.3rem;
    font-weight: 700;
    text-align: center;
  }

  /* ======================================
   HERO TEXT BEAUTIFY
====================================== */

  .product-hero .hero-text {
    display: flex;
    flex-direction: column;
    gap: 22px;
    padding-left: 40px;
  }

  /* 商品名 */
  .product-hero .hero-text h1 {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1.35;
    color: #3e2a1f;
    margin-bottom: 4px;
  }

  /* サブ情報ブロック（評価 + レビューリンク） */
  .product-hero .hero-text .sub-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  /* 評価テキスト */
  .product-hero .hero-text .rating-text {
    font-size: 0.95rem;
    color: #7a6b62;
    letter-spacing: 0.3px;
    margin-bottom: 0;
  }

  .product-hero .hero-text .rating-stars i {
    color: #e6b800;
    margin-right: 2px;
  }

  /* レビューを見る */
  .product-hero .hero-text .btn-review {
    display: inline-block;
    width: fit-content;
    padding: 6px 14px;
    background: #fff2d9;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #8a6b32;
    border: 1px solid #e6d5b8;
    transition: 0.3s ease;
  }

  .product-hero .hero-text .btn-review:hover {
    background: #ffe4a8;
  }

  /* 価格 */
  .product-hero .hero-text .product-price {
    font-size: 2.2rem;
    font-weight: 800;
    color: #5a2e1a;
    margin-top: 6px;
  }

  /* アクションエリア（数量 + カート + お気に入り） */
  .product-hero .hero-text .action-row {
    background: #fff9f3;
    border: 1px solid #f5e6d8;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 3px 8px rgba(0, 0, 0, .05);
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  /* 数量 */
  .product-hero .hero-text .form-label {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: #5a4637;
  }

  .product-hero .hero-text select.form-select {
    border-radius: 6px;
    border: 1px solid #d6c8bb;
    padding: 6px 10px;
    font-size: 0.9rem;
  }

  /* モバイル整形 */
  @media (max-width: 768px) {
    .product-hero .hero-text {
      padding-left: 0;
    }

    .product-hero .hero-text h1 {
      font-size: 1.6rem;
    }

    .product-hero .hero-text .product-price {
      font-size: 1.8rem;
    }
  }
</style>

<div class="section-block">

  <!-- 商品ヘッダー -->
  <div class="product-hero">

    <div class="hero-text">

      <!-- 商品タイトル -->
      <h1><?= e($product['name']) ?></h1>

      <!-- サブ情報 -->
      <div class="sub-info">

        <!-- 平均評価 -->
        <p class="rating-text">
          平均評価：
          <?php if ($stat['avg_rating']): ?>
            <span class="rating-stars">
              <?php
              $avg = (float)$stat['avg_rating'];
              $fullStars = floor($avg);
              $half = ($avg - $fullStars >= 0.5);
              ?>
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

      </div>

      <!-- 価格 -->
      <p class="product-price">¥<?= number_format((int)$product['price']) ?></p>

      <!-- アクション -->
      <div class="action-row">
        <p class="form-label"><strong>数量</strong>
          <select name="count" class="form-select">
            <?php for ($i = 1; $i <= 10; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
          </select>
        </p>

        <form action="cart-insert.php" method="get">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <input type="hidden" name="name" value="<?= e($product['name']) ?>">
          <input type="hidden" name="price" value="<?= $product['price'] ?>">
          <button class="btn-buy"><i class="bi bi-bag-plus"></i> カートに入れる</button>
        </form>

        <?php if ($cid > 0): ?>
          <button class="btn-fav" data-id="<?= $product['id'] ?>" data-fav="<?= $isFav ? 1 : 0 ?>">
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
  $desc = $product['description'] ?? '';
  $lines = preg_split('/\r\n|\r|\n/', trim($desc));

  function splitLine($line)
  {
    if (strpos($line, ':') !== false) return explode(':', $line, 2);
    if (strpos($line, '：') !== false) return explode('：', $line, 2);
    return [$line, ""];
  }
  ?>

  <div class="section-block spec-container">

    <!-- LEFT TABLE -->
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

    <div style="flex:1;">

      <div class="spec-images mb-4">
        <div class="spec-img-box"><img src="./image/noshi.jpg"></div>
        <div class="spec-img-box"><img src="./image/wrap.jpg"></div>
        <div class="spec-img-box"><img src="./image/mcard.jpg"></div>
        <div class="spec-img-box"><img src="./image/bag.jpg"></div>
      </div>

      <?php if ($recommended): ?>
        <h3 class="reco-title my-1">こちらもおすすめ</h3>

        <div id="recoCarousel" class="carousel slide my-2" data-bs-ride="carousel">
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
                          <a href="detail.php?id=<?= $p['id'] ?>"
                            class="btn btn-sm btn-outline-primary w-100">詳細</a>
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

    </div>
  </div>


  <section id="review-section" class="section-block review">

    <h2 class="mb-4 text-center">レビュー</h2>

    <p class="text-muted mb-3">
      平均評価：
      <?php if ($stat['avg_rating']): ?>
        <?= $stat['avg_rating'] ?> / 5（<?= $stat['cnt'] ?>件）
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

</div>

<script>
  document.addEventListener("click", async function(e) {
    const btn = e.target.closest(".btn-fav");
    if (!btn) return;

    const pid = btn.dataset.id;
    const isFav = Number(btn.dataset.fav);

    const url = isFav ? "favorite-delete.php" : "favorite-insert.php";

    const fd = new FormData();
    fd.append("product_id", pid);

    const res = await fetch(url, {
      method: "POST",
      body: fd
    });

    if (!res.ok) {
      alert("通信エラー");
      return;
    }

    // UI 更新
    if (isFav) {
      btn.dataset.fav = "0";
      btn.innerHTML = '<i class="bi bi-heart"></i> お気に入り';
    } else {
      btn.dataset.fav = "1";
      btn.innerHTML = '<i class="bi bi-heart-fill"></i> お気に入り';
    }
  });
</script>

<?php require_once 'footer.php'; ?>