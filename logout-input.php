<?php
require_once 'app.php';
$page_title = 'Logout';
require_once 'header.php'; ?>
<div class="container py-4 content-narrow">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="h5 mb-3">ログアウトしますか？</h1>
      <a class="btn btn-danger" href="logout-output.php"><i class="bi bi-box-arrow-right"></i> ログアウト</a>
      <a class="btn btn-outline-secondary" href="index.php">キャンセル</a>
    </div>
  </div>
</div>
<?php require_once 'footer.php'; ?>