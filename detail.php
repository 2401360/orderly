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

    <div class="row g-4">
      <div class="col-md-5">
        <?php if (!empty($product['image_url'])): ?>
          <img class="img-fluid rounded" src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
      </div>

      <div class="col-md-7">
        <h1 class="h3"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="fs-4 fw-bold">¥<?= number_format((int)$product['price']) ?></p>
        <p><?= nl2br(htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>

        <div class="d-flex gap-2 mt-3 align-items-end flex-wrap">
          <!-- カートフォーム -->
          <form action="cart-insert.php" method="get" class="d-flex flex-wrap gap-2 align-items-end">
            <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="price" value="<?= (int)$product['price'] ?>">

            <div>
              <label class="form-label">数量</label>
              <select name="count" class="form-select">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>

            <button type="submit" class="btn btn-primary">
              <i class="bi bi-bag-plus"></i> カートに入れる
            </button>
          </form>

          <!-- お気に入りボタン -->
          <?php if ($cid > 0): ?>
            <button type="button"
              class="btn <?= $isFav ? 'btn-danger' : 'btn-outline-danger' ?> fav-btn"
              data-id="<?= (int)$product['id'] ?>"
              data-fav="<?= $isFav ? '1' : '0' ?>"
              aria-pressed="<?= $isFav ? 'true' : 'false' ?>"
              title="<?= $isFav ? 'お気に入り解除' : 'お気に入りに追加' ?>">
              <i class="bi <?= $isFav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
            </button>
          <?php else: ?>
            <a class="btn btn-outline-danger" href="login-input.php" title="ログインしてお気に入りに追加">
              <i class="bi bi-heart"></i>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- レビュー表示 -->
    <section class="mt-5">
      <h2 class="h4">レビュー</h2>

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
            <li class="border rounded p-3">
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