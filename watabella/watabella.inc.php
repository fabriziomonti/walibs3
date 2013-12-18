<?php
/**
* @package waTabella
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WATBL_PACKAGE'))
	{
	/**
	* @ignore
	*/
	define('_WATBL_PACKAGE',1);

	//***************************************************************************
	//***   utilities   *********************************************************
	//***************************************************************************
	/**
	* @ignore
	*/
	include dirname(__FILE__) . "/utilita.inc.php";
	
	/**
	* -
	* lunghezza massima di una cella in caratteri (default)
	*/
	define ('WATBL_CELLA_MAX_CARATTERI', 100);
	
	/**
	* -
	* nr. massimo di righe per pagina (default)
	*/
	define ('WATBL_LISTA_MAX_REC', 20);
	
	/**
	* -
	* path del package waDb
	*/
	define('WATBL_PATH_WADB',			dirname(__FILE__) . '/../wadb');
	
	/**
	* -
	* formato data delle celle
	*/
	define('WATBL_FMT_DATA',		'data');
	
	/**
	* -
	* formato data/ora delle celle
	*/
	define('WATBL_FMT_DATAORA',		'dataora');
	
	/**
	* -
	* formato ora delle celle
	*/
	define('WATBL_FMT_ORA',			'ora');
	
	/**
	* -
	* formato decimale delle celle
	*/
	define('WATBL_FMT_DECIMALE',	'valuta');
	define('WATBL_FMT_VALUTA',		'valuta');
	
	/**
	* -
	* formato intero delle celle
	*/
	define('WATBL_FMT_INTERO',		'intero');
	
	/**
	* -
	* formato stringa delle celle html-encoded
	*/
	define('WATBL_FMT_STRINGA',		'testo');
	define('WATBL_FMT_TESTO',		'testo');
	
	/**
	* -
	* formato stringa delle celle non html-encoded
	*/
	define('WATBL_FMT_CRUDO',		'nessuna');
	define('WATBL_FMT_NESSUNA',		'nessuna');
	
	/**
	* -
	* allineamento cella a sinistra
	*/
	define('WATBL_ALLINEA_SX',		0);
	
	/**
	* -
	* allineamento cella al centro
	*/
	define('WATBL_ALLINEA_CENTRO',	1);
	
	/**
	* -
	* allineamento cella a destra
	*/
	define('WATBL_ALLINEA_DX',		2);
	
	/**
	* -
	* definizione di tipo input areatesto
	* 
	*/
	define('WATBL_INPUT_AREATESTO',	'areatesto');
	
	/**
	* -
	* definizione di tipo input data
	* 
	*/
	define('WATBL_INPUT_DATA',	'data');
	
	/**
	* -
	* definizione di tipo input data/ora
	* 
	*/
	define('WATBL_INPUT_DATAORA',	'dataora');
	
	/**
	* -
	* definizione di tipo input ora
	* 
	*/
	define('WATBL_INPUT_ORA',		'ora');
	
	/**
	* -
	* definizione di tipo input intero
	* 
	*/
	define('WATBL_INPUT_INTERO',	'intero');
	
	/**
	* -
	* definizione di tipo input logico
	* 
	*/
	define('WATBL_INPUT_LOGICO',	'logico');
	
	/**
	* -
	* definizione di tipo input selezione
	* 
	*/
	define('WATBL_INPUT_SELEZIONE',	'selezione');
	
	/**
	* -
	* definizione di tipo input testo
	* 
	*/
	define('WATBL_INPUT_TESTO',	'testo');
	
	/**
	* -
	* definizione di tipo input valuta
	* 
	*/
	define('WATBL_INPUT_VALUTA',	'valuta');
	
	/**
	* -
	* definizione del codice OK per rpc
	* 
	*/
	define('WATBL_RPC_OK',	'__rpc_ok__');
	
	/**
	* -
	* definizione del codice NON OK per rpc
	* 
	*/
	define('WATBL_RPC_KO',	'__rpc_ko__');
	
	/**
	* -
	* nr di record da leggere in un blocco di esportazione (csv/xls/pdf)
	* 
	*/
	define('WATBL_NR_REC_BLOCCO_ESPORTAZIONE',	100);
	
	/**
	* -
	* definizione del codice di errore generico per quick edit
	* 
	*/
	define('WATBL_RPC_KO',	'watbl_rpc_ko');
	
	/**
	* -
	* include del package waDb per l'accesso al database
	* @ignore
	*/
	include_once(WATBL_PATH_WADB . "/wadb.inc.php");
	
	/**
	* -
	* include della classe waColonna
	* @ignore
	*/
	include_once(dirname(__FILE__) . "/wacolonna.class.php");

	/**
	* -
	* include della classe waAzioneTabella
	* @ignore
	*/
	include_once(dirname(__FILE__) . "/waazionetabella.class.php");

	/**
	* -
	* include della classe waTabella
	* @ignore
	*/
	include_once(dirname(__FILE__) . "/watabella.class.php");

	} //  if (!defined('_WATBL_PACKAGE'))
?>