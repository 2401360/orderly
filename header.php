<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= isset($page_title) ? e($page_title) . ' | ' : '' ?>Orderly Shop</title>
  <meta http-equiv="Content-Security-Policy" content="default-src 'self' https: data:; img-src 'self' https: data:; style-src 'self' 'unsafe-inline' https:; script-src 'self' https: 'unsafe-inline';">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Noto Sans JP', sans-serif;
      background-color: #F8F1E7;
      color: #000000ff;
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    /* 写真スライダー用 */
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

    /* オープニング動画用 */
    #intro-video {
      position: fixed;
      inset: 0;
      z-index: 9999;
      background: #000;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    #intro-video video {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* ===== HERO ===== */
    .hero {
      position: relative;
      height: 90vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      overflow: hidden;
    }

    .hero video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: 1;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.4);
      z-index: 2;
    }

    .hero::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0.5px, transparent 0.5px);
      background-size: 6px 6px;
      z-index: 3;
      pointer-events: none;
    }

    .hero-inner {
      position: relative;
      z-index: 4;
      color: #fff;
      font-family: 'Noto Serif JP', serif;
    }

    .hero .brand {
      font-size: 3rem;
      font-weight: 900;
      margin-bottom: 10px;
    }

    .hero .catch {
      font-size: 1.3rem;
      font-weight: 300;
    }

    /* ===== フェードアニメ ===== */
    .fade-in {
      opacity: 0;
      animation: fadeIn 1.6s ease forwards;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .fade-out {
      opacity: 1;
      animation: fadeOut 1.2s ease forwards;
    }

    @keyframes fadeOut {
      from {
        opacity: 1;
      }

      to {
        opacity: 0;
      }
    }

    /* ===== 隠し部分 =====  */
    #main-content {
      display: none;
    }

    /* ===== ジャンル ===== */
    .section {
      padding: 50px 20px;
      text-align: center;
    }

    .section-title {
      font-size: 1.6rem;
      font-weight: 700;
      margin-bottom: 20px;
      font-family: 'Noto Serif JP', serif;
    }

    .grid {
      display: grid;
      gap: 16px;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      max-width: 1200px;
      margin: 0 auto;
    }

    /* 高級感ボタン風カード */
    .card {
      background: linear-gradient(145deg, #fdf6f0, #f8e8dc);
      border-radius: 16px;
      padding: 15px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
      text-align: center;
      cursor: pointer;
      transition: transform 0.35s ease, box-shadow 0.35s ease, opacity 0.35s ease;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      align-items: center;
    }

    .card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 10px;
    }

    .card div {
      width: 100%;
      font-size: 1rem;
      font-weight: 500;
      color: #4A3B2A;
      background: #FFFFFF;
      padding: 4px 8px;
      border-radius: 8px;
      display: inline-block;
      font-family: 'Noto Serif JP', serif;
      letter-spacing: 0.5px;
    }

    /* ===== 写真スライダー ===== */
    .slider-wrap {
      overflow: hidden;
      position: relative;
      padding: 20px 0;
      background: #f8f1e7;
    }

    .slider-container {
      max-width: 1200px;
      margin: 0 auto;
    }

    .slider-track {
      display: flex;
      gap: 16px;
      align-items: center;
    }

    .slider-track img {
      width: 200px;
      height: 140px;
      object-fit: cover;
      border-radius: 10px;
      flex: 0 0 auto;
    }

    .slider-mover {
      display: flex;
      gap: 16px;
      animation: scroll linear infinite;
      will-change: transform;
    }

    @keyframes scroll {
      0% {
        transform: translate3d(0, 0, 0);
      }

      100% {
        transform: translate3d(-50%, 0, 0);
      }
    }

    @media (max-width:600px) {
      .slider-track img {
        width: 140px;
        height: 100px;
      }
    }

    /* ===== シェフのこだわり ===== */
    .chef-section {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: flex-start;
      gap: 20px;
      padding: 50px 20px;
      background: #FFFFFF;
      position: relative;
    }

    .chef-side {
      display: flex;
      flex-direction: column;
      gap: 20px;
      flex: 1;
      min-width: 150px;
      max-width: 200px;
    }

    .chef-side img {
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
      width: 100%;
      object-fit: cover;
    }

    .chef-text {
      flex: 2;
      min-width: 300px;
      max-width: 600px;
      font-size: 1rem;
      line-height: 1.8;
      text-align: center;
      padding: 100px 20px;
      font-family: 'Noto Serif JP', serif;
      color: #4A3B2A;
      letter-spacing: 0.5px;
    }

    .chef-text h2 {
      font-family: 'Noto Serif JP', serif;
      font-weight: 600;
      font-size: 26px;
      margin-bottom: 15px;
      color: #3B2A1E;
    }

    .chef-text p {
      font-weight: 300;
    }

    /* とろける波 */
    .melt-decoration {
      width: 100%;
      line-height: 0;
      background: #FFFFFF;
    }

    .melt-decoration svg {
      width: 100%;
      height: 180px;
      display: block;
    }

    .melt-decoration path {
      fill: #F8F1E7;
    }

    /* ===== おすすめ ===== */
    .carousel-inner img {
      object-fit: cover;
    }

    .carousel-item img {
      border-radius: 10px;
      height: 450px;
    }

    .content-narrow {
      max-width: 960px;
      margin: 0 auto;
    }

    .card-hover:hover {
      transform: translateY(-2px);
      transition: .15s ease;
    }

    /* purchase-input page */
    .customer-info {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .customer-info h1 {
      font-size: 1.2rem;
      font-weight: 600;
      color: #333;
      border-left: 4px solid #0d6efd;
      padding-left: 10px;
      margin-bottom: 15px;
    }

    .customer-info p {
      margin-bottom: 8px;
      font-size: 1rem;
    }

    .customer-info p span {
      font-weight: bold;
      color: #555;
    }

    /* detail page */
    .product-hero {
      display: flex;
      align-items: center;
      background: #FFF6EE;
      box-sizing: border-box;
      padding: 40px;
      margin-bottom: 40px;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
      gap: 40px;
      border: 1px solid #f0f0f0;
      transition: box-shadow .25s ease;
    }

    .product-hero:hover {
      box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
    }

    .product-hero .hero-image {
      flex: 1 1 40%;
      max-width: 500px;
      order: 0;
    }

    .product-hero .hero-image img {
      width: 100%;
      border-radius: 12px;
    }

    .product-hero .hero-text {
      flex: 1 1 50%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 20px;
      padding-left: 50px;
      order: 1;
      text-align: left;
    }

    .product-hero .btn-review:hover {
      background-color: #e6a800;
    }

    .product-hero .btn-buy,
    .product-hero .btn-fav {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      font-weight: bold;
      border: none;
      margin-bottom: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .product-hero .btn-buy {
      background-color: #693529;
      color: #fff;
    }

    .product-hero .btn-buy:hover {
      background-color: #e6a800;
    }

    .product-hero .btn-fav {
      background-color: #ff4d4d;
      color: #fff;
      text-align: center;
    }

    .product-hero .btn-fav:hover {
      background-color: #e60000;
    }

    .product-hero .action-row {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      align-items: center;
    }

    .product-hero .action-row>* {
      width: 100%;
    }

    @media (max-width: 768px) {
      .product-hero {
        flex-direction: column;
        padding: 20px;
      }

      .product-hero .hero-image,
      .product-hero .hero-text {
        flex: 1 1 100%;
        max-width: 100%;
        order: initial;
      }

      .product-hero .hero-text {
        padding-left: 0;
      }

      .form-label {
        display: block;
        margin-bottom: 10px;
      }
    }

    .spec-container {
      background-color: #FFFFFF;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      box-sizing: border-box;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
      gap: 40px;
      border: 1px solid #f0f0f0;
      transition: box-shadow .25s ease;
    }

    .spec-container:hover {
      box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
    }

    .spec-images {
      display: flex;
      flex-direction: row;
      gap: 20px;
      flex-wrap: nowrap;
    }

    .spec-images img {
      width: 120px;
      height: auto;
      border-radius: 8px;
    }

    .spec-img-box {
      text-align: center;
    }

    .spec-img-box img {
      width: 130px;
      height: auto;
      border-radius: 10px;
      display: block;
      margin: 0 auto 8px;
    }

    .spec-caption {
      font-size: 0.9rem;
      color: #555;
    }

    .table-spec {
      width: 100%;
      max-width: 600px;
      border-collapse: collapse;
      font-size: 0.95rem;
      color: #444;
      margin-bottom: 40px;
      border: 1px solid #ddd;
    }

    .table-spec td {
      padding: 10px 20px;
      border-bottom: 1px solid #e0dcd3;
      vertical-align: top;
    }

    .table-spec tr:last-child td {
      border-bottom: none;
    }

    .table-spec .td-key {
      width: 40%;
      background-color: #F9F6ED;
      font-weight: 600;
    }

    .table-spec .td-value {
      width: 65%;
    }

    .related-products {
      background-color: #F9F6ED;
      box-sizing: border-box;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
      gap: 40px;
      border: 1px solid #f0f0f0;
      transition: box-shadow .25s ease;
    }

    .related-products:hover {
      box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
    }

    .review {
      background-color: #EFE8E4;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
      gap: 40px;
      border: 1px solid #f0f0f0;
      transition: box-shadow .25s ease;
    }

    .review:hover {
      box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
    }

    /* product page */
    .showcase-container {
      position: relative;
      width: 1202px;
      margin: 0 auto;
      aspect-ratio: 1202/ 954;
      /* 背景画像の縦横比 */
      overflow: hidden;
      display: flex;
      justify-content: center;
    }

    /* 背景 */
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
      /* ← 上部中央に見やすく配置 */
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 8;
      /* ★ ケーキより前面へ */
    }

    .cat-plate-img {
      width: 100%;
      height: 100%;
      position: absolute;
      left: 0;
      top: 0;
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

    /* 各段 */
    .showcase-tier {
      position: absolute;
      width: 100%;
      height: 150px;
      display: flex;
      justify-content: center;
      align-items: flex-end;
      z-index: 3;
      overflow: visible;
    }

    /* ケーキ＋皿 */
    .cake-item {
      position: relative;
      height: 100%;
      width: 25%;
      z-index: 4;
      transition: transform 0.25s ease, z-index 0s;
    }

    /* 皿 */
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
      bottom: 0;
      left: 0;
      object-fit: fill;
      z-index: 6;
    }

    .name-plate p {
      position: relative;
      color: #3a2d1a;
      /* お皿に合うように少し濃いブラウン */
      font-size: 0.9rem;
      font-weight: bold;
      text-align: center;
      margin: 0;
      padding: 0;
      z-index: 7;
    }

    /* ケーキ画像 */
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

    /* hover effect */
    .cake-item:hover {
      transform: scale(1.15);
    }
  </style>
</head>

<body>
  <?php require_once 'menu.php'; ?>
  <?php foreach (flashes() as $f): ?>
    <div class="container pt-3">
      <div class="alert alert-<?= e($f['t']) ?>"><?= e($f['m']) ?></div>
    </div>
  <?php endforeach; ?>