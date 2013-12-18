<?php
include "applicazionetest.inc.php";

//*****************************************************************************
/**
* tabellacorsi 
* 
* questa classe si preoccupera' di mostrare all'utente una tabella di classe 
* {@link waTabella} contenente tutte le amministyrazioni gestite dalla nostra 
* applicazione
* 
* Deriva da {@link applicazioneTest}, dalla quale quindi
* eredita l'interfaccia programmatica (proprieta' e metodi); a questa noi potremo
* aggiungere i nostri metodi applicativi e se necessario modificare il 
* comportamento della classe di default mediante l'override dei metodi.
*/
class tabellaamministrazioni extends applicazioneTest
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
		$this->aggiungiElemento("Tabella amministrazioni", "titolo");
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
		$sql = "SELECT * FROM amministrazioni";
		$tabella = $this->dammiTabella($sql);
		
		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "amministrazioni";
		$tabella->paginaModulo = "moduloamministrazioni.php";
		
		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("id_amministrazione", "ID", true, false, true, WATBL_ALLINEA_DX, WATBL_FMT_INTERO);
		$tabella->aggiungiColonna("NOME", "Nome");
		$tabella->aggiungiColonna("SIGLA", "Sigla");
		$col = $tabella->aggiungiColonna("EMAIL", "E-mail");
			$col->link = true;
		$tabella->aggiungiColonna("TELEFONO", "Telefono");
		
		// se la tabella fosse destinata anche all'input (post o rpc), questo 
		// sarebbe il punto dove chiamare $tabella->leggiValoriIngresso()
			
		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe())
			$this->mostraErroreDB($tabella->righeDB->connessioneDB);
		
		return $tabella;
		}
		
	
	}	// fine classe pagina
	
//*****************************************************************************
// istanzia la pagina
$page = new tabellaamministrazioni();
$page->mostraPagina();

