-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `cd`;
CREATE TABLE `cd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `cena` int(11) NOT NULL,
  `foto` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


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

DROP TABLE IF EXISTS `hudba_agentura`;
CREATE TABLE `hudba_agentura` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `www` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `foto` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `hudba_bazar`;
CREATE TABLE `hudba_bazar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `tel` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `datum` date NOT NULL,
  `typ` enum('prodej','poptavka') COLLATE utf8_czech_ci NOT NULL,
  `hudba_bazar_kategorie_id` int(11) NOT NULL,
  `foto1` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `foto2` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `foto3` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hudba_bazar_kategorie_id` (`hudba_bazar_kategorie_id`),
  CONSTRAINT `hudba_bazar_ibfk_1` FOREIGN KEY (`hudba_bazar_kategorie_id`) REFERENCES `hudba_bazar_kategorie` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `hudba_bazar_kategorie`;
CREATE TABLE `hudba_bazar_kategorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `hudba_bazar_kategorie` (`id`, `nazev`) VALUES
(1,	'dechové nástroje'),
(2,	'strunné nástroje'),
(3,	'klávesové nástroje'),
(4,	'bicí nástroje'),
(5,	'aparatura'),
(6,	'staré krámy');

DROP TABLE IF EXISTS `hudba_hpback`;
CREATE TABLE `hudba_hpback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `cena` int(11) NOT NULL,
  `soubor` varchar(150) COLLATE utf8_czech_ci DEFAULT NULL,
  `hudba_hpback_kategorie_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `soubor_id` (`soubor`),
  KEY `hudba_hpback_kategorie_id` (`hudba_hpback_kategorie_id`),
  CONSTRAINT `hudba_hpback_ibfk_1` FOREIGN KEY (`hudba_hpback_kategorie_id`) REFERENCES `hudba_hpback_kategorie` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `hudba_hpback_kategorie`;
CREATE TABLE `hudba_hpback_kategorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `hudba_hpback_kategorie` (`id`, `nazev`) VALUES
(1,	'podklady pro instrumentalisty'),
(2,	'podklady pro nekompletní kapely'),
(3,	'kompletní halfplayback');

DROP TABLE IF EXISTS `hudba_noty`;
CREATE TABLE `hudba_noty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `cena` int(11) NOT NULL,
  `foto` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `soubor_id` int(11) DEFAULT NULL,
  `hudba_noty_kategorie_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `soubor_id` (`soubor_id`),
  KEY `hudba_noty_kategorie_id` (`hudba_noty_kategorie_id`),
  CONSTRAINT `hudba_noty_ibfk_1` FOREIGN KEY (`soubor_id`) REFERENCES `soubor` (`id`),
  CONSTRAINT `hudba_noty_ibfk_2` FOREIGN KEY (`hudba_noty_kategorie_id`) REFERENCES `hudba_noty_kategorie` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `hudba_noty_kategorie`;
CREATE TABLE `hudba_noty_kategorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `zkratka` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `obsazeni` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `hudba_noty_kategorie` (`id`, `nazev`, `zkratka`, `obsazeni`) VALUES
(1,	'malý dechový orchestr',	'MDO',	'<ul><li>partitura</li><li>Zpěv</li><li>Klarinet Es</li><li>Klarinet 1. B</li><li>Křídlovka 1. B</li><li>Křídlovka 2.B</li><li>Tenor</li><li>Baryton</li><li>Doprovod B hlas</li><li>Bicí</li></ul>'),
(2,	'velký dechový orchestr',	'VDO',	'<ul>\r\n<li>Pikola</li>\r\n<li>Flétna</li>\r\n<li>Hoboj</li>\r\n<li>Fagot</li>\r\n<li>Klarinety – Es, 1.B, 2.B, 3.B, Bas klarinet</li>\r\n<li>Saxofony – 1.Es alt, 2.B tenor, 3. Es alt, 4.B tenor, Es Baryton</li>\r\n<li>Křídlovky – 1. a 2. B</li>\r\n<li>Tenor</li>\r\n<li>Baryton</li>\r\n<li>Trubky 1.B, 2.B, 3.B</li>\r\n<li>Trubky doprovod – 1.a 2.Es (možno upravit na B hlasy, případně trombon, melofon)</li>\r\n<li>Horna 1. – 4. F</li>\r\n<li>Pozouny 1. – 3.</li>\r\n<li>F tuba</li>\r\n<li>B tuba</li>\r\n<li>Contrabas</li>\r\n<li>Bicí</li>\r\n</ul>\r\n<p>Toto obsazení je standardní, některé aranže nemusí obsahovat všechny nástroje. Po konzultaci je možno obsazení měnit.</p>'),
(3,	'malý taneční orchestr',	'MTO',	'<ul>\r\n<li>klávesy</li>\r\n<li>saxofon</li>\r\n<li>trubka</li>\r\n<li>basy</li>\r\n<li>kytara</li>\r\n<li>dle potřeby konrétní kapely</li>\r\n</ul>'),
(4,	'zpěvník',	'Z',	'<ul>\r\n<li>melodie</li>\r\n<li>text</li>\r\n<li>akordy</li>\r\n</ul>');

DROP TABLE IF EXISTS `karaoke_dvd`;
CREATE TABLE `karaoke_dvd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `cena` int(11) NOT NULL,
  `foto` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `karaoke_dvd` (`id`, `nazev`, `popis`, `cena`, `foto`) VALUES
(1,	'Zpíváme v klubu 1',	'1. Bella Maria - Sandmann / Jiřina Fikejzová\r\n2. Beskyde, beskyde - lidová / úprava L. Piskoř\r\n3. Cikánka - K. Vacek / úprava L. Piskoř\r\n4. Co je to za děvčátko - lidová / úprava L. Piskoř\r\n5. Děti z Pirea - M. Hadjidakis / Z. Borovec\r\n6. Do lesíčka na čekanou - lidová / úprava L. Piskoř\r\n7. Hájku háječku - lidová / úprava L. Piskoř\r\n8. Hledám galanečku - lidová / úprava L. Piskoř\r\n9. Javorinka šedivá - lidová / úprava Moravanka\r\n10. Rožnovské hodiny - lidová / úprava L. Piskoř',	300,	'dvd-1.jpg');

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
  CONSTRAINT `soubor_ibfk_2` FOREIGN KEY (`format_id`) REFERENCES `format` (`id`),
  CONSTRAINT `soubor_ibfk_3` FOREIGN KEY (`skladba_id`) REFERENCES `skladba` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `uzivatel`;
CREATE TABLE `uzivatel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) CHARACTER SET latin2 COLLATE latin2_czech_cs NOT NULL,
  `role` enum('admin','spravce','zakaznik') COLLATE utf8_czech_ci NOT NULL DEFAULT 'zakaznik',
  `salt` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `heslo` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `posledni_prihlaseni` datetime DEFAULT NULL,
  `datum_registrace` datetime DEFAULT NULL,
  `kredit` int(11) NOT NULL DEFAULT '0',
  `heslo_token` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `heslo_token_platnost` datetime DEFAULT NULL,
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

INSERT INTO `zanr` (`id`, `nazev`) VALUES
(2,	'pop-rock'),
(5,	'lidovky'),
(6,	'ostatní písně'),
(7,	'country');

-- 2014-09-28 15:51:42
