<?php
require_once 'app.php';
$page_title = 'Favorites';
require_once 'header.php';
echo '<div class="container py-4">';
if (!isset($_SESSION['customer'])) {
    echo '<div class="alert alert-warning">お気に入りを表示するには、ログインしてください。</div>';
    echo '</div>';
    return;
}
$pdo = db();
$sql = $pdo->prepare('SELECT p.id, p.name, p.price FROM favorite f JOIN product p ON p.id = f.product_id WHERE f.customer_id = ? ORDER BY p.id');
$sql->execute([$_SESSION['customer']['id']]);
$rows = $sql->fetchAll();
echo '<h1 class="h4 mb-3"><i class="bi bi-heart text-danger"></i> お気に入り</h1>';
if (!$rows) {
    echo '<div class="alert alert-info">お気に入りはまだありません。</div>';
    echo '</div>';
    return;
}
?>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>商品番号</th>
                <th>商品名</th>
                <th>価格</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): $id = (int)$r['id']; ?>
                <tr>
                    <td><?= $id ?></td>
                    <td><a href="detail.php?id=<?= $id ?>"><?= htmlspecialchars($r['name']) ?></a></td>
                    <td>¥<?= number_format((int)$r['price']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger btn-del-fav" data-id="<?= $id ?>">
                            <i class="bi bi-trash"></i> 削除
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php echo '</div>'; ?>
<script>
    document.addEventListener("click", async function(e) {
        const btn = e.target.closest(".btn-del-fav");
        if (!btn) return;
        const pid = btn.dataset.id;
        const row = btn.closest("tr");
        const fd = new FormData();
        fd.append("product_id", pid);
        const res = await fetch("favorite-delete.php", {
            method: "POST",
            body: fd
        });
        if (!res.ok) {
            alert("削除できませんでした");
            return;
        }
        row.style.transition = "0.3s";
        row.style.opacity = "0";
        setTimeout(() => {
            row.remove();
        }, 300);
    });
</script>
<?php require_once 'footer.php'; ?>