<?php
include "wadocapp.inc.php";

//*****************************************************************************
/**
*/
class pagina extends waDocApp
	{
	
	/**
	 *
	 * @var waConnessioneDB
	 */
	var $dbconn;
	
	//*****************************************************************************
	/**
	* mostraPagina
	* 
	* @return void
	*/
	function mostraPagina()
		{
		$this->dbconn = $this->dammiConnessioneDB();
		$this->stampaHtmlIndice();
		$this->stampaHtmlIndiceDB();
		
		}
		
	//*****************************************************************************
	/**
	* 
	* @return void
	*/
	function stampaHtmlIndice()
		{
		$sql = "SELECT * FROM wadoc_release";
		$rs = $this->dammiRigheDB($sql, $this->dbconn, 1);
		$riga = $rs->righe[0];

		// dati release
		$buffer .= $this->dammiVoceXml("titolo", $riga->valore("nomeProcedura"));
		$buffer .= $this->dammiVoceXml("versione", $riga->valore("nrRelease"));
		$buffer .= $this->dammiVoceXml("data_versione", date("Y-m-d", $riga->valore("dataRelease")));
		$buffer .= $this->dammiVoceXml("autore", $riga->valore("autore"));
		$buffer .= $this->dammiVoceXml("descrizione", $riga->valore("descrizione"));

		// sezioni
		$sql = "SELECT * FROM wadoc_sezioni ORDER BY nome";
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			{
			$sezioni[$riga->valore("idSezione")] = $riga->valore("nome");
			$this->stampaHtmlSezione($riga);
			}
		$buffer .= $this->dammiVoceXml("sezioni", $sezioni);

		// allegati
		$sql = "SELECT * FROM wadoc_allegati ORDER BY posizione";
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			$allegati[$riga->valore("idAllegato")] = array("nome" => $riga->valore("nome"), "titolo" => $riga->valore("titolo"));
		$buffer .= $this->dammiVoceXml("allegati", $allegati);

		$this->trasforma($buffer, "index");
		}

	
	//*****************************************************************************
	/**
	* 
	* @return void
	*/
	function stampaHtmlIndiceDB()
		{
		// tabelle 
		$sql = "SELECT * FROM wadoc_tabelleDB ORDER BY nomeDB, nome";
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			{
			$tabelle_db[$riga->valore("nomeDB")][$riga->valore("idTabellaDB")] = $riga->valore("nome");
			$this->stampaHtmlTabellaDB($riga);
			}
		$buffer .= $this->dammiVoceXml("tabelle_db", $tabelle_db);

		$this->trasforma($buffer, "db");
		}

	//*****************************************************************************
	/**
	* 
	* @return void
	*/
	function stampaHtmlTabellaDB(waRecord $rigaTabellaDB)
		{ 
		$buffer .= $this->dammiVoceXml("nome_db", $rigaTabellaDB->valore("nomeDB"));
		$buffer .= $this->dammiVoceXml("nome", $rigaTabellaDB->valore("nome"));
		$buffer .= $this->dammiVoceXml("tipo", $rigaTabellaDB->valore("tipo"));
		$buffer .= $this->dammiVoceXml("descrizione", $rigaTabellaDB->valore("descrizione"));

		// campi
		$sql = "SELECT * FROM wadoc_campi" .
				"  WHERE idTabellaDB=". $this->dbconn->interoSql($rigaTabellaDB->valore("idTabellaDB")) .
				" ORDER BY posizione";
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			$campi[$riga->valore("idCampo")] = $this->rec2array ($riga);
		$buffer .= $this->dammiVoceXml("campi", $campi);

		$this->trasforma($buffer, "tabella_db", $rigaTabellaDB->valore("idTabellaDB"));
		}

	//*****************************************************************************
	/**
	* 
	* @return void
	*/
	function stampaHtmlSezione(waRecord $rigaSezione)
		{ 
		$buffer .= $this->dammiVoceXml("sigla", $rigaSezione->valore("sigla"));
		$buffer .= $this->dammiVoceXml("nome", $rigaSezione->valore("nome"));
		$buffer .= $this->dammiVoceXml("descrizione", $rigaSezione->valore("descrizione"));

		// menu
		$sql = "SELECT * FROM wadoc_menu" .
				"  WHERE idSezione=". $this->dbconn->interoSql($rigaSezione->valore("idSezione"));
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			{
			$menu[$riga->valore("idMenu")] = $riga->valore("titolo");
			$this->stampaHtmlMenu($rigaSezione, $riga);
			}
		$buffer .= $this->dammiVoceXml("menu", $menu);

		// pagine
		$sql = "SELECT * FROM wadoc_pagine" .
				"  WHERE idSezione=". $this->dbconn->interoSql($rigaSezione->valore("idSezione"));
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			{
			$pagine[$riga->valore("idPagina")] = $riga->valore("titolo");
			$this->stampaHtmlPagina($rigaSezione, $riga);
			}
			
		$buffer .= $this->dammiVoceXml("pagine", $pagine);

		$this->trasforma($buffer, "sezione", $rigaSezione->valore("idSezione"));
		}

	//*****************************************************************************
	/**
	* 
	* @return void
	*/
	function stampaHtmlMenu(waRecord $rigaSezione, waRecord $rigaMenu)
		{ 
		$sezione["idSezione"] = $rigaSezione->valore("idSezione");
		$sezione["nome"] = $rigaSezione->valore("nome");
		$buffer .= $this->dammiVoceXml("sezione", $sezione);

		$menu["nome"] = $rigaMenu->valore("nome");
		$menu["titolo"] = $rigaMenu->valore("titolo");
		$menu["descrizione"] = $rigaMenu->valore("descrizione");
		$buffer .= $this->dammiVoceXml("menu", $menu);
		
		// voci
		$sql = "SELECT wadoc_vociMenu.*," .
				" wadoc_pagine.idPagina" .
				" FROM wadoc_vociMenu" .
				" LEFT JOIN wadoc_pagine ON wadoc_vociMenu.destinazione=wadoc_pagine.nome" .
					" AND wadoc_pagine.idSezione=" . $this->dbconn->interoSql($rigaSezione->valore("idSezione")) .
				" WHERE idMenu=". $this->dbconn->interoSql($rigaMenu->valore("idMenu")) .
				" ORDER BY posizione";
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			$voci[$riga->valore("idVoceMenu")] = $this->rec2array ($riga);
		$buffer .= $this->dammiVoceXml("voci", $voci);

		$this->trasforma($buffer, "menu", $rigaMenu->valore("idMenu"));
		}

	//*****************************************************************************
	/**
	* 
	* @return void
	*/
	function stampaHtmlPagina(waRecord $rigaSezione, waRecord $rigaPagina)
		{ 
		$sezione["idSezione"] = $rigaSezione->valore("idSezione");
		$sezione["nome"] = $rigaSezione->valore("nome");
		$buffer .= $this->dammiVoceXml("sezione", $sezione);

		$pagina["nome"] = $rigaPagina->valore("nome");
		$pagina["titolo"] = $rigaPagina->valore("titolo");
		$pagina["descrizione"] = $rigaPagina->valore("descrizione");
		$buffer .= $this->dammiVoceXml("pagina", $pagina);
		
		// tabelle
		$sql = "SELECT wadoc_tabelle.*" .
				" FROM wadoc_tabelle" .
				" WHERE idPagina=". $this->dbconn->interoSql($rigaPagina->valore("idPagina"));
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			{
			$tabelle[$riga->valore("idTabella")] = $this->rec2array ($riga);
			$this->stampaHtmlTabella($rigaSezione, $rigaPagina, $riga);
			}
		$buffer .= $this->dammiVoceXml("tabelle", $tabelle);

		// moduli
		$sql = "SELECT wadoc_moduli.*" .
				" FROM wadoc_moduli" .
				" WHERE idPagina=". $this->dbconn->interoSql($rigaPagina->valore("idPagina"));
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			{
			$moduli[$riga->valore("idModulo")] = $this->rec2array ($riga);
			$this->stampaHtmlModulo($rigaSezione, $rigaPagina, $riga);
			}
		$buffer .= $this->dammiVoceXml("moduli", $moduli);

		$this->trasforma($buffer, "pagina", $rigaPagina->valore("idPagina"));
		}

	//*****************************************************************************
	/**
	* 
	* @return void
	*/
	function stampaHtmlTabella(waRecord $rigaSezione, waRecord $rigaPagina, waRecord $rigaTabella)
		{ 
		$sezione["idSezione"] = $rigaSezione->valore("idSezione");
		$sezione["nome"] = $rigaSezione->valore("nome");
		$buffer .= $this->dammiVoceXml("sezione", $sezione);

		$pagina["idPagina"] = $rigaPagina->valore("idPagina");
		$pagina["nome"] = $rigaPagina->valore("nome");
		$pagina["titolo"] = $rigaPagina->valore("titolo");
		$pagina["descrizione"] = $rigaPagina->valore("descrizione");
		$buffer .= $this->dammiVoceXml("pagina", $pagina);
		
		$tabella["nome"] = $rigaTabella->valore("nome");
		$tabella["titolo"] = $rigaTabella->valore("titolo");
		$tabella["descrizione"] = $rigaTabella->valore("descrizione");
		$buffer .= $this->dammiVoceXml("tabella", $tabella);
		
		// colonne
		$sql = "SELECT wadoc_colonne.*," .
				" wadoc_campi.idTabellaDB" .
				" FROM wadoc_colonne" .
				" LEFT JOIN wadoc_campi ON wadoc_colonne.idCampo=wadoc_campi.idCampo" .
				" WHERE wadoc_colonne.idTabella=". $this->dbconn->interoSql($rigaTabella->valore("idTabella")) .
				" ORDER BY wadoc_colonne.posizione";
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			$colonne[$riga->valore("idColonna")] = $this->rec2array ($riga);
		$buffer .= $this->dammiVoceXml("colonne", $colonne);

		// azioni
		$sql = "SELECT wadoc_azioni.*" .
				" FROM wadoc_azioni" .
				" WHERE idTabella=". $this->dbconn->interoSql($rigaTabella->valore("idTabella")) .
				" ORDER BY suRecord, posizione";
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			$azioni[$riga->valore("suRecord") ? 'record' : 'pagina'][$riga->valore("idAzione")] = $this->rec2array ($riga);
		$buffer .= $this->dammiVoceXml("azioni", $azioni);

		$this->trasforma($buffer, "tabella", $rigaTabella->valore("idTabella"));
		}

	//*****************************************************************************
	/**
	* 
	* @return void
	*/
	function stampaHtmlModulo(waRecord $rigaSezione, waRecord $rigaPagina, waRecord $rigaModulo)
		{ 
		$sezione["idSezione"] = $rigaSezione->valore("idSezione");
		$sezione["nome"] = $rigaSezione->valore("nome");
		$buffer .= $this->dammiVoceXml("sezione", $sezione);

		$pagina["idPagina"] = $rigaPagina->valore("idPagina");
		$pagina["nome"] = $rigaPagina->valore("nome");
		$pagina["titolo"] = $rigaPagina->valore("titolo");
		$pagina["descrizione"] = $rigaPagina->valore("descrizione");
		$buffer .= $this->dammiVoceXml("pagina", $pagina);
		
		$modulo["nome"] = $rigaModulo->valore("nome");
		$modulo["titolo"] = $rigaModulo->valore("titolo");
		$modulo["descrizione"] = $rigaModulo->valore("descrizione");
		$buffer .= $this->dammiVoceXml("modulo", $modulo);
		
		// controlli
		$sql = "SELECT wadoc_controlli.*," .
				" wadoc_campi.idTabellaDB" .
				" FROM wadoc_controlli" .
				" LEFT JOIN wadoc_campi ON wadoc_controlli.idCampo=wadoc_campi.idCampo" .
				" WHERE wadoc_controlli.idModulo=". $this->dbconn->interoSql($rigaModulo->valore("idModulo")) .
				" ORDER BY wadoc_controlli.posizione";
		$rs = $this->dammiRigheDB($sql, $this->dbconn);
		foreach ($rs->righe as $riga)
			$controlli[$riga->valore("idControllo")] = $this->rec2array ($riga);
		$buffer .= $this->dammiVoceXml("controlli", $controlli);

		$this->trasforma($buffer, "modulo", $rigaModulo->valore("idModulo"));
		}

	
	//*****************************************************************************
	/**
	* 
	* @return array
	*/
	function rec2array(waRecord $riga)
		{
		for($i = 0; $i < $riga->righeDB->nrCampi(); $i++)
			$retval[$riga->righeDB->nomeCampo($i)] = $riga->valore($i);
		
		return $retval;
		}
		
	//*****************************************************************************
	/**
	* 
	* @return string
	*/
	function dammiVoceXml($nome, $valore)
		{
		$retval = is_numeric($nome) ? "<item id='$nome'>" : "<$nome>";
		
		if (is_array($valore))
			{
			foreach ($valore as $k => $v)
				$retval .= $this->dammiVoceXml ($k, $v);
			}
		else
			$retval .= $nome != "descrizione" ? htmlspecialchars ($valore) : "<![CDATA[$valore]]>";
		
		$retval .= is_numeric($nome) ? "</item>" : "</$nome>";
		
		return $retval;
		}
		
		
	//***************************************************************************
	/**
	* trasforma il buffer xml con il foglio di stile associato
	*
	 * @ignore
	*/
	protected function trasforma($buffer, $nomePagina, $id = false)
		{
		$xslt = "$this->dirXlstOut/$nomePagina.xsl";
		
		$buffer = "<?xml version='1.0' encoding='UTF-8'?>" .
					"<wadocumentazione>$buffer</wadocumentazione>";
		
		// Create an XSLT processor
		$xp = new XsltProcessor();
		
		// create a DOM document and load the XSL stylesheet
		$xsl = new DomDocument;
		$xsl->load($xslt);
		
		// import the XSL styelsheet into the XSLT process
		$xp->importStylesheet($xsl);
		
		// create a DOM document and load the XML datat
		$xml_doc = new DomDocument;
		//  $xml_doc->load("test.xml");
		$xml_doc->loadXML($buffer);
		
		// transform the XML into HTML using the XSL file
		if (!$out = $xp->transformToXML($xml_doc))
			trigger_error('XSL transformation failed.', E_USER_ERROR);
			
		$nomeOut = $id ? "$nomePagina.$id" : $nomePagina;
		file_put_contents("$this->dirOutput/$nomeOut.html", $out);
		} 

	//*****************************************************************************
	}	// fine classe pagina
	
//*****************************************************************************
// istanzia la pagina
$page = new pagina();
$page->mostraPagina();

