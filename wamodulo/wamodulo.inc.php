<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WAMODULO_PACKAGE'))
	{
	/**
	* @ignore
	*/
	define('_WAMODULO_PACKAGE',1);

	//***************************************************************************
	//***   utilities   *********************************************************
	//***************************************************************************
	/**
	* @ignore
	*/
	include dirname(__FILE__) . "/utilita.inc.php";
	
	// defines...
	/**
	* boolean che indica se i controlli delle form sono utilizzati
	* in bind con un campo di un record; 
	 * 
	 * se vero, viene richiesta la classe waRecord
	*/
	define('WAMODULO_CONNESSO_DB', 	TRUE);
	
	/**
	* directory dove risiede la classe warecord
	*/
	define('WAMODULO_WADB_PATH', 		dirname(__FILE__) . '/../wadb');
	
	/**
	* nr. massimo di caratteri da scrivere in una cella di un portale
	 * tbd
	 * @ignore 
	*/
//	define('WAMODULO_PORTALE_MAX_CARATTERI', 	50);
	
	//codici operazione
	/**
	* codice dell'operazione di visualizzazione dettaglio record
	*/
	define('WAMODULO_OPE_VIS_DETTAGLIO',	1);
	
	/**
	* codice dell'operazione di inserimento record
	*/
	define('WAMODULO_OPE_INSERIMENTO',		2);
	
	/**
	* codice dell'operazione di modifica record
	*/
	define('WAMODULO_OPE_MODIFICA',		3);
	
	/**
	* codice dell'operazione di eliminazione record
	*/
	define('WAMODULO_OPE_ELIMINA',		4);
	
	/**
	* codice dell'operazione di annullamento (abort)
	*/
	define('WAMODULO_OPE_ANNULLA',		5);
	
	/**
	* codice dell'operazione di chiamata RPC
	*/
	define('WAMODULO_OPE_RPC',		6);
	
	/**
	* definizione del codice OK per rpc
	* 
	*/
	define('WAMODULO_RPC_OK',	'__rpc_ok__');
	
	/**
	* definizione del codice NON OK per rpc
	* 
	*/
	define('WAMODULO_RPC_KO',	'__rpc_ko__');
	
	/**
	* chiave del codice operazione nella query string
	*/
	define('WAMODULO_CHIAVE_OPERAZIONE',		'wamodulo_operazione');
	
	
	if (WAMODULO_CONNESSO_DB === TRUE)
		{
		/**
		* include del package wadb per l'accesso al database
		* @ignore
		*/
		include_once(WAMODULO_WADB_PATH . "/wadb.inc.php");
		}

		
	// inclusione automatica delle classi del package
	$classi = glob(dirname(__FILE__) . "/*.class.php");
	foreach ($classi as $classe)
		/**
		* @ignore
		*/
		include_once($classe);
	
	// eventuale inclusione automatica delle classi estese per i controlli
	// specifici dell'applicazione
	if (defined("WAMODULO_EXTENSIONS_DIR"))
		{
		$classi = glob(WAMODULO_EXTENSIONS_DIR . "/*.class.php");
		foreach ($classi as $classe)
			/**
			* @ignore
			*/
			include_once($classe);
		}
	else 
		// inserito solo per documentazione
		/**
		* nome di una directory da cui effettuare le include delle classi
		* di controlli custom creati ad hoc per l'applicazione;
		* 
		* di questa directory, se valorizzata, saranno inclusi tutti i
		* file con estensione <b>*.class.php</b>; si ometta quindi 
		* di inserire in questa directory file che potrebbero creare
		* confusione
		*/
		define('WAMODULO_EXTENSIONS_DIR',		'');
	
	
	} //  if (!defined('_WAMODULO_PACKAGE'))
?>