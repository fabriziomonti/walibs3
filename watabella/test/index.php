<?php
error_reporting(E_ERROR|E_WARNING);
//******************************************************************************
/**
 * inclusione dei moduli walibs che ci servono in questa pagina
 */
include("../../wamodulo/wamodulo.inc.php");
include("../watabella.inc.php");

// file contenente i parametri di connessione al database
define ("FILE_CONFIG_DB", dirname(__FILE__) . "/dbconfig.inc.php");

// creazione degli oggetti waTabella che mostreremo nella pagina
$table0 = dammiTabella("tabella0");
$table1 = dammiTabella("tabella1");

// creazione dell'header della pagina html
$outputBuffer = creaTop();

// corpo della pagina contenente le due tabelle
$outputBuffer .= $table0->mostra(true);
$outputBuffer .=  "<p /><hr /><p />";
$outputBuffer .= $table1->mostra(true);

// inclusione delle procedure di controllo specifiche di questa pagina
// che verranno utilizzate in interfaccia utente
if (!strpos($table0->xslt, 'wa_usabile'))
	$outputBuffer .=  "<script type='text/javascript' src='ui/js/index.js'></script>\n";

// chiusura della pagina html
$outputBuffer .= creaFooter();

// risistema il codice html rendendolo "strict"
$outputBuffer = fammeloStrict($outputBuffer);

			
exit($outputBuffer);

//*************************************************************************
/**
 * Enter description here...
 *
 * @param unknown_type $nome
 * @return waTabella
 */
