<?php
error_reporting(E_ERROR|E_WARNING);

include("../wadb.inc.php");

// file contenente i parametri di connessione al database
define ("FILE_CONFIG_DB", dirname(__FILE__) . "/dbconfig.inc.php");

// **** main flow ******
creaTop();
showForm();
if ($_POST)
	showResult();
creaFooter();

//*****************************************************************************
function creaTop()
	{
	header("Content-Type: text/html; charset=UTF-8");
	echo "
	<html>
	<body style='font-family: verdana, helvetica; font-size: 11px'>
	";
	}
	
//*****************************************************************************
function creaFooter()
	{
	echo "	</body>
			</html>";
	
	}
	
//*****************************************************************************
// costruisce e manda in output il modulo di richiesta comando
function showForm()
	{
	$sql = $_POST['sql'] ? $_POST['sql'] :
			"SELECT * FROM organismi WHERE id_organismo<'1000'" ;
	
	$rowstart = $_POST['rowstart'] ?  $_POST['rowstart'] : 0;
	$rowlimit = $_POST['rowlimit'] ? $_POST['rowlimit'] : 10;
	
	echo "
	
	<center>
	<p>
	Inserisci la query...
	<p>
	<form action='' method='post'>
	<textarea name='sql' style='width: 500px; height: 120px; border: #000000 1px solid;'>$sql</textarea>
	<p>
	Dalla riga nr:
	<input name='rowstart' value='$rowstart' size='10'>
	<p>
	Nr. righe:&nbsp;&nbsp;&nbsp;&nbsp;
	<input name='rowlimit' value='$rowlimit' size='10'>
	<p>
	<input type='submit' value='invia...'>
	
	</form>
	</p>
	</center>
	<hr>";
	
	}
	
//*****************************************************************************
// mostriamo i risultati della query sql
function showResult()
	{
	$dbconn = wadb_dammiConnessione(FILE_CONFIG_DB);
	if ($dbconn->nrErrore()) exit("Errore connetti: " . $dbconn->nrErrore() . " - " . $dbconn->messaggioErrore() . "<hr>");
	
	$tipoQuery = dammiTipoQuery($sql);
	if ($tipoQuery == "SELECT")
		{
		$rs = new waRigheDB($dbconn);
		$rs->caricaDaSql($_POST['sql'], $_POST['rowlimit'], $_POST['rowstart']);
		if ($dbconn->nrErrore()) exit("Errore caricaDaSql: " . $dbconn->nrErrore() . " - " . $dbconn->messaggioErrore() . "<hr>");
		mostraRecordset($rs);
		}
	elseif ($tipoQuery == "INSERT")
		{
		$dbconn->esegui($_POST['sql']);
		if ($dbconn->nrErrore()) exit("Errore esegui INSERT: " . $dbconn->nrErrore() . " - " . $dbconn->messaggioErrore() . "<hr>");
		echo "Operazione terminata correttamente; id inserito = " . $dbconn->ultimoIdInserito();
		}
	elseif ($tipoQuery == "UPDATE")
		{
		$dbconn->esegui($_POST['sql']);
		if ($dbconn->nrErrore()) exit("Errore esegui UPDATE: " . $dbconn->nrErrore() . " - " . $dbconn->messaggioErrore() . "<hr>");
		echo "Operazione terminata correttamente";
		}
	elseif ($tipoQuery == "DELETE")
		{
		$dbconn->esegui($_POST['sql']);
		if ($dbconn->nrErrore()) exit("Errore esegui DELETE: " . $dbconn->nrErrore() . " - " . $dbconn->messaggioErrore() . "<hr>");
		echo "Operazione terminata correttamente";
		}
	else
		echo "Comando non riconosciuto";
	
	$dbconn->disconnetti();
	
	}

//*****************************************************************************
// definisce che tipo di queryu e' stata richiesta
function dammiTipoQuery($sql)
	{
	list($statement, $resto) = explode(" ", $_POST['sql'], 2);
	return trim(strtoupper($statement));
	}

//*****************************************************************************
// mostra la tabella dei record letti
function mostraRecordset(waRigheDB $rs)
	{
	echo "record da " . ($_POST['rowstart'] + 1) . 
			" a " . 
			(count($rs->righe) + $_POST['rowstart']) . 
			" di " . $rs->nrRigheSenzaLimite() . "<hr>";
	
	echo "<table style='border: none; margin: 0px; padding: 0px; border-collapse: collapse; font-family: verdana, helvetica; font-size: 11px'>\n";
	
	// intestazione colonne
	echo "\t<tr>\n";
	for ($i = 0; $i < $rs->nrCampi(); $i++)
		echo "\t\t<td style='border: #000000 1px solid; padding-right: 5px; background-color: #c0c0c0;'>" . $rs->nomeCampo($i) . "</td>\n";
	echo "\t</tr>\n";
		
	// ciclo delle righe del recordset...
	foreach ($rs->righe as $riga)
		{
		echo "\t<tr>\n";
		for ($i = 0; $i < $rs->nrCampi(); $i++)
			echo "\t\t<td style='border: #000000 1px solid; padding-right: 5px'>" . $riga->valore($i) . "</td>\n";
		echo "\t</tr>\n";
		}
		
	echo "</table>\n";
		
		
	}

	
?>