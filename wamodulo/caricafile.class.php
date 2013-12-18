<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
if (!defined('_WA_CARICAFILE'))
{
/**
* @ignore
*/
define('_WA_CARICAFILE',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waCaricaFile *******************************************************
//***************************************************************************
/**
* waCaricaFile
*
* classe per la gestione di un controllo complesso che permette di:
* - caricare un file (upload)
* - scaricare/vedere inline il file caricato
* - sostituire un file
* - eliminare un file precedentemente caricato
 * 
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waCaricaFile extends waControllo
	{
	
	/**
	* URL per visualizzazione file
	*
	* e' l'URL di una pagina tramite cui e' possibile 
	* prendere visione del file
	* Se valorizzato, dovra' anche contenere tutti i parametri per accedere 
	* al file corretto (la classe non aggiunge nulla).
	* @var string
	*/
	var $paginaVisualizzazione		=	"";
	
	/**
	* indica se visualizzare il nome del file precedentemente caricato
	*
	* Del file viene ovviamente mostrato solamente il basename.
	* @var boolean
	*/
	var $mostraNomeFile	= 	true;
	
	/**
	* size del file in Kb
	*
	* Se valorizzato, viene mostrato il size del file accanto al nome 
	* del file precedentemente caricato.
	* @var integer
	*/
	var $dimensioniFile		=	0;

	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'caricafile';
	
	/**
	* - 
	*
	* array destinato a contenere i valori di input del modulo, valorizzato
	* a fronte di chiamata a {@link waModulo::leggiValoriIngresso}
	* @ignore
	* @var array
	*/	
	var $input	= null;
	
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	function mostra()
		{

		// se non e' stato specificato un width, facciamo che e' la meta' dello spazio
		// desintato al campo all'interno della form...
		if (empty($this->larghezza))
			$this->larghezza = intval(($this->modulo->larghezza - $this->sinistra) / 2);

		$this->xmlOpen();
		$this->xmlAdd("valore", $this->valore);
		$this->xmlAdd("pagina_visualizzazione", $this->paginaVisualizzazione);
		$this->xmlAdd("dimensioni_file", $this->dimensioniFile);
		$this->xmlAdd("basename", basename($this->valore));
		
		$this->xmlClose();
			
		}

	//****************************************************************************************
	/**
	* Restituisce il basename del file. 
	* 
	* Attenzione:
	*
	* o se e' stata richiesta la eliminazione, torna una stringa vuota
	* o se si e' verificato un errore durante il caricamento torna false
	* o se il campo non e' stato valorizzato dall'utente torna il valore con cui il controllo e' stato inizializzato in fase di creazione, sia esso derivante da DB o valore di default (ossia: il valore non deve cambiare)
	* o se non si e' verificato nessuno dei casi precedenti, ritorna il basename del file selezionato dall'utente
	*
	* @ignore
	* @return mixed il timestamp della data se valorizzata correttamente; altrimenti FALSE
	*/
	function input2valoreInput($valoreIn)
		{
		if ($valoreIn === "__wamodulo_valore_non_ritornato__")
			{
			$this->inputNonRitornato = true;
			return null;
			}
			
		// ci salviamo i valori dell'array perche' poi ci serviranno per il salvataggio del file
		// e altrimenti vanno persi
		$this->input = $valoreIn;
		if ($valoreIn['elimina'])
			return $this->valoreInput = '';
		if ($valoreIn['error'] != 0 && $valoreIn['error'] != 4)
			return $this->valoreInput = false;
		if (!$valoreIn['name'])
			// se non vogliona l'eliminazione del file e il nome e' vuoto allora il valore rimane quello di partenza
			return $this->valoreInput = $this->valore;
			
		return $this->valoreInput = $valoreIn['name'];
		}

	//****************************************************************************************
	/**
	* Restituisce il path del file temporaneo caricato dall'utente
	*
	* Si usa in fase di ricezione dei dati, non durante il display del modulo.
	*
	* @return string 
	*/
	function dammiValoreTmp()
		{
		return $this->input['tmp_name'];
		}
		
	//****************************************************************************************
	/**
	* Restituisce true se l'utente ha richiesto la eliminazione del file esistente
	*
	* Si usa in fase di ricezione dei dati, non durante il display del modulo.
	*
	* @return boolean 
	*/
	function daEliminare()
		{
		return $this->input['elimina'];

		}
		
	//****************************************************************************************
	/**
	* Restituisce true se c'e' effettivamente qualcosa da salvare
	*
	* Si usa in fase di ricezione dei dati, non durante il display del modulo.
	* 
	* @return boolean 
	*/
	function daSalvare()
		{
		return $this->input['tmp_name'] ? true : false;

		}
		
	//****************************************************************************************
	/**
	* salva il file nella destinazione richiesta
	*
	* Si usa in fase di ricezione dei dati, non durante il display del modulo.
	*
	* @param string $destinazione puo' essere sia un nome completo di file, sia il nome di una 
	* directory; in questo secondo caso, il nome del file sara' quello del file temporaneo
	* attribuito dall'engine PHP
	* @return boolean true = ok
	*/
	function salvaFile($destinazione)
		{
		return move_uploaded_file($this->input['tmp_name'], $destinazione);
		}
		
	//****************************************************************************************
	/**
	* ritorna il codice di errore avvenuto durante il caricamento o false in caso di nessun errore
	*
	* Si usa in fase di ricezione dei dati, non durante il display del modulo.
	*
	* @return mixed false in caso di nessun errore; altrimenti il codice errore generato da PHP
	* durante il caricamento
	*/
	function erroreCaricamento()
		{
		return $this->input['error'] == 0 || $this->input['error'] == 4 ? false : $this->input['error'];
		}

	}	// fine classe waCaricaFile
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_CARICAFILE'))
?>