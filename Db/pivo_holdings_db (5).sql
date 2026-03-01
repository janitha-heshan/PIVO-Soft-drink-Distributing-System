-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2026 at 12:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pivo_holdings_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_configurations`
--

CREATE TABLE `api_configurations` (
  `config_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `api_configurations`
--

INSERT INTO `api_configurations` (`config_id`, `service_name`, `api_key`, `is_active`, `updated_at`) VALUES
(1, 'google_maps', '', 1, '2026-02-06 15:58:46');

-- --------------------------------------------------------

--
-- Table structure for table `area_assignments`
--

CREATE TABLE `area_assignments` (
  `assignment_id` int(11) NOT NULL,
  `sales_rep_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geofenced_areas`
--

CREATE TABLE `geofenced_areas` (
  `area_id` int(11) NOT NULL,
  `area_name` varchar(100) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `boundary_polygon` geometry NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity_in_stock` int(11) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `product_id`, `quantity_in_stock`, `last_updated`) VALUES
(1, 7, 300, '2026-02-22 10:30:35'),
(2, 8, 5, '2026-01-18 15:00:56'),
(3, 5, 10, '2026-01-18 15:27:45'),
(4, 2, 888, '2026-02-22 11:07:52'),
(5, 3, 300, '2026-02-22 10:34:25'),
(7, 11, 86, '2026-02-22 10:49:00');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `log_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `change_amount` int(11) NOT NULL,
  `new_quantity` int(11) NOT NULL,
  `changed_by_user_id` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`log_id`, `product_id`, `change_amount`, `new_quantity`, `changed_by_user_id`, `reason`, `timestamp`) VALUES
(1, 3, 400, 500, 10, 'Manual Update', '2026-01-22 10:30:23'),
(2, 7, 294, 300, 10, 'Manual Update', '2026-02-22 10:30:35'),
(3, 3, -200, 300, 10, 'Manual Update', '2026-02-22 10:34:25'),
(4, 2, -2, 888, 10, 'Order #26 Confirmed', '2026-02-22 11:07:52');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `sales_rep_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_status` enum('Pending','Preparing','Dispatched','Delivered','Cancelled') DEFAULT 'Pending',
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `confirmed_lat` decimal(10,8) DEFAULT NULL,
  `confirmed_lng` decimal(11,8) DEFAULT NULL,
  `status_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_critical` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `shop_id`, `sales_rep_id`, `order_date`, `delivery_status`, `total_amount`, `confirmed_lat`, `confirmed_lng`, `status_updated_at`, `is_critical`) VALUES
