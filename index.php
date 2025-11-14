<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require 'db-connect.php';
$page_title = 'ホーム';
require 'header.php';

$cid = isset($_SESSION['customer']['id']) ? (int)$_SESSION['customer']['id'] : 0;

$pdo = new PDO($connect, USER, PASS);

if ($cid > 0) {
    $stReco = $pdo->prepare("
    SELECT p.*,
           EXISTS(SELECT 1 FROM favorite f2 WHERE f2.customer_id = :cid AND f2.product_id = p.id) AS is_fav
      FROM product p
     WHERE COALESCE(p.is_recommended,0)=1
     ORDER BY p.id DESC
     LIMIT 12
  ");
    $stReco->bindValue(':cid', $cid, PDO::PARAM_INT);
    $stReco->execute();
} else {
    $stReco = $pdo->query("
    SELECT p.*, 0 AS is_fav
      FROM product p
     WHERE COALESCE(p.is_recommended,0)=1
     ORDER BY p.id DESC
     LIMIT 12
  ");
}
$recommended = $stReco->fetchAll();

$sqlNew = $cid > 0
    ? "SELECT p.*, EXISTS(SELECT 1 FROM favorite f WHERE f.customer_id = :cid AND f.product_id = p.id) AS is_fav
       FROM product p ORDER BY p.id DESC LIMIT 12"
    : "SELECT p.*, 0 AS is_fav FROM product p ORDER BY p.id DESC LIMIT 12";
$stNew = $pdo->prepare($sqlNew);
if ($cid > 0) $stNew->bindValue(':cid', $cid, PDO::PARAM_INT);
$stNew->execute();
$newItems = $stNew->fetchAll();

function card_item(array $p, int $cid): string
{
    $img = !empty($p['image_url'])
        ? '<img src="' . e($p['image_url']) . '" class="card-img-top img-fluid rounded-top-3" alt="' . e($p['name']) . '">'
        : '';
    $cat = !empty($p['category'])
        ? '<span class="badge bg-secondary-subtle text-secondary-emphasis">' . e($p['category']) . '</span>'
        : '';
    $desc = e(mb_strimwidth((string)($p['description'] ?? ''), 0, 80, '…', 'UTF-8'));

    $detailBtn = '<a class="btn btn-sm btn-outline-secondary" href="detail.php?id=' . (int)$p['id'] . '">
                  <i class="bi bi-info-circle"></i> 詳細
                </a>';

    if ($cid > 0) {
        $favBtn = '<button type="button"
                      class="btn btn-sm ' . (!empty($p['is_fav']) ? 'btn-danger' : 'btn-outline-danger') . ' fav-btn"
                      data-id="' . (int)$p['id'] . '"
                      data-fav="' . (!empty($p['is_fav']) ? '1' : '0') . '"
                      aria-pressed="' . (!empty($p['is_fav']) ? 'true' : 'false') . '"
                      title="' . (!empty($p['is_fav']) ? 'お気に入り解除' : 'お気に入りに追加') . '">
                  <i class="bi ' . (!empty($p['is_fav']) ? 'bi-heart-fill' : 'bi-heart') . '"></i>
                </button>';
    } else {
        $favBtn = '<a class="btn btn-sm btn-outline-danger" href="login-input.php" title="ログインしてお気に入りに追加">
                 <i class="bi bi-heart"></i>
               </a>';
    }

    return '
  <div class="col">
    <div class="card h-100 shadow-sm rounded-3">
      ' . $img . '
      <div class="card-body d-flex flex-column">
        <div class="d-flex align-items-start justify-content-between mb-1">
          <h3 class="h6 card-title mb-0">' . e($p['name']) . '</h3>
          <span class="fw-semibold">¥' . number_format((int)$p['price']) . '</span>
        </div>
        <div class="mb-2">' . $cat . '</div>
        <p class="text-body-secondary small mb-3 text-truncate">' . $desc . '</p>
        <div class="mt-auto d-flex justify-content-between align-items-center">
          ' . $detailBtn . '
          ' . $favBtn . '
        </div>
      </div>
    </div>
  </div>';
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
                        お使いのブラウザは動画タグをサポートしていません。
                    </video>
                </div>
            </div>
        </div>
        <a class="btn btn-primary btn-lg" href="product.php"><i class="bi bi-bag"></i> 商品を見る</a>
    </div>

    <?php if (!empty($recommended)): ?>
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-hand-thumbs-up text-danger"></i>
            <h2 class="h5 m-0">おすすめ</h2>
        </div>
        <p class="text-body-secondary small mb-3">スタッフおすすめの商品</p>
        <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 mb-4">
            <?php foreach ($recommended as $p) echo card_item($p, $cid); ?>
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

<?php if ($cid === 0): ?>
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ログインが必要です</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
                </div>
                <div class="modal-body">お気に入り機能を利用するにはログインしてください。</div>
                <div class="modal-footer">
                    <a class="btn btn-primary" href="login-input.php">ログインへ</a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="favToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body small" id="favToastMsg">更新しました</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="閉じる"></button>
        </div>
    </div>
</div>

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
                const modalEl = document.getElementById('loginModal');
                if (modalEl && window.bootstrap) bootstrap.Modal.getOrCreateInstance(modalEl).show();
                else alert('お気に入りにはログインが必要です。');
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

<?php require 'footer.php'; ?>