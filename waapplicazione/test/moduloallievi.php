<?php
include "applicazionetest.inc.php";

//*****************************************************************************
/**
* moduloallievi 
* 
* questa classe si preoccupera' di mostrare all'utente un modulo di classe 
* {@link waModulo} pr la manipolazione di un record allievi gestito dalla 
* nostra applicazione.
* 
* Deriva da {@link applicazioneTest}, dalla quale quindi
* eredita l'interfaccia programmatica (proprieta' e metodi); a questa noi potremo
* aggiungere i nostri metodi applicativi e se necessario modificare il 
* comportamento della classe di default mediante l'override dei metodi.
*/
//*****************************************************************************
class moduloallievi extends applicazioneTest
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
		if ($this->modulo->daAnnullare())
			// l'utente ha richiesto abort dell'editing
			$this->ritorna();
		elseif ($this->modulo->daEliminare())
			// l'utente o il programma hanno richiesto l'eliminazione del record 
			// in editing o comunque di quello passato come parametro
			$this->eliminaRecord();
		elseif ($this->modulo->daAggiornare())
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
		$this->aggiungiElemento("Modulo allievi", "titolo");
		$this->aggiungiElemento($this->modulo);
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
		$dbconn = $this->dammiConnessioneDB();
	  	$sql = "SELECT * FROM allievi";
	  	if ($_GET['id_allievo'])
			// se ci viene passato un identificativo di record restringiamo la
			// ricerca allo specifico allievo
	  		$sql .= " WHERE id_allievo=" . $dbconn->interoSql($_GET['id_allievo']);
	  	else 
			// se non ci viene passato un identificativo di record leggiamo un
			// recordset vuoto (zero righe: solo intestazioni); questo velocizza
			// l'accesso al DB e ritorna le informazioni sui campi da editare
			$nrRighe = 0;

		// lettura del recordset e associazione del recordset al modulo...
		$this->modulo->righeDB = $this->dammiRigheDB($sql, $dbconn, $nrRighe, 0);
		
	 	// inserimento dei controlli all'interno del modulo
		$ctrl = $this->modulo->aggiungiIntero("id_allievo", "Identificativo", true);
		$ctrl = $this->modulo->aggiungiSelezione("id_corso", "Corso", false, true);
			$ctrl->lista = $this->dammiLista("corsi", "id_corso", "nome");
	 	
		
		$ctrl = $this->modulo->aggiungiTesto("NOME", "Nome", false, true);
		$ctrl = $this->modulo->aggiungiTesto("INDIRIZZO", "Indirizzo");
		$ctrl = $this->modulo->aggiungiIntero("CAP", "CAP");
		$ctrl = $this->modulo->aggiungiTesto("PROVINCIA", "Provincia");
			$ctrl->larghezza = 20;
		$ctrl = $this->modulo->aggiungiTesto("CITTA", "Città");
		$ctrl = $this->modulo->aggiungiTesto("CODICE_FISCALE", "Codice fiscale");
		$ctrl = $this->modulo->aggiungiPassword("PASSWORD", "Password");
		$ctrl = $this->modulo->aggiungiDataOra("DATAORA_COLLOQUIO", "Data/Ora colloquio");
			$ctrl->valore = time();
		$ctrl = $this->modulo->aggiungiLogico("FLAG_AMMISSIONE", "Ammesso");
		$ctrl = $this->modulo->aggiungiLogico("FLAG_PROMOZIONE", "Promosso");

		$ctrl = $this->modulo->aggiungiCaricaFile("NOME_FILE_CURRICULUM", "Curriculum");
		if (!empty($this->modulo->righeDB->righe))
			$ctrl->paginaVisualizzazione = "docs/" . $this->modulo->righeDB->righe[0]->valore("NOME_FILE_CURRICULUM");
		
		// inserimento bottoni di submit all'interno del modulo
		$this->aggiungiBottoniModulo($this->modulo);		

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
		$this->salvaDoc($this->modulo->controlliInput["NOME_FILE_CURRICULUM"]);
		if (!$this->modulo->salva(true))
			$this->mostraErroreDB($this->modulo->righeDB->connessioneDB);
		
		$this->ritorna();
		}
	
	//*****************************************************************************
	/**
	* eliminaRecord
	* 
	* e' il metodo invocato quando l'utente preme il bottone di submit con
	* richiesta di cancellazione del record, oppure quando viene richiesta la 
	* cancellazione del record a partire da una azione su riga di waTabella
	* @return void
	*/
	function eliminaRecord()
		{
		if (!$this->modulo->elimina(true))
			$this->mostraErroreDB($this->modulo->righeDB->connessioneDB);
		
		$this->ritorna();
		}
		
	//***************************************************************************** 
	// salva l'eventuale documento allegato
	//***************************************************************************** 
	function salvaDoc(waCaricaFile $ctrl)
		{
		// salvataggio del documento (se c'e'...)
		if ($ctrl->daEliminare())
			// cancelliamo un eventuale documento esistente
			@unlink("docs/$ctrl->valore");
		elseif ($ctrl->erroreCaricamento())			
	    	$this->mostraMessaggio("Errore caricamento file", 
	    						"Si e' verificato l'errore " . 
	    						$ctrl->erroreCaricamento() . 
	    						" durante il caricamento del documento $ctrl->nome." .
	    						" Si prega di avvertire l'assistenza tecnica.", 
    							false, true);
		elseif ($ctrl->daSalvare())
			{
			@unlink("docs/$ctrl->valore");
			if (!$ctrl->salvaFile("docs/$ctrl->valoreInput"))
	    		$this->mostraMessaggio("Errore spostamento file", 
	    						"Si e' verificato un errore " . 
	    						" durante lo spostamento del documento $ctrl->nome." .
	    						" Si prega di avvertire l'assistenza tecnica.", 
    							false, true);
			}
			
		return true;			
		}
	
	}	// fine classe pagina
	
//*****************************************************************************
// istanzia la pagina
$page = new moduloallievi();

