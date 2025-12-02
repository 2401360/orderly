<?php
require_once 'app.php';
$page_title = '注文一覧';
require_once 'header.php';

$is_admin = (isset($_SESSION['customer']['role']) && $_SESSION['customer']['role'] === 'admin');
if (!$is_admin) {
    echo '<div class="container pt-3"><div class="alert alert-danger">権限がありません。（管理者のみ）</div></div>';
    require 'footer.php';
    exit;
}
$pdo = db();

// 注文一覧取得
$sql = "
SELECT 
    pu.id AS purchase_id,
    pu.created_at,
    c.name AS customer_name,
    p.name AS product_name,
    p.price,
    pd.count,
    (p.price * pd.count) AS subtotal
FROM purchase pu
JOIN customer c ON pu.customer_id = c.id
JOIN purchase_detail pd ON pu.id = pd.purchase_id
JOIN product p ON pd.product_id = p.id
ORDER BY pu.created_at DESC, pu.id DESC;
";

$st = $pdo->prepare($sql);
$st->execute();
$orders = $st->fetchAll();

?>

<div class="container mt-5">
    <h1 class="mb-4">注文一覧</h1>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>注文ID</th>
                <th>購入者</th>
                <th>購入日</th>
                <th>商品名</th>
                <th>単価</th>
                <th>個数</th>
                <th>小計</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= htmlspecialchars($o['purchase_id']) ?></td>
                    <td><?= htmlspecialchars($o['customer_name']) ?></td>
                    <td><?= htmlspecialchars($o['created_at']) ?></td>
                    <td><?= htmlspecialchars($o['product_name']) ?></td>
                    <td><?= number_format($o['price']) ?> 円</td>
                    <td><?= number_format($o['count']) ?> 個</td>
                    <td><?= number_format($o['subtotal']) ?> 円</td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7" class="text-center">注文データがありません。</td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>