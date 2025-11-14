<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require 'db-connect.php';
$page_title = '商品一覧';
require 'header.php';

if (!function_exists('e')) {
  function e($s)
  {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
  }
}

$q   = trim($_GET['q']   ?? '');
$cat = trim($_GET['cat'] ?? 'all');
$cid = isset($_SESSION['customer']['id']) ? (int)$_SESSION['customer']['id'] : 0;

$pdo = new PDO($connect, USER, PASS);

$categories = $pdo->query("SELECT DISTINCT category FROM product ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

$likeEscape = static function (string $s): string {
  return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $s);
};

$base = $cid > 0
  ? "SELECT p.*, EXISTS(SELECT 1 FROM favorite f WHERE f.customer_id = :cid AND f.product_id = p.id) AS is_fav FROM product p"
  : "SELECT p.*, 0 AS is_fav FROM product p";

$conds = [];
$params = [];
if ($cid > 0) $params[':cid'] = $cid;

if ($q !== '') {
  $kw = '%' . $likeEscape($q) . '%';
  $params[':kw1'] = $kw;
  $params[':kw2'] = $kw;
  $conds[] = "(p.name LIKE :kw1 ESCAPE '\\\\' OR COALESCE(p.description,'') LIKE :kw2 ESCAPE '\\\\')";
}

if ($cat !== '' && $cat !== 'all') {
  $conds[] = "p.category = :cat";
  $params[':cat'] = $cat;
}

$sql = $base . ($conds ? " WHERE " . implode(" AND ", $conds) : "") . " ORDER BY p.created_at DESC, p.id DESC";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, is_int($v) ? $v : $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
$stmt->execute();
$rows = $stmt->fetchAll();

$group_mode = ($cat === 'all');
if ($group_mode) {
  $groups = [];
  foreach ($rows as $p) {
    $key = $p['category'] ?? 'その他';
    $groups[$key][] = $p;
  }
  ksort($groups, SORT_NATURAL | SORT_FLAG_CASE);
}

function card_item(array $p, int $cid): string
{
  $img = !empty($p['image_url'])
    ? '<img src="' . e($p['image_url']) . '" class="card-img-top img-fluid rounded-top-3" alt="' . e($p['name']) . '">'
    : '';
  $cat = !empty($p['category'])
    ? '<span class="badge bg-secondary-subtle text-secondary-emphasis mb-2">' . e($p['category']) . '</span>'
    : '';
  $desc = e(mb_strimwidth((string)($p['description'] ?? ''), 0, 80, '…', 'UTF-8'));
  $detail = '<a class="btn btn-sm btn-outline-secondary" href="detail.php?id=' . (int)$p['id'] . '"><i class="bi bi-info-circle"></i> 詳細</a>';

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
    $favBtn = '<a class="btn btn-sm btn-outline-danger" href="login-input.php" title="ログインしてお気に入りに追加"><i class="bi bi-heart"></i></a>';
  }

  return '
  <div class="col">
    <div class="card shadow-sm rounded-3 h-100">
      ' . $img . '
      <div class="card-body d-flex flex-column">
        <div class="d-flex align-items-start justify-content-between mb-1">
          <h3 class="h6 card-title mb-0">' . e($p['name']) . '</h3>
          <span class="fw-semibold">¥' . number_format((int)$p['price']) . '</span>
        </div>
        ' . $cat . '
        <p class="text-body-secondary small mb-3 text-truncate">' . $desc . '</p>
        <div class="mt-auto d-flex justify-content-between align-items-center">
          ' . $detail . $favBtn . '
        </div>
      </div>
    </div>
  </div>';
}
?>
<div class="container py-4">
  <h1 class="h4 mb-1">商品一覧</h1>

  <form class="row g-2 mb-4 align-items-center" method="get" action="product.php">
    <div class="col-sm-6 col-md-4">
      <input type="text" class="form-control" name="q" value="<?= e($q) ?>" placeholder="キーワードを入力してください">
    </div>
    <div class="col-sm-4 col-md-3">
      <select name="cat" class="form-select">
        <option value="all" <?= $cat === 'all' ? ' selected' : ''; ?>>すべてのカテゴリ</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= e($c) ?>" <?= $cat === $c ? ' selected' : ''; ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-primary">検索</button></div>
    <?php if ($q !== '' || ($cat !== '' && $cat !== 'all')): ?>
      <div class="col-12"><small class="text-muted">検索: <?= $q !== '' ? '「' . e($q) . '」' : '（キーワードなし）' ?> ／ カテゴリ: <?= $cat === 'all' ? 'すべて' : e($cat) ?></small></div>
    <?php endif; ?>
  </form>

  <?php if (!$rows): ?>
    <div class="alert alert-info">該当する商品がありません。</div>
  <?php else: ?>
    <?php if ($group_mode): ?>
      <?php foreach ($groups as $catName => $list): ?>
        <section class="mb-4">
          <h2 class="h5 mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-folder2-open text-primary"></i> <?= e($catName) ?>
          </h2>
          <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
            <?php foreach ($list as $p) echo card_item($p, $cid); ?>
          </div>
        </section>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
        <?php foreach ($rows as $p) echo card_item($p, $cid); ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
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

<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080;">
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
      bootstrap.Toast.getOrCreateInstance(el, {
        delay: 1400
      }).show();
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