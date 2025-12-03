<?php
$me = $_SESSION['customer'] ?? null;
$is_admin = isset($me['role']) && $me['role'] === 'admin';
?>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="index.php"><i class="bi bi-cake2"></i> Orderly</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="product.php">商品</a></li>
        <li class="nav-item"><a class="nav-link" href="favorite-show.php">お気に入り</a></li>
        <li class="nav-item"><a class="nav-link" href="history.php">購入履歴</a></li>
        <?php if ($is_admin): ?>
          <li class="nav-item"><a class="nav-link" href="admin-products.php">商品管理</a></li>
          <li class="nav-item"><a class="nav-link" href="orders-details.php">注文一覧</a></li>
          <li class="nav-item"><a class="nav-link" href="sales-details.php">商品売上</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item me-2">
          <a class="btn btn-outline-secondary position-relative" href="cart-show.php">
            <i class="bi bi-cart"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-primary">
              <?= cart_count() ?>
            </span>
          </a>
        </li>
        <?php if ($me): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle"></i> <?= e($me['name']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="customer-input.php">プロフィール</a></li>
              <?php if ($is_admin): ?>
                <li>
                  <hr class="dropdown-divider">
                </li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="logout-input.php">ログアウト</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login-input.php">ログイン</a></li>
          <li class="nav-item"><a class="btn btn-primary ms-2" href="customer-input.php">新規登録</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>