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
		$this->aggiungiElemento("Colonne", "titolo");
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
		$sql = "SELECT wadoc_colonne.*," .
				" CONCAT(wadoc_sezioni.sigla, '/', IFNULL(wadoc_pagine.titolo, wadoc_pagine.nome), '/', IFNULL(wadoc_tabelle.titolo, wadoc_tabelle.nome)) as nomeTabella," .
				" CONCAT(wadoc_tabelleDB.nome, '.', wadoc_campi.nome) AS nomeCampo" .
				" FROM wadoc_colonne" .
				" INNER JOIN wadoc_tabelle ON wadoc_colonne.idTabella=wadoc_tabelle.idTabella" .
				" INNER JOIN wadoc_pagine ON wadoc_tabelle.idPagina=wadoc_pagine.idPagina" .
				" INNER JOIN wadoc_sezioni ON wadoc_pagine.idSezione=wadoc_sezioni.idSezione" .
				" LEFT JOIN wadoc_campi ON wadoc_colonne.idCampo=wadoc_campi.idCampo" .
				" LEFT JOIN wadoc_tabelleDB on wadoc_campi.idTabellaDB=wadoc_tabelleDB.idTabellaDB" .
				" WHERE 1";
		if ($_GET['idTabella'])
			$sql .= " AND wadoc_colonne.idTabella=" . $dbconn->interoSql($_GET['idTabella']);
		$sql .= " ORDER BY nomeTabella, wadoc_colonne.posizione";
		$tabella = $this->dammiTabella($sql);
		
		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "colonne";
		
		// azioni
		
		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("idColonna", "ID", false, false, false);

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
			$col->aliasDi = "wadoc_colonne.etichetta";
		$col = $tabella->aggiungiColonna("nome", "Nome");
			$col->inputTipo = WATBL_INPUT_TESTO;
			$col->aliasDi = "wadoc_colonne.nome";
			
		$col = $tabella->aggiungiColonna("idCampo", "Campo");
			$sql = "SELECT wadoc_campi.idCampo, CONCAT(wadoc_tabelleDB.nome, '.', wadoc_campi.nome) as nomeCampo" .
					" FROM wadoc_campi" .
					" INNER JOIN wadoc_tabelleDB on wadoc_campi.idTabellaDB=wadoc_tabelleDB.idTabellaDB" .
					" ORDER BY nomeCampo";
			$col->inputOpzioni = $this->dammiLista($sql);
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->aliasDi = "CONCAT(wadoc_tabelleDB.nome, '.', wadoc_campi.nome)";
			
		$col = $tabella->aggiungiColonna("mostra", "Mostra");
			$col->inputTipo = WATBL_INPUT_LOGICO;
			$col->aliasDi = "wadoc_colonne.mostra";
			$col->allineamento = WATBL_ALLINEA_CENTRO;
			
		$col = $tabella->aggiungiColonna("ordina", "Ordina");
			$col->inputTipo = WATBL_INPUT_LOGICO;
			$col->aliasDi = "wadoc_colonne.ordina";
			$col->allineamento = WATBL_ALLINEA_CENTRO;
			
		$col = $tabella->aggiungiColonna("filtra", "Filtra");
			$col->inputTipo = WATBL_INPUT_LOGICO;
			$col->aliasDi = "wadoc_colonne.filtra";
			$col->allineamento = WATBL_ALLINEA_CENTRO;

		$col = $tabella->aggiungiColonna("totalizza", "Totalizza");
			$col->inputTipo = WATBL_INPUT_LOGICO;
			$col->aliasDi = "wadoc_colonne.totalizza";
			$col->allineamento = WATBL_ALLINEA_CENTRO;
			
		$col = $tabella->aggiungiColonna("aliasDi", "Alias di");
			$col->inputTipo = WATBL_INPUT_TESTO;
		
		
		$col = $tabella->aggiungiColonna("posizione", "Posizione", true, true, true, WATBL_ALLINEA_DX, WATBL_FMT_INTERO);
			$col->inputTipo = WATBL_INPUT_INTERO;
			$col->aliasDi = "wadoc_colonne.posizione";
			
		$col = $tabella->aggiungiColonna("descrizione", "Descrizione");
			$col->aliasDi = "wadoc_colonne.descrizione";
			$col->inputTipo = WATBL_INPUT_AREATESTO;
			
		$col = $tabella->aggiungiColonna("help", "Help");
			$col->aliasDi = "wadoc_colonne.help";
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

