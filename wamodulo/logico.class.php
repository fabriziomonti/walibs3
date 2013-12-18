<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_LOGICO'))
{
/**
* @ignore
*/
define('_WA_LOGICO',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waLogico *******************************************************
//***************************************************************************
/**
* waLogico
*
* classe per la gestione dei controlli di tipo si/no (checkbox, ecc.)
*
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waLogico extends waControllo
	{
	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'logico';
	
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	function mostra()
		{
		$this->xmlOpen();
		$this->xmlAdd("valore",  $this->valore);
		$this->xmlClose();

		}

	}	// fine classe waLogico
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_LOGICO'))
?>