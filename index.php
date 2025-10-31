<?php $page_title = 'Home';
require 'header.php'; ?>
<?php require 'db-connect.php'; ?>
<div class="container py-4">
    
    <div class="p-4 p-md-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-2">
            <h1 class="display-6 fw-semibold">Welcome to Orderly Shop</h1>
            <p class="col-md-8 fs-6">Healthy & tasty cake — pick your favorites and order in a few clicks.</p>
            <a class="btn btn-primary btn-lg" href="product.php"><i class="bi bi-bag"></i> Shop Now</a>
        </div>
    </div>
    <!-- 上記のメッセージは無くす。理由：動画の中にメッセージを表示させるため。 -->

    <h2 class="h4 mb-3">デザート</h2>
    <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
        <?php
        $pdo = new PDO($connect, USER, PASS);
        $sql = $pdo->query('SELECT * FROM product WHERE id NOT IN (1, 3, 5, 8) ORDER BY id LIMIT 4');
        // 画像挿入が必要。
        foreach ($sql as $row):
            $id = (int)$row['id'];
        ?>
            <div class="col">
                <div class="card h-100 card-hover">
                    <?php if (!empty($row['image_url'])): ?>
                        <img src="<?= htmlspecialchars($row['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h3 class="h6 card-title mb-1"><?= htmlspecialchars($row['name']) ?></h3>
                        <div class="text-muted mb-3">¥<?= number_format($row['price']) ?></div>
                        <div class="mt-auto d-flex gap-2">
                            <a class="btn btn-sm btn-outline-secondary" href="detail.php?id=<?= $id ?>"><i class="bi bi-info-circle"></i> Details</a>
                            <a class="btn btn-sm btn-outline-danger" href="favorite-insert.php?id=<?= $id ?>"><i class="bi bi-heart"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require 'footer.php'; ?>