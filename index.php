<?php
require_once 'app.php';
$page_title = 'ホーム';
require_once 'header.php';

$cid = $_SESSION['customer']['id'] ?? 0;
$pdo = db();

/* おすすめ商品 20件 */
$sqlReco = "
SELECT p.*,
       CASE WHEN :cid1 > 0 AND EXISTS(
            SELECT 1 FROM favorite f
             WHERE f.customer_id = :cid2 AND f.product_id = p.id)
       THEN 1 ELSE 0 END AS is_fav
  FROM product p
 WHERE COALESCE(p.is_recommended,0)=1
 ORDER BY p.id DESC
 LIMIT 20";

$stReco = $pdo->prepare($sqlReco);
$stReco->bindValue(':cid1', $cid, PDO::PARAM_INT);
$stReco->bindValue(':cid2', $cid, PDO::PARAM_INT);
$stReco->execute();

$recommended = $stReco->fetchAll();
?>


<!-- オープニング動画 -->
<div id="intro-video">
    <video id="video-intro" autoplay muted playsinline>
        <source src="お店の動画だよ.mp4" type="video/mp4">
    </video>
</div>

<!-- メインコンテンツ -->
<div id="main-content">

    <!-- HERO -->
    <div class="hero">
        <video autoplay muted loop playsinline>
            <source src="o-dari.mp4" type="video/mp4">
        </video>
        <div class="hero-inner">
            <div class="brand">ORDERLY</div>
            <div class="catch">Adding a touch of sweetness to your precious moments.</div>
        </div>
    </div>

    <!-- ジャンル -->
    <section class="section">
        <div class="section-title">ＧＥＮＲＥ</div>
        <div class="grid">
            <div>
                <a href="product.php?q=&cat=ホールケーキ" class="card">
                    <img src="image/ホールケーキ③.jpg" alt="">
                    <div>ホールケーキ</div>
                </a>
            </div>
            <div>
                <a href="product.php?q=&cat=クッキー" class="card">
                    <img src="image/kultuki-.jpg" alt="">
                    <div>クッキー</div>
                </a>
            </div>
            <div>
                <a href="product.php?q=&cat=チョコレート" class="card">
                    <img src="image/tyokore-to.jpg" alt="">
                    <div>チョコレート</div>
                </a>
            </div>
            <div>
                <a href="product.php?q=&cat=ショートケーキ" class="card">
                    <img src="image/ショートケーキ②.jpg" alt="">
                    <div>ショートケーキ</div>
                </a>
            </div>
        </div>
    </section>

    <!-- スライダー：おすすめ商品ループ -->
    <section class="slider-wrap">
        <div class="slider-container">
            <div class="slider-track">
                <div class="slider-mover" id="sliderMover">

                    <?php foreach ($recommended as $p): ?>
                        <a href="detail.php?id=<?= (int)$p['id'] ?>" class="slide-item">
                            <img src="<?= e($p['image_url']) ?>"
                                alt="<?= e($p['name']) ?>"
                                class="slide-img">
                        </a>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </section>

    <!-- シェフのこだわり -->
    <section class="chef-section">
        <div class="chef-side">
            <img src="image/作っている人④.jpeg" style="height:120px;" alt="">
            <img src="image/ke-kiki.jpg" style="height:160px;" alt="">
            <img src="image/チョコレート.avif" style="height:100px;" alt="">
        </div>

        <div class="chef-text">
            <h2>当店のこだわり</h2>
            <p>
                素材の選び抜きから焼成時の温度管理、そして繊細なデコレーションに至るまで、
                すべての工程に一切の妥協を許さず、ひとつひとつのケーキを丹念に仕上げております。
                旬の果実や地元産の良質な素材を贅沢に使用し、四季の移ろいを感じられるラインナップをご用意しております。
                さらに、味わいだけでなく、思わず目を奪われるような美しさにもこだわり、お客様に驚きと感動のひとときをお届けできるよう心を尽くしております。
                職人の情熱を込めた、上質なケーキの数々をぜひご堪能ください。
            </p>
        </div>

        <div class="chef-side">
            <img src="image/焼くところ.jpg" style="height:140px;" alt="">
            <img src="image/お芋ケーキ.jpg" style="height:120px;" alt="">
            <img src="image/作る人③.avif" style="height:160px;" alt="">
        </div>
    </section>

    <!-- とろける波 -->
    <div class="melt-decoration">
        <svg viewBox="0 0 1440 200" preserveAspectRatio="none">
            <path d="
        M0,40
        C120,80 240,120 360,100
        C480,80 600,40 720,70
        C840,100 960,160 1080,140
        C1200,120 1320,80 1440,100
        L1440,200 L0,200 Z">
            </path>
        </svg>
    </div>

    <!-- 季節のおすすめ（おすすめ商品を自動表示） -->
    <section class="section" style="padding:50px 20px; background:#F8F1E7;">
        <div class="carousel-reco-grid" style="display:grid;grid-template-columns:1fr 1fr; align-items:center; max-width:1200px; margin:0 auto; position:relative; gap:20px;">

            <!-- 左：カルーセル -->
            <div style="position:relative; z-index:2;">
                <div id="recoCarousel" class="carousel slide mb-4" data-bs-ride="carousel">

                    <!-- インジケーター（動的） -->
                    <div class="carousel-indicators">
                        <?php foreach ($recommended as $index => $item): ?>
                            <?php if ($index >= 3) break; ?>
                            <button type="button"
                                data-bs-target="#recoCarousel"
                                data-bs-slide-to="<?= $index ?>"
                                class="<?= $index === 0 ? 'active' : '' ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- スライド本体（動的） -->
                    <div class="carousel-inner rounded-4 shadow-sm">

                        <?php foreach ($recommended as $index => $item): ?>
                            <?php if ($index >= 3) break; ?>

                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <a href="detail.php?id=<?= (int)$item['id'] ?>">
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img src="<?= e($item['image_url']) ?>"
                                            class="d-block w-100"
                                            style="aspect-ratio: 2 / 1; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary d-flex align-items-center justify-content-center text-white"
                                            style="aspect-ratio: 2 / 1;">
                                            No Image
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>

                        <?php endforeach; ?>
                    </div>

                    <!-- 左右ボタン -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#recoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#recoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>

                </div>
            </div>

            <!-- 右：タイトル -->
            <div style="position:relative; z-index:2; text-align:center;">
                <svg width="150" height="50" style="margin-bottom:10px;">
                    <path d="M0,25 Q75,0 150,25" stroke="#4A3B2A" stroke-width="2" fill="none"></path>
                    <path d="M0,25 Q75,50 150,25" stroke="#4A3B2A" stroke-width="2" fill="none"></path>
                </svg>

                <h2 style="font-size:3rem; font-weight:700; color:#4A3B2A; font-family:'Noto Serif JP', serif;">
                    季節のおすすめ
                </h2>

                <svg width="150" height="50" style="margin-top:10px;">
                    <path d="M0,25 Q75,0 150,25" stroke="#4A3B2A" stroke-width="2" fill="none"></path>
                    <path d="M0,25 Q75,50 150,25" stroke="#4A3B2A" stroke-width="2" fill="none"></path>
                </svg>
            </div>

            <div style="
            position:absolute;
            top:0;
            left:50%;
            width:50%;
            height:100%;
            background: linear-gradient(to right, rgba(248,241,231,0) 0%, #F8F1E7 100%);
            pointer-events:none;
            z-index:1;">
            </div>

        </div>
    </section>


</div> <!-- /#main-content -->

<?php require 'footer.php'; ?>


<!-- スライダー無限ループ JS -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const mover = document.getElementById("sliderMover");

        mover.addEventListener("animationiteration", () => {
            const firstImg = mover.querySelector("img");

            // 最初の画像を末尾へ移動
            mover.appendChild(firstImg);

            // アニメーション再起動（ものすごく滑らか）
            mover.style.animation = "none";
            requestAnimationFrame(() => {
                mover.style.animation = "";
            });
        });
    });
