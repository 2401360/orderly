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
      

   .navbar-brand.name {
  display: flex;
  flex-direction: column;
  align-items: center;
  line-height: 1.1;
      width: 18%;
    font-size: 38px;
    font-weight: 800px;
    font-family: "Playwrite AU SA", cursive;
    color: #ddc6b6;
  }

  .navbar-brand.name i {
    font-size: 1.7rem;
    margin-bottom: 3px;
  }
  .navbar .container-fluid {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  .nav-item{
    font-size: 25px;
    font-weight: bold;
    text-decoration: none;
  }
  .navbar>.container, .navbar>.container-fluid, .navbar>.container-lg, .navbar>.container-md, .navbar>.container-sm, .navbar>.container-xl, .navbar>.container-xxl {
    display: flex;
    flex-wrap: inherit;
    align-items: center;
    justify-content: space-between;
  }
  .navbar-collapse {
    flex-basis: 100%;
    flex-grow: 1;
    align-items: center;
    padding-right: 54px;
    padding-top: 20px;
    padding-bottom: 20px;
    
  }
  .navbar {
    --bs-navbar-padding-x: 0;
    --bs-navbar-padding-y: 0.5rem;
    --bs-navbar-color: rgba(var(--bs-emphasis-color-rgb), 0.65);
    --bs-navbar-hover-color: rgba(var(--bs-emphasis-color-rgb), 0.8);
    --bs-navbar-disabled-color: rgba(var(--bs-emphasis-color-rgb), 0.3);
    --bs-navbar-active-color: rgba(var(--bs-emphasis-color-rgb), 1);
    --bs-navbar-brand-padding-y: 0.3125rem;
    --bs-navbar-brand-margin-end: 1rem;
    --bs-navbar-brand-font-size: 1.25rem;
    --bs-navbar-brand-color: rgba(var(--bs-emphasis-color-rgb), 1);
    --bs-navbar-brand-hover-color: rgba(var(--bs-emphasis-color-rgb), 1);
    --bs-navbar-nav-link-padding-x: 0.5rem;
    --bs-navbar-toggler-padding-y: 0.25rem;
    --bs-navbar-toggler-padding-x: 0.75rem;
    --bs-navbar-toggler-font-size: 1.25rem;
    --bs-navbar-toggler-icon-bg: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2833, 37, 41, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e);
    --bs-navbar-toggler-border-color: rgba(var(--bs-emphasis-color-rgb), 0.15);
    --bs-navbar-toggler-border-radius: var(--bs-border-radius);
    --bs-navbar-toggler-focus-width: 0.25rem;
    --bs-navbar-toggler-transition: box-shadow 0.15s 
ease-in-out;
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: var(--bs-navbar-padding-y) var(--bs-navbar-padding-x);
    background-color: #6C6059;
}
.nav-link{
  color: white;
  font-family: 'Arial', sans-serif;
}
.btn-outline-secondary {
    /* --bs-btn-color: #6c757d; */
    --bs-btn-border-color: #bfcad5;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: #6c757d;
    --bs-btn-hover-border-color: #6c757d;
    --bs-btn-focus-shadow-rgb: 108, 117, 125;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: #6c757d;
    --bs-btn-active-border-color: #6c757d;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #6c757d;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color: #6c757d;
    --bs-gradient: none;
    background-color: bisque;
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