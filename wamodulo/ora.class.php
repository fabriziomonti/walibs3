<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_ORA'))
{
/**
* @ignore
*/
define('_WA_ORA',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waOra *******************************************************
//***************************************************************************
/**
* waOra
*
* classe per la gestione dei controlli di tipo time.
*
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waOra extends waControllo
	{
	/**
	* intervallo minuti
	*
	* indica che intervallo di minuti e' selezionabile 
	* @var integer
	*/
	var $intervalloMinuti = 5;
	
	/**
	* indica se mostrare il controllo anche per i secondi
	*
	* @var boolean
	*/
	var $mostraSecondi	= FALSE;
	
	/**
	* intervallo secondi
	*
	* indica che intervallo di secondi e' selezionabile (se presente il controllo
	* dei secondi)
	* @var integer
	*/
	var $intervalloSecondi = 10;
	
	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'ora';

	//***************************************************************************
	//***************************************************************************
	//***************************************************************************
	/**
	* @ignore
	*/
	function mostra()
		{
		$this->xmlOpen();
		$this->modulo->buffer .= "\t\t\t<valore>" .
									($this->valore ? date("H:i:s", $this->valore) : '') .
									"</valore>\n";
		
		$this->xmlAdd("intervallo_minuti", $this->intervalloMinuti);
		$this->xmlAdd("mostra_secondi", $this->mostraSecondi);
		$this->xmlAdd("intervallo_secondi", $this->intervalloSecondi);
		$this->xmlClose();

		}

	//****************************************************************************************
	/**
	* Restituisce l'ora inputata in formato timestamp
	*
	 * @ignore
	* @return mixed il timestamp dell'ora se valorizzata correttamente 
	* <B>con spiazzamento della data al 01/01/1980!!!</B>; altrimenti null
	*/
	function input2valoreInput($valoreIn)
		{
		if ($valoreIn === "__wamodulo_valore_non_ritornato__")
			{
			$this->inputNonRitornato = true;
			return $this->valoreInput = null;
			}

		if (!preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $valoreIn, $parts)) 
			return $this->valoreInput = null;
	
		return $this->valoreInput = mktime($parts[1], $parts[2], $parts[3], 1,1,1980);
		}

	}	// fine classe waOra
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_ORA'))
?>