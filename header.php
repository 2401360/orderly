<?php

if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
  ]);
  session_start();
}
require_once '/app.php';
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
    body {
      background: #f8fafc;
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
  <?php require_once '/menu.php'; ?>
  <?php foreach (flashes() as $f): ?>
    <div class="container pt-3">
      <div class="alert alert-<?= e($f['t']) ?>"><?= e($f['m']) ?></div>
    </div>
  <?php endforeach; ?>