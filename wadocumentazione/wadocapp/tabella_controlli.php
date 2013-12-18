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
		$this->aggiungiElemento("Controlli", "titolo");
		$this->aggiungiElemento($this->Tabella());
		
		// manda in output l'intera pagina
		$this->mostra();
		}
		
	//*****************************************************************************
	/**
	* Tabella
	* 
	* costruisce la tabella 
	* 
	* @return waModulo
	*/
	function Tabella()
		{
		// creazione della tabella sulla base della query sql
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT wadoc_controlli.*," .
				" CONCAT(wadoc_sezioni.sigla, '/', IFNULL(wadoc_pagine.titolo, wadoc_pagine.nome), '/', IFNULL(wadoc_moduli.titolo, wadoc_moduli.nome)) as nomeModulo," .
				" CONCAT(wadoc_tabelleDB.nome, '.', wadoc_campi.nome) AS nomeCampo" .
				" FROM wadoc_controlli" .
				" INNER JOIN wadoc_moduli ON wadoc_controlli.idModulo=wadoc_moduli.idModulo" .
				" INNER JOIN wadoc_pagine ON wadoc_moduli.idPagina=wadoc_pagine.idPagina" .
				" INNER JOIN wadoc_sezioni ON wadoc_pagine.idSezione=wadoc_sezioni.idSezione" .
				" LEFT JOIN wadoc_campi ON wadoc_controlli.idCampo=wadoc_campi.idCampo" .
				" LEFT JOIN wadoc_tabelleDB on wadoc_campi.idTabellaDB=wadoc_tabelleDB.idTabellaDB" .
				" WHERE 1";
		if ($_GET['idModulo'])
			$sql .= " AND wadoc_controlli.idModulo=" . $dbconn->interoSql($_GET['idModulo']);
		$sql .= " ORDER BY nomeModulo, wadoc_controlli.posizione";
		$tabella = $this->dammiTabella($sql);
		
		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "controlli";
		
		// azioni
		
		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("idControllo", "ID", false, false, false);
		
		$col = $tabella->aggiungiColonna("idModulo", "Modulo", !$_GET['idModulo'], true, true, WATBL_ALLINEA_CENTRO);
			$sql = "SELECT wadoc_moduli.idModulo, CONCAT(wadoc_sezioni.sigla, '/', IFNULL(wadoc_pagine.titolo, wadoc_pagine.nome), '/', IFNULL(wadoc_moduli.titolo, wadoc_moduli.nome)) as nomeModulo" .
					" FROM wadoc_moduli" .
					" INNER JOIN wadoc_pagine ON wadoc_moduli.idPagina=wadoc_pagine.idPagina" .
					" INNER JOIN wadoc_sezioni on wadoc_pagine.idSezione=wadoc_sezioni.idSezione" .
					" ORDER BY nomeModulo";
			$col->inputOpzioni = $this->dammiLista($sql);
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->aliasDi = "CONCAT(wadoc_sezioni.sigla, '/', IFNULL(wadoc_pagine.titolo, wadoc_pagine.nome), '/', IFNULL(wadoc_moduli.titolo, wadoc_moduli.nome))";
		
		$col = $tabella->aggiungiColonna("etichetta", "Etichetta");
			$col->inputTipo = WATBL_INPUT_TESTO;
			$col->aliasDi = "wadoc_controlli.etichetta";
			
		$col = $tabella->aggiungiColonna("nome", "Nome");
			$col->inputTipo = WATBL_INPUT_TESTO;
			$col->aliasDi = "wadoc_controlli.nome";
			
		$col = $tabella->aggiungiColonna("idCampo", "Campo", true, true, true, WATBL_ALLINEA_CENTRO);
			$sql = "SELECT wadoc_campi.idCampo, CONCAT(wadoc_tabelleDB.nome, '.', wadoc_campi.nome) as nomeCampo" .
					" FROM wadoc_campi" .
					" INNER JOIN wadoc_tabelleDB on wadoc_campi.idTabellaDB=wadoc_tabelleDB.idTabellaDB" .
					" ORDER BY nomeCampo";
			$col->inputOpzioni = $this->dammiLista($sql);
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->aliasDi = "CONCAT(wadoc_tabelleDB.nome, '.', wadoc_campi.nome)";
			
			
		$col = $tabella->aggiungiColonna("tipo", "Tipo");
			$col->inputTipo = WATBL_INPUT_TESTO;
			$col->aliasDi = "wadoc_controlli.tipo";
			
		$col = $tabella->aggiungiColonna("visibile", "Visibile");
			$col->inputTipo = WATBL_INPUT_LOGICO;
			$col->aliasDi = "wadoc_controlli.visibile";
			$col->allineamento = WATBL_ALLINEA_CENTRO;
			
		$col = $tabella->aggiungiColonna("solaLettura", "Sola lettura");
			$col->inputTipo = WATBL_INPUT_LOGICO;
			$col->aliasDi = "wadoc_controlli.solaLettura";
			$col->allineamento = WATBL_ALLINEA_CENTRO;
			
		$col = $tabella->aggiungiColonna("obbligatorio", "Obbligatorio");
			$col->inputTipo = WATBL_INPUT_LOGICO;
			$col->aliasDi = "wadoc_controlli.obbligatorio";
			$col->allineamento = WATBL_ALLINEA_CENTRO;
			
		$col = $tabella->aggiungiColonna("posizione", "Posizione", true, true, true, WATBL_ALLINEA_DX, WATBL_FMT_INTERO);
			$col->inputTipo = WATBL_INPUT_INTERO;
			$col->aliasDi = "wadoc_controlli.posizione";
			
		$col = $tabella->aggiungiColonna("descrizione", "Descrizione");
			$col->aliasDi = "wadoc_controlli.descrizione";
			$col->inputTipo = WATBL_INPUT_AREATESTO;
			
		$col = $tabella->aggiungiColonna("help", "Help");
			$col->aliasDi = "wadoc_controlli.help";
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

