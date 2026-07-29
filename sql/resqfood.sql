-- ResQFood database schema

CREATE DATABASE IF NOT EXISTS resqfood;
USE resqfood;

-- ---------------------------------------------------------
-- Users: donors post food, NGOs claim it, admin oversees it
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  user_id      INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(100) NOT NULL,
  email        VARCHAR(100) NOT NULL UNIQUE,
  password     VARCHAR(255) NOT NULL,
  role         ENUM('donor','ngo','admin') NOT NULL,
  phone        VARCHAR(20)  DEFAULT NULL,
  organization VARCHAR(150) DEFAULT NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Food listings posted by donors
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS food_listings (
  listing_id     INT AUTO_INCREMENT PRIMARY KEY,
  donor_id       INT NOT NULL,
  food_name      VARCHAR(150) NOT NULL,
  food_type      ENUM('cooked','raw','packaged','bakery','other') NOT NULL DEFAULT 'other',
  quantity       DECIMAL(6,2) NOT NULL,
  unit           VARCHAR(20) NOT NULL DEFAULT 'kg',
  pickup_location VARCHAR(255) NOT NULL,
  expiry_time    DATETIME NOT NULL,
  description    TEXT,
  status         ENUM('available','claimed','completed','expired') NOT NULL DEFAULT 'available',
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (donor_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Claims made by NGOs on a listing
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS claims (
  claim_id   INT AUTO_INCREMENT PRIMARY KEY,
  listing_id INT NOT NULL,
  ngo_id     INT NOT NULL,
  claimed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status     ENUM('pending','completed') NOT NULL DEFAULT 'pending',
  FOREIGN KEY (listing_id) REFERENCES food_listings(listing_id) ON DELETE CASCADE,
  FOREIGN KEY (ngo_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Sample data (all sample accounts use the password: password123)
-- ---------------------------------------------------------
INSERT INTO users (name, email, password, role, phone, organization) VALUES
('Admin', 'admin@resqfood.test', '$2y$10$bl6Wkhllw5AFj7ECJDhtreuDlDWxFifexkQ3wk5I4czzm.7eHSw7i', 'admin', NULL, NULL),
('Karim Hotel', 'karim@resqfood.test', '$2y$10$bl6Wkhllw5AFj7ECJDhtreuDlDWxFifexkQ3wk5I4czzm.7eHSw7i', 'donor', '01710000001', 'Karim Hotel & Restaurant'),
('Farhana Catering', 'farhana@resqfood.test', '$2y$10$bl6Wkhllw5AFj7ECJDhtreuDlDWxFifexkQ3wk5I4czzm.7eHSw7i', 'donor', '01710000002', 'Farhana Catering Services'),
('Ashar Alo Foundation', 'asharalo@resqfood.test', '$2y$10$bl6Wkhllw5AFj7ECJDhtreuDlDWxFifexkQ3wk5I4czzm.7eHSw7i', 'ngo', '01810000001', 'Ashar Alo Foundation'),
('Community Kitchen Mymensingh', 'commkitchen@resqfood.test', '$2y$10$bl6Wkhllw5AFj7ECJDhtreuDlDWxFifexkQ3wk5I4czzm.7eHSw7i', 'ngo', '01810000002', 'Community Kitchen Mymensingh');

INSERT INTO food_listings (donor_id, food_name, food_type, quantity, unit, pickup_location, expiry_time, description, status) VALUES
(2, 'Leftover Biryani Trays', 'cooked', 15.00, 'kg', 'Karim Hotel, Zero Point, Mymensingh', DATE_ADD(NOW(), INTERVAL 2 HOUR), 'From a corporate lunch event, kept in warmers.', 'available'),
(2, 'Bread & Bakery Surplus', 'bakery', 8.00, 'kg', 'Karim Hotel, Zero Point, Mymensingh', DATE_ADD(NOW(), INTERVAL 20 HOUR), 'Unsold bread and buns from today.', 'available'),
(3, 'Wedding Function Surplus Rice & Curry', 'cooked', 25.00, 'kg', 'Community Center, Mymensingh', DATE_ADD(NOW(), INTERVAL 5 HOUR), 'Large batch from a wedding, sealed containers.', 'available'),
(3, 'Packaged Snack Boxes', 'packaged', 40.00, 'pieces', 'Farhana Catering Office, Mymensingh', DATE_ADD(NOW(), INTERVAL 2 DAY), 'Unopened, still within shelf life.', 'available');
