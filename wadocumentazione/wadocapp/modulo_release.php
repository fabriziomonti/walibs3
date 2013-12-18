<?php
include "wadocapp.inc.php";

//*****************************************************************************
/**
*/
class pagina extends waDocApp
	{

	/**
	 * oggetto proprieta'  della pagina che conterra' i dati del modulo
	 * da mostrare all'utente
	 *
	 * @var waModulo
	 */
	var $modulo;
	
	//*****************************************************************************
	/**
	* 
	*/
	function __construct()
		{
		parent::__construct();
		
		// creiamo l'oggetto di classe waModulo e leggiamo l'eventuale input
		// utente/programma: questo ci dira' quale operazione e' stata richiesta
		$this->creaModulo();
		
		// eseguiamo l'operazione a seconda della scelta dell' utente o del
		// programma
		if ($this->modulo->daAggiornare())
			// l'utente ha richiesto update  o insert del record
			$this->aggiornaRecord();
		else
			// non e' stato richiesto di eseguire un'azione, ma semplicemente di
			// mostrare il modulo all'utente ai fini dell'editing
			$this->mostraPagina();
		
		}
		
	//*****************************************************************************
	/**
	* mostraPagina
	* 
	* costruisce la pagina contenente il modulo e la manda in output
	* @return void
	*/
	function mostraPagina()
		{
		$this->aggiungiElemento($this->dammiMenu());
		$this->aggiungiElemento("Dati release", "titolo");
		$this->aggiungiElemento($this->modulo);
		
		// manda in output l'intera pagina
		$this->mostra();
		}
		
	//*****************************************************************************
	/**
	* creaModulo
	* 
	* costruisce  il modulo 
	* @return waModulo
	*/
	function creaModulo()
		{
		// creazione del modulo...
		$this->modulo = $this->dammiModulo();
		
		// creazione del recordset da associare al modulo
		$sql = "SELECT * FROM wadoc_release";
		$this->modulo->righeDB = $this->dammiRigheDB($sql);
		
	 	// inserimento dei controlli all'interno del modulo
		$ctrl = $this->modulo->aggiungiTesto("nomeProcedura", "Nome procedura", false, true);
			$ctrl->valore = $this->datiApplicazione["titolo"];
		$ctrl = $this->modulo->aggiungiTesto("nrRelease", "Rel.", false, true);
			$ctrl->valore = $this->datiApplicazione["versione"];
		$ctrl = $this->modulo->aggiungiData("dataRelease", "Data rel.", false, true);
			$ctrl->valore = $this->datiApplicazione["dataVersione"];
		$ctrl = $this->modulo->aggiungiTesto("autore", "Autore/i", false, true);
		$ctrl = $this->modulo->aggiungiAreaTesto("descrizione", "Descrizione", false);
		$ctrl = $this->modulo->aggiungiAreaTesto("help", "Help", false);
			$ctrl->alto += $this->modulo->interlineaControlli * 20;
			$this->modulo->etichette["help"]->alto = $ctrl->alto;
		
		// inserimento bottoni di submit all'interno del modulo
		$butt = $this->modulo->aggiungiBottone("cmdInvia", "REGISTRA");
		$butt->alto += $this->modulo->interlineaControlli * 20;
		$butt->larghezza = 90;

		// leggiamo eventuali valori di input (se e' stato fatto submit o 
		// chiesta RPC)
		$this->modulo->leggiValoriIngresso();
		}
		
		
	//*****************************************************************************
	/**
	* aggiornaRecord
	* 
	* e' il metodo invocato quando l'utente preme il bottone di submit e che
	* permette l'inserimento o la modifica di un record
	* @return void
	*/
	function aggiornaRecord()
		{
		$this->modulo->verificaObbligo();
		if (!$this->modulo->salva(true))
			$this->mostraErroreDB($this->modulo->righeDB->connessioneDB);
		
		$this->ridireziona("");
		}
	
	
	}	// fine classe pagina
	
//*****************************************************************************
// istanzia la pagina
$page = new pagina();

