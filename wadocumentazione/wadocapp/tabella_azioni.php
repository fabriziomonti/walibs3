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
		$this->aggiungiElemento("Azioni tabelle", "titolo");
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
		$sql = "SELECT wadoc_azioni.*," .
				" CONCAT(wadoc_sezioni.sigla, '/', IFNULL(wadoc_pagine.titolo, wadoc_pagine.nome), '/', IFNULL(wadoc_tabelle.titolo, wadoc_tabelle.nome)) as nomeTabella" .
				" FROM wadoc_azioni" .
				" INNER JOIN wadoc_tabelle ON wadoc_azioni.idTabella=wadoc_tabelle.idTabella" .
				" INNER JOIN wadoc_pagine ON wadoc_tabelle.idPagina=wadoc_pagine.idPagina" .
				" INNER JOIN wadoc_sezioni ON wadoc_pagine.idSezione=wadoc_sezioni.idSezione" .
				" WHERE 1";
		if ($_GET['idTabella'])
			$sql .= " AND wadoc_azioni.idTabella=" . $dbconn->interoSql($_GET['idTabella']);
		$sql .= " ORDER BY nomeTabella, wadoc_azioni.posizione";
		$tabella = $this->dammiTabella($sql);
		
		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "azioni tabelle";
		
		// azioni
		
		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("idAzione", "ID", false, false, false);

		$col = $tabella->aggiungiColonna("idTabella", "Tabella", !$_GET['idTabella'], true, true, WATBL_ALLINEA_CENTRO);
			$sql = "SELECT wadoc_tabelle.idTabella, CONCAT(wadoc_sezioni.sigla, '/', IFNULL(wadoc_pagine.titolo, wadoc_pagine.nome), '/', IFNULL(wadoc_tabelle.titolo, wadoc_tabelle.nome)) as nomeTabella" .
					" FROM wadoc_tabelle" .
					" INNER JOIN wadoc_pagine ON wadoc_tabelle.idPagina=wadoc_pagine.idPagina" .
					" INNER JOIN wadoc_sezioni on wadoc_pagine.idSezione=wadoc_sezioni.idSezione" .
					" ORDER BY nomeTabella";
			$col->inputOpzioni = $this->dammiLista($sql);
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->aliasDi = "CONCAT(wadoc_sezioni.sigla, '/', IFNULL(wadoc_pagine.titolo, wadoc_pagine.nome), '/', IFNULL(wadoc_tabelle.titolo, wadoc_tabelle.nome))";
		
		$col = $tabella->aggiungiColonna("etichetta", "Etichetta");
			$col->inputTipo = WATBL_INPUT_TESTO;
			$col->aliasDi = "wadoc_azioni.etichetta";
			
		$col = $tabella->aggiungiColonna("suRecord", "Su record");
			$col->inputTipo = WATBL_INPUT_LOGICO;
			$col->aliasDi = "wadoc_azioni.suRecord";
			$col->allineamento = WATBL_ALLINEA_CENTRO;
			
		$col = $tabella->aggiungiColonna("condizionata", "Condizionata");
			$col->inputTipo = WATBL_INPUT_LOGICO;
			$col->aliasDi = "wadoc_azioni.condizionata";
			$col->allineamento = WATBL_ALLINEA_CENTRO;
			
		$col = $tabella->aggiungiColonna("posizione", "Posizione");
			$col->inputTipo = WATBL_INPUT_INTERO;
			$col->aliasDi = "wadoc_azioni.posizione";

		$col = $tabella->aggiungiColonna("descrizione", "Descrizione");
			$col->aliasDi = "wadoc_azioni.descrizione";
			$col->inputTipo = WATBL_INPUT_AREATESTO;
			
		$col = $tabella->aggiungiColonna("help", "Help");
			$col->aliasDi = "wadoc_azioni.help";
			$col->inputTipo = WATBL_INPUT_AREATESTO;
			
		// colonne non visibili
			
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

