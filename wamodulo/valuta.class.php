<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_VALUTA'))
{
/**
* @ignore
*/
define('_WA_VALUTA',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waValuta *******************************************************
//***************************************************************************
/**
* waValuta
*
* classe per la gestione dei controlli di tipo currency. 
*
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waValuta extends waControllo
	{
	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'valuta';
	
	/**
	* numero digit interi
	* @var integer
	*/
	var $nrInteri		= 5;
	
	/**
	* numero digit decimali
	* @var integer
	*/
	var $nrDecimali		= 2;

	/**
	* il controllo rimane vuoto se il valore da mostrare e' zero; altrimenti viene 
	* mostrato "0,00"
	* @var boolean
	*/
	var $vuotoSeZero	= true;

	//***************************************************************************
	/**
	* @access protected
	* @ignore
	*/
	function mostra()
		{
		$this->xmlOpen();
		$this->xmlAdd("valore", $this->valore);
		$this->xmlAdd("nr_decimali", $this->nrDecimali);
		$this->xmlAdd("nr_interi", $this->nrInteri);
		$this->xmlAdd("vuoto_se_zero", $this->vuotoSeZero);
		$this->xmlClose();

		}

	//****************************************************************************************
	/**
	* Restituisce il numero decimale inputato in formato float
	*
	* Si usa in fase di ricezione dei dati, non
	* durante la costruzione della form.
	*
	* @ignore
	* @return float
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
			
		return $this->valoreInput = round(floatval($valoreIn), $this->nrDecimali);
		}

	}	// fine classe waValuta
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_VALUTA'))
?>