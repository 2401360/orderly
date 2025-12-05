<?php
require_once 'app.php';
require_once 'header.php';

$is_admin = (isset($_SESSION['customer']['role']) && $_SESSION['customer']['role'] === 'admin');
if (!$is_admin) {
    echo '<div class="container pt-3"><div class="alert alert-danger">権限がありません。（管理者のみ）</div></div>';
    require 'footer.php';
    exit;
}

$pdo = db();

$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$totalItems = (int)$pdo->query("SELECT COUNT(*) FROM product")->fetchColumn();
$totalPages = max(1, ceil($totalItems / $perPage));

$sqlSales = "
SELECT 
    p.id,
    p.name,
    p.price,
    COALESCE(SUM(pd.count), 0) AS total_count,
    COALESCE(SUM(pd.count * p.price), 0) AS total_sales
FROM product p
LEFT JOIN purchase_detail pd ON p.id = pd.product_id
GROUP BY p.id, p.name, p.price
ORDER BY p.id
LIMIT :limit OFFSET :offset;
";

$stSales = $pdo->prepare($sqlSales);
$stSales->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stSales->bindValue(':offset', $offset, PDO::PARAM_INT);
$stSales->execute();
$salesList = $stSales->fetchAll();

$sqlTotal = "
SELECT 
    COALESCE(SUM(pd.count * p.price), 0) AS total_sales_all
FROM product p
JOIN purchase_detail pd ON p.id = pd.product_id;
";
$totalSales = $pdo->query($sqlTotal)->fetchColumn();
?>

<div class="container mt-5">

    <h1 class="mb-4">商品売上</h1>

    <!-- 総売上表示 -->
    <div class="alert alert-success mb-5">
        <h3 class="mb-0">総売上：<?= number_format($totalSales) ?> 円</h3>
    </div>

    <!-- 商品別 売上一覧 -->
    <h2 class="mb-3">商品別 売上一覧</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>商品ID</th>
                <th>商品名</th>
                <th>価格</th>
                <th>販売数</th>
                <th>売上合計</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($salesList as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= number_format($row['price']) ?> 円</td>
                    <td><?= number_format($row['total_count']) ?> 個</td>
                    <td><?= number_format($row['total_sales']) ?> 円</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- ===== ページネーション ===== -->
    <nav>
        <ul class="pagination">

            <!-- 前へ -->
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>">前へ</a>
            </li>

            <!-- ページ番号 -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <!-- 次へ -->
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>">次へ</a>
            </li>

        </ul>
    </nav>

</div>

<?php require_once 'footer.php'; ?>