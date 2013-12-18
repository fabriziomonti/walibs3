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
		$this->aggiungiElemento("Pagine", "titolo");
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
		$sql = "SELECT wadoc_pagine.*," .
				" wadoc_sezioni.sigla as siglaSezione" .
				" FROM wadoc_pagine" .
				" INNER JOIN wadoc_sezioni ON wadoc_pagine.idSezione=wadoc_sezioni.idSezione" .
				" WHERE 1";
		if ($_GET['idSezione'])
			$sql .= " AND wadoc_pagine.idSezione=" . $dbconn->interoSql($_GET['idSezione']);
		if ($_GET['idPagina'])
			$sql .= " AND wadoc_pagine.idPagina=" . $dbconn->interoSql($_GET['idPagina']);
		$sql .= " ORDER BY siglaSezione, wadoc_pagine.nome";
		$tabella = $this->dammiTabella($sql);
		
		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "pagine";
		
		// azioni
		$tabella->aggiungiAzione("Tabelle", true);
		$tabella->aggiungiAzione("Moduli", true);
		
		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("idPagina", "ID", false, false, false);
		
		$col = $tabella->aggiungiColonna("idSezione", "Sezione", !$_GET['idSezione'], true, true, WATBL_ALLINEA_CENTRO);
			$col->inputOpzioni = $this->dammiLista("wadoc_sezioni", "idSezione", "sigla");
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->aliasDi = "wadoc_sezioni.sigla";

		$col = $tabella->aggiungiColonna("nome", "Nome");
			$col->inputTipo = WATBL_INPUT_TESTO;
			$col->aliasDi = "wadoc_pagine.nome";
			
		$col = $tabella->aggiungiColonna("titolo", "Titolo");
			$col->inputTipo = WATBL_INPUT_TESTO;
			$col->aliasDi = "wadoc_pagine.titolo";
			
		$col = $tabella->aggiungiColonna("descrizione", "Descrizione");
			$col->aliasDi = "wadoc_pagine.descrizione";
			$col->inputTipo = WATBL_INPUT_AREATESTO;
			
		$col = $tabella->aggiungiColonna("help", "Help");
			$col->aliasDi = "wadoc_pagine.help";
			$col->inputTipo = WATBL_INPUT_AREATESTO;
			
		// colonne non visibili
			
		$tabella->leggiValoriIngresso();
		$tabella->aggiungiColonna("siglaSezione", "siglaSezione", false, false, false);

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

