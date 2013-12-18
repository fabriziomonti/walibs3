<?php
include "applicazionetest.inc.php";

//*****************************************************************************
/**
* modulocorsi 
* 
* questa classe si preoccupera' di mostrare all'utente un modulo di classe 
* {@link waModulo} pr la manipolazione di un record corsi gestito dalla 
* nostra applicazione.
* 
* Deriva da {@link applicazioneTest}, dalla quale quindi
* eredita l'interfaccia programmatica (proprieta' e metodi); a questa noi potremo
* aggiungere i nostri metodi applicativi e se necessario modificare il 
* comportamento della classe di default mediante l'override dei metodi.
*/
//*****************************************************************************
class moduloamministrazioni extends applicazioneTest
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
		$this->aggiungiElemento("Modulo amministrazioni", "titolo");
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
	  	$sql = "SELECT * FROM amministrazioni";
	  	if ($_GET['id_amministrazione'])
			// se ci viene passato un identificativo di record restringiamo la
			// ricerca allo specifico corso
	  		$sql .= " WHERE id_amministrazione=" . $dbconn->interoSql($_GET['id_amministrazione']);
	  	else 
			// se non ci viene passato un identificativo di record leggiamo un
			// recordset vuoto (zero righe: solo intestazioni); questo velocizza
			// l'accesso al DB e ritorna le informazioni sui campi da editare
			$nrRighe = 0;

		// lettura del recordset e associazione del recordset al modulo...
		$this->modulo->righeDB = $this->dammiRigheDB($sql, $dbconn, $nrRighe, 0);
		
	 	// inserimento dei controlli all'interno del modulo
		$ctrl = $this->modulo->aggiungiIntero("ID_AMMINISTRAZIONE", "Identificativo", true);
	 	
		$ctrl = $this->modulo->aggiungiTesto("SIGLA", "Sigla", false, true);
		$ctrl = $this->modulo->aggiungiTesto("NOME", "Nome");
		$ctrl = $this->modulo->aggiungiTesto("INDIRIZZO", "Indirizzo");
		$ctrl = $this->modulo->aggiungiIntero("CAP", "CAP");
			$ctrl->larghezza = 40;
		$ctrl = $this->modulo->aggiungiTesto("PROVINCIA", "Provincia");
			$ctrl->larghezza = 20;
		$ctrl = $this->modulo->aggiungiTesto("CITTA", "Citta'");
		$ctrl = $this->modulo->aggiungiEmail("EMAIL", "E-mail");
		$ctrl = $this->modulo->aggiungiTesto("TELEFONO", "Telefono");
			$ctrl->larghezza = 70;
		
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
		
	}	// fine classe pagina
	
//*****************************************************************************
// istanzia la pagina
$page = new moduloamministrazioni();
