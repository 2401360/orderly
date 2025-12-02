<?php
require_once 'app.php';
$page_title = 'Sign up / Profile';
require_once 'header.php'; ?>
<?php
$editing = isset($_SESSION['customer']);
$name    = $editing ? ($_SESSION['customer']['name'] ?? '')    : '';
$address = $editing ? ($_SESSION['customer']['address'] ?? '') : '';
$login   = $editing ? ($_SESSION['customer']['login'] ?? '')   : '';
?>
<div class="container py-4 content-narrow">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="h5 mb-3"><?= $editing ? 'プロフィール更新' : '新規登録' ?></h1>

      <form action="customer-output.php" method="post" class="vstack gap-3" novalidate>
        <div>
          <label class="form-label">名前</label>
          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" require_onced>
        </div>
        <div>
          <label class="form-label">住所</label>
          <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($address) ?>" require_onced>
        </div>
        <div>
          <label class="form-label">ログイン名</label>
          <input type="text" name="login" class="form-control" value="<?= htmlspecialchars($login) ?>" require_onced>
          <div class="form-text">英数字推奨・重複不可</div>
        </div>
        <div>
          <label class="form-label">パスワード</label>
          <input type="password" name="password" class="form-control" minlength="6" require_onced>
          <div class="form-text">6文字以上</div>
        </div>
        <button class="btn btn-primary w-100"><?= $editing ? '更新' : '登録' ?></button>
      </form>
    </div>
  </div>
</div>
<?php require_once 'footer.php'; ?>