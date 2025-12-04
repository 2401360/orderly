<footer class="mt-4 py-3 border-top">

<style>
  footer {
    background: #6C6059;
 ; /* ← フッター全体の背景色を統一 */
    margin: 0;
    padding: 0;
  }

  .footer-container {
    display: flex;
    flex-wrap: nowrap;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Noto Serif JP', serif;
    background: #6C6059;
 ;
    color: #ffffffff;
    border-radius: 10px;
    justify-content: space-between;
    align-items: flex-start;
  }

  .footer-map {
    flex: 0 0 250px;
  }
  .footer-map iframe {
    width: 100%;
    height: 150px;
    border: 0;
    border-radius: 8px;
  }

  .footer-info {
    flex: 0.8 0 300px;
    font-size: 18px;
    text-align: left;
  }
  .footer-info h4 {
    font-size: 20px;
    margin-bottom: 10px;
  }

  .footer-links {
    flex: 0 0 150px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: flex-end;
  }
  .footer-links a {
    text-decoration: none;
    color: #ffffffff;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
  }
  .footer-links a:hover {
    color: #ff9900;
  }

  @media (max-width: 768px) {
    .footer-container {
      flex-direction: column;
      align-items: center;
      gap: 15px;
    }
    .footer-links {
      flex-direction: row;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
    }
    .footer-info {
      text-align: left;
    }
  }
</style>

<div class="footer-container">
  <div class="footer-map">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3323.83962290788!2d130.41859821182652!3d33.58351307322588!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x354191b0a7a68e51%3A0xac6f704b1b5c621a!2z44CSODEyLTAwMTYg56aP5bKh55yM56aP5bKh5biC5Y2a5aSa5Yy65Y2a5aSa6aeF5Y2X77yS5LiB55uu77yR77yS4oiS77yT77yS77yS!5e0!3m2!1sja!2sjp" allowfullscreen="" loading="lazy"></iframe>
  </div>

  <div class="footer-info">
    <h4>店舗案内</h4>
    <p>
      〒812-0016 福岡県福岡市博多区博多駅南２丁目１２−３２<br>
      TEL：092-415-2291<br>
      営業時間：10:00 - 19:00（不定休）
    </p>
  </div>

  <div class="footer-links">
    <a href="favorite-show.php"><i class="bi bi-heart"></i> Favorites</a>
    <a href="cart-show.php"><i class="bi bi-cart"></i> Cart</a>
    <a href="history.php"><i class="bi bi-clock-history"></i> History</a>
  </div>
</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
