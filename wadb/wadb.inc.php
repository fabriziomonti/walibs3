<?php
/**
 * file delle defines
 * 
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/


if (!defined('_WADB_PACKAGE'))
{
	/**
	* @ignore
	*/
	define('_WADB_PACKAGE',1);
	
	//******************* tipi database **************************
	/**
	* 
	* Tipo database FileMaker
	*/
	define('WADB_TIPODB_FM', "fm");
	
	/**
	* 
	* Tipo database MySQL
	*/
	define('WADB_TIPODB_MYSQL', "mysql");
	
	/**
	* 
	* Tipo database MySQL-Improved
	*/
	define('WADB_TIPODB_MYSQLI', "mysqli");
	
	/**
	* 
	* Tipo database MS SQL Server
	*/
	define('WADB_TIPODB_MSSQL', "mssql");
	
	/**
	* 
	* Tipo database ODBC
	*/
	define('WADB_TIPODB_ODBC', 	"odbc");
	
	/**
	* 
	* Tipo database ORACLE
	*/
	define('WADB_TIPODB_ORACLE', 	"oracle");
	
	
	//******************* tipi campo applicativi **************************
	/**
	* 
	* Tipo campo applicativo stringa
	*/
	define ("WADB_STRINGA",             "STRINGA");
	
	/**
	* 
	* Tipo campo applicativo numerico intero
	*/
	define ("WADB_INTERO",              "INTERO");
	
	/**
	* 
	* Tipo campo applicativo contenitore (text, longtext, container, ecc)
	*/
	define ("WADB_CONTENITORE",         "CONTENITORE");
	
	/**
	* 
	* Tipo campo applicativo data
	*/
	define ("WADB_DATA",                "DATA");
	
	/**
	* 
	* Tipo campo applicativo ora
	*/
	define ("WADB_ORA",                 "ORA");
	
	/**
	* 
	* Tipo campo applicativo data/ora
	*/
	define ("WADB_DATAORA",             "DATAORA");
	
	/**
	* 
	* Tipo campo applicativo numerico decimale
	*/
	define ("WADB_DECIMALE",            "DECIMALE");
	
	/**
	* 
	* Tipo campo applicativo sconosciuto
	*/
	define ("WADB_TIPOSCONOSCIUTO",     "TIPOSCONOSCIUTO");
	
	
	//******************* stati dei record **************************
	/**
	* @ignore
	*/
	define('WADB_RECORD_INALTERATO', 0);
	/**
	* @ignore
	*/
	define('WADB_RECORD_MODIFICATO', 1);
	/**
	* @ignore
	*/
	define('WADB_RECORD_NUOVO', 2);
	/**
	* @ignore
	*/
	define('WADB_RECORD_DA_CANCELLARE', 3);
	
	/**
	* include utility package
	* @ignore
	*/
	require_once(dirname(__FILE__) . '/utilita.inc.php');
	
	/**
	* include classe waRigheDB
	* @ignore
	*/
	require_once(dirname(__FILE__) . '/warighedb.class.php');
	
	/**
	* include classe waRecord
	* @ignore
	*/
	require_once(dirname(__FILE__) . '/warecord.class.php');
	
	
//*****************************************************************************
} //  if (!defined('_WADB_PACKAGE'))
?>