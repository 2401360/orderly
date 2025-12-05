<?php
require_once 'app.php';
$page_title = '商品一覧';
require_once 'header.php';

$pdo = db();
$categories = $pdo->query("SELECT DISTINCT category FROM product ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

$kw = trim($_GET['keyword'] ?? '');
$cat = trim($_GET['category'] ?? '');

function cake_display($cake)
{
    $id = (int)$cake['id'];
    $name = htmlspecialchars($cake['name'], ENT_QUOTES, 'UTF-8');
    $image = htmlspecialchars($cake['image_url'], ENT_QUOTES, 'UTF-8');
    return
        '<div class="cake-item">
            <div class="name-plate">
                <img src="image/name_tag.png" class="name-plate-img">
                <a href="detail.php?id=' . $id . '"><p>' . $name . '</p></a>
            </div>
            <a href="detail.php?id=' . $id . '">
                <img src="' . $image . '" class="cake-img" alt="' . $name . '">
            </a>
        </div>';
}

function category_display($cake)
{
    return
        '<div class="cake-item">
            <a href="product.php?category=' . $cake['category'] . '">
                <div class="name-plate">
                    <img src="image/name_tag.png" class="name-plate-img">
                    <p>' . $cake['category'] . '</p>
                </div>
                <img src="' . $cake['image_url'] . '" class="cake-img" alt="' . $cake['name'] . '">
            </a>
        </div>';
}

function category_plate($cat)
{
    return
        '<img src="image/category_plate.png" class="cat-plate-img">
         <p>' . $cat . '</p>';
}
?>

<style>
    .showcase-container {
        position: relative;
        width: 1202px;
        margin: 0 auto;
        aspect-ratio: 1202/954;
        overflow: hidden;
        display: flex;
        justify-content: center;
    }

    .showcase-img {
        position: absolute;
        width: 100%;
        bottom: 0;
        left: 0;
        z-index: 1;
    }

    .cake-area {
        position: absolute;
        width: 90%;
        height: 100%;
        bottom: 265px;
        display: flex;
        justify-content: center;
        z-index: 2;
    }

    .cat-plate {
        position: absolute;
        width: 20%;
        height: 10%;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 8;
    }

    .cat-plate-img {
        width: 100%;
        height: 100%;
        position: absolute;
        object-fit: contain;
        z-index: 9;
    }

    .cat-plate p {
        position: relative;
        font-size: 1.4rem;
        color: #3a2d1a;
        font-weight: bold;
        z-index: 10;
    }

    .showcase-tier {
        position: absolute;
        width: 100%;
        height: 150px;
        display: flex;
        justify-content: center;
        align-items: flex-end;
        z-index: 3;
    }

    .cake-item {
        position: relative;
        height: 100%;
        width: 25%;
        z-index: 4;
        transition: transform 0.25s ease;
    }

    .name-plate {
        width: 100%;
        height: 20%;
        position: absolute;
        bottom: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 5;
    }

    .name-plate-img {
        width: 100%;
        height: 100%;
        position: absolute;
        object-fit: fill;
        z-index: 6;
    }

    .name-plate p {
        position: relative;
        color: #3a2d1a;
        font-size: 0.9rem;
        font-weight: bold;
        text-align: center;
        margin: 0;
        z-index: 7;
    }

    .cake-img {
        position: absolute;
        bottom: 20%;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        height: 80%;
        object-fit: contain;
        z-index: 5;
    }

    .cake-item:hover {
        transform: scale(1.15);
    }
</style>

<div class="container mt-5">
    <h1 class="mb-4">商品一覧</h1>
    <form method="get" action="product.php" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="search" name="keyword" class="form-control"
                placeholder="商品名で検索" value="<?= e($_GET['keyword'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <select name="category" class="form-select">
                <option value="" selected>カテゴリーで検索</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= e($c) ?>"><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">検索</button>
        </div>
    </form>
</div>

<div class="showcase-container">
    <img src="image/showcase_test4.png" class="showcase-img">
    <div class="cat-plate">
        <?= ($cat !== '') ? category_plate($cat) : '' ?>
    </div>
    <div class="cake-area">
        <?php
        if ($kw !== '') {
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $kw) . '%';
            $kw_stmt = $pdo->prepare("SELECT * FROM product WHERE name LIKE ? ORDER BY updated_at DESC");
            $kw_stmt->execute([$like]);
            $results = $kw_stmt->fetchAll();
            $mode = 'cake_display';
        } elseif ($cat !== '') {
            $cat_stmt = $pdo->prepare("SELECT * FROM product WHERE category = ? ORDER BY updated_at DESC");
            $cat_stmt->execute([$cat]);
            $results = $cat_stmt->fetchAll();
            $mode = 'cake_display';
        } else {
            $all_cat_stmt = $pdo->query("SELECT * FROM product GROUP BY category ORDER BY category DESC, updated_at DESC");
            $results = $all_cat_stmt->fetchAll();
            $mode = 'category_display';
        }

        $tiers = [
            array_slice($results, 0, 4),
            array_slice($results, 4, 4),
            array_slice($results, 8, 4),
            array_slice($results, 12, 4)
        ];
        $tiers_y = [0, 155, 320, 480];

        foreach ($tiers as $tier_index => $cakes):
            $tier_y = $tiers_y[sizeof($tiers) - 1 - $tier_index] ?? null;
        ?>
            <div class="showcase-tier" style="bottom: <?= $tier_y ?>px;">
                <?php foreach ($cakes as $cake): ?>
                    <?= ($mode == 'cake_display') ? cake_display($cake) : category_display($cake) ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>