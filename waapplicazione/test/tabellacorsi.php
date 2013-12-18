<?php
include "applicazionetest.inc.php";

//*****************************************************************************
/**
* tabellacorsi 
* 
* questa classe si preoccupera' di mostrare all'utente una tabella di classe 
* {@link waTabella} contenente tutti i corsi gestiti dalla nostra applicazione
* 
* Deriva da {@link applicazioneTest}, dalla quale quindi
* eredita l'interfaccia programmatica (proprieta' e metodi); a questa noi potremo
* aggiungere i nostri metodi applicativi e se necessario modificare il 
* comportamento della classe di default mediante l'override dei metodi.
*/
class tabellacorsi extends applicazioneTest
	{

	//*****************************************************************************
	/**
	* mostraPagina
	* 
	* costruisce la pagina contenente la tabella e la manda in output
	* @return void
	*/
	function mostraPagina()
		{
		// costruiamo la tabella
		$tabella = $this->creaTabella();
			
		// prepara la pagina, ossia il contenitore della tabella
		$this->aggiungiElemento($this->dammiMenu());
		$this->aggiungiElemento("Tabella corsi", "titolo");
		$this->aggiungiElemento($tabella);
		
		// manda in output l'intera pagina
		$this->mostra();
		}
		
	//*****************************************************************************
	/**
	* creaTabella
	* 
	* costruisce la tabella 
	* 
	* @return waTabella
	*/
	function creaTabella()
		{
		// creazione della tabella sulla base della query sql
		$sql = "SELECT corsi.*," .
				"organismi.id_organismo, organismi.nome as nomeorgan," .
				" amministrazioni.sigla, amministrazioni.nome as nomeamm" .
				" FROM corsi" .
				" INNER JOIN amministrazioni ON corsi.id_amministrazione=amministrazioni.id_amministrazione".
				" INNER JOIN organismi ON corsi.id_organismo=organismi.id_organismo";
		$tabella = $this->dammiTabella($sql);
		
		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "corsi";
		$tabella->paginaModulo = "modulocorsi.php";
		
		// definizione delle azioni della tabella
		$tabella->azioni['Elimina']->funzioneAbilitazione = array($this, "verificaAbilitaElimina");
		$tabella->aggiungiAzione("Test", true);
		if ($_GET["figlia"])
			$tabella->aggiungiAzione("chiudi");
		
		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("id_corso", "ID", true, true, true, WATBL_ALLINEA_DX, WATBL_FMT_INTERO);
		$tabella->aggiungiColonna("rifpa", "Rif. P.A.");
		$col = $tabella->aggiungiColonna("nome", "Corso");
			$col->aliasDi = 'corsi.nome';
		$col = $tabella->aggiungiColonna("id_organismo", "Cod. org.", true, true, true, WATBL_ALLINEA_DX, WATBL_FMT_INTERO);
			$col->aliasDi = 'corsi.id_organismo';
		$col = $tabella->aggiungiColonna("nomeorgan", "Organismo");
			$col->aliasDi = 'organismi.nome';
		$col = $tabella->aggiungiColonna("sigla", "Amministrazione");
			$col->link = true;
		$col = $tabella->aggiungiColonna("data_inizio", "Data inizio", true, true, true, WATBL_ALLINEA_CENTRO);
		$col = $tabella->aggiungiColonna("importo", "Importo", true, true, true, WATBL_ALLINEA_DX);
			$col->totalizza = true;
		$col = $tabella->aggiungiColonna("eliminabile", "Eliminabile", true, false, false, WATBL_ALLINEA_CENTRO);
			$col->funzioneCalcolo = array($this, "mostraEliminabilita");
		$col = $tabella->aggiungiColonna("nomeamm", "nomeamm", false, false, false);
		
		// se la tabella fosse destinata anche all'input (post o rpc), questo 
		// sarebbe il punto dove chiamare $tabella->leggiValoriIngresso()
			
		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe())
			$this->mostraErroreDB($tabella->righeDB->connessioneDB);
		
		return $tabella;
		}
		
	//*****************************************************************************
	/**
	* mostraEliminabilita
	* 
	* callback function da invocare in corrispondenza della visualizzazione di una
	* cella della colonna <b>ELIMINABILE</b>. All'oggetto di classe {@link waTabella}, 
	* infatti, viene detto che per quella colonna non si deve mostrare il contenuto
	* del campo, bensi' il valore di ritorno di questo metodo.
	* 
	* Nel nostro caso specifico, all'interno della cella verra' mostrato <b>si</b> se
	* l'importo del corso e' minore di 100.000 euro; <b>no</b> negli altri casi
	* @return string
	*/
	function mostraEliminabilita(waTabella $tabella)
		{		
		return $tabella->record->valore("importo") <= 100000 ? 'si' : 'no';
		}
	
	//*****************************************************************************
	/**
	* verificaAbilitaElimina
	* 
	* callback function da invocare per l'abilitazione, o meno, di una azione su
	* record in corrispondenza del record corrente durante la costruzione della
	* tabella.
	* 
	* Il valore di ritorno di questo metodo dira' alla tabella se in corrispondenza della visualizzazione di una
	* cella della colonna <b>ELIMINABILE</b>. All'oggetto di classe {@link waTabella}, 
	* infatti, viene detto che per quella colonna non si deve mostrare il contenuto
	* del campo, bensi il valore di ritorno di questo metodo
	* @return string
	*/
	function verificaAbilitaElimina(waTabella $tabella)
		{		
		return $tabella->record->valore("importo") <= 100000;
		}
	
	
	}	// fine classe pagina
	
//*****************************************************************************
// istanzia la pagina
$page = new tabellacorsi();
$page->mostraPagina();
	
