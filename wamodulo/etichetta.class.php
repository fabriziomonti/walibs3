<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_ETICHETTA'))
{
/**
* @ignore
*/
define('_WA_ETICHETTA',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waEtichetta *******************************************************
//***************************************************************************
/**
* waEtichetta
*
* classe per la creazione di una label
*
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waEtichetta extends waControllo
	{
	/**
	* indica se la label debba fungere anche da meccanismo di
	* accesso all'help online
	*
	* @var boolean
	*/
	var $help			= true;
	
	/**
	* @access protected
	* @ignore
	*/
	var $tipo			= 'etichetta';
	
	/**
	 * indica se il controllo e' di input o un etichetta/cornice
	 * 
	* @access protected
	*/
	protected $diInput			= false;
	

	//***************************************************************************
	/**
	* @access protected
	* @ignore
	*/
	function mostra()
		{

		$this->xmlOpen();
		$this->xmlAdd("valore",  $this->valore);
		$this->xmlAdd("help", $this->help);
		if (!empty($this->modulo->righeDB))
			$maxlen = $this->modulo->righeDB->lunghezzaMaxCampo($this->nome);
		$this->xmlAdd("caratteri_max", $maxlen);
		$this->xmlClose();

		}

	//***************************************************************************
	/**
	* @ignore
	*/	
	function xmlInput()
		{
		}
	
	//***************************************************************************
	/**
	* @ignore
	*/	
	function definisciValoreIniziale()
		{
		}
	
	//***************************************************************************
	/**
	 * converte il valore proveniente dal post nelvalore logico del controllo
	* @ignore
	*/	
	function input2valoreInput($valoreIn)
		{
		}
	
	//***************************************************************************
	/**
	* verificaObbligo
	* @ignore
	*
	*/
	function verificaObbligo()
		{
		return true;
		}

	//***************************************************************************
	/**
	 * salva sul campo del record il valore di input
	* @ignore
	*/	
	function input2record()
		{
		}
	

	}	// fine classe waEtichetta
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_ETICHETTA'))
?>