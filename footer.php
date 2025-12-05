<footer class="mt-4 py-4 border-top">
  <style>
    footer {
      background: #5A4F48;
      margin: 0;
      padding: 0;
    }

    .footer-container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 30px 20px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 30px;
      background: #5A4F48;
      color: #FFFFFF;
      font-family: 'Noto Serif JP', serif;
    }

    .footer-map {
      flex: 0 0 260px;
    }

    .footer-map iframe {
      width: 100%;
      height: 160px;
      border: none;
      border-radius: 10px;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
    }

    .footer-info {
      flex: 1;
      font-size: 15px;
      line-height: 1.7;
    }

    .footer-info h4 {
      font-size: 22px;
      font-weight: 600;
      border-left: 4px solid #FFCC66;
      padding-left: 10px;
      margin-bottom: 12px;
    }

    .footer-links {
      flex: 0 0 150px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      align-items: flex-end;
    }

    .footer-links a {
      text-decoration: none;
      color: #FFFFFF;
      font-size: 15px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: 0.2s;
    }

    .footer-links a:hover {
      color: #FFCC66;
      transform: translateX(-3px);
    }

    @media (max-width: 768px) {
      .footer-container {
        flex-direction: column;
        text-align: center;
        align-items: center;
        gap: 25px;
      }

      .footer-info h4 {
        text-align: center;
        border-left: none;
        padding-left: 0;
        border-bottom: 2px solid #FFCC66;
        display: inline-block;
        padding-bottom: 6px;
      }

      .footer-links {
        align-items: center;
      }
    }
  </style>

  <div class="footer-container">
    <div class="footer-map">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3323.83962290788!2d130.41859821182652!3d33.58351307322588!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x354191b0a7a68e51%3A0xac6f704b1b5c621a!2z44CSODEyLTAwMTYg56aP5bKh55yM56aP5bKh5biC5Y2a5aSa5Yy65Y2a5aSa6aeF5Y2X77yS5LiB55uu77yR77yS4oiS77yT77yS77yS!5e0!3m2!1sja!2sjp"></iframe>
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
      <a href="favorite-show.php"><i class="bi bi-heart"></i> お気に入り</a>
      <a href="cart-show.php"><i class="bi bi-cart"></i> カート</a>
      <a href="history.php"><i class="bi bi-clock-history"></i> 購入履歴</a>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>