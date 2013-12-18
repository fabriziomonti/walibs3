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
		$this->aggiungiElemento("Sezioni", "titolo");
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
		$sql = "SELECT wadoc_sezioni.*" .
				" FROM wadoc_sezioni" .
				" WHERE 1";
		if ($_GET['idSezione'])
			$sql .= " AND wadoc_sezioni.idSezione=" . $dbconn->interoSql($_GET['idSezione']);
		$sql .= " ORDER BY sigla";
		$tabella = $this->dammiTabella($sql);
			
		$tabella->titolo = "sezioni";
		
		$tabella->aggiungiAzione("Menu",true);
		$tabella->aggiungiAzione("Pagine", true);
		$tabella->aggiungiAzione("Tabelle", true);
		$tabella->aggiungiAzione("Moduli", true);
		
		$tabella->aggiungiColonna("idSezione", "ID", false, false, false);
		
		$col = $tabella->aggiungiColonna("sigla", "Sigla");
			$col->inputTipo = WATBL_INPUT_TESTO;
			
		$col = $tabella->aggiungiColonna("nome", "Nome");
			$col->inputTipo = WATBL_INPUT_TESTO;
			
		$col = $tabella->aggiungiColonna("descrizione", "Descrizione");
			$col->inputTipo = WATBL_INPUT_AREATESTO;
			
		$col = $tabella->aggiungiColonna("help", "Help");
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

