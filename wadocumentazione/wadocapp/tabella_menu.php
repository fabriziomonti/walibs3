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
		$this->aggiungiElemento("Menu", "titolo");
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
		$sql = "SELECT wadoc_menu.*," .
				" wadoc_sezioni.sigla AS siglaSezione" .
				" FROM wadoc_menu" .
				" LEFT JOIN wadoc_sezioni ON wadoc_menu.idSezione=wadoc_sezioni.idSezione" .
				" WHERE 1";
		if ($_GET['idSezione'])
			$sql .= " AND wadoc_menu.idSezione=" . $dbconn->interoSql($_GET['idSezione']);
		
		$tabella = $this->dammiTabella($sql);
		$tabella->titolo = "menu";
		
		$tabella->aggiungiAzione("Voci", true);

		$tabella->aggiungiColonna("idMenu", "ID", false, false, false);
		
		$col = $tabella->aggiungiColonna("idSezione", "Sezione", !$_GET['idSezione'], true, true, WATBL_ALLINEA_CENTRO);
			$col->inputOpzioni = $this->dammiLista("wadoc_sezioni", "idSezione", "sigla");
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->aliasDi = "wadoc_sezioni.sigla";
			
		$col = $tabella->aggiungiColonna("nome", "Nome");
			$col->aliasDi = "wadoc_menu.nome";
			$col->inputTipo = WATBL_INPUT_TESTO;
			
		$col = $tabella->aggiungiColonna("titolo", "Titolo");
			$col->aliasDi = "wadoc_menu.titolo";
			$col->inputTipo = WATBL_INPUT_TESTO;
			
		$col = $tabella->aggiungiColonna("descrizione", "Descrizione");
			$col->aliasDi = "wadoc_menu.descrizione";
			$col->inputTipo = WATBL_INPUT_AREATESTO;
			
		$col = $tabella->aggiungiColonna("help", "Help");
			$col->aliasDi = "wadoc_menu.help";
			$col->inputTipo = WATBL_INPUT_AREATESTO;

		// colonne non visibili
		$tabella->aggiungiColonna("siglaSezione", "siglaSezione", false, false, false);

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

