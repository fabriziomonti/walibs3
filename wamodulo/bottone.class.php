<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_BOTTONE'))
{
/**
* @ignore
*/
define('_WA_BOTTONE',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waBottone *******************************************************
//***************************************************************************
/**
* waBottone
*
* classe per la gestione dei controlli di tipo button e submit
 * 
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waBottone extends waControllo
	{

	/**
	* @ignore
	* @access protected
	*/
	var $tipo		= 'bottone';
	
	/**
	* indica che il bottone deve provocare submit
	* @var boolean
	*/
	var $invia	= TRUE;
	
	/**
	* indica che il bottone deve provocare abort
	* @var boolean
	*/
	var $annulla	= FALSE;
	
	/**
	* indica che il bottone deve provocare la cancellazione di un record
	* @var boolean
	*/
	var $elimina	= FALSE;

	/**
	* un bottone per sua natura non ha mai la corrispondenza con un campo del db
	* 
	* @var boolean
	*/
	var $corrispondenzaDB		= false;
	
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	function mostra()
		{
		$this->xmlOpen();
		$this->xmlAdd("valore", $this->valore);
		$this->xmlAdd("invia", $this->invia);
		$this->xmlAdd("annulla", $this->annulla);
		$this->xmlAdd("elimina", $this->elimina);
		$this->xmlClose();

		}

	//***************************************************************************
	/**
	* @ignore
	*/	
	function xmlInput()
		{
		if ($this->invia)
			$this->modulo->buffer .= "\t\t<$this->tipo.input id='$this->nome'></$this->tipo.input>\n";
		}
	

	}	// fine classe waBottone
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_BOTTONE'))

?>