<?php
require_once 'app.php';
$page_title = '商品管理';
require_once 'header.php';

$is_admin = (isset($_SESSION['customer']['role']) && $_SESSION['customer']['role'] === 'admin');
if (!$is_admin) {
    echo '<div class="container pt-3"><div class="alert alert-danger">権限がありません。（管理者のみ）</div></div>';
    require_once 'footer.php';
    exit;
}

$pdo = db();

if (!function_exists('e')) {
    function e($s)
    {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}

const UPLOAD_REL = 'uploads';
$uploadDir = __DIR__ . '/' . UPLOAD_REL;
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

function handle_image_upload(array &$errors): string
{
    if (empty($_FILES['image_file']) || $_FILES['image_file']['error'] === UPLOAD_ERR_NO_FILE) return '';

    $f = $_FILES['image_file'];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        $errors[] = '画像のアップロードに失敗しました。';
        return '';
    }
    if ($f['size'] > 5 * 1024 * 1024) {
        $errors[] = '画像サイズは最大5MBです。';
        return '';
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($f['tmp_name']) ?: '';
    $extMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    if (!isset($extMap[$mime])) {
        $errors[] = '対応していない画像形式です。（jpg/png/gif/webp）';
        return '';
    }

    $ext = $extMap[$mime];
    $name = bin2hex(random_bytes(8)) . '.' . $ext;

    $destAbs = __DIR__ . '/' . UPLOAD_REL . '/' . $name;
    $destRel = UPLOAD_REL . '/' . $name;

    if (!move_uploaded_file($f['tmp_name'], $destAbs)) {
        $errors[] = '画像の保存に失敗しました。';
        return '';
    }
    return $destRel;
}

$notice = '';
$errors = [];
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'delete' && $id > 0) {
        $stmt = $pdo->prepare('DELETE FROM product WHERE id=?');
        $stmt->execute([$id]);
        $notice = '商品を削除しました。';
    } elseif ($action === 'create' || ($action === 'update' && $id > 0)) {

        $name        = trim($_POST['name'] ?? '');
        $category    = trim($_POST['category'] ?? 'その他');
        $price       = (int)($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $is_recommended = isset($_POST['is_recommended']) ? 1 : 0;

        if ($name === '')     $errors[] = '商品名は必須です。';
        if ($category === '') $errors[] = 'カテゴリは必須です。';
        if ($price <= 0)      $errors[] = '価格は1以上を入力してください。';

        $uploadedPath = handle_image_upload($errors);

        if ($action === 'create' && !$errors) {
            $image_url = $uploadedPath;
            $stmt = $pdo->prepare('INSERT INTO product (name, category, price, image_url, description, is_recommended) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $category, $price, $image_url, $description, $is_recommended]);
            $notice = '商品を追加しました。';
        } elseif ($action === 'update' && !$errors) {
            $image_url = $uploadedPath !== '' ? $uploadedPath : (string)($_POST['current_image_url'] ?? '');
            $stmt = $pdo->prepare('UPDATE product SET name=?, category=?, price=?, image_url=?, description=?, is_recommended=? WHERE id=?');
            $stmt->execute([$name, $category, $price, $image_url, $description, $is_recommended, $id]);
            $notice = '商品を更新しました。';
        }
    }
}

$edit_item = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM product WHERE id=? LIMIT 1');
    $stmt->execute([$id]);
    $edit_item = $stmt->fetch();
}

$perPage = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$total = (int)$pdo->query('SELECT COUNT(*) FROM product')->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

$stmt = $pdo->prepare('SELECT * FROM product ORDER BY id DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<div class="container py-4">
    <h1 class="h4 mb-3">商品管理</h1>

    <?php if ($notice): ?>
        <div class="alert alert-success"><?= e($notice) ?></div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><?php foreach ($errors as $er) echo '<div>' . e($er) . '</div>'; ?></div>
    <?php endif; ?>



    <?php if ($edit_item): ?>
        <div class="card mb-4">
            <div class="card-header">編集</div>
            <div class="card-body">
                <form method="post" class="row g-3" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= e($edit_item['id']) ?>">

                    <div class="col-md-5">
                        <label class="form-label">商品名 *</label>
                        <input type="text" name="name" class="form-control" required value="<?= e($edit_item['name']) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">カテゴリ *</label>
                        <input type="text" name="category" class="form-control" required value="<?= e($edit_item['category']) ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">価格(円) *</label>
                        <input type="number" name="price" class="form-control" min="1" step="1" required value="<?= e($edit_item['price']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">画像ファイル</label>
                        <input type="file" name="image_file" class="form-control">
                        <?php if ($edit_item['image_url']): ?>
                            <div class="mt-2">
                                <img src="<?= e($edit_item['image_url']) ?>" style="max-width:160px;" class="rounded border">
                            </div>
                        <?php endif; ?>
                        <input type="hidden" name="current_image_url" value="<?= e($edit_item['image_url']) ?>">
                    </div>

                    <div class="col-md-3 d-flex align-items-center">
                        <div>
                            <input class="form-check-input" type="checkbox" name="is_recommended" <?= $edit_item['is_recommended'] ? 'checked' : '' ?>>
                            <label class="form-check-label">おすすめ</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">説明</label>
                        <textarea name="description" class="form-control" rows="3"><?= e($edit_item['description']) ?></textarea>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-success" type="submit">更新</button>
                        <a class="btn btn-outline-secondary" href="admin-products.php">取消</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">商品一覧</div>
        <div class="card-body table-responsive">

            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>商品名</th>
                        <th>カテゴリ</th>
                        <th>価格</th>
                        <th>画像</th>
                        <th>おすすめ</th>
                        <th>説明</th>
                        <th style="width:180px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= e($p['name']) ?></td>
                            <td><?= e($p['category']) ?></td>
                            <td><?= e($p['price']) ?></td>
                            <td><?php if ($p['image_url']): ?><img src="<?= e($p['image_url']) ?>" style="max-width:80px;max-height:60px;"><?php endif; ?></td>
                            <td><?= $p['is_recommended'] ? '★' : '' ?></td>
                            <td class="small"><?= nl2br(e($p['description'])) ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="admin-products.php?action=edit&id=<?= e($p['id']) ?>">編集</a>

                                <form method="post" style="display:inline;" onsubmit="return confirm('削除しますか？');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($p['id']) ?>">
                                    <button class="btn btn-sm btn-outline-danger">削除</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (!$products): ?>
                        <tr>
                            <td colspan="7" class="text-muted">商品がありません。</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center mt-3">

                    <!-- Previous -->
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>">« 前へ</a>
                    </li>

                    <!-- Pages -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next -->
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">次へ »</a>
                    </li>

                </ul>
            </nav>

        </div>
    </div>
    <!-- 新規追加 -->
    <div class="card mb-4">
        <div class="card-header">新規追加</div>
        <div class="card-body">
            <form method="post" class="row g-3" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">

                <div class="col-md-5">
                    <label class="form-label">商品名 *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">カテゴリ *</label>
                    <input type="text" name="category" class="form-control" value="その他" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">価格(円) *</label>
                    <input type="number" name="price" class="form-control" min="1" step="1" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">画像ファイル</label>
                    <input type="file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <div>
                        <input class="form-check-input" type="checkbox" name="is_recommended" id="createRec">
                        <label class="form-check-label" for="createRec">おすすめ</label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">説明</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary" type="submit">追加</button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php require_once 'footer.php'; ?>