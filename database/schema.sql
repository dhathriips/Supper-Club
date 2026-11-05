CREATE DATABASE IF NOT EXISTS supper_club CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE supper_club;
CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(60) NOT NULL, email VARCHAR(120) NOT NULL UNIQUE,
 student_code CHAR(7) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL,
 role ENUM('customer','admin') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB;
CREATE TABLE restaurants (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80) NOT NULL, cuisine VARCHAR(40) NOT NULL,
 area VARCHAR(60) NOT NULL, rating DECIMAL(2,1) NOT NULL, minutes SMALLINT UNSIGNED NOT NULL,
 art VARCHAR(12) NOT NULL, tagline VARCHAR(160) NOT NULL
) ENGINE=InnoDB;
CREATE TABLE menu_items (
 id INT AUTO_INCREMENT PRIMARY KEY, restaurant_id INT NOT NULL, name VARCHAR(80) NOT NULL,
 description VARCHAR(160) NOT NULL, price INT UNSIGNED NOT NULL, veg TINYINT(1) NOT NULL,
 available TINYINT(1) NOT NULL DEFAULT 1,
 FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE orders (
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, restaurant_id INT NULL,
 restaurant_name VARCHAR(80) NOT NULL, total INT UNSIGNED NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id), FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE order_items (
 id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, menu_item_id INT NULL,
 item_name VARCHAR(80) NOT NULL, price INT UNSIGNED NOT NULL, qty TINYINT UNSIGNED NOT NULL,
 FOREIGN KEY (order_id) REFERENCES orders(id), FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE SET NULL
) ENGINE=InnoDB;
INSERT INTO restaurants(id,name,cuisine,area,rating,minutes,art,tagline) VALUES
(1, 'Ghee & Grain', 'South Indian', 'Basavanagudi', 4.8, 20, 'dosa', 'Golden dosas straight off the iron tawa.'),
(2, 'Slice Society', 'Italian', 'Indiranagar', 4.7, 35, 'pizza', 'Blistered crusts from a neighbourhood oven.'),
(3, 'Dum Diaries', 'Biryani', 'Frazer Town', 4.6, 30, 'rice', 'Fragrant rice and slow-cooked spice.'),
(4, 'Noodle Radio', 'Pan Asian', 'Koramangala', 4.5, 25, 'noodles', 'Big bowls of wok-tossed favourites.'),
(5, 'Stack House', 'Burgers', 'HSR Layout', 4.4, 30, 'burger', 'Griddled patties and soft toasted buns.'),
(6, 'Little Kulfi Co.', 'Desserts', 'Jayanagar', 4.7, 15, 'kulfi', 'Small-batch treats for a sweet finish.');
INSERT INTO menu_items(restaurant_id,name,description,price,veg) VALUES
(1, 'Benne dosa', 'Crisp rice crêpe with cultured butter and chutney.', 95, 1),
(1, 'Thatte idli', 'Soft plate idli with lentil sambar.', 70, 1),
(1, 'Filter coffee', 'Slow-dripped coffee with hot milk.', 45, 1),
(2, 'Basil margherita', 'Tomato, basil and fresh mozzarella.', 285, 1),
(2, 'Pepper chicken pizza', 'Roasted chicken with cracked pepper.', 345, 0),
(2, 'Garlic knots', 'Warm dough knots with parsley butter.', 120, 1),
(3, 'Chicken dum biryani', 'Long-grain rice with spiced chicken.', 240, 0),
(3, 'Kathal biryani', 'Tender jackfruit with saffron rice.', 195, 1),
(3, 'Cucumber raita', 'Cool yoghurt with cucumber.', 55, 1),
(4, 'Chilli garlic noodles', 'Spring onion and toasted garlic.', 180, 1),
(4, 'Chicken sesame noodles', 'Wok-fried chicken with sesame.', 220, 0),
(4, 'Crispy corn', 'Sweet corn tossed with chilli salt.', 110, 1),
(5, 'Garden stack', 'Beetroot patty with pickle relish.', 165, 1),
(5, 'Chicken classic', 'Crispy chicken with house slaw.', 195, 0),
(5, 'Paprika fries', 'Skin-on fries with smoky paprika.', 90, 1),
(6, 'Pistachio kulfi', 'Slow-set milk with crushed pistachios.', 85, 1),
(6, 'Mango kulfi', 'Alphonso mango and cream.', 95, 1),
(6, 'Rose falooda', 'Rose milk with basil seeds.', 125, 1);

