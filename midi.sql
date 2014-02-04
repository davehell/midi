-- Adminer 4.0.2 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = '+01:00';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `dobijeni`;
CREATE TABLE `dobijeni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uzivatel_id` int(11) NOT NULL,
  `castka` int(11) NOT NULL,
  `vs` varchar(11) COLLATE utf8_czech_ci NOT NULL,
  `datum` datetime NOT NULL,
  `vyrizeno` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `zakaznik` (`uzivatel_id`),
  CONSTRAINT `dobijeni_ibfk_1` FOREIGN KEY (`uzivatel_id`) REFERENCES `uzivatel` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `format`;
CREATE TABLE `format` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `demo` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `hudba_bazar`;
CREATE TABLE `hudba_bazar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(1000) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `tel` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `datum` date NOT NULL,
  `foto1` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `foto2` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `foto3` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `nakup`;
CREATE TABLE `nakup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uzivatel_id` int(11) NOT NULL,
  `skladba_id` int(11) NOT NULL,
  `datum` datetime NOT NULL,
  `cena` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `skladba` (`skladba_id`),
  KEY `uzivatel_id` (`uzivatel_id`),
  CONSTRAINT `nakup_ibfk_3` FOREIGN KEY (`uzivatel_id`) REFERENCES `uzivatel` (`id`),
  CONSTRAINT `nakup_ibfk_6` FOREIGN KEY (`skladba_id`) REFERENCES `skladba` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `skladba`;
CREATE TABLE `skladba` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `autor` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `cena` int(11) NOT NULL,
  `poznamka` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `zanr_id` int(11) NOT NULL,
  `verze` enum('MIDI','Karaoke') COLLATE utf8_czech_ci NOT NULL,
  `pocet_stazeni` int(11) NOT NULL DEFAULT '0',
  `datum_pridani` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zanr` (`zanr_id`),
  CONSTRAINT `skladba_ibfk_3` FOREIGN KEY (`zanr_id`) REFERENCES `zanr` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `soubor`;
CREATE TABLE `soubor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skladba_id` int(11) NOT NULL,
  `format_id` int(11) NOT NULL,
  `nazev` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `skladba_id_format_id` (`skladba_id`,`format_id`),
  KEY `skladba_id` (`skladba_id`),
  KEY `format_id` (`format_id`),
  CONSTRAINT `soubor_ibfk_1` FOREIGN KEY (`skladba_id`) REFERENCES `skladba` (`id`),
  CONSTRAINT `soubor_ibfk_2` FOREIGN KEY (`format_id`) REFERENCES `format` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `uzivatel`;
CREATE TABLE `uzivatel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) CHARACTER SET latin2 COLLATE latin2_czech_cs NOT NULL,
  `admin` bit(1) NOT NULL DEFAULT b'0',
  `salt` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `heslo` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `posledni_prihlaseni` datetime DEFAULT NULL,
  `datum_registrace` datetime DEFAULT NULL,
  `kredit` int(11) NOT NULL DEFAULT '0',
  `zapomenute_heslo` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `zanr`;
CREATE TABLE `zanr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2014-02-04 20:48:46