<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$me = $_SESSION['customer'] ?? null;
$is_admin = isset($me['role']) && $me['role'] === 'admin';
?>

<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($page_title) ? e($page_title) . ' | ' : '' ?>Orderly</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    .navbar {
      background: #6C6059;
      padding: 0.6rem 1rem;
    }

    .navbar-brand {
      font-size: 2rem;
      font-weight: bold;
      font-family: "Playwrite AU SA", cursive;
      color: #ddc6b6 !important;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .nav-link {
      color: #fff !important;
      font-weight: 600;
      padding: 8px 12px;
    }

    .nav-link:hover,
    .dropdown-item:hover {
      color: #ddc6b6 !important;
    }

    .btn-cart {
      background: #fff;
      color: #6C6059;
      border-radius: 8px;
      padding: 6px 12px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .btn-cart:hover {
      background: #ddc6b6;
    }

    .cart-badge {
      font-size: 0.75rem;
      font-weight: bold;
    }

    .dropdown-menu {
      border-radius: 10px;
      min-width: 160px;
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">

      <a class="navbar-brand" href="index.php">
        <i class="bi bi-cake2"></i> Orderly
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div id="navMain" class="collapse navbar-collapse">

        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="product.php">商品</a></li>
          <li class="nav-item"><a class="nav-link" href="favorite-show.php">お気に入り</a></li>
          <li class="nav-item"><a class="nav-link" href="history.php">購入履歴</a></li>

          <?php if ($is_admin): ?>
            <li class="nav-item"><a class="nav-link" href="admin-products.php">商品管理</a></li>
            <li class="nav-item"><a class="nav-link" href="orders-details.php">注文一覧</a></li>
            <li class="nav-item"><a class="nav-link" href="sales-details.php">商品売上</a></li>
          <?php endif; ?>
        </ul>

        <ul class="navbar-nav ms-auto align-items-center">

          <li class="nav-item me-3">
            <a class="btn-cart" href="cart-show.php">
              <i class="bi bi-cart"></i>
              <span class="badge bg-primary rounded-pill cart-badge">
                <?= cart_count() ?>
              </span>
            </a>
          </li>

          <?php if ($me): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i> <?= e($me['name']) ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="customer-input.php">プロフィール</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="logout-input.php">ログアウト</a></li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login-input.php">ログイン</a></li>
            <li class="nav-item"><a class="nav-link" href="customer-input.php">新規登録</a></li>
          <?php endif; ?>

        </ul>
      </div>
    </div>
  </nav>