<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_AREATESTO'))
{
/**
* @ignore
*/
define('_WA_AREATESTO',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waAreaTesto *******************************************************
//***************************************************************************
/**
* waAreaTesto
*
* classe per la gestione di un controllo textarea.
*
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waAreaTesto extends waControllo
	{
	/**
	* larghezza in caratteri del controllo
	*
	* questa informazione viene utilizzata a piacere nella UI
	* @var integer
	*/
	var $colonne			= 50;
	
	/**
	* altezza in caratteri del controllo
	*
	* questa informazione viene utilizzata a piacere nella UI
	* @var integer
	*/
	var $righe			= 10;

	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'areatesto';
	
	//***************************************************************************
	/**
	* @ignore
	*/
	function mostra()
		{

		$this->xmlOpen();
		//$this->xmlAdd("valore",  str_replace("'", '&#39', htmlspecialchars($this->valore)));
		$this->xmlAdd("valore",  $this->valore);
		$this->xmlAdd("colonne", $this->colonne);
		$this->xmlAdd("righe", $this->righe);
		$this->xmlClose();
		}


	}	// fine classe waAreaTesto
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_AREATESTO'))
?>