</script>


<!-- 初回だけオープニング動画 -->
<script>
    const introDiv = document.getElementById('intro-video');
    const introVideo = document.getElementById('video-intro');
    const mainContent = document.getElementById('main-content');

    // 最後に動画を再生した時間を取得
    const lastPlayed = localStorage.getItem('introLastPlayed');
    const now = Date.now();

    // 5分
    const LIMIT = 30 * 1000;

    // 動画を再生すべきかの判定
    const shouldPlay = !lastPlayed || // 初回
        (now - Number(lastPlayed) > LIMIT); // 4分経過

    if (shouldPlay) {
        // 動画を再生する
        introVideo.addEventListener('ended', () => {
            introDiv.classList.add('fade-out');
            setTimeout(() => {
                introDiv.style.display = 'none';
                mainContent.style.display = 'block';
                mainContent.classList.add('fade-in');

                // 最終再生時間を保存
                localStorage.setItem('introLastPlayed', Date.now());
            }, 1200);
        });

    } else {
        // スキップ
        introDiv.style.display = 'none';
        mainContent.style.display = 'block';
        mainContent.classList.add('fade-in');
    }
</script>



<!-- スライダー CSS -->
<style>
    .slider-track {
        width: 100%;
        overflow: hidden;
    }

    #sliderMover {
        display: flex;
        gap: 18px;
        animation: slide 4s linear infinite;
    }

    .slide-img {
        width: 200px;
        height: 140px;
        border-radius: 12px;
        object-fit: cover;
    }

    @keyframes slide {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(-218px);
        }
    }
</style>