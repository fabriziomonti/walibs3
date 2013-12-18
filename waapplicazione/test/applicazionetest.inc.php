<?php
error_reporting(E_ERROR|E_WARNING);

if (!defined('_APPLICATION_CLASS'))
{
/**
* @ignore
*/
define('_APPLICATION_CLASS',1);

/**
* @ignore
*/
include_once (dirname(__FILE__) . "/config.inc.php");
/**
* @ignore
*/
include_once (dirname(__FILE__) . "/../waapplicazione.inc.php");


//*****************************************************************************
/**
* applicazionetest 
* 
* questa classe conterra' tutte le proprieta' e i metodi comuni della nostra 
* specifica applicazione. Deriva da {@link waApplicazione}, dalla quale quindi
* eredita l'interfaccia programmatica (proprieta' e metodi); a questa noi potremo
* aggiungere i nostri metodi applicativi e se necessario modificare il 
* comportamento della classe di default mediante l'override dei metodi.
*/
class applicazionetest extends waApplicazione
	{
	// file di configurazione della connessione al db dell'applicazione
	var $fileConfigDB;
	
	// struttura in cui salvare le preferenze di navigazione (ma volendo anche
	// altro) dell'utente
	var $preferenzeUtente;
	
	//*****************************************************************************
	/**
	* costruttore dell'applicazione 
	* 
	* in questo caso l'inizializzazione e' annegata nel costruttore, ma sono
	* frequenti i casi in cui e' meglio tenere separate le due azioni
	* @return void
	*/
	function __construct()
		{
		$this->nome = APPL_NOME;
		$this->titolo = APPL_TITOLO;
		$this->versione = APPL_REL;
		$this->dataVersione = APPL_REL_DATA;
		$this->serverSmtp = APPL_SMTP_SERVER;
		$this->emailSupporto = APPL_INDIRIZZO_ASSISTENZA;
		$this->emailInfo = APPL_INDIRIZZO_INFO;
		
		
		$this->fileConfigDB = dirname(__FILE__) . "/dbconfig.inc.php";
		
		$this->inizializza();
		
		$this->preferenzeUtente = & $_SESSION['preferenze_utente'];
		$this->modalitaNavigazione = $this->preferenzeUtente['navigazione'] ? 
										$this->preferenzeUtente['navigazione'] :
										WAAPPLICAZIONE_NAV_PAGINA;
		
		if (defined("APPL_GENERA_WADOC") && APPL_GENERA_WADOC)
			$this->waDoc = new waDocumentazione($this, $this->fileConfigDB, APPL_WADOC_CONFIG);
		if ($this->waDoc && $_GET['APPL_AVVIA_WADOC'])
			$this->waDoc->avviaGestione();
		}
	
	//*****************************************************************************
	/**
	* costruisce e manda in output il menu dell'applicazione 
	* 
	* @return waMenu
	*/
	function dammiMenu()
	    {
	
		$m = new waMenu();
		$m->apri();
		
		$m->apriSezione("amministrazioni", "tabellaamministrazioni.php");
		$m->chiudiSezione();
		$m->apriSezione("organismi", "tabellaorganismi.php");
		$m->chiudiSezione();
		$m->apriSezione("corsi", "tabellacorsi.php");
		$m->chiudiSezione();
		$m->apriSezione("allievi", "tabellaallievi.php");
		$m->chiudiSezione();
		$m->apriSezione("navigazione");
			$m->aggiungiVoce("nella stessa pagina", "index.php?navigazione=" . WAAPPLICAZIONE_NAV_PAGINA);
			$m->aggiungiVoce("a finestre", "index.php?navigazione=" . WAAPPLICAZIONE_NAV_FINESTRA);
			$m->aggiungiVoce("iframe", "index.php?navigazione=" . WAAPPLICAZIONE_NAV_INTERNA);
		$m->chiudiSezione();
		if ($this->waDoc)
			{
			$m->apriSezione("waDoc", "?APPL_AVVIA_WADOC=1");
			$m->chiudiSezione();
			}

		$m->chiudi();
		return $m;
		
	    }
	    
	//***************************************************************************
	/**
	* - 
	* 
	* restiuisce una connessione al database oppure, in caso di errore, termina 
	* l'esecuzione dello script invocando il metodo {@link mostraErroreDB}
	* 
	* @return waConnessioneDB
	*/
	function dammiConnessioneDB()
		{
		$connessioneDB = wadb_dammiConnessione($this->fileConfigDB);
		if ($connessioneDB->nrErrore())
			$this->mostraErroreDB($connessioneDB);
		return $connessioneDB;
		}		
	
	//*****************************************************************************
	/**
	* restituisce una tabella standard dell'applicazione
	* 
	* questo metodo e' molto comodo in una applicazione, perche' permette di:
	* - definire in un unico punto gli attributi di TUTTE le tabelle dell'applicazione
	* - qualora in fase di lavorazione si decidesse di non utilizzare direttamente la classe {@link waTabella}, bensi' una sua derivata, e' sufficiente intervenire in questo unico punto dell'applicazione, anziche' in tutte le pagine che contengono una tabella
	* 
	* @param string $sql stringa sql che generera' il recordset che popolera'
	* la tabella
	* @return waTabella
	*/
	function dammiTabella($sql)
	    {
		$table = new waTabella($sql, $this->fileConfigDB);
		
		$table->eliminaAzione("Vedi");
		return $table;
	    }
	
	//*****************************************************************************
	/**
	* restituisce un modulo standard dell'applicazione
	* 
	* questo metodo e' molto comodo in una applicazione, perche' permette di:
	* - definire in un unico punto gli attributi di TUTTI i moduli dell'applicazione
	* - qualora in fase di lavorazione si decidesse di non utilizzare direttamente la classe {@link waModulo}, bensi' una sua derivata, e' sufficiente intervenire in questo unico punto dell'applicazione, anziche' in tutte le pagine che contengono un modulo
	* 
	* @return waModulo
	*/
	function dammiModulo()
	    {
		$modulo = new waModulo(null, $this);
		$modulo->larghezza = 800;
		return $modulo;
	    }
	
	//*****************************************************************************
	/**
	* restituisce una lista da associare a un controllo di classe {@link waSelezione}
	* 
	* partendo da una tabella di database, restituisce un array associativo in cui 
	* la chiave e' l'dentificativo del record e il valore la descrizione del record.
	* 
	* Tipicamente questo array viene associato ad un controllo di classe {@link waSelezione}
	* all'interno di un modulo nel quale occorre scegliere il valore di un campo
	* foreign-key
	* @param string $table nome della tabella del db
	* @param string $idFieldName nome del campo identificativo univoco del record
	* @param string $descriptionFieldName nome del campo che contiene una descrizione umanamente conprensibile del record
	* @return array
	*/
	function dammiLista($table, $idFieldName, $descriptionFieldName)
		{
		$sql = "SELECT * FROM $table ORDER BY $descriptionFieldName";
		$righeDB = $this->dammiRigheDB($sql, $dbconn, $nrRighe, 0);
		
		$retval = array();
		foreach ($righeDB->righe as $riga)
			$retval[$riga->valore($idFieldName)] = $riga->valore($descriptionFieldName);
			
		return $retval;
		}
	
	//*****************************************************************************
	/**
	* definisce la bottoniera di submit standard di un modulo
	* 
	* tipicamente, ogni modulo di una applicazione ha una bottoniera con azioni
	* ricorrenti (salva, elimina, annulla, ecc.). La chiamata a questo metodo
	* definisce la bottoniera standard per questa applicazione.
	* 
	* @param waModulo $modulo modulo nel quale i bottoni andranno inseriti
	* 
	* @return void
	*/
	//*****************************************************************************
	function aggiungiBottoniModulo(waModulo $modulo)
		{
		$butt = $modulo->aggiungiBottone("cmdInvia", "REGISTRA");
		$butt->alto += $modulo->interlineaControlli * 2;
		$butt->larghezza = 90;
	  	if ($modulo->righeDB->righe)
	  		{
			$butt2 = $modulo->aggiungiBottone("cmdElimina", "ELIMINA");
			$butt2->alto = $butt->alto;
			$butt2->sinistra = $butt->sinistra + $butt->larghezza;
			$butt2->larghezza = $butt->larghezza;
			$butt2->elimina = true;
	  		}
		$butt3 = $modulo->aggiungiBottone("cmdAnnulla", "ABBANDONA");
		$butt3->alto = $butt->alto;
		$butt3->sinistra = $butt->sinistra + $butt->larghezza + ($butt2 ? $butt2->larghezza : 0);
		$butt3->larghezza = $butt->larghezza;
		$butt3->annulla = true;
		
		}
	
	    
//***************************************************************************
	} 	// fine classe application
	
//***************************************************************************

//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_APPLICATION_CLASS'))

