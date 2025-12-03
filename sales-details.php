<?php
require_once 'app.php';
$page_title = '注文一覧';
require_once 'header.php';

$is_admin = (isset($_SESSION['customer']['role']) && $_SESSION['customer']['role'] === 'admin');
if (!$is_admin) {
    echo '<div class="container pt-3"><div class="alert alert-danger">権限がありません。（管理者のみ）</div></div>';
    require_once 'footer.php';
    exit;
}

$pdo = db();

/* ===== PAGINATION SETTINGS ===== */
$perPage = 20; // 1ページに20件
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

/* ===== GET TOTAL ORDER COUNT ===== */
$totalSql = "SELECT COUNT(*) FROM purchase";
$totalOrders = (int)$pdo->query($totalSql)->fetchColumn();
$totalPages = max(1, (int)ceil($totalOrders / $perPage));

/* ===== GET PAGED ORDER LIST ===== */
$sql = "
SELECT 
    p.id AS purchase_id,
    p.created_at,
    c.name AS customer_name,
    COUNT(pd.product_id) AS item_count,
    SUM(pd.count * pr.price) AS total_amount
FROM purchase p
JOIN customer c ON c.id = p.customer_id
JOIN purchase_detail pd ON p.id = pd.purchase_id
JOIN product pr ON pr.id = pd.product_id
GROUP BY p.id, p.created_at, c.name
ORDER BY p.id DESC
LIMIT :limit OFFSET :offset
";

$st = $pdo->prepare($sql);
$st->bindValue(':limit', $perPage, PDO::PARAM_INT);
$st->bindValue(':offset', $offset, PDO::PARAM_INT);
$st->execute();
$orders = $st->fetchAll();
?>

<div class="container mt-5">

    <h1 class="mb-4">注文一覧</h1>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>注文番号</th>
                <th>購入者</th>
                <th>注文日時</th>
                <th>商品数</th>
                <th>合計金額</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= e($o['purchase_id']) ?></td>
                    <td><?= e($o['customer_name']) ?></td>
                    <td><?= e($o['created_at']) ?></td>
                    <td><?= number_format($o['item_count']) ?> 個</td>
                    <td><?= number_format($o['total_amount']) ?> 円</td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$orders): ?>
                <tr>
                    <td colspan="5" class="text-muted">注文がありません。</td>
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

<?php require_once 'footer.php'; ?>