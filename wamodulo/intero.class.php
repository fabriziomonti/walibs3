<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_INTERO'))
{
/**
* @ignore
*/
define('_WA_INTERO',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/testo.class.php");

//***************************************************************************
//****  classe waIntero *******************************************************
//***************************************************************************
/**
* waIntero
*
* classe per la gestione dei controlli di tipo integer. 
*
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waIntero extends waTesto
	{
	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'intero';

	/**
	* nr. massimo di caratteri accettabili dal controllo
	*
	* laddove possibile, la classe provvedera' a desumere dal database questa informazione
	* @var integer
	*/
	var $caratteriMax		= 10;

	//****************************************************************************************
	/**
	* Restituisce il numero inputato in formato  intero
	*
	* Si usa in fase di ricezione dei dati, non
	* durante la costruzione della form.
	*
	* @ignore
	* @return int
	*/
	function input2valoreInput($valoreIn)
		{
		if ($valoreIn === "__wamodulo_valore_non_ritornato__")
			{
			$this->inputNonRitornato = true;
			return null;
			}
			
		if ($valoreIn === '')
			return $this->valoreInput = null;
		return $this->valoreInput = intval($valoreIn);
		}

	}	// fine classe waIntero
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_INTERO'))
?>