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
		$this->aggiungiElemento("Tabelle", "titolo");
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
		$sql = "SELECT wadoc_tabelle.*," .
				" CONCAT(wadoc_sezioni.sigla, '/', IFNULL(wadoc_pagine.titolo, wadoc_pagine.nome)) AS nomePagina" .
				" FROM wadoc_tabelle" .
				" INNER JOIN wadoc_pagine ON wadoc_tabelle.idPagina=wadoc_pagine.idPagina" .
				" INNER JOIN wadoc_sezioni ON wadoc_pagine.idSezione=wadoc_sezioni.idSezione" .
				" WHERE 1";
		if ($_GET['idPagina'])
			$sql .= " AND wadoc_pagine.idPagina=" . $dbconn->interoSql($_GET['idPagina']);
		$sql .= " ORDER BY nomePagina, wadoc_tabelle.nome";
		
		$tabella = $this->dammiTabella($sql);
		
		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "tabelle";
		
		// azioni
		$tabella->aggiungiAzione("Colonne", true);
		$tabella->aggiungiAzione("Azioni", true);
		
		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("idTabella", "ID", false, false, false);

		$col = $tabella->aggiungiColonna("idPagina", "Pagina", !$_GET['idPagina'], true, true, WATBL_ALLINEA_CENTRO); 
			$sql = "SELECT wadoc_pagine.idPagina, CONCAT(wadoc_sezioni.sigla, '/', IFNULL(wadoc_pagine.titolo, wadoc_pagine.nome)) AS nomePagina" .
					" FROM wadoc_pagine" .
					" INNER JOIN wadoc_sezioni on wadoc_pagine.idSezione=wadoc_sezioni.idSezione" .
					" ORDER BY nomePagina";
			$col->inputOpzioni = $this->dammiLista($sql);
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->aliasDi = "CONCAT(wadoc_sezioni.sigla, '/', IFNULL(wadoc_pagine.titolo, wadoc_pagine.nome))";
			
		$col = $tabella->aggiungiColonna("nome", "Nome");
			$col->inputTipo = WATBL_INPUT_TESTO;
			$col->aliasDi = "wadoc_tabelle.nome";
			
		$col = $tabella->aggiungiColonna("titolo", "Titolo");
			$col->inputTipo = WATBL_INPUT_TESTO;
			$col->aliasDi = "wadoc_tabelle.titolo";
			
		$col = $tabella->aggiungiColonna("descrizione", "Descrizione");
			$col->aliasDi = "wadoc_tabelle.descrizione";
			$col->inputTipo = WATBL_INPUT_AREATESTO;
			
		$col = $tabella->aggiungiColonna("help", "Help");
			$col->aliasDi = "wadoc_tabelle.help";
			$col->inputTipo = WATBL_INPUT_AREATESTO;
		
		// colonne non visibili
		$tabella->aggiungiColonna("nomePagina", "nomePagina", false, false, false);
			
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

