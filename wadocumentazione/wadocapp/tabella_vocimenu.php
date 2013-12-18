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
		$this->aggiungiElemento("Voci menu", "titolo");
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
		$sql = "select wadoc_vociMenu.*," .
				" CONCAT(wadoc_sezioni.sigla, '/', wadoc_menu.nome) AS nomeMenu" .
				" FROM wadoc_vociMenu" .
				" LEFT JOIN wadoc_menu ON wadoc_vociMenu.idMenu=wadoc_menu.idMenu" .
				" LEFT JOIN wadoc_sezioni ON wadoc_menu.idSezione=wadoc_sezioni.idSezione";
				" WHERE 1";
		if ($_GET['idMenu'])
			$sql .= " AND wadoc_vociMenu.idMenu=" . $dbconn->interoSql($_GET['idMenu']);
		$sql .= " ORDER BY nomeMenu, wadoc_vociMenu.posizione";
		$tabella = $this->dammiTabella($sql);
		
		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "Voci menu";
		
		// azioni
		$tabella->aggiungiAzione("Pagina", true, "Pagina", array($this, "esistePagina"));
		
		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("idVoceMenu", "ID", false, false, false);
		
		$col = $tabella->aggiungiColonna("idMenu", "Menu", !$_GET['idMenu'], true, true, WATBL_ALLINEA_CENTRO);
			$sql = "SELECT wadoc_menu.idMenu, CONCAT(wadoc_sezioni.sigla, '/', wadoc_menu.nome) AS nomeMenu" .
					" FROM wadoc_menu" .
					" INNER JOIN wadoc_sezioni on wadoc_menu.idSezione=wadoc_sezioni.idSezione" .
					" ORDER BY nomeMenu";
			$col->inputOpzioni = $this->dammiLista($sql);
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->aliasDi = "CONCAT(wadoc_sezioni.sigla, '/', wadoc_menu.nome)";
			
		$col = $tabella->aggiungiColonna("etichetta", "Etichetta");
			$col->inputTipo = WATBL_INPUT_TESTO;
			
		$col = $tabella->aggiungiColonna("destinazione", "Destinazione");
			$col->inputTipo = WATBL_INPUT_TESTO;
//			$col->link = true;
			
		$col = $tabella->aggiungiColonna("livello", "Livello");
			$col->inputTipo = WATBL_INPUT_INTERO;
			
		$col = $tabella->aggiungiColonna("posizione", "Posizione");
			$col->inputTipo = WATBL_INPUT_INTERO;
			
		$col = $tabella->aggiungiColonna("descrizione", "Descrizione");
			$col->aliasDi = "wadoc_vociMenu.descrizione";
			$col->inputTipo = WATBL_INPUT_AREATESTO;
			
		$col = $tabella->aggiungiColonna("help", "Help");
			$col->aliasDi = "wadoc_vociMenu.help";
			$col->inputTipo = WATBL_INPUT_AREATESTO;
		
		// colonne non visibili
		$tabella->aggiungiColonna("nomeMenu", "nomeMenu", false, false, false);
		
		$tabella->leggiValoriIngresso();

		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe())
			$this->mostraErroreDB($tabella->righeDB->connessioneDB);
		
		return $tabella;
		}
		
	//*****************************************************************************
	/**
	 * verifica se per il record dato la pagina è censita
	*/
	function esistePagina(waTabella $table)
		{
		return ! ! $table->record->valore("destinazione");
		}
		
	//*****************************************************************************
	/**
	 * dato un idVoceMenu cerca di restituire l'identificativo della pagina di
	 * destinazione
	*/
	function rpc_dammiIdPagina($idVoceMenu)
		{
		if (!$idVoceMenu)
			return 0;

		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT wadoc_pagine.idPagina" .
				" FROM wadoc_vociMenu" .
				" INNER JOIN wadoc_menu ON wadoc_vociMenu.idMenu=wadoc_menu.idMenu" .
				" INNER JOIN wadoc_pagine ON wadoc_menu.idSezione=wadoc_pagine.idSezione" .
				" WHERE wadoc_vociMenu.idVoceMenu=" . $dbconn->interoSql($idVoceMenu) .
				" AND wadoc_vociMenu.destinazione=wadoc_pagine.nome";
		$riga = $this->dammiRigheDB($sql, $dbconn)->righe[0];
		if (!$riga)
			return 0;
		
		return $riga->valore("idPagina");
		
		}
	
	}	// fine classe pagina
	
//*****************************************************************************
// istanzia la pagina
$page = new pagina();
$page->mostraPagina();

