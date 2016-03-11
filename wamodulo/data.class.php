<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_DATA'))
{
/**
* @ignore
*/
define('_WA_DATA',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waData *******************************************************
//***************************************************************************
/**
* waData
*
* classe per la gestione dei controlli di tipo date. 
 * 
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waData extends waControllo
	{
	
	/**
	* @ignore
	* @access protected
	*/
	var $tipo				= 'data';
	
	/**
	* anno minimo
	* @var integer
	*/
	var $annoPartenza		= 1901;
	
	/**
	* anno massimo
	*
	* per default, l'anno corrente
	* @var integer
	*/
	var $annoTermine		= '';
	
	/**
	* indica se gli anni vengono ordinati in modalita' discendente o meno
	*
	* @var boolean
	*/
	var $annoDecrescente		= TRUE;
	
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
									($this->valore ? date("Y-m-d", $this->valore) : '') .
									"</valore>\n";
		
		$this->xmlAdd("anno_decrescente", $this->annoDecrescente);
		$this->xmlAdd("anno_partenza", $this->annoPartenza);
		$this->xmlAdd("anno_termine", empty($this->annoTermine) ? date('Y') : $this->annoTermine);
		
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

		if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $valoreIn, $parts)) 
			return $this->valoreInput = null;
	
		if (!checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1]))
			return $this->valoreInput = null;
		return $this->valoreInput = mktime(0,0,0, $parts[2], $parts[3], $parts[1]);
		}

	}	// fine classe waData
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_DATA'))
?>