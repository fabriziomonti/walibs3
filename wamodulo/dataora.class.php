<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_DATAORA'))
{
/**
* @ignore
*/
define('_WA_DATAORA',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/data.class.php");

//***************************************************************************
//****  classe waDataOra *******************************************************
//***************************************************************************
/**
* waDataOra
*
* classe per la gestione dei controlli di tipo datetime. 

* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waDataOra extends waData
	{
	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'dataora';
	
	/**
	* intervallo minuti
	*
	* indica che intervallo di minuti e' selezionabile 
	* @var integer
	*/
	var $intervalloMinuti = 1;
	
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
	var $intervalloSecondi = 1;
	
	//***************************************************************************
	//***************************************************************************
	//***************************************************************************
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	function mostra()
		{
		$this->xmlOpen();
		$this->modulo->buffer .= "\t\t\t<valore>" .
									($this->valore ? date("Y-m-d H:i:s", $this->valore) : '') .
									"</valore>\n";
		
		$this->xmlAdd("anno_decrescente", $this->annoDecrescente);
		$this->xmlAdd("anno_partenza", $this->annoPartenza);
		$this->xmlAdd("anno_termine", $this->annoTermine);
		$this->xmlAdd("intervallo_minuti", $this->intervalloMinuti);
		$this->xmlAdd("mostra_secondi", $this->mostraSecondi);
		$this->xmlAdd("intervallo_secondi", $this->intervalloSecondi);
		$this->xmlClose();
		
		}

	//****************************************************************************************
	/**
	* Restituisce la data inputata in formato timestamp
	*
	* Si usa in fase di ricezione dei dati, non
	* durante la costruzione della form.
	*
	* @ignore
	* @return mixed il timestamp della data se valorizzata correttamente; altrimenti NULL
	*/
	function input2valoreInput($valoreIn)
		{
		if ($valoreIn === "__wamodulo_valore_non_ritornato__")
			{
			$this->inputNonRitornato = true;
			return $this->valoreInput = null;
			}

		if (!preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $valoreIn, $parts)) 
			return $this->valoreInput = null;
	
		if (!checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1]))
			return $this->valoreInput = null;
		return $this->valoreInput = mktime($parts[4], $parts[5], $parts[6], $parts[2], $parts[3], $parts[1]);

		}

	}	// fine classe waDataOra
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_DATAORA'))
?>