function dammiTabella($nome)
	{		
	// creazione della tabella sulla base della query sql
	$sql = "SELECT corsi.*, organismi.nome as nomeorgan, amministrazioni.sigla, amministrazioni.nome as nomeamm" .
			" FROM corsi" .
			" LEFT JOIN amministrazioni ON corsi.id_amministrazione=amministrazioni.id_amministrazione".
			" LEFT JOIN organismi ON corsi.id_organismo=organismi.id_organismo" .
			"";
//			" ORDER BY corsi.id_corso";
	$table = new waTabella($sql, FILE_CONFIG_DB);
	$table->nome = $nome;
	$table->listaMaxRec = 3;
	$table->pdf_orientazione = "L";
	
	// definizione delle proprieta' di base della tabella
	$table->titolo = "corsi";
	$table->paginaModulo = "../../wamodulo/test/index.php";
	
	// definizione dell'xslt da applicare ai dati della tabella
	if (file_exists("../uis/$_GET[watbl_azione_tabella0]/xslt"))
		$xsltType = $_GET['watbl_azione_tabella0'];
	elseif (file_exists("../uis/$_GET[watbl_azione_tabella1]/xslt"))
		$xsltType = $_GET['watbl_azione_tabella0'];
	else
		$xsltType = $_GET['type'] ?  $_GET['type'] : "wa_azioni_sx_default";
	$table->xslt = "../uis/$xsltType/xslt/watabella.xsl";
	
	// definizione delle azioni della tabella
	$table->azioni['Elimina']->funzioneAbilitazione = "verificaAbilitaElimina";
	$table->aggiungiAzione("wa_usabile", false, "Usabile");
//	$table->aggiungiAzione("nonInLinea", false, "Non in linea");
	$table->aggiungiAzione("wa_azioni_sx_default", false, "A sinistra");
	$table->aggiungiAzione("wa_azioni_dx", false, "A destra");
	$table->aggiungiAzione("wa_azioni_context", false, "Contestuale");
	$table->aggiungiAzione("wa_azioni_sx_edit", false, "Edit");
	$table->aggiungiAzione("wa_azioni_sx_quick_edit", false, "Quick edit");
	$table->aggiungiAzione("Custom", true, "Cus tom");
	$table->aggiungiAzione("RPC", true, "rpc");
	
	// definizione delle colonne della tabella e delle relative proprieta'
	$col = $table->aggiungiColonna("id_corso", "ID", true, true, true, WATBL_ALLINEA_DX, WATBL_FMT_INTERO);
		$col->pdf_perc = 5;
	
	// nelle successive 2 colonne facciao cose diverse a seconda del fatto che
	// siamo in edit o meno
	if (stripos($xsltType, "edit"))
		{
		$col = $table->aggiungiColonna("id_organismo", "Org.", true, true, true, WATBL_ALLINEA_CENTRO, WATBL_FMT_INTERO);
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->inputObbligatorio = true;
			$col->aliasDi = "organismi.id_organismo";
			$dbconn = wadb_dammiConnessione(FILE_CONFIG_DB);
			$rs = new waRigheDB($dbconn);
			$rs->caricaDaSql("SELECT * FROM organismi ORDER BY nome");
			$col->inputOpzioni[''] = '';
			foreach ($rs->righe as $riga)
				$col->inputOpzioni[$riga->valore("id_organismo")] = $riga->valore("nome");
		$col->pdf_perc = 5;
		}
	else
		{
		$col = $table->aggiungiColonna("nomeorgan", "Org.");
			$col->aliasDi = "organismi.nome";
		$col->pdf_perc = 12;
		
		// serve per l'rpc...
		$col = $table->aggiungiColonna("id_organismo", "Org.", false,false,false);
		}		
		
	if (stripos($xsltType, "edit"))
		{
		$col = $table->aggiungiColonna("id_amministrazione", "Amm.ne");
			$col->inputTipo = WATBL_INPUT_SELEZIONE;
			$col->inputObbligatorio = true;
			$col->aliasDi = "amministrazioni.id_amministrazione";
			$rs = new waRigheDB($dbconn);
			$rs->caricaDaSql("SELECT * FROM amministrazioni ORDER BY nome");
			$col->inputOpzioni[''] = '';
			foreach ($rs->righe as $riga)
				$col->inputOpzioni[$riga->valore("id_amministrazione")] = $riga->valore("sigla");
		}
	else
		{
		$col = $table->aggiungiColonna("sigla", "Amm.ne");
			$col->link = true;
		}		
		$col->pdf_perc = 9;
		
	$col = $table->aggiungiColonna("rifpa", "Rif. P.A.");
		$col->inputTipo = WATBL_INPUT_TESTO;
		$col->maxcaratteri = 10;
		$col->inputObbligatorio = true;
		$col->pdf_perc = 8;
	
	$col = $table->aggiungiColonna("nome", "Nome");
		$col->aliasDi = "corsi.nome";
		$col->inputTipo = WATBL_INPUT_TESTO;
		$col->maxcaratteri = 50;
//		$col->inputObbligatorio = true;
		$col->pdf_perc = 10;
//		$col->convertiHTML = false;
	
	$col = $table->aggiungiColonna("data_inizio", "Data Inizio", true, true, true, WATBL_ALLINEA_CENTRO);
		$col->inputTipo = WATBL_INPUT_DATA;
//		$col->inputObbligatorio = true;
		$col->pdf_perc = 10;
	
	$col = $table->aggiungiColonna("data_fine", "Data ora fine", true, true, true, WATBL_ALLINEA_CENTRO);
		$col->inputTipo = WATBL_INPUT_DATAORA;
//		$col->inputObbligatorio = true;
		$col->pdf_perc = 15;
	
	$col = $table->aggiungiColonna("nr_ore", "Nr. ore", true, true, true, WATBL_ALLINEA_DX);
		$col->inputTipo = WATBL_INPUT_INTERO;
		$col->pdf_perc = 5;
	
	$col = $table->aggiungiColonna("importo", "Importo", true, true, true, WATBL_ALLINEA_DX);
		$col->totalizza = true;
		$col->inputTipo = WATBL_INPUT_VALUTA;
		$col->pdf_perc = 12;
	
//	$col = $table->aggiungiColonna("attivo", "Attivo", true, true, true, WATBL_ALLINEA_CENTRO);
//		$col->inputTipo = WATBL_INPUT_LOGICO;
//		$col->pdf_perc = 7;
	
	$col = $table->aggiungiColonna("eliminabile", "Eliminabile", true, false, false, WATBL_ALLINEA_CENTRO);
		$col->funzioneCalcolo = "mostraEliminabilita";
		$col->pdf_perc = 11;
	
	// una colonna non visibile il cui contenuto viene usato dalle 
	//procedure di controllo della UI
	$col = $table->aggiungiColonna("nomeamm", "nomeamm", false, false, false);
	
	// verifica che non sia stato richiesto un eventuale input dati
	// (ovviamente la chiamata ha senso solo se la tabella prevede input)
	$table->leggiValoriIngresso ();
	if ($table->daAggiornare())
		{
		if (!($table->salva (true)))
			exit("Errore su db: " . $table->righeDB->nrErrore() . " - " . $table->righeDB->messaggioErrore() ."<hr>");
		}

	// lettura dal database delle righe che andranno a popolare la tabella
	if (!$table->caricaRighe())
		exit("Errore su db: " . $table->righeDB->nrErrore() . " - " . $table->righeDB->messaggioErrore() ."<hr>");
		
	// se ci viene passato il parametro xml in query-string, l'utnte vuole
	// solo vedere l'xml generato per l'oggetto waTabella; ovviamente viene 
	// mostrato solo quello della prima tabella della pagina
	if ($_GET['xml'])
		exit($table->mostraXML());
	
	return $table;
	}

