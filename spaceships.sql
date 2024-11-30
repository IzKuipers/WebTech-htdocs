SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `errors` (`id`, `title`, `message`) VALUES
(1, 'User not found', 'I don\'t know who you are! Please try entering your login information again.'),
(2, 'Wrong password', 'The password you entered is incorrect. Try entering your credentials again.'),
(3, 'Connection failed', 'Failed to connect to the server! We\'re probably offline. Please try again later.'),
(4, 'Passwords don\'t match', 'The passwords you entered don	 match! Please try again.'),
(5, 'User already exists', 'Sorry! That username is already taken. Choose another. How about \"purple-trex\"?'),
(6, 'Ship not found', 'Sorry! I don\'t know which ship you\'re trying to read about. Maybe try something else?'),
(7, 'User Not Found', 'Sorry! I don\'t know what went wrong, but the user you\'re trying to look up doesn\'t exist. Maybe try again?');

CREATE TABLE IF NOT EXISTS `ships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `authorId` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `image` longblob NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `authorId` (`authorId`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `toast` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caption` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'check_circle',
  `type` varchar(50) NOT NULL DEFAULT 'success',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `toast` (`id`, `caption`, `icon`, `type`) VALUES
(1, 'Account created', 'person_add', 'success'),
(2, 'You\'re logged in', 'login', 'success'),
(3, 'Added spaceship', 'check_circle', 'success'),
(4, 'You\'re logged out', 'logout', 'notify'),
(5, 'Spaceship deleted', 'delete', 'success'),
(6, 'You have to be logged in', 'warning', 'waarschuwing');

CREATE TABLE IF NOT EXISTS `tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `value` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `value` (`value`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `hash` varchar(512) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `ships`
  ADD CONSTRAINT `ships_ibfk_1` FOREIGN KEY (`authorId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
