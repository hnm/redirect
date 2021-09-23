-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.1.26-MariaDB - mariadb.org binary distribution
-- Server Betriebssystem:        Win32
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportiere Struktur von Tabelle gefahrgutshop.redirect_log_entry
CREATE TABLE IF NOT EXISTS `redirect_log_entry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_url_str` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=155 DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle gefahrgutshop.redirect_log_entry_not_found
CREATE TABLE IF NOT EXISTS `redirect_log_entry_not_found` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url_str` varchar(512) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `last_occurence` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle gefahrgutshop.redirect_rule
CREATE TABLE IF NOT EXISTS `redirect_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host_pattern` varchar(512) DEFAULT NULL,
  `path_pattern` varchar(512) DEFAULT NULL,
  `target_url_str` varchar(1024) DEFAULT NULL,
  `order_index` int(11) DEFAULT NULL,
  `is_regex` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
