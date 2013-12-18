<?php
include "wadocapp.inc.php";

//*****************************************************************************
/**
*/
class pagina extends waDocApp
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
			
		// prepara la pagina, ossia il contenitore della tabella
		$this->aggiungiElemento($this->dammiMenu());
		$this->aggiungiElemento("Tabelle DB", "titolo");
		$this->aggiungiElemento($this->creaTabella());
		
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
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT * FROM wadoc_tabelleDB" .
				" WHERE 1";
		if ($_GET['idTabellaDB'])
			$sql .= " AND wadoc_tabelleDB.idTabellaDB=" . $dbconn->interoSql($_GET['idTabellaDB']);
		$sql .= " ORDER BY nomeDB, nome";
		
		$tabella = $this->dammiTabella($sql);
		
		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "tabelle DB";
		
		// azioni
		$tabella->aggiungiAzione("Campi", true);
		
		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("idTabellaDB", "ID", false, false, false);
		
		$col = $tabella->aggiungiColonna("tipo", "Tipo");
			$col->inputTipo = WATBL_INPUT_TESTO;
			
		$col = $tabella->aggiungiColonna("nomeDB", "Nome DB");
			$col->inputTipo = WATBL_INPUT_TESTO;
			
		$col = $tabella->aggiungiColonna("nome", "Nome tabella");
			$col->inputTipo = WATBL_INPUT_TESTO;
			
		$col = $tabella->aggiungiColonna("descrizione", "Descrizione");
			$col->inputTipo = WATBL_INPUT_AREATESTO;

		$tabella->leggiValoriIngresso();
			
		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe())
			$this->mostraErroreDB($tabella->righeDB->connessioneDB);
		
		return $tabella;
		}
		
	
	}	// fine classe pagina
	
//*****************************************************************************
// istanzia la pagina
$page = new pagina();
$page->mostraPagina();

