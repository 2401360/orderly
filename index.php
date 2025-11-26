<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'app.php';
$page_title = 'ホーム';
require_once 'header.php';

$cid = $_SESSION['customer']['id'] ?? 0;
$pdo = db();

$sqlReco = "
SELECT p.*,
       CASE WHEN :cid > 0 AND EXISTS(SELECT 1 FROM favorite f WHERE f.customer_id = :cid AND f.product_id = p.id)
            THEN 1 ELSE 0 END AS is_fav
  FROM product p
 WHERE COALESCE(p.is_recommended,0)=1
 ORDER BY p.id DESC
 LIMIT 4";
$stReco = $pdo->prepare($sqlReco);
$stReco->bindValue(':cid', $cid, PDO::PARAM_INT);
$stReco->execute();
$recommended = $stReco->fetchAll();

$sqlNew = "
SELECT p.*,
       CASE WHEN :cid > 0 AND EXISTS(SELECT 1 FROM favorite f WHERE f.customer_id = :cid AND f.product_id = p.id)
            THEN 1 ELSE 0 END AS is_fav
  FROM product p
 ORDER BY p.id DESC
 LIMIT 8";
$stNew = $pdo->prepare($sqlNew);
$stNew->bindValue(':cid', $cid, PDO::PARAM_INT);
$stNew->execute();
$newItems = $stNew->fetchAll();

function card_item(array $p, int $cid): string
{
    $img = $p['image_url'] ? '<img src="' . e($p['image_url']) . '" class="card-img-top rounded-top-3" alt="' . e($p['name']) . '">' : '';
    $cat = $p['category'] ? '<span class="badge bg-secondary-subtle text-secondary-emphasis">' . e($p['category']) . '</span>' : '';
    $desc = e(mb_strimwidth($p['description'] ?? '', 0, 80, '…', 'UTF-8'));

    $detailBtn = '<a class="btn btn-sm btn-outline-secondary" href="detail.php?id=' . (int)$p['id'] . '">
                    <i class="bi bi-info-circle"></i> 詳細
                  </a>';

    if ($cid) {
        $isFav = !empty($p['is_fav']);
        $favBtn = '<button type="button"
                      class="btn btn-sm ' . ($isFav ? 'btn-danger' : 'btn-outline-danger') . ' fav-btn"
                      data-id="' . $p['id'] . '" data-fav="' . ($isFav ? 1 : 0) . '"
                      title="' . ($isFav ? 'お気に入り解除' : 'お気に入りに追加') . '">
                      <i class="bi ' . ($isFav ? 'bi-heart-fill' : 'bi-heart') . '"></i>
                   </button>';
    } else {
        $favBtn = '<a class="btn btn-sm btn-outline-danger" href="login-input.php" title="ログインしてお気に入りに追加">
                     <i class="bi bi-heart"></i>
                   </a>';
    }

    return "
    <div class='col'>
      <div class='card h-100 shadow-sm rounded-3'>
        $img
        <div class='card-body d-flex flex-column'>
          <div class='d-flex justify-content-between align-items-start mb-1'>
            <h3 class='h6 card-title mb-0'>" . e($p['name']) . "</h3>
            <span class='fw-semibold'>¥" . number_format($p['price']) . "</span>
          </div>
          <div class='mb-2'>$cat</div>
          <p class='text-body-secondary small mb-3 text-truncate'>$desc</p>
          <div class='mt-auto d-flex justify-content-between align-items-center'>
            $detailBtn
            $favBtn
          </div>
        </div>
      </div>
    </div>";
}
?>

<div class="container py-4">
    <div class="bg-body-tertiary rounded-4 shadow-sm p-4 p-md-5 mb-4 text-center">
        <h1 class="display-6 fw-semibold mb-2">ようこそ Orderly へ</h1>
        <p class="lead mb-4">ヘルシーで美味しいケーキを、あなたのお気に入りから簡単に注文できます。</p>
        <div class="row justify-content-center mb-4">
            <div class="col-12 col-md-8">
                <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-sm">
                    <video controls autoplay muted playsinline class="w-100 h-100">
                        <source src="orderly.mp4" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
        <a class="btn btn-primary btn-lg" href="product.php"><i class="bi bi-bag"></i> 商品を見る</a>
    </div>

    <?php if ($recommended): ?>

        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-hand-thumbs-up text-danger"></i>
            <h2 class="h5 m-0">おすすめ</h2>
        </div>

        <div id="recoCarousel" class="carousel slide mb-4" data-bs-ride="carousel">

            <div class="carousel-indicators">
                <?php foreach ($recommended as $index => $p): ?>
                    <button type="button"
                        data-bs-target="#recoCarousel"
                        data-bs-slide-to="<?= $index ?>"
                        class="<?= $index === 0 ? 'active' : '' ?>">
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="carousel-inner rounded-4 shadow-sm">
                <?php foreach ($recommended as $index => $p): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <a href="detail.php?id=<?= (int)$p['id'] ?>">

                            <?php if (!empty($p['image_url'])): ?>
                                <img src="<?= e($p['image_url']) ?>"
                                    class="d-block w-100"
                                    style="aspect-ratio: 2 / 1; object-fit: cover;"
                                    alt="<?= e($p['name']) ?>">
                            <?php else: ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                    style="aspect-ratio: 2 / 1;">
                                    No Image
                                </div>
                            <?php endif; ?>

                        </a>

                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                            <h5 class="mb-1"><?= e($p['name']) ?></h5>
                            <p class="mb-0">¥<?= number_format($p['price']) ?></p>
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

    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-stars text-warning"></i>
        <h2 class="h5 m-0">新着</h2>
    </div>
    <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
        <?php foreach ($newItems as $p) echo card_item($p, $cid); ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>