(1, 1, NULL, '2025-09-04 18:30:00', 'Delivered', 15000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(2, 2, NULL, '2025-09-11 18:30:00', 'Delivered', 12000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(3, 3, NULL, '2025-09-17 18:30:00', 'Delivered', 18000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(4, 1, NULL, '2025-09-23 18:30:00', 'Delivered', 14000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(5, 2, NULL, '2025-09-27 18:30:00', 'Delivered', 16000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(6, 1, NULL, '2025-10-03 18:30:00', 'Delivered', 22000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(7, 2, NULL, '2025-10-09 18:30:00', 'Delivered', 25000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(8, 3, NULL, '2025-10-15 18:30:00', 'Delivered', 21000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(9, 1, NULL, '2025-10-22 18:30:00', 'Delivered', 28000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(10, 2, NULL, '2025-10-29 18:30:00', 'Delivered', 24000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(11, 1, NULL, '2025-11-02 18:30:00', 'Delivered', 35000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(12, 2, NULL, '2025-11-08 18:30:00', 'Delivered', 42000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(13, 3, NULL, '2025-11-14 18:30:00', 'Delivered', 38000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(14, 1, NULL, '2025-11-20 18:30:00', 'Delivered', 45000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(15, 2, NULL, '2025-11-27 18:30:00', 'Delivered', 50000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(16, 1, NULL, '2025-12-01 18:30:00', 'Delivered', 60000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(17, 2, NULL, '2025-12-06 18:30:00', 'Delivered', 55000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(18, 3, NULL, '2025-12-13 18:30:00', 'Delivered', 62000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(19, 1, NULL, '2025-12-20 18:30:00', 'Delivered', 58000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(20, 2, NULL, '2025-12-28 18:30:00', 'Delivered', 61000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(21, 1, NULL, '2026-01-02 18:30:00', 'Delivered', 45000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(22, 2, NULL, '2026-01-06 18:30:00', 'Delivered', 40000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(23, 3, NULL, '2026-01-10 18:30:00', 'Delivered', 48000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(24, 1, NULL, '2026-01-14 18:30:00', 'Delivered', 42000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(25, 2, NULL, '2026-01-16 18:30:00', 'Delivered', 46000.00, NULL, NULL, '2026-02-06 14:46:47', 0),
(26, 4, NULL, '2026-02-06 16:04:30', 'Preparing', 500.00, NULL, NULL, '2026-02-22 11:07:52', 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_order` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price_at_order`) VALUES
(1, 1, 1, 10, 450.00),
(2, 6, 1, 30, 450.00),
(3, 11, 1, 60, 450.00),
(4, 16, 1, 90, 450.00),
(5, 21, 1, 120, 450.00),
(6, 2, 6, 100, 180.00),
(7, 7, 6, 70, 180.00),
(8, 12, 6, 40, 180.00),
(9, 17, 6, 20, 180.00),
(10, 22, 6, 5, 180.00),
(11, 3, 8, 80, 220.00),
(12, 8, 8, 60, 220.00),
(13, 13, 8, 30, 220.00),
(14, 18, 8, 15, 220.00),
(15, 23, 8, 2, 220.00),
(16, 4, 3, 20, 480.00),
(17, 9, 3, 40, 480.00),
(18, 14, 3, 60, 480.00),
(19, 19, 3, 100, 480.00),
(20, 24, 3, 80, 480.00),
(21, 26, 2, 2, 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_tracking_logs`
--

CREATE TABLE `order_tracking_logs` (
  `log_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_tracking_logs`
--

INSERT INTO `order_tracking_logs` (`log_id`, `order_id`, `status`, `changed_by`, `timestamp`) VALUES
(1, 26, 'Pending', 8, '2026-02-06 16:04:30'),
(2, 26, 'Preparing', 10, '2026-02-22 11:07:52');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','Cheque','Credit') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_date`, `amount_paid`, `payment_method`) VALUES
(1, 26, '2026-02-22 11:07:53', 500.00, 'Credit');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` enum('Mix Fruit','Mango','Wood Apple','Aloe Vera','Helo') NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `volume_ml` varchar(50) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `size_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `image_path`, `volume_ml`, `unit_price`, `description`, `size_id`) VALUES
(1, 'Mango', NULL, 'Large', 450.00, 'Organic Fresh Mango Juice - 1L', 1),
(2, 'Mango', NULL, 'Medium', 250.00, 'Organic Fresh Mango Juice - 500ml', 3),
(3, 'Mix Fruit', NULL, 'Large', 480.00, 'Organic Tropical Mix Fruit - 1L', 1),
(4, 'Mix Fruit', NULL, 'Small', 150.00, 'Organic Tropical Mix Fruit - 250ml', 2),
(5, 'Aloe Vera', NULL, 'Medium', 300.00, 'Natural Aloe Vera Refresh - 500ml', 3),
(6, 'Aloe Vera', NULL, 'Small', 180.00, 'Natural Aloe Vera Refresh - 250ml', 2),
(7, 'Wood Apple', NULL, 'Large', 420.00, 'Traditional Organic Wood Apple - 1L', 1),
(8, 'Wood Apple', NULL, 'Medium', 220.00, 'Traditional Organic Wood Apple - 500ml', 3),
(11, 'Mix Fruit', 'assets/images/products/1771756850_Gemini_Generated_Image_6tnx716tnx716tnx (1).png', '300ml', 170.00, '', 4);

-- --------------------------------------------------------

--
-- Table structure for table `product_returns`
--

CREATE TABLE `product_returns` (
  `return_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `return_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Collected','Stored') DEFAULT 'Collected'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pw_reset_tickets`
--

CREATE TABLE `pw_reset_tickets` (
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('Open','Resolved') NOT NULL DEFAULT 'Open',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pw_reset_tickets`
--

INSERT INTO `pw_reset_tickets` (`ticket_id`, `user_id`, `status`, `requested_at`, `resolved_at`) VALUES
(1, 14, 'Resolved', '2026-02-22 10:01:31', '2026-02-22 10:02:21');

-- --------------------------------------------------------

--
-- Table structure for table `sales_predictions`
--

CREATE TABLE `sales_predictions` (
  `prediction_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `predicted_demand` int(11) DEFAULT NULL,
  `prediction_date` date DEFAULT NULL,
  `confidence_score` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_predictions`
--

INSERT INTO `sales_predictions` (`prediction_id`, `product_id`, `predicted_demand`, `prediction_date`, `confidence_score`) VALUES
(361, 1, 147, '2026-02-01', NULL),
(362, 1, 174, '2026-03-01', NULL),
(363, 1, 201, '2026-04-01', NULL),
(364, 3, 106, '2026-02-01', NULL),
(365, 3, 132, '2026-03-01', NULL),
(366, 3, 158, '2026-04-01', NULL),
(367, 6, 0, '2026-02-01', NULL),
(368, 6, 0, '2026-03-01', NULL),
(369, 6, 0, '2026-04-01', NULL),
(370, 8, 0, '2026-02-01', NULL),
(371, 8, 0, '2026-03-01', NULL),
(372, 8, 0, '2026-04-01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `shop_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `shop_name` varchar(100) NOT NULL,
  `owner_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `assigned_sales_rep_id` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`shop_id`, `owner_id`, `shop_name`, `owner_name`, `address`, `contact_number`, `assigned_sales_rep_id`, `latitude`, `longitude`) VALUES
(1, 0, 'City Supermarket', 'Mr. Perera', '123 Main St, Colombo', NULL, NULL, 6.92710000, 79.86120000),
(2, 0, 'Nature\'s Basket', 'Ms. Silva', '45 Orchid Lane, Kandy', NULL, NULL, 6.92710000, 79.86120000),
(3, 0, 'Organic Life Hub', 'Mr. Fernando', '88 Green Road, Galle', NULL, NULL, 6.92710000, 79.86120000),
(4, 8, 'dqwfes', NULL, 'fghj', '0717627634', NULL, 6.92710000, 79.86120000);

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `size_id` int(11) NOT NULL,
  `volume_ml` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`size_id`, `volume_ml`) VALUES
(4, '300ml'),
(1, 'Large'),
(3, 'Medium'),
(2, 'Small');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('Admin','SalesRep','SalesSupervisor','StoreManager','ITSupport','FactoryOwner','ShopOwner') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pw_reset_pending` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `full_name`, `role`, `email`, `contact_number`, `created_at`, `pw_reset_pending`) VALUES
(8, 'shop2', '$2y$10$eR4vjlZO4pUO22BZnjxqK.8nJk/4lTAtuvdHcUgv/Jk8pZbmcE/2u', NULL, 'ShopOwner', 'shop@shop.com', NULL, '2026-02-06 16:03:19', 0),
(9, 'shop1', '$2y$10$yQyKqtNy2MXzZQn8VS0/TOFAEAjy5ePyLu7kwdV2Q4vMXb64/DDNK', 'Primary Shop Owner', 'ShopOwner', 'shop1@pivo.lk', NULL, '2026-02-06 17:09:18', 0),
(10, 'manager', '$2y$10$Ux4xI1oxNIZ6/6Hgm1zJtOJnARjA9BnMIiUTea5g3te5AineBFLmS', 'Regional Store Manager', 'StoreManager', 'manager@pivo.lk', NULL, '2026-02-06 17:09:18', 0),
(11, 'driver', '$2y$10$QymfgJKQJlz4.O0XO4lxeuXICPbmCb5eZ4mI8LR9KvNe4kgBbn5Wm', 'Lead Sales Rep', 'SalesRep', 'driver@pivo.lk', NULL, '2026-02-06 17:09:18', 0),
(12, 'admin', '$2y$10$./mBhWGyzX2KxGuuZKMfJOKfwWUkEr7j7RzgmC6xKA3AyYRJqVgX2', 'System Administrator', 'Admin', 'admin@pivo.lk', NULL, '2026-02-06 17:09:18', 0),
(13, 'factory', '$2y$10$dtvzIWO3ttRKDhZ5XErSEe.f5/.lAYGSgISdJysFAgGi6dIhBhBPu', 'Factory Proprietor', 'FactoryOwner', 'owner@pivo.lk', NULL, '2026-02-06 17:09:18', 0),
(14, 'shop', '$2y$10$rPbgaE.Bv17wIIrAL.Uf1ONPveGl80mF7L87bc3mvE1bXKaF3UmtC', NULL, 'ShopOwner', 'shop@shop.com', NULL, '2026-02-06 17:16:24', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_configurations`
--
ALTER TABLE `api_configurations`
  ADD PRIMARY KEY (`config_id`),
  ADD UNIQUE KEY `service_name` (`service_name`);

--
-- Indexes for table `area_assignments`
--
ALTER TABLE `area_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `sales_rep_id` (`sales_rep_id`),
  ADD KEY `area_id` (`area_id`);

--
-- Indexes for table `geofenced_areas`
--
ALTER TABLE `geofenced_areas`
  ADD PRIMARY KEY (`area_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `changed_by_user_id` (`changed_by_user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `sales_rep_id` (`sales_rep_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_tracking_logs`
--
ALTER TABLE `order_tracking_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `unique_product_size` (`product_name`,`volume_ml`),
  ADD KEY `fk_product_size` (`size_id`);

--
-- Indexes for table `product_returns`
--
ALTER TABLE `product_returns`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `pw_reset_tickets`
--
ALTER TABLE `pw_reset_tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sales_predictions`
--
ALTER TABLE `sales_predictions`
  ADD PRIMARY KEY (`prediction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`shop_id`),
  ADD KEY `assigned_sales_rep_id` (`assigned_sales_rep_id`),
  ADD KEY `fk_shop_owner` (`owner_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`),
  ADD UNIQUE KEY `volume_ml` (`volume_ml`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_configurations`
--
ALTER TABLE `api_configurations`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `area_assignments`
--
ALTER TABLE `area_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geofenced_areas`
--
ALTER TABLE `geofenced_areas`
  MODIFY `area_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `order_tracking_logs`
--
ALTER TABLE `order_tracking_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_returns`
--
ALTER TABLE `product_returns`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pw_reset_tickets`
--
ALTER TABLE `pw_reset_tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_predictions`
--
ALTER TABLE `sales_predictions`
  MODIFY `prediction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=373;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `shop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `area_assignments`
--
ALTER TABLE `area_assignments`
  ADD CONSTRAINT `area_assignments_ibfk_1` FOREIGN KEY (`sales_rep_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `area_assignments_ibfk_2` FOREIGN KEY (`area_id`) REFERENCES `geofenced_areas` (`area_id`);

--
-- Constraints for table `geofenced_areas`
--
ALTER TABLE `geofenced_areas`
  ADD CONSTRAINT `geofenced_areas_ibfk_1` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `inventory_logs_ibfk_2` FOREIGN KEY (`changed_by_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`sales_rep_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `order_tracking_logs`
--
ALTER TABLE `order_tracking_logs`
  ADD CONSTRAINT `order_tracking_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_tracking_logs_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_size` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`);

--
-- Constraints for table `product_returns`
--
ALTER TABLE `product_returns`
  ADD CONSTRAINT `product_returns_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `product_returns_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `pw_reset_tickets`
--
ALTER TABLE `pw_reset_tickets`
  ADD CONSTRAINT `pw_reset_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_predictions`
--
ALTER TABLE `sales_predictions`
  ADD CONSTRAINT `sales_predictions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `shops`
--
ALTER TABLE `shops`
  ADD CONSTRAINT `fk_shop_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `shops_ibfk_1` FOREIGN KEY (`assigned_sales_rep_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
