-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 30 set, 2010 at 12:03 AM
-- Versione MySQL: 5.1.41
-- Versione PHP: 5.3.2-1ubuntu4.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: 'testwalibs'
--

-- --------------------------------------------------------

--
-- Struttura della tabella 'allievi'
--

CREATE TABLE IF NOT EXISTS allievi (
  id_allievo int(10) NOT NULL AUTO_INCREMENT,
  id_corso int(10) NOT NULL,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  indirizzo varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  cap int(5) NOT NULL,
  provincia varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  citta varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  codice_fiscale varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  dataora_colloquio datetime NOT NULL,
  flag_ammissione int(1) NOT NULL DEFAULT '0',
  flag_promozione int(1) NOT NULL DEFAULT '0',
  nome_file_curriculum varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  curriculum text COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_allievo),
  KEY id_corso (id_corso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dump dei dati per la tabella 'allievi'
--


-- --------------------------------------------------------

--
-- Struttura della tabella 'amministrazioni'
--

CREATE TABLE IF NOT EXISTS amministrazioni (
  id_amministrazione int(10) NOT NULL AUTO_INCREMENT,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  indirizzo varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  cap int(5) NOT NULL,
  provincia varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  citta varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  email varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  telefono varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  sigla varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_amministrazione)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dump dei dati per la tabella 'amministrazioni'
--


-- --------------------------------------------------------

--
-- Struttura della tabella 'corsi'
--

CREATE TABLE IF NOT EXISTS corsi (
  id_corso int(10) NOT NULL AUTO_INCREMENT,
  id_organismo int(10) NOT NULL,
  id_amministrazione int(10) NOT NULL,
  rifpa varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  nome varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  nr_ore int(5) NOT NULL,
  data_inizio date NOT NULL,
  data_fine date NOT NULL,
  importo decimal(13,6) NOT NULL,
  PRIMARY KEY (id_corso),
  KEY id_organismo (id_organismo),
  KEY id_amministrazione (id_amministrazione)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dump dei dati per la tabella 'corsi'
--


-- --------------------------------------------------------

--
-- Struttura della tabella 'organismi'
--

CREATE TABLE IF NOT EXISTS organismi (
  id_organismo int(10) NOT NULL AUTO_INCREMENT,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  indirizzo varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  cap int(5) NOT NULL,
  provincia varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  citta varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  email varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  telefono varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_organismo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dump dei dati per la tabella 'organismi'
--

