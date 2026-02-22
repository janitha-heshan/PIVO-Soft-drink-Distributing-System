-- PIVO: Password Reset Ticket System Migration
-- Run this ONCE in phpMyAdmin on the pivo_holdings_db database

-- 1. Add pending flag to users table
ALTER TABLE `users`
  ADD COLUMN `pw_reset_pending` TINYINT(1) NOT NULL DEFAULT 0;

-- 2. Create password reset tickets table
CREATE TABLE IF NOT EXISTS `pw_reset_tickets` (
  `ticket_id`    INT(11)      NOT NULL AUTO_INCREMENT,
  `user_id`      INT(11)      NOT NULL,
  `status`       ENUM('Open','Resolved') NOT NULL DEFAULT 'Open',
  `requested_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at`  TIMESTAMP    NULL DEFAULT NULL,
  PRIMARY KEY (`ticket_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `pw_reset_tickets_ibfk_1`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
