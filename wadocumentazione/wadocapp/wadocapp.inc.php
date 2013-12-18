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
include_once (dirname(__FILE__) . "/../../waapplicazione/waapplicazione.inc.php");


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
class waDocApp extends waApplicazione
	{
	// file di configurazione della connessione al db dell'applicazione che
	// deve esserci passato in sessione
	var $fileConfigDB;
	
	// directory di output che deve esserci passato in sessione
	var $dirOutput;
	
	// dati dell'applicazione da documentare che devono esserci passato in sessione
	var $datiApplicazione;
	
	// directory che contiene i fogli xslt di output
	var $dirXlstOut;
	
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
		$this->nome = "wadocapp";
		$this->titolo = 'Gestione documentazione';
		$this->versione = "3.0";
		$this->dataVersione = mktime(0,0,0, 1, 15, 2013);
		
		$this->inizializza();

		// DEVO trovare in sessione un parametro di nome wadoc_file_dbconfig,
		// altrimenti significa che sono stato avviato alla boia d'un giuda
		if (!is_file($_SESSION["waDocumentazione"]["fileConfigurazioneDB"]) ||
			!is_readable($_SESSION["waDocumentazione"]["fileConfigurazioneDB"]))
			exit();
		
		$this->fileConfigDB = $_SESSION["waDocumentazione"]["fileConfigurazioneDB"];
		include $_SESSION["waDocumentazione"]["nomeFileConfigurazione"];
		$this->dirOutput = $WADOC_DIR_DEST;
		$this->dirXlstOut = dirname(__FILE__) . "/ui/xslt.out";
		$this->datiApplicazione = $_SESSION["waDocumentazione"]["datiApplicazione"];
		}
	
	//*****************************************************************************
	/**
	* costruisce e manda in output il menu dell'applicazione 
	* 
	* @return void
	*/
	function dammiMenu()
	    {
	
		$m = new waMenu();
		$m->nome = "wadoc_menu";
		$m->apri();
		$m->apriSezione("Release");
			$m->aggiungiVoce("Dati release", "modulo_release.php");
			$m->aggiungiVoce("Allegati", "tabella_allegati.php");
		$m->chiudiSezione();
		$m->apriSezione("DB");
			$m->aggiungiVoce("Tabelle",  "tabella_tabelledb.php");
			$m->aggiungiVoce("Campi", "tabella_campi.php");
		$m->chiudiSezione();
		$m->apriSezione("Sezioni", "tabella_sezioni.php");
		$m->chiudiSezione();
		$m->apriSezione("Menu");
			$m->aggiungiVoce("Menu", "tabella_menu.php");
			$m->aggiungiVoce("Voci", "tabella_vocimenu.php");
		$m->chiudiSezione();
		$m->apriSezione("Pagine", "tabella_pagine.php");
		$m->chiudiSezione();
		$m->apriSezione("Tabelle applicative");
			$m->aggiungiVoce("Tabelle", "tabella_tabelle.php");
			$m->aggiungiVoce("Colonne", "tabella_colonne.php");
			$m->aggiungiVoce("Azioni", "tabella_azioni.php");
		$m->chiudiSezione();
		$m->apriSezione("Moduli");
			$m->aggiungiVoce("Moduli", "tabella_moduli.php");
			$m->aggiungiVoce("Controlli", "tabella_controlli.php");
		$m->chiudiSezione();
		$m->apriSezione("Stampa");
//			$m->aggiungiVoce("HTML modificabile", "tabella_stampa_html_modificabile");
			$m->aggiungiVoce("HTML", "stampa_html.php");
//			$m->aggiungiVoce("PDF", "stampa_pdf.php");
		$m->chiudiSezione();
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
		$tabella = new waTabella($sql, $this->fileConfigDB);
		$tabella->xslt = dirname(__FILE__) . "/ui/xslt/watabella.xsl";
		$tabella->applicazione = $this;
		
//		$tabella->eliminaAzione("Nuovo");
		$tabella->eliminaAzione("Vedi");
		if ($_GET['nomepaginaritorno'])
			$tabella->aggiungiAzione ("Torna");

		return $tabella;
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
		$modulo->xslt = dirname(__FILE__) . "/ui/xslt/wamodulo.xsl";
		$modulo->larghezza = 800;
		return $modulo;
	    }
	
	//*****************************************************************************
	/**
	* restituisce una lista da associare a un controllo di classe {@link waSelezione}
	* 
	* parendo da una tabella di database, restituisce un array associativo in cui 
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
	function dammiLista($sql_or_table, $idFieldName = '', $descriptionFieldName = '')
		{
		if (stripos($sql_or_table, "SELECT") === 0)
			{
			$sql = $sql_or_table;
			$idFieldName = 0;
			$descriptionFieldName = 1;
			}
		else
			$sql = "SELECT * FROM $sql_or_table ORDER BY $descriptionFieldName";
		$righeDB = $this->dammiRigheDB($sql, $dbconn);
		
		$retval = array("", "");
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
	function aggiungiBottoniModulo(waModulo $modulo, $elimina = false)
		{
		$butt = $modulo->aggiungiBottone("cmdInvia", "REGISTRA");
		$butt->alto += $modulo->interlineaControlli * 2;
		$butt->larghezza = 90;
	  	if ($modulo->righeDB->righe && $elimina)
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
	/**
	* manda in output la pagina
	* 
	* manda in output la pagina, compresi gli elementi aggiunti alla pagina 
	* tramite il metodo {@link aggiungiElemento}, utilizzando il foglio di 
	* stile indicato nella proprietà {@link xslt}.
	* @param boolean $bufferizza se false, allora viene immediatamente effettuato 
	* l'output della pagina; altrimenti la funzione ritorna il buffer di output 
	* @return void|string
	*/
	function mostra($bufferizza = false)
		{
		if ($_GET['nomepaginaritorno'])
			$this->aggiungiElemento ($_GET['nomepaginaritorno'], "nome_pagina_ritorno");
		
		$this->xslt = dirname(__FILE__) . "/ui/xslt/waapplicazione.xsl";
		parent::mostra($bufferizza);
		}
		
//***************************************************************************
	} 	// fine classe application
	
//***************************************************************************

//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_APPLICATION_CLASS'))

