<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_CORNICE'))
{
/**
* @ignore
*/
define('_WA_CORNICE',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waCornice *******************************************************
//***************************************************************************
/**
* waCornice
*
* classe per la creazione di una cornice che raggruppa (graficamente) piu' controlli.
*
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waCornice extends waControllo
	{
	/**
	* indica se la label della cornice debba fungere anche da meccanismo di
	* accesso all'help online
	*
	* questa informazione viene utilizzata a piacere nella UI
	* @var boolean
	*/
	var $help			= false;

	/**
	* posizionamento verticale della label del frame
	*
 	* questa informazione viene utilizzata a piacere nella UI
	* @var integer
	*/
	var $altoEtichetta		= 5;

	/**
	* posizionamento orizzontale della label del frame
	 * 
	* questa informazione viene utilizzata a piacere nella UI
	 * @var integer
	*/	var $sinistraEtichetta		= 5;
	
	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'cornice';

	/**
	 * indica se il controllo e' di input o un etichetta/cornice
	 * 
	* @access protected
	*/
	protected $diInput			= false;
	
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	function mostra()
		{

		$this->xmlOpen();
		$this->xmlAdd("valore", $this->valore);
		$this->xmlAdd("help", $this->help);
		if (!empty($this->modulo->righeDB))
			$maxlen = $this->modulo->righeDB->lunghezzaMaxCampo($this->nome);
		$this->xmlAdd("caratteri_max", $maxlen);
		$this->xmlAdd("altoEtichetta", $this->altoEtichetta);
		$this->xmlAdd("sinistraEtichetta", $this->sinistraEtichetta);
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
	

	}	// fine classe waCornice
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_CORNICE'))
?>