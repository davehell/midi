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

INSERT INTO `dobijeni` (`id`, `uzivatel_id`, `castka`, `vs`, `datum`, `vyrizeno`) VALUES
(2,	1,	100,	'1390765073',	'2014-01-26 20:37:53',	'2014-01-26 20:37:59'),
(5,	1,	-10,	'vratka',	'2014-01-27 09:08:41',	'2014-01-27 09:08:41'),
(6,	1,	-10,	'vratka',	'2014-01-27 09:09:30',	'2014-01-27 09:09:30'),
(7,	1,	100,	'1390810183',	'2014-01-27 09:09:43',	'2014-01-27 09:09:50'),
(8,	4,	100,	'1390912401',	'2014-01-28 13:33:21',	'2014-01-28 13:33:43');

DROP TABLE IF EXISTS `format`;
CREATE TABLE `format` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `demo` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `format` (`id`, `nazev`, `demo`) VALUES
(1,	'text',	1),
(3,	'SMF s  diakritikou',	0),
(4,	'SMF bez diakritiky',	0),
(5,	'XG s diakritikou',	0),
(6,	'XG bez diakritiky',	0),
(7,	'mp3 včetně melodické linky',	0),
(8,	'mp3 bez melodické linky',	0),
(10,	'SMF s  diakritikou',	1),
(11,	'SMF bez diakritiky',	1),
(12,	'XG s diakritikou',	1),
(13,	'XG bez diakritiky',	1),
(14,	'mp3 včetně melodické linky',	1),
(15,	'mp3 bez melodické linky',	1);

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

INSERT INTO `nakup` (`id`, `uzivatel_id`, `skladba_id`, `datum`, `cena`) VALUES
(1,	1,	1,	'2014-01-28 12:44:20',	20),
(4,	4,	3,	'2014-01-27 07:14:38',	40),
(6,	4,	3,	'2014-01-27 09:37:06',	50),
(7,	1,	2,	'2014-01-27 10:47:30',	45),
(8,	4,	1,	'2014-01-28 13:33:57',	40);

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

INSERT INTO `skladba` (`id`, `nazev`, `autor`, `cena`, `poznamka`, `zanr_id`, `verze`, `pocet_stazeni`, `datum_pridani`) VALUES
(1,	'A ty len tancuj',	'romský čardáš',	40,	'výroba Honza Mareš',	5,	'Karaoke',	3,	'2014-01-27 11:48:41'),
(2,	'Angels in love',	'Love story',	45,	'dance version',	2,	'MIDI',	2,	'2014-01-27 11:49:04'),
(3,	'Až mi dáš znamení',	'Pavel Liška',	40,	NULL,	2,	'Karaoke',	0,	'2014-01-27 11:49:04'),
(7,	'pok',	'pok',	15,	'pok',	2,	'MIDI',	0,	'2014-01-28 09:31:52'),
(8,	'asdf',	'asfd',	50,	'',	2,	'Karaoke',	0,	'2014-01-28 10:23:52'),
(9,	'aaa',	'aa',	1,	'',	2,	'MIDI',	0,	'2014-01-28 13:13:52');

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

INSERT INTO `soubor` (`id`, `skladba_id`, `format_id`, `nazev`) VALUES
(1,	1,	3,	'pok');

DROP TABLE IF EXISTS `uzivatel`;
CREATE TABLE `uzivatel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `admin` bit(1) NOT NULL DEFAULT b'0',
  `salt` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `heslo` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `posledni_prihlaseni` datetime DEFAULT NULL,
  `datum_registrace` datetime DEFAULT NULL,
  `kredit` int(11) NOT NULL DEFAULT '0',
  `zapomenute_heslo` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `uzivatel` (`id`, `login`, `admin`, `salt`, `heslo`, `email`, `posledni_prihlaseni`, `datum_registrace`, `kredit`, `zapomenute_heslo`) VALUES
(1,	'admin',	CONV('1', 2, 10) + 0,	'567po7wae1h5b2m8y2ut',	'56ds9eXok.Btk',	'admin@admin2.cz',	'2014-01-29 06:56:06',	'2014-01-21 13:40:05',	55,	''),
(4,	'david',	CONV('0', 2, 10) + 0,	'567po7wae1h5b2m8y2ut',	'56OFEproRPeFk',	'david@david.cz',	'2014-01-28 13:33:01',	'2014-01-21 21:53:56',	15,	NULL),
(5,	'pavel',	CONV('0', 2, 10) + 0,	'rjy6xrpt51qrephvj4g4',	'rjMDyvaYwAEPI',	'pavel@pavel.cz',	NULL,	'2014-01-21 21:56:09',	0,	NULL),
(34,	'jakub',	CONV('0', 2, 10) + 0,	'l7abck4ghvydtkj2a9eu',	'l7tg9mSqjSXGQ',	'j@j.cz',	'2014-01-28 15:22:12',	'2014-01-28 15:22:12',	0,	NULL);

DROP TABLE IF EXISTS `zanr`;
CREATE TABLE `zanr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `zanr` (`id`, `nazev`) VALUES
(2,	'pop-rock'),
(5,	'lidovky'),
(6,	'ostatní písně'),
(7,	'country');

-- 2014-01-29 14:05:53
