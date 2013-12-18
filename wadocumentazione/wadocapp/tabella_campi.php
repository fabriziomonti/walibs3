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
		$this->aggiungiElemento("Campi DB", "titolo");
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
		$sql = "SELECT wadoc_campi.*," .
				" CONCAT(wadoc_tabelleDB.nomeDB, '.', wadoc_tabelleDB.nome) AS nomeTabellaDB" .
				" FROM wadoc_campi" .
				" INNER JOIN wadoc_tabelleDB ON wadoc_campi.idTabellaDB=wadoc_tabelleDB.idTabellaDB" .
				" WHERE 1";
		if ($_GET['idTabellaDB'])
			$sql .= " AND wadoc_campi.idTabellaDB=" . $dbconn->interoSql($_GET['idTabellaDB']);
		if ($_GET['idCampo'])
			$sql .= " AND wadoc_campi.idCampo=" . $dbconn->interoSql($_GET['idCampo']);
		$sql .= " ORDER BY nomeTabellaDB, posizione";
		$tabella = $this->dammiTabella($sql);
		
		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "campi DB";
		
		// azioni
		
		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("idCampo", "ID", false, false, false);
		
		$col = $tabella->aggiungiColonna("idTabellaDB", "Nome tabella", !$_GET['idTabellaDB'], true, true, WATBL_ALLINEA_CENTRO);
			$sql = "SELECT idTabellaDB, CONCAT(nomeDB, '.', nome) AS nomeTabellaDB" .
					" FROM wadoc_tabelleDB" .
					" ORDER BY nomeDB, nome";
			$col->inputOpzioni = $this->dammiLista($sql);
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->aliasDi = "CONCAT(wadoc_tabelleDB.nomeDB, '.', wadoc_tabelleDB.nome)";
			
		$col = $tabella->aggiungiColonna("nome", "Nome");
			$col->inputTipo = WATBL_INPUT_TESTO;
			$col->aliasDi = "wadoc_campi.nome";
		
		$col = $tabella->aggiungiColonna("tipo", "Tipo", true, true, true, WATBL_ALLINEA_CENTRO);
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->inputOpzioni = array("CONTENITORE" => "CONTENITORE", "DATA" => "DATA", "DATAORA" => "DATAORA", "DECIMALE" => "DECIMALE", "INTERO" => "INTERO", "ORA" => "ORA", "STRINGA" => "STRINGA");
			$col->aliasDi = "wadoc_campi.tipo";

		$col = $tabella->aggiungiColonna("tipoDB", "Tipo DB");
			$col->inputTipo = WATBL_INPUT_TESTO;
			
		$col = $tabella->aggiungiColonna("lunghezza", "Lunghezza");
			$col->inputTipo = WATBL_INPUT_TESTO;
			
		$col = $tabella->aggiungiColonna("chiavePrimaria", "Chiave primaria");
			$col->inputTipo = WATBL_INPUT_LOGICO;
			$col->allineamento = WATBL_ALLINEA_CENTRO;
			
		$col = $tabella->aggiungiColonna("posizione", "Posizione", true, true, true, WATBL_ALLINEA_DX);
			$col->inputTipo = WATBL_INPUT_INTERO;
			
		$col = $tabella->aggiungiColonna("descrizione", "Descrizione");
			$col->aliasDi = "wadoc_campi.descrizione";
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

