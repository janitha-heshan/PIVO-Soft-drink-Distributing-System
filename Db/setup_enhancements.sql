-- PIVO System Enhancements Setup Script

-- 1. Create inventory_logs table for tracking history
CREATE TABLE IF NOT EXISTS `inventory_logs` (
  `log_id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `change_amount` INT(11) NOT NULL,
  `new_quantity` INT(11) NOT NULL,
  `changed_by_user_id` INT(11) NOT NULL,
  `reason` VARCHAR(255) DEFAULT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `product_id` (`product_id`),
  KEY `changed_by_user_id` (`changed_by_user_id`),
  CONSTRAINT `fk_inv_logs_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inv_logs_user` FOREIGN KEY (`changed_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Add Geolocation columns to shops table for Geofencing
ALTER TABLE `shops`
ADD COLUMN `latitude` DECIMAL(10, 8) NULL AFTER `address`,
ADD COLUMN `longitude` DECIMAL(11, 8) NULL AFTER `latitude`;

-- Set some dummy coordinates around Colombo for testing if shops exist
UPDATE `shops` SET `latitude` = 6.9271, `longitude` = 79.8612 WHERE `latitude` IS NULL;
