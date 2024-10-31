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
  `is_read` tinyint(1) DEFAULT 0,
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
  `logout_time` TIMESTAMP NULL DEFAULT NULL, 
  `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sn`),
  UNIQUE KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Dumping data for table `user`
--

INSERT INTO `user` (`sn`, `userId`, `first_name`, `last_name`, `email`, `password`, `status`, `login_time`, `logout_time`, `last_activity`) VALUES
(1, 'nWjKdz_1', 'Sam', 'David', 'samdavid2468@gmail.com', '$2y$10$uzukkBhXZRZMuAJ/eGGhseEpqM0AY6nGJrF89nNjRu65hJiVw0rhW', 1, '0000-00-00 00:00:00', NULL, '2024-10-28 07:21:17'),
(2, 'lGTh32_2', 'Vishal', 'Arora', 'vishalarora2468@gmail.com', '$2y$10$RhTuucoyjwxvqbco1lZDzegVWM5fhIbaapD6v7uECujmpyjuzvTs2', 1, '0000-00-00 00:00:00', NULL, '2024-10-28 07:22:27'),
(3, '0ak8cY_3', 'Deepika', 'Singh', 'deepikasingh1359@gmail.com', '$2y$10$iXNdXHqCH.JYgUoAjmudku3iOAwUcCPG9Ttx.1I/cwWxd12xfoGVu', 1, '0000-00-00 00:00:00', NULL, '2024-10-28 07:23:11'),
(4, '0Yuoe0_4', 'Eliza', 'Sans', 'elizasans1359@gmial.com', '$2y$10$DR0gI54341gDDTUvD8T/Ouyw9FB3JvglKem06CcI4bSvVs8zAe6r6', 1, '0000-00-00 00:00:00', NULL, '2024-10-28 07:23:59'),
(5, 'iMgYI4_5', 'Eliana', 'Ford', 'elinaford13579@gmail.com', '$2y$10$8YX9rsf.QiYwBzV6HoGmHOPj/oJ9irj3aivXxgXZqjIwTJtG6K.9a', 1, '0000-00-00 00:00:00', NULL, '2024-10-28 07:25:43');



CREATE TABLE `notifications` (
    `notification_id` INT(11) NOT NULL AUTO_INCREMENT,
    `sender_userid` VARCHAR(255) NOT NULL,
    `receiver_userid` VARCHAR(255) NOT NULL,
    `unread_messages` INT(11) DEFAULT 0,
    PRIMARY KEY (`notification_id`),
    UNIQUE INDEX `sender_receiver_unique` (`sender_userid`, `receiver_userid`),
    FOREIGN KEY (`sender_userid`) REFERENCES `user`(`userId`) ON DELETE CASCADE,
    FOREIGN KEY (`receiver_userid`) REFERENCES `user`(`userId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DELIMITER $$


-- CREATE TRIGGER `user_login_trigger`
-- BEFORE UPDATE ON `user`
-- FOR EACH ROW
-- BEGIN
--     IF NEW.login_time IS NOT NULL AND NEW.login_time <> OLD.login_time THEN
--         SET NEW.status = 1;
--     END IF;
-- END$$


-- CREATE TRIGGER `user_logout_trigger`
-- BEFORE UPDATE ON `user`
-- FOR EACH ROW
-- BEGIN
--     IF NEW.logout_time IS NOT NULL AND NEW.logout_time <> OLD.logout_time THEN
--         SET NEW.status = 0;
--     END IF;
-- END$$

CREATE TRIGGER `update_unread_messages`
AFTER INSERT ON `message`
FOR EACH ROW
BEGIN
    DECLARE unread_count INT DEFAULT 0;

    -- Check if a record already exists in notifications
    SELECT `unread_messages` INTO unread_count
    FROM `notifications`
    WHERE `sender_userid` = NEW.sender_userid AND `receiver_userid` = NEW.receiver_userid;

    IF unread_count IS NULL THEN
        -- Insert a new record if it does not exist
        INSERT INTO `notifications` (sender_userid, receiver_userid, unread_messages)
        VALUES (NEW.sender_userid, NEW.receiver_userid, 1);
    ELSE
        -- Update the unread count if a record already exists
        UPDATE `notifications`
        SET unread_messages = unread_messages + 1
        WHERE sender_userid = NEW.sender_userid AND receiver_userid = NEW.receiver_userid;
    END IF;
END$$

DELIMITER ;


COMMIT;
