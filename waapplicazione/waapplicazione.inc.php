<?php
/**
* file delle defines 
* 
* @package waApplicazione
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_APPLICAZIONE_PACKAGE'))
{
	/**
	* @ignore
	*/
	define('_WA_APPLICAZIONE_PACKAGE',1);
	
	//codici modalita' di navigazione
	/**
	* -
	* codice della modalita' di navigazione su singola pagina
	* 
	* comunica alla ui che l'applicazione prevede una navigazione
	* in cui ogni finestra figlia si apra nella stessa
	* "window" della mamma
	* 
	* solamente la UI sa come comportarsi a fronte di questa informazione: il
	* comportamento di waApplicazione non è in alcun modo modificato da questo
	* valore
	*/
	define('WAAPPLICAZIONE_NAV_PAGINA',		1);
	
	/**
	* -
	* codice della modalita' di navigazione su finestra esterna alla pagina
	* 
	* comunica alla ui che l'applicazione prevede una navigazione
	* in cui ogni pagina figlia si apra in un 
	* una nuova "window" che sara' figlia della mamma
	* 
	* solamente la UI sa come comportarsi a fronte di questa informazione: il
	* comportamento di waApplicazione non è in alcun modo modificato da questo
	* valore
	*/
	define('WAAPPLICAZIONE_NAV_FINESTRA',	2);
	
	/**
	* -
	* codice della modalita' di navigazione su iframe interno alla pagina
	* 
	* comunica alla ui che l'applicazione prevede una navigazione
	* in cui ogni pagina figlia si apra in un 
	* una nuova "iFrame" che sara' figlia della mamma
	* 
	* solamente la UI sa come comportarsi a fronte di questa informazione: il
	* comportamento di waApplicazione non è in alcun modo modificato da questo
	* valore
	*/
	define('WAAPPLICAZIONE_NAV_INTERNA',	3);
	
	/**
	* @ignore
	*/
	include_once (dirname(__FILE__) . "/waapplicazione.class.php");

//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_APPLICAZIONE_PACKAGE'))

?>