<?php
session_start();
// Unset all session values
$_SESSION = [];
// Delete session cookie
if (ini_get('session.use_cookies')) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
// Destroy session
session_destroy();
$page_title = 'Logged out';
require_once 'header.php';
?>
<div class="container py-4 content-narrow">
  <div class="alert alert-info">ログアウトしました。</div>
  <a class="btn btn-primary" href="index.php">トップへ</a>
</div>
<?php require_once 'footer.php'; ?>