//*************************************************************************
function mostraEliminabilita(waTabella $table)
	{		
	return $table->record->valore("importo") <= 100000 ? 'si' : 'no';
	}

//*************************************************************************
function verificaAbilitaElimina(waTabella $table)
	{		
	return $table->record->valore("importo") <= 100000;
	}

//*****************************************************************************
function showArr($val)
	{
	echo "<pre>" . print_r($val, true) . "</pre><hr>";
	}
	
//*****************************************************************************
function creaTop()
	{
	// intestazione della pagina html...
	header("Content-Type: text/html; charset=UTF-8");
	$buffer = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
			<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='it' lang='it'>";

	$buffer .= "<head><title>test watabella</title><meta http-equiv='Content-Type' content='text/html;charset=utf-8' /></head>\n";
	$buffer .= "<body>\n";
	
	return $buffer;
	}
	
//*****************************************************************************
function creaFooter()
	{
	// chiusura della pagina html
	return "</body></html>";
	}
	
//*****************************************************************************
function fammeloStrict($buffer)
	{
	// spostiamo eventuali "<link ...>" nell'head per essere "strict"
	list ($pre_head, $resto) = explode("<head>", $buffer, 2);
	list ($head, $resto) = explode("</head>", $resto, 2);
	list ($tra_head_e_body, $body) = explode("<body", $resto, 2);
	while (true)
		{
		list ($prima, $dopo) = explode("<link ", $body, 2);
		if (!$dopo)
			break;
		list ($link, $dopo) = explode(">", $dopo, 2);
		$head .= "<link $link" . (substr(rtrim($link), -1) == "/" ? '' : " /") . ">\n";
		$body = "$prima$dopo";
		}

	$buffer = "$pre_head\n<head>\n$head</head>$tra_head_e_body\n<body$body";

	return $buffer;
	}

//*****************************************************************************
function fammiUnaRPC($id_organismo)
	{
	$dbconn = wadb_dammiConnessione(FILE_CONFIG_DB);
	if ($dbconn->nrErrore())
		exit("Errore connetti: " . $dbconn->nrErrore() . " - " . $dbconn->messaggioErrore());
		
	$righeDB = new waRigheDB($dbconn);
	$sql = "SELECT * FROM corsi WHERE id_organismo='$id_organismo' ORDER BY id_corso";
	$righeDB->caricaDaSql($sql);
	if ($dbconn->nrErrore())
		exit("Errore caricaDaSql: " . $dbconn->nrErrore() . " - " . $dbconn->messaggioErrore());
		
	$retval = array();
	foreach ($righeDB->righe as $riga)
		$retval[$riga->valore("id_corso")] = $riga->valore("nome");
		
	return $retval;
	}
	
?>