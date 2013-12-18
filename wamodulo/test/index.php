<?php
error_reporting(E_ERROR|E_WARNING);
include("../wamodulo.inc.php");

// file contenente i parametri di connessione al database
define ("FILE_CONFIG_DB", dirname(__FILE__) . "/dbconfig.inc.php");

// main flow ******

// costruiamo i moduli che andranno gestiti nella pagina
$modulo0 = dammiModulo("modulo0");
$modulo1 = dammiModulo("modulo1");

// verifichiamo quale e' l'azione da compiere 
if ($modulo0->daEliminare())
	doDelete($modulo0);
elseif ($modulo0->daAggiornare())
	doUpdate($modulo0);
elseif ($modulo1->daEliminare())
	doDelete($modulo1);
elseif ($modulo1->daAggiornare())
	doUpdate($modulo1);
else
	// se non e' stata richiesta un'operazione, allora mostriamo i moduli per 
	// permettere l'input utente
	showPage($modulo0, $modulo1);
	
	
//*****************************************************************************
function showPage(waModulo $modulo0, waModulo $modulo1)
	{
	
	if ($_GET['xml'])
		exit($modulo0->mostraXML());
		
	// intestazione della pagina html...
	$outputBuffer = creaTop();

	// output del modulo
	$outputBuffer .= $modulo0->mostra(true);
	$outputBuffer .= $modulo1->mostra(true);

	// chiusura della pagina html
	$outputBuffer .= creaFooter();

	// risistema il codice html rendendolo "strict"
	$outputBuffer = fammeloStrict($outputBuffer);
	
	// output del buffer contenente la pagina 
	exit($outputBuffer);
	
	}

//*****************************************************************************
function creaTop()
	{
	// intestazione della pagina html...
	header("Content-Type: text/html; charset=UTF-8");
	$buffer = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
			<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='it' lang='it'>";

	$buffer .= "<head><title>test wamodulo</title><meta http-equiv='Content-Type' content='text/html;charset=utf-8' /></head>\n";
	$buffer .= "<body>\n";
	
	return $buffer;
	}
	
//*****************************************************************************
function creaFooter()
	{
	// includiamo i js specifici della pagina
	$buffer = "<script type='text/javascript' src='ui/js/index.js'></script>\n";
	
	// chiusura della pagina html
	$buffer .= "</body></html>";

	return $buffer;
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
function dammiModulo($nome)
	{
	// creazione del modulo...
	$modulo = new waModulo($_SERVER['REQUEST_URI']);
	$modulo->nome = $nome;
	
	// creazione del recorset da associare al modulo
	$dbconn = wadb_dammiConnessione(FILE_CONFIG_DB);
	if ($dbconn->nrErrore()) exit("Errore connetti: " . $dbconn->nrErrore() . " - " . $dbconn->messaggioErrore() . "<hr>");
  	$sql = "SELECT * FROM corsi WHERE id_corso=" . $dbconn->interoSql($_GET['id_corso']);
	$righeDB = new waRigheDB($dbconn);
	$righeDB->caricaDaSql($sql, $nrRighe);
	if ($dbconn->nrErrore()) exit("Errore caricaDaSql: " . $dbconn->nrErrore() . " - " . $dbconn->messaggioErrore() . "<hr>");

	// associazione del recordset al modulo...
	$modulo->righeDB = $righeDB;
	
	// definizioni delle proprieta' del modulo...
	$modulo->larghezza = 800;
 	$modulo->nomeCampoRecId = 'id_corso';
	
 	// inserimento dei controlli all'interno del modulo
	$ctrl = $modulo->aggiungiSelezione("id_organismo", "Organismo");
		$ctrl->lista = dammiLista("organismi", "id_organismo", "nome");
	$ctrl = $modulo->aggiungiSelezione("id_amministrazione", "Sigla");
		$ctrl->lista = dammiLista("amministrazioni", "id_amministrazione", "sigla");
//		$ctrl->obbligatorio = true;
 	$modulo->aggiungiTesto("rifpa", "Rif. P.A.", false, true);
 	$ctrl = $modulo->aggiungiTesto("nome", "Titolo corso");
		$modulo->giustificaControllo($ctrl);
 	$modulo->aggiungiIntero("nr_ore", "Monte ore complessivo");
 	$ctrl = $modulo->aggiungiData("data_inizio", "Data inizio");
		$ctrl->valore = time();
		$ctrl->annoPartenza = 1901;
		$ctrl->annoTermine = 2050;
 	$ctrl = $modulo->aggiungiDataOra("data_fine", "Data fine");
		$ctrl->annoPartenza = 1901;
		$ctrl->annoTermine = 2050;
 	$ctrl = $modulo->aggiungiValuta("importo", "Importo complessivo");
	
	$ctrl = $modulo->aggiungiElemento("test_non_controllo");
		$ctrl->corrispondenzaDB = false;
		$ctrl->valore = file_get_contents("../doc/index.html");
		$ctrl->sinistra = $modulo->sinistraEtichette;
		$ctrl->altezza = 300;
		$modulo->giustificaControllo($ctrl, false);
	
	$ctrl = $modulo->aggiungiAreaTesto("area", "area");
		$ctrl->corrispondenzaDB = false;
	$ctrl = $modulo->aggiungiLogico("check", "check");
		$ctrl->corrispondenzaDB = false;
		
	$ctrl = $modulo->aggiungiMultiSelezione("multiselect", "multiselect");
		$ctrl->corrispondenzaDB = false;
		$ctrl->lista = array (5 => "cinque",10 => "dieci" , 20 => "venti",30 => "trenta" , 40 => "quaranta", 50 => "cinquanta" , 60 => "sessanta");
		$ctrl->valore = array (10, 30, 50);
		$ctrl->listaNonSelezionabili = array (20, 60);
		$ctrl->altezza = 100;
		$modulo->giustificaControllo($ctrl, false);
		
	$ctrl = $modulo->aggiungiCaricaFile("file", "file");
		$ctrl->corrispondenzaDB = false;
		$ctrl->valore = "jethro.pdf";
		$ctrl->larghezza = 260;
 	$ctrl = $modulo->aggiungiOra("soloora", "ora test");
		$ctrl->corrispondenzaDB = false;
	
	// bottoni del modulo
	$butt = $modulo->aggiungiBottone("btnInvia", "REGISTRA");
		$butt->alto += $modulo->interlineaControlli * 3;
		$butt->larghezza = 90;
  	if ($_GET['id_corso'])
  		{
		$butt2 = $modulo->aggiungiBottone("btnElimina", "ELIMINA");
		$butt2->alto = $butt->alto;
		$butt2->sinistra = $butt->sinistra + $butt->larghezza;
		$butt2->larghezza = $butt->larghezza;
		$butt2->elimina = true;
  		}
	
	setBottone($modulo, "cmdAbilita", "abilita", 0);
	setBottone($modulo, "cmdDisabilita", "disabilita", 1);
	setBottone($modulo, "cmdObbligatorio", "obbligatori", 2);
	setBottone($modulo, "cmdNonObbligatorio", "non obblig.", 3);
	setBottone($modulo, "cmdVisualizza", "visualizza", 4);
	setBottone($modulo, "cmdNascondi", "nascondi", 5);
	
//exit($modulo->mostraXML());	
	// lettura di eventuali dati in input di cui il modulo potrebbe essere
	// destinatario
	$modulo->leggiValoriIngresso();
			
	return $modulo;
	}
	
//*****************************************************************************
function setBottone(waModulo $modulo, $nome, $valore, $pos)
	{
	static $alto = false;
	
	$butt = $modulo->aggiungiBottone($nome, $valore);
	if (!$alto)
		$alto = $butt->alto += $modulo->interlineaControlli  * 2;
	else
		$butt->alto = $alto;
	$butt->larghezza = 90;
	$butt->sinistra += $pos * $butt->larghezza;
	$butt->invia = false;
	}
	
//*****************************************************************************
function dammiLista($table, $idFieldName, $descriptionFieldName, $where = '')
	{
	$dbconn = wadb_dammiConnessione(FILE_CONFIG_DB);
	if ($dbconn->nrErrore())
		exit("Errore connetti: " . $dbconn->nrErrore() . " - " . $dbconn->messaggioErrore() . "<hr>");
		
	$righeDB = new waRigheDB($dbconn);
	$sql = "SELECT * FROM $table $where ORDER BY $descriptionFieldName";
	$righeDB->caricaDaSql($sql);
	if ($dbconn->nrErrore())
		exit("Errore caricaDaSql: " . $dbconn->nrErrore() . " - " . $dbconn->messaggioErrore() . "<hr>");
		
	$retval = array();
	foreach ($righeDB->righe as $riga)
		$retval[$riga->valore($idFieldName)] = $riga->valore($descriptionFieldName);
		
	return $retval;
	}


//*****************************************************************************
// funzione RPC che viene richiamata dal client
function fammiUnaRPC($id_amministrazione)
	{
	return dammiLista("corsi", "id_corso", "nome", " WHERE id_amministrazione='$id_amministrazione'");
	}
	
//*****************************************************************************
// inserimento/modifica su db del record gestito tramite il modulo
function doUpdate($modulo)
	{
	$esito = $modulo->verificaObbligo();
	if (!$esito) exit("mancano campi obbligatori!<hr>");
	$esito = $modulo->salva(true);
	if (!$esito) exit("Errore inserimento/modifica: " . $modulo->righeDB->connessioneDB->nrErrore() . " - " . $modulo->righeDB->connessioneDB->messaggioErrore() . "<hr>");

	$recid = $_GET['id_corso'] ? $_GET['id_corso'] : $esito;

	echo "Operazione eseguita correttamente<p>" .
				"<a href=''>torna al modulo vuoto</a><br>" .
				"<a href='?id_corso=$recid'>torna in modifica sul record</a><br>";	

	}

//*****************************************************************************
// eliminazione da db del record gestito tramite il modulo
function doDelete($modulo)
	{
	if (!$modulo->elimina(true))
		exit("Errore eliminazione: " . $modulo->righeDB->connessioneDB->nrErrore() . " - " . $modulo->righeDB->connessioneDB->messaggioErrore() . "<hr>");
	
	echo "Operazione eseguita correttamente<p>" .
				"<a href=''>torna al modulo</a>";	

	}
	
?>