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


DROP TABLE IF EXISTS `hudba_agentura`;
CREATE TABLE `hudba_agentura` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `www` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `foto` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `hudba_agentura` (`id`, `nazev`, `popis`, `www`, `foto`) VALUES
(1,	'Michal Tučný revival band',	'Jsme revival Michala Tučného na Moravě, se sídlem ve Frýdku-Místku.\r\n\r\nNaše produkce je vhodná pro společenské a obecní akce, festivaly, kluby, firemní a soukromé večírky. V neposlední řadě jsou uspěšné i samostatné koncerty.\r\n\r\nCelý koncert je spíš vzpomínka na velkou osobnost a nezapomenutelnou legendu české country, Michala Tučného.',	'http://www.michaltucnyrevival.cz/',	'kapela-1.jpg'),
(2,	'Lubomír Piskoř - posezení s harmonikou',	'Vystoupení je bez jakékoliv aparatury a maximální délka trvání je 4 hodiny.',	'',	'');

DROP TABLE IF EXISTS `hudba_bazar`;
CREATE TABLE `hudba_bazar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `tel` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `datum` date NOT NULL,
  `foto1` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `foto2` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `foto3` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `hudba_bazar` (`id`, `text`, `email`, `tel`, `datum`, `foto1`, `foto2`, `foto3`) VALUES
(2,	'Prodám akordeon Lignatone Melodia  III, 80 basů, plně funkční, včetně kufru na přepravu',	'lubos.p@cbox.cz',	'602744055',	'2014-02-06',	'inzerat-2-1.JPG',	'inzerat-2-2.JPG',	'inzerat-2-3.JPG'),
(3,	'Prodám Alt Saxofon Amati Classic SUPER, po celkové generální opravě, včetně kufru. \nCena: 6.500,- Kč',	'lubos.p@cbox.cz',	'602744055',	'2014-02-06',	'inzerat-3-1.JPG',	'inzerat-3-2.JPG',	'inzerat-3-3.JPG'),
(4,	'Prodám dynamický nástrojový mikrofon Beyerdynamic TGX 5. Cena 1.300,- Kč',	'lubos.p@cbox.cz',	'602744055',	'2014-02-06',	'inzerat-4-1.JPG',	'inzerat-4-2.JPG',	NULL);

DROP TABLE IF EXISTS `hudba_cd`;
CREATE TABLE `hudba_cd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `autor` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `cena` int(11) NOT NULL,
  `foto` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `hudba_cd` (`id`, `autor`, `nazev`, `popis`, `cena`, `foto`) VALUES
(1,	'Karel Gott',	'Konec ptačích árií',	'Karel Gott zpívá písně s texty Jiřího Štaidla (2013) - Supraphon',	100,	'cd-1.jpg');

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
  CONSTRAINT `soubor_ibfk_1` FOREIGN KEY (`skladba_id`) REFERENCES `skladba` (`id`) ON DELETE CASCADE,
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


-- 2014-02-06 17:58:35