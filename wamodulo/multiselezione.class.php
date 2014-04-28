<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_MULTISELEZIONE'))
{
/**
* @ignore
*/
define('_WA_MULTISELEZIONE',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waMultiSelezione *******************************************************
//***************************************************************************
/**
* waMultiSelezione
*
* classe per la gestione di una multiselect, ossia una select all'interno
* della quale e' possibile selezionare piu' di un elemento.
* 
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waMultiSelezione extends waControllo
	{
	/**
	* array dei valori selezionabili.
	*
	* In modo del tutto analogo alla {@link waSelezione} la lista e' un array 
	* associativo in cui la chiave dell'elemento e' l'identificativo della
	* riga (tipicamente un record di una tabella in relazione) e il valore
	* dell'elemento e' la descrizione (text) da porre in corrispondenza della
	* riga.
	*
	* La lista puo' essere creata, in alternativa al passaggio esplicito,
	* a partire da una query da sottoporre al DB: vedi {@link $sql}.
	* @var array
	*/
	var $lista			= array();
	
	/**
	* array dei valori non selezionabili.
	*
	* La lista puo' essere creata, in alternativa al passaggio esplicito,
	* a partire da una query da sottoporre al DB: vedi {@link $sqlNonSelezionabili}.
	* @var array
	*/
	var $listaNonSelezionabili	= array();
	
	/**
	* query SQL per la creazione della lista dei valori selezionabili.
	*
	* Tramite questa query, la classe accedera' al DB e con il risultato della
	* query creera' la lista.
	* Il primo campo sara' la chiave dell'elemento, l'ultimo campo sara'
	* la descrizione dell'elemento.
	* E' possibile definire una query che restituisce piu' di 2 campi; in
	* questo caso, la classe concatenera' tutti i campi, separati da "|" 
	* ad eccezione dell'ultimo (descrizione); questa stringa concatenata
	* diventera' la chiave di ogni elemento. In questo modo e' possibile
	* passare all'applicazione, per ogni riga selezionabile, anche ulteriori
	* informazioni relative alla riga selezionata, oltre all'identificativo
	* univoco.
	* @var string
	*/
	var $sql	= '';
	
	/**
	* query SQL che individua gli elementi selezionati della lista.
	*
	* Tramite questa query, la classe accedera' al DB e con il risultato della
	* query selezionera' gli elementi corrispondenti della lista.
	* 
	* Anche se la lista degli elementi selezionabili ha come chiave un campo
	* composto (identificativo e altri attributi separati da "|"), e' sufficiente
	* (e necessario) passare solo l'identificativo univoco degli elementi 
	* selezionati, senza gli altri eventuali attributi.
	* @var string
	*/
	var $sqlSelezioni	= '';
	
	/**
	* query SQL che individua gli elementi non selezionabili della lista.
	*
	* Tramite questa query, la classe accedera' al DB e con il risultato della
	* query rendera' indisponibili gli elementi corrispondenti della lista.
	* 
	* Anche se la lista degli elementi non selezionabili ha come chiave un campo
	* composto (identificativo e altri attributi separati da "|"), e' sufficiente
	* (e necessario) passare solo l'identificativo univoco degli elementi 
	* non selezionabili, senza gli altri eventuali attributi.
	* @var string
	*/
	var $sqlNonSelezionabili	= '';
	
	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'multiselezione';

	//***************************************************************************
	//***************************************************************************
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	function mostra()
		{

		// se mi viene passata una query dei non selezionaili, costruisco la lista 
		// dei non selezionailisulla base della query
		if (!empty($this->sqlNonSelezionabili))
			$this->listaNonSelezionabili = $this->dammiListaDaDB($this->sqlNonSelezionabili, false);

		// se mi viene passata una query, costruisco la lista sulla
		// base della query
		if (!empty($this->sql))
			$this->lista = $this->dammiListaDaDB($this->sql);
			
		$this->xmlOpen();
		$this->modulo->buffer .= "\t\t\t<valore>\n";
		if (is_array($this->valore))
			{
			foreach ($this->valore as $v)
				{
				$v = htmlspecialchars($v);
				$this->modulo->buffer .= "\t\t\t\t<elemento id='$v'>$v</elemento>\n";
				}
			}
		$this->modulo->buffer .= "\t\t\t</valore>\n";

		$this->modulo->buffer .= "\t\t\t<non_selezionabili>\n";
		if (is_array($this->listaNonSelezionabili))
			{
			foreach ($this->listaNonSelezionabili as $v)
				{
				$v = htmlspecialchars($v);
				$this->modulo->buffer .= "\t\t\t\t<elemento id='$v'>$v</elemento>\n";
				}
			}
		$this->modulo->buffer .= "\t\t\t</non_selezionabili>\n";

		$this->modulo->buffer .= "\t\t\t<lista>\n";
		foreach ($this->lista as $k => $v)
			{
			$v = htmlspecialchars($v);
			$this->modulo->buffer .= "\t\t\t\t<elemento id='$k'>$v</elemento>\n";
			}
		$this->modulo->buffer .= "\t\t\t</lista>\n";
		
		$this->xmlClose();
		}

	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	function dammiListaDaDB($sql, $estesa = true)
		{

		// se mi viene passata una query dei selezionati, costruisco la lista 
		// dei selezionati sulla base della query

		if (empty($this->modulo->righeDB))
			// se l'applicazione non ha messo in bind un recordset alla form, non
			// abbiamo le informazioni per connetterci al db
			return (array());

		
		$rs = new waRigheDB($this->modulo->righeDB->connessioneDB);
		$righe = $rs->caricaDaSql($sql);
		if ($rs->nrErrore())
			return (array());

		$list = array();
		foreach ($righe as $riga)
			{
			if ($estesa)
				{
				// costruisco la chiave dell'elemento; la chiave e' composta da tutti i campi
				// ritornati dalla query, separati da "|", ad eccezione dell'ultimo che e' la
				// descrizione da mettere a video. Nella grande maggioranza dei casi avro' solo
				// 2 campi: la chiave di relazione e la descrizione. Gli altri campi, se presenti,
				// servono a ritornare attributi del record selezionato all'applicazione
				$key = $riga->valore(0);
				for ($i = 1; $i < ($rs->nrCampi() - 1); $i++)
					$key .= "|" . $riga->valore($i);
				$list[$key] = $riga->valore($rs->nrCampi() - 1);
				}
			else
				// prendiamo solo il primo campo che deve essere la chiave del record
				// da selezionare
				$list[] = $riga->valore(0);
			}

		return $list;
		}


	//***************************************************************************
	/**
	* @ignore
	*/	
	function definisciValoreIniziale()
		{
		// se mi viene passata una query dei selezionati, costruisco la lista 
		// dei selezionati sulla base della query
		if ($this->sqlSelezioni)
			$this->valore = $this->dammiListaDaDB($this->sqlSelezioni, false);
		}
	
	//***************************************************************************
	/**
	 * converte il valore proveniente dal post nel valore logico del controllo
	* @ignore
	*/	
	function input2valoreInput($valoreIn)
		{
		$this->valoreInput = array();
		
		if ($valoreIn === "__wamodulo_valore_non_ritornato__")
			$this->inputNonRitornato = true;
		elseif (is_array($valoreIn))
			$this->valoreInput = $valoreIn;
		elseif ($valoreIn)
			$this->valoreInput[0] = $valoreIn;
		
		return $this->valoreInput;
		}
	
	//***************************************************************************
	/**
	* verificaObbligo
	* @ignore
	*
	*/
	function verificaObbligo()
		{
		return !$this->obbligatorio || !empty($this->valoreInput);
		}

	}	// fine classe waMultiSelezione
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_MULTISELEZIONE'))
?>