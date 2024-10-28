SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `message` (
  `sn` int(11) NOT NULL AUTO_INCREMENT,
  `userId` varchar(255) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `sender_userid` varchar(255) NOT NULL,
  `receiver_email` varchar(255) NOT NULL,
  `receiver_userid` varchar(255) NOT NULL,
  `message` text,
  `file_path` varchar(255),
  `file_type` varchar(50),
  `is_read` tinyint(1) DEFAULT 0, -- 0 for unread, 1 for read
  `read_status` tinyint(1) DEFAULT 0,
  `chat_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user` (
  `sn` int(11) NOT NULL AUTO_INCREMENT,
  `userId` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `logout_time` TIMESTAMP NULL DEFAULT NULL, -- Track when user logs out
  `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sn`),
  UNIQUE KEY (`userId`) -- Adding unique constraint for `userId`
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Creating notifications table with foreign key reference to `user.userId`
CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` varchar(255) NOT NULL,
  `unread_messages` int(11) DEFAULT 0, -- Number of unread messages
  PRIMARY KEY (`notification_id`),
  INDEX (`userId`), -- Adding index on `userId` in notifications table
  FOREIGN KEY (`userId`) REFERENCES `user`(`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELIMITER $$

-- Trigger to set status to 1 before updating login_time
CREATE TRIGGER `user_login_trigger`
BEFORE UPDATE ON `user`
FOR EACH ROW
BEGIN
    IF NEW.login_time IS NOT NULL AND NEW.login_time <> OLD.login_time THEN
        SET NEW.status = 1;
    END IF;
END$$

-- Trigger to set status to 0 before updating logout_time
CREATE TRIGGER `user_logout_trigger`
BEFORE UPDATE ON `user`
FOR EACH ROW
BEGIN
    IF NEW.logout_time IS NOT NULL AND NEW.logout_time <> OLD.logout_time THEN
        SET NEW.status = 0;
    END IF;
END$$

DELIMITER ;


COMMIT;
