<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
if (!defined('_WA_CONTROLLO'))
{
/**
* @ignore
*/
define('_WA_CONTROLLO',1);


//***************************************************************************
//****  classe waControllo ****************************************************
//***************************************************************************
/**
* waControllo
*
* classe generica che definisce le proprieta' comuni a tutti i controlli
* di un modulo
 * 
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waControllo
	{
	/**
	* modulo a cui il controllo appartiene
	* @var waModulo
	*/
	var $modulo;
	
	/**
	* nome del controllo
	* @var string
	*/
	var $nome 			= '';
	
	/**
	* valore del controllo
	*
	* il valore assume significati diversi a seconda del controllo. Alcuni esempi:
	* - {@link waTesto}, {@link waAreaTesto}, {@link waValuta}, {@link waData}, 
	*   {@link waDataOra}, {@link waCaricaFile} -> e' il valore di default, che puo' essere 
	*   ricoperto da cio'che viene trovato sul db
	* - {@link waEtichetta}, {@link waBottone}, {@link waCornice}, {@link waPortale} -> 
	*   e' la caption
	* - {@link waSelezione}/{@link waOpzione} -> viene selezionato l'elemento corrispondente al valore passato,
	*   purche' l'array degli elementi selezionabili (ancorche' proveniente da una 
	*   query) lo contenga
	* - {@link waLogico} -> 1 se checked - 0 o vuoto altrimenti
	* - {@link waMultiSelezione}, lista -> e' un array contenente i valori degli elementi selezionati
	* @var mixed
	*/
	var $valore			= '';
	
	/**
	* indica se il controllo e' obbligatorio o meno
	*
	* per etichette e cornici ha senso piu' che altro per la renderizzazione
	* @var boolean
	*/
	var $obbligatorio 		= FALSE;

	/**
	* indica se il controllo e' abilitato o meno
	*
	* si presti attenzione che tipicamente tutti i controlli solaLettura sono disabilitati,
	* e in quanto tali in HTML non passano il proprio valore alla pagina di destinazione.
	*
	* @var boolean
	*/
	var $solaLettura 		= FALSE;
	
	/**
	* indica se il controllo e' associato ad un campo di un record; per default
	* lo è
	*
	* il campo del record dovra' avere il medesimo nome del controllo,
	* case-insensitive
	* 
	* @var boolean
	*/
	var $corrispondenzaDB		= true;
	
	/**
	* tabIndex del controllo
	*
	* questa informazione viene utilizzata a piacere nella UI
	 * 
	* @var integer
	*/
	var $indiceTab		= 0;
	
	/**
	* indica se il controllo e' visibile o meno
	*
	* a differenza della proprieta' {@link $solaLettura}, il valore di un
	* controllo hidden, in HTML,  viene passato alla pagina di destinazione.
	* 
	* @var boolean
	*/
	var $visibile		= TRUE;

	/**
	* posizionamento orizzontale del controllo
	*
	* questa informazione viene utilizzata a piacere nella UI
	 * 
	* @var integer
	*/
	var	$sinistra		= 0;
	
	/**
	* posizionamento verticale del controllo
	*
	* questa informazione viene utilizzata a piacere nella UI
	 * 
	* @var integer
	*/
	var	$alto			= 0;
	
	/**
	* altezza del controllo; 
	 * 
	* questa informazione viene utilizzata a piacere nella UI
	 * 
	* @var integer
	*/
	var $altezza		= 0;
	
	/**
	* larghezza del controllo
	 * 
	* questa informazione viene utilizzata a piacere nella UI
	 * 
	* @var integer
	*/
	var $larghezza			= 0;
	
	/**
	* valore ritornato dalla fase di input
	* @var string
	*/
	var $valoreInput;
	
	/**
	* flag che indica che non e' stato ritornato alcun valore dalla fase di input,
	 * ossia che il controllo era disabilitato al momento del submit
	 * 
	* @var boolean
	*/
	var $inputNonRitornato = false;
	
	/**
	 * tipo del controllo (valorizzato automaticamente dalla classe)
	 * 
	* @access protected
	*/
	var $tipo			= '';
	
	/**
	 * indica se il controllo e' di input o un etichetta/cornice
	 * 
	* @access protected
	*/
	protected $diInput			= true;
	
	//***************************************************************************
	//***************************************************************************
	//***************************************************************************
	/**
	* costruttore
	*
	* inizializza il controllo.
	* @param waModulo $modulo oggetto di classe {@link waModulo} a cui il controllo appartiene
	* @param string $nome nome del controllo; vedi {@link $nome}
	* @param string $valore valore di default del controllo; vedi {@link $valore}
	*/
	function __construct($modulo, $nome = '', $valore = '')
		{
		$this->modulo = &$modulo;
		$this->nome = $nome;
		$this->valore = $valore;
		$modulo->controlli[] = &$this;
		
		if ($this->diInput)
			{
			$modulo->controlliInput[$nome] = &$this;
			$modulo->controlliInputNum[] = &$this;
			}
		else
			{
			$modulo->etichette[$nome] = &$this;
			$modulo->etichetteNum[] = &$this;
			}

		}
		
	//***************************************************************************
	/**
	* @access protected
	* @ignore
	*/	
	protected function dammiPathCorrente()
		{
		return wamodulo_miaPath();
		}
		
	//***************************************************************************
	/**
	* @access protected
	* @ignore
	*/	
	protected function xmlOpen()
		{
		$this->modulo->buffer .= "\t\t<$this->tipo id='$this->nome'>\n";

		$this->xmlAdd("visibile" , $this->visibile);
		$this->xmlAdd("sola_lettura" , $this->solaLettura);
		$this->xmlAdd("obbligatorio" , $this->obbligatorio);
		$this->xmlAdd("indice_tab" , $this->indiceTab);
		$this->xmlAdd("larghezza" , $this->larghezza);
		$this->xmlAdd("altezza" , $this->altezza);
		$this->xmlAdd("sinistra" , $this->sinistra);
		$this->xmlAdd("alto" , $this->alto);
		
		}
	
	//***************************************************************************
	/**
	* @access protected
	* @ignore
	*/	
	protected function xmlAdd($nome, $valore)
		{
		if ($valore === true)
			$valore = 1;
		elseif ($valore === false)
			$valore = 0;
		if (substr($valore, 0, strlen("<![CDATA[")) != "<![CDATA[")
			$valore = htmlspecialchars(($valore));
		$this->modulo->buffer .= "\t\t\t<$nome>$valore</$nome>\n";
		}
		
	//***************************************************************************
	/**
	* @access protected
	* @ignore
	*/	
	protected function xmlClose()
		{
		$this->modulo->buffer .= "\t\t</$this->tipo>\n";
		}
	
	//***************************************************************************
	/**
	* @ignore
	*/	
	function definisciValoreIniziale()
		{
		// se la form e' in bind con un record, ed il record e' valorizzato, prelevo
		// il valore dal record; altrimenti il valore e' quello di default
		if ($this->corrispondenzaDB && $this->modulo->record)
			$this->valore = $this->modulo->record->valore($this->nome);
			
		}
	
	//***************************************************************************
	/**
	* @ignore
	*/	
	function xmlInput()
		{
		$this->modulo->buffer .= "\t\t<$this->tipo.input id='$this->nome'></$this->tipo.input>\n";
		}
	
	//***************************************************************************
	/**
	 * converte il valore proveniente dal post nel valore logico del controllo
	* @ignore
	*/	
	function input2valoreInput($valoreIn)
		{
		if ($valoreIn === "__wamodulo_valore_non_ritornato__")
			$this->inputNonRitornato = true;
		else
			return $this->valoreInput = $valoreIn;
		}
	
	//***************************************************************************
	/**
	* verificaObbligo
	* @ignore
	*
	*/
	function verificaObbligo()
		{
		return !$this->obbligatorio || 
				($this->valoreInput !== null && $this->valoreInput !== '' && $this->valoreInput !== false);
		}

	//***************************************************************************
	/**
	 * salva sul campo del record il valore di input
	* @ignore
	*/	
	function input2record()
		{
		if (!$this->corrispondenzaDB || !$this->modulo->record || $this->inputNonRitornato)
			return;
		$this->modulo->record->inserisciValore($this->nome, $this->valoreInput);
		}
	
	}	// fine classe waControllo
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_CONTROLLO'))
?>