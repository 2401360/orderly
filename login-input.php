<?php $page_title = 'Login';
require_once 'header.php'; ?>
<div class="container py-4 content-narrow">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="h4 mb-3">ログイン</h1>
      <form action="login-output.php" method="post" class="vstack gap-3">
        <div>
          <label class="form-label">ログイン名</label>
          <input type="text" name="login" class="form-control" require_onced>
        </div>
        <div>
          <label class="form-label">パスワード</label>
          <input type="password" name="password" class="form-control" require_onced>
        </div>
        <button class="btn btn-primary w-100">ログイン</button>
      </form>
    </div>
  </div>
</div>
<?php require_once 'footer.php'; ?>