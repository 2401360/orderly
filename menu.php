<?php
$me = $_SESSION['customer'] ?? null;
$is_admin = isset($me['role']) && $me['role'] === 'admin';
?>
<style>
  /* NAVBAR BASE */
  .navbar {
    background-color: #6C6059;
    padding-top: 0.6rem;
    padding-bottom: 0.6rem;
  }

  /* BRAND */
  .navbar-brand.name {
    display: flex;
    flex-direction: row;
    align-items: center;
    font-size: 2rem;
    font-weight: 800;
    font-family: "Playwrite AU SA", cursive;
    color: #ddc6b6 !important;
    gap: 10px;
  }

  .navbar-brand.name i {
    font-size: 2rem;
    margin: 0;
  }

  /* NAV LINKS */
  .nav-link {
    color: #fff !important;
    font-size: 1.2rem;
    font-weight: 600;
    padding: 10px 14px;
    transition: 0.2s;
  }

  .nav-link:hover {
    color: #ddc6b6 !important;
  }

  /* ACTIVE PAGE STYLE */
  .nav-item.active>.nav-link {
    color: #ddc6b6 !important;
  }

  /* CART BUTTON */
  .btn-cart {
    position: relative;
    background-color: #fff;
    border: none;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 1.2rem;
    color: #6C6059;
    transition: 0.2s ease;
  }

  .btn-cart:hover {
    background-color: #ddc6b6;
  }

  .cart-badge {
    font-size: 0.65rem;
    font-weight: bold;
  }

  /* DROPDOWN */
  .dropdown-menu {
    font-size: 1.1rem;
  }

  .dropdown-item:hover {
    background-color: #f2e6dd;
  }

  /* TOGGLER */
  .navbar-toggler {
    border-color: #ddc6b6;
  }

  .navbar-toggler-icon {
    filter: invert(90%);
  }
</style>
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">

    <!-- BRAND -->
    <a class="navbar-brand name" href="index.php">
      <i class="bi bi-cake2"></i>
      Orderly
    </a>

    <!-- TOGGLER -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- NAV LINKS -->
    <div id="navMain" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        <li class="nav-item"><a class="nav-link" href="product.php">商品</a></li>
        <li class="nav-item"><a class="nav-link" href="favorite-show.php">お気に入り</a></li>
        <li class="nav-item"><a class="nav-link" href="history.php">購入履歴</a></li>

        <?php if ($is_admin): ?>
          <li class="nav-item">
            <a class="nav-link" href="admin-products.php">商品管理</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="orders-details.php">注文一覧</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="sales-details.php">商品売上</a>
          </li>
        <?php endif; ?>


      </ul>

      <!-- RIGHT SIDE -->
      <ul class="navbar-nav ms-auto align-items-center">

        <!-- CART -->
        <li class="nav-item me-2">
          <a class="btn btn-cart" href="cart-show.php">
            <i class="bi bi-cart"></i>
            <span class="badge rounded-pill bg-primary cart-badge">
              <?= cart_count() ?>
            </span>
          </a>
        </li>

        <!-- USER -->
        <?php if ($me): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
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