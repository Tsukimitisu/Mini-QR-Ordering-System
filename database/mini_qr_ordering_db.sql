-- Create Database
CREATE DATABASE IF NOT EXISTS `mini_qr_ordering_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mini_qr_ordering_db`;

-- 1. Products Table
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `availability_status` TINYINT(1) DEFAULT 1 COMMENT '1 = Available, 0 = Out of Stock',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Orders Table
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(255) NOT NULL,
  `table_number` INT NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `order_status` VARCHAR(50) DEFAULT 'pending' COMMENT 'pending, preparing, completed, cancelled',
  `payment_status` VARCHAR(50) DEFAULT 'unpaid' COMMENT 'unpaid, paid, failed',
  `payment_result` VARCHAR(50) DEFAULT NULL COMMENT 'success, failed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Order Items Table
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tables Table (Optional but supporting)
CREATE TABLE IF NOT EXISTS `tables` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `table_number` INT UNIQUE NOT NULL,
  `qr_code_path` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(50) DEFAULT 'active' COMMENT 'active, inactive',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Sample Products
INSERT INTO `products` (`id`, `product_name`, `description`, `price`, `image`, `category`, `availability_status`) VALUES
(1, 'Classic Cheeseburger', 'A juicy flame-grilled beef patty, melted cheddar cheese, crisp lettuce, fresh tomatoes, pickles, and our signature burger sauce on a toasted brioche bun.', 5.99, 'cheeseburger.png', 'Burgers', 1),
(2, 'Pepperoni Pizza', 'Freshly baked hand-tossed crust topped with rich marinara sauce, premium mozzarella cheese, and generous slices of spicy pepperoni.', 8.99, 'pizza.png', 'Pizzas', 1),
(3, 'Crispy French Fries', 'Golden-brown, double-fried potato fries lightly seasoned with sea salt. Served hot with a side of ketchup.', 2.99, 'fries.png', 'Sides', 1),
(4, 'Sweet Iced Tea', 'Brewed black tea infused with natural lemon juice and sweetened to perfection. Served cold over ice.', 1.99, 'iced_tea.png', 'Drinks', 1),
(5, 'Chocolate Milkshake', 'Creamy and rich vanilla ice cream blended with premium chocolate syrup, topped with fresh whipped cream and chocolate shavings.', 3.49, 'milkshake.png', 'Drinks', 0);

-- Seed Tables
INSERT INTO `tables` (`table_number`, `status`) VALUES
(1, 'active'),
(2, 'active'),
(3, 'active'),
(4, 'active'),
(5, 'active');
