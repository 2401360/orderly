<?php require 'db-connect.php';
$pdo = new PDO($connect, USER, PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
if (isset($_POST['button']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = $_POST['productid'];
    $username = trim($_POST['username']);
    $content = trim($_POST['content']);

    if (!empty($username) && !empty($content)) {
        $sql = "INSERT INTO comment(product_id, user, comment) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$pid, $username, $content]);
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}
$page_title = 'Product Detail';
require 'header.php';
?>
<style>
  .comment-section {
  background: #fafafa;
  border-radius: 18px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
  padding: 30px;
  margin: 70px auto 50px;
  max-width: 800px;
  font-family: "Noto Sans JP", "Inter", sans-serif;
  border: 1px solid #eee;
  position: relative;
}
.comment-section::before {
  content: "";
  position: absolute;
  top: -45px;
  left: 10%;
  width: 80%;
  height: 3px;
  background: linear-gradient(90deg, #cde8ff, #f8e6d9);
  border-radius: 6px;
}

.comment-section h3 {
  font-size: 1.4rem;
  font-weight: 700;
  color: #4b7ea8;
  margin-bottom: 25px;
  border-left: 6px solid #a3c4dc;
  padding-left: 12px;
}

.comment-box {
  background: #fff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 14px 18px;
  margin-bottom: 16px;
  transition: background 0.2s ease, box-shadow 0.2s ease;
}

.comment-box:hover {
  background: #f6faff;
  box-shadow: 0 4px 10px rgba(200, 220, 255, 0.3);
}

.comment-box strong {
  color: #527da3;
  font-weight: 600;
  font-size: 1rem;
}

.comment-box p {
  margin: 6px 0;
  font-size: 0.96rem;
  color: #444;
  line-height: 1.6;
}

.comment-box small {
  color: #999;
  font-size: 0.8rem;
}

/* Form */
.comment-form {
  border-top: 2px dashed #e5edf2;
  padding-top: 20px;
  margin-top: 30px;
}

.comment-form input,
.comment-form textarea {
  width: 100%;
  border: 1.5px solid #d5e1ea;
  border-radius: 10px;
  padding: 10px 14px;
  margin-bottom: 12px;
  font-size: 0.95rem;
  background: #fff;
  transition: border-color 0.25s, box-shadow 0.25s;
}

.comment-form input:focus,
.comment-form textarea:focus {
  border-color: #9ecaf2;
  box-shadow: 0 0 6px rgba(158, 202, 242, 0.5);
  outline: none;
}

.comment-form {
  border-top: 2px dashed #e5edf2;
  padding-top: 20px;
  margin-top: 30px;
  text-align: center; 
}

.comment-form button {
  background: linear-gradient(90deg, #4fa3f7, #7ec6ff);
  color: #fff;
  border: none;
  border-radius: 50px;
  padding: 12px 32px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 6px 14px rgba(79, 163, 247, 0.3);
  display: inline-block;
}

.comment-form button:hover {
  background: linear-gradient(90deg, #3a8ce0, #69b9ff);
  box-shadow: 0 8px 18px rgba(79, 163, 247, 0.4);
  transform: translateY(-2px);
}

.comment-form button:active {
  transform: scale(0.98);
  opacity: 0.9;
}

.no-comment {
  color: #999;
  font-style: italic;
  text-align: center;
  margin: 15px 0 25px;
}
.fade-in {
  animation: fadeIn 0.6s ease both;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
<div class="container py-4 content-narrow">
<?php
$sql = $pdo->prepare('select * from product where id=?');
$sql->execute([$_REQUEST['id'] ?? 0]);
foreach ($sql as $row): ?>
  <div class="card shadow-sm">
    <div class="row g-0">
      <div class="col-md-5">
        <?php if (!empty($row['image_url'])): ?>
          <img src="<?= htmlspecialchars($row['image_url']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($row['name']) ?>">
        <?php endif; ?>
      </div>
      <div class="col-md-7">
        <div class="card-body">
          <h1 class="h4 mb-1"><?= htmlspecialchars($row['name']) ?></h1>
          <div class="text-muted mb-3">¥<?= number_format($row['price']) ?></div>
          <?php if (!empty($row['description'])): ?>
            <p class="mb-4"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
          <?php endif; ?>
          <form action="cart-insert.php" method="post" class="d-flex flex-wrap gap-2 align-items-end">
            <div>
              <label class="form-label">数量</label>
              <select name="count" class="form-select">
                <?php for ($i=1; $i<=10; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
            <input type="hidden" name="price" value="<?= (int)$row['price'] ?>">
            <button class="btn btn-primary"><i class="bi bi-bag-plus"></i> カートに追加</button>
            <a href="favorite-insert.php?id=<?= (int)$row['id'] ?>" class="btn btn-outline-danger"><i class="bi bi-heart"></i> お気に入り</a>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php
    $stmt = $pdo->prepare("SELECT * FROM comment WHERE product_id = ? ORDER BY created_at DESC");
    $stmt->execute([(int)$row['id']]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>
 <div class="comment-section">
  <h3>💬 コメント（レビュー）</h3>
    <?php if (!empty($comments)): ?>
    <?php foreach ($comments as $c): ?>
        <div class="comment-box">
            <strong><?= htmlspecialchars($c['user']) ?></strong><br>
            <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
            <small><?= $c['created_at'] ?></small>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="no-comment">まだコメントがありません。最初のコメントを書いてみましょう 💬</p>
<?php endif; ?>


    <div class="comment-form">
        <form method="POST">
            <input type="hidden" name="productid" value="<?= (int)$row['id'] ?>">
            <input type="text" name="username" placeholder="お名前は入力してください" required>
            <textarea name="content" rows="3" placeholder="コメントを入力してください" required></textarea>
            <button type="submit" name="button">送信</button>
        </form>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php require 'footer.php'; ?>
