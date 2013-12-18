<?php
if (!defined('__CONFIG_VARS'))
{
	define('__CONFIG_VARS',1);
	
	// parametri di inizializzazione usati da waapplicazione
	define('APPL_NOME', 						'app_prova');
	define('APPL_TITOLO', 						'applicazione di prova');
	define('APPL_REL', 							'0.1');
	define('APPL_REL_DATA', 					mktime(0,0,0, 11, 14, 2012));
	define('APPL_SMTP_SERVER', 					'webappls.com');
	define('APPL_INDIRIZZO_ASSISTENZA',			'f.monti@webappls.com');
	define('APPL_INDIRIZZO_INFO', 				'f.monti@webappls.com');
	
	// se true genera documentazione della pagina
	// (da usare ovviamente solo in laboratorio, non in produzione)
	define('APPL_GENERA_WADOC',					true);
	// file di configurazione della documentazione
	define('APPL_WADOC_CONFIG',					dirname(__FILE__) . "/wadocconfig.inc.php");
	
} //  if (!defined('__CONFIG_VARS'))

