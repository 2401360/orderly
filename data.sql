-- ===== 0) データベース作成 =====
CREATE DATABASE IF NOT EXISTS cake_shop
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_0900_ai_ci;
USE cake_shop;

-- セーフ再作成のため依存順に削除
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS purchase_detail;
DROP TABLE IF EXISTS purchase;
DROP TABLE IF EXISTS favorite;
DROP TABLE IF EXISTS review;
DROP TABLE IF EXISTS product;
DROP TABLE IF EXISTS customer;
SET FOREIGN_KEY_CHECKS = 1;

-- ===== 1) product =====
CREATE TABLE product (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL,
  price       INT NOT NULL,
  image_url   VARCHAR(255),
  description TEXT,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== 2) customer =====
CREATE TABLE customer (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  name     VARCHAR(100) NOT NULL,
  address  VARCHAR(200) NOT NULL,
  login    VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== 3) purchase（親：customer）=====
CREATE TABLE purchase (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_purchase_customer
    FOREIGN KEY (customer_id) REFERENCES customer(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== 4) purchase_detail（親：purchase, product）=====
CREATE TABLE purchase_detail (
  purchase_id INT NOT NULL,
  product_id  INT NOT NULL,
  count       INT NOT NULL,
  PRIMARY KEY (purchase_id, product_id),
  CONSTRAINT fk_pd_purchase
    FOREIGN KEY (purchase_id) REFERENCES purchase(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_pd_product
    FOREIGN KEY (product_id)  REFERENCES product(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== 5) favorite（複合PK）=====
CREATE TABLE favorite (
  customer_id INT NOT NULL,
  product_id  INT NOT NULL,
  PRIMARY KEY (customer_id, product_id),
  CONSTRAINT fk_fav_customer
    FOREIGN KEY (customer_id) REFERENCES customer(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_fav_product
    FOREIGN KEY (product_id)  REFERENCES product(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== 6) review（任意：1〜5）=====
CREATE TABLE review (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  product_id  INT NOT NULL,
  customer_id INT NOT NULL,
  rating      TINYINT NOT NULL,
  comment     TEXT,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_review_rating CHECK (rating BETWEEN 1 AND 5),
  CONSTRAINT fk_rev_product  FOREIGN KEY (product_id)  REFERENCES product(id)  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_rev_customer FOREIGN KEY (customer_id) REFERENCES customer(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== 7) 初期データ：商品 =====
INSERT INTO product (name, price, image_url, description) VALUES
('ミルキーミルクレープ', 380, 'https://hacca-aso2401360.ssl-lolipop.jp/2025/php2/orderly/image/milky-mille-crepe.jpg', '濃厚ミルククリームを幾重にも重ねた、とろける口どけのミルクレープ。'),
('ミルクレープ',         220, 'https://hacca-aso2401360.ssl-lolipop.jp/2025/php2/orderly/image/mille-crepe.jpg',       'クレープ生地と生クリームを重ねた定番ケーキ。軽やかな食感。'),
('チョコケーキ',         280, 'https://hacca-aso2401360.ssl-lolipop.jp/2025/php2/orderly/image/choco-cake.jpg',        'カカオの香り豊かなしっとりチョコレートケーキ。'),
('チーズケーキ',         200, 'https://hacca-aso2401360.ssl-lolipop.jp/2025/php2/orderly/image/cheese-cake.jpg',       'クリームチーズのコクを生かした、なめらかなベイクドタイプ。'),
('プリンタルト',         250, 'https://hacca-aso2401360.ssl-lolipop.jp/2025/php2/orderly/image/pudding-tart.jpg',      'さくさくタルトに濃厚プリンをのせた満足スイーツ。'),
('京わらびもち',         280, 'https://hacca-aso2401360.ssl-lolipop.jp/2025/php2/orderly/image/warabi-mochi.jpg',      '京風の本わらび粉を使用。ぷるんとした食感と上品な甘さ。'),
('黒ごま団子',           120, 'https://hacca-aso2401360.ssl-lolipop.jp/2025/php2/orderly/image/black-sesame-dango.jpg','香ばしい黒ごま餡をもちもち生地で包みました。'),
('和栗のパウンドケーキ', 420, 'https://hacca-aso2401360.ssl-lolipop.jp/2025/php2/orderly/image/waguri-pound.jpg',      '国産和栗を贅沢に練り込んだ、しっとりパウンドケーキ。');

-- ===== 8) 初期データ（任意）：テスト顧客 ※パスワードは "pass1" 等のハッシュ例に置換してください =====
-- 例）$2y$10$... はダミーです。実運用は PHP の password_hash() で生成した値に差し替え推奨。
INSERT INTO customer (name, address, login, password) VALUES
('Taro',   'Fukuoka 1-1-1', 'taro',   '$2y$10$exampleexampleexampleexampleexa'),
('Hanako', 'Fukuoka 2-2-2', 'hanako', '$2y$10$exampleexampleexampleexampleexa');

-- ===== 9) サンプルお気に入り／レビュー（任意）=====
INSERT INTO favorite VALUES (1, 1), (1, 4);

INSERT INTO review (product_id, customer_id, rating, comment) VALUES
(1, 1, 5, 'ミルキーミルクレープが最高でした！また買います。'),
(4, 2, 4, 'チーズのコクがしっかり。紅茶と合います。');
