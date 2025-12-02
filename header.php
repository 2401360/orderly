<?php

if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
  ]);
  session_start();
}
require_once 'app.php';
?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= isset($page_title) ? e($page_title) . ' | ' : '' ?>Orderly Shop</title>
  <meta http-equiv="Content-Security-Policy" content="default-src 'self' https: data:; img-src 'self' https: data:; style-src 'self' 'unsafe-inline' https:; script-src 'self' https: 'unsafe-inline';">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body,
    html {
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
  </style>
</head>

<body>
  <?php require_once 'menu.php'; ?>
  <?php foreach (flashes() as $f): ?>
    <div class="container pt-3">
      <div class="alert alert-<?= e($f['t']) ?>"><?= e($f['m']) ?></div>
    </div>
  <?php endforeach; ?>