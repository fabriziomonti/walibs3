CREATE TABLE IF NOT EXISTS wadoc_allegati (
  idAllegato int(10) NOT NULL AUTO_INCREMENT,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  titolo varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  posizione int(3) NOT NULL DEFAULT '0',
  descrizione text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idAllegato)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_azioni (
  idAzione int(10) NOT NULL AUTO_INCREMENT,
  idTabella int(10) NOT NULL,
  etichetta varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  suRecord int(1) NOT NULL DEFAULT '0',
  condizionata int(1) NOT NULL DEFAULT '0',
  posizione int(3) NOT NULL DEFAULT '0',
  descrizione text COLLATE utf8_unicode_ci,
  `help` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idAzione),
  KEY idTabella (idTabella),
  KEY etichetta (etichetta)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_campi (
  idCampo int(10) NOT NULL AUTO_INCREMENT,
  idTabellaDB int(10) NOT NULL,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  tipo varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  tipoDB varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  lunghezza int(10) DEFAULT NULL,
  chiavePrimaria int(1) NOT NULL DEFAULT '0',
  posizione int(3) NOT NULL DEFAULT '0',
  descrizione text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idCampo),
  KEY idTabella (idTabellaDB),
  KEY nome (nome)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_colonne (
  idColonna int(10) NOT NULL AUTO_INCREMENT,
  idTabella int(10) NOT NULL,
  etichetta varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  idCampo int(10) DEFAULT NULL,
  mostra int(1) NOT NULL DEFAULT '0',
  ordina int(1) NOT NULL DEFAULT '0',
  filtra int(1) NOT NULL DEFAULT '0',
  totalizza int(1) NOT NULL DEFAULT '0',
  aliasDi text COLLATE utf8_unicode_ci,
  posizione int(3) NOT NULL DEFAULT '0',
  descrizione text COLLATE utf8_unicode_ci,
  `help` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idColonna),
  KEY idTabella (idTabella),
  KEY etichetta (etichetta)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_controlli (
  idControllo int(10) NOT NULL AUTO_INCREMENT,
  idModulo int(10) NOT NULL,
  etichetta varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  idCampo int(10) DEFAULT NULL,
  tipo varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  visibile int(1) NOT NULL DEFAULT '0',
  solaLettura int(1) NOT NULL DEFAULT '0',
  obbligatorio int(1) NOT NULL DEFAULT '0',
  posizione int(3) NOT NULL,
  descrizione text COLLATE utf8_unicode_ci,
  `help` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idControllo),
  KEY sezione (idModulo)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_menu (
  idMenu int(10) NOT NULL AUTO_INCREMENT,
  idSezione int(10) NOT NULL,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  titolo varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  descrizione text COLLATE utf8_unicode_ci,
  `help` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idMenu),
  KEY sezione (idSezione)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_moduli (
  idModulo int(10) NOT NULL AUTO_INCREMENT,
  idPagina int(10) NOT NULL,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  titolo varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  descrizione text COLLATE utf8_unicode_ci,
  `help` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idModulo),
  KEY nome (nome),
  KEY idPagina (idPagina)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_pagine (
  idPagina int(10) NOT NULL AUTO_INCREMENT,
  idSezione int(10) NOT NULL,
  nome varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  titolo varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  descrizione text COLLATE utf8_unicode_ci,
  `help` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idPagina),
  KEY sezione (idSezione),
  KEY nome (nome)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_release (
  idRelease int(10) NOT NULL AUTO_INCREMENT,
  nomeProcedura varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  nrRelease varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  dataRelease date NOT NULL,
  autore varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  descrizione text COLLATE utf8_unicode_ci,
  `help` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idRelease)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_sezioni (
  idSezione int(10) NOT NULL AUTO_INCREMENT,
  sigla varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  descrizione text COLLATE utf8_unicode_ci,
  `help` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idSezione),
  KEY sigla (sigla)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_tabelle (
  idTabella int(10) NOT NULL AUTO_INCREMENT,
  idPagina int(10) NOT NULL,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  titolo varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  descrizione text COLLATE utf8_unicode_ci,
  `help` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idTabella),
  KEY nome (nome),
  KEY idPagina (idPagina)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_tabelleDB (
  idTabellaDB int(10) NOT NULL AUTO_INCREMENT,
  tipo varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  nomeDB varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  nome varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  descrizione text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idTabellaDB),
  KEY sezione (tipo)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS wadoc_vociMenu (
  idVoceMenu int(10) NOT NULL AUTO_INCREMENT,
  idMenu int(10) NOT NULL,
  etichetta varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  destinazione varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  livello int(2) NOT NULL DEFAULT '0',
  posizione int(3) NOT NULL DEFAULT '0',
  descrizione text COLLATE utf8_unicode_ci,
  `help` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idVoceMenu),
  KEY idMenu (idMenu),
  KEY etichetta (etichetta)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
