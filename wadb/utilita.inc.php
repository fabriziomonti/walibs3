<?php
/**
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* -
*
* funzione che restituisce l'oggetto waConnessioneDB dal file di configurazione 
* passato come parametro
* @param string $nomeFileConfigurazione path del file di configurazione
* @return waConnessioneDB
*/
function wadb_dammiConnessione($nomeFileConfigurazione = '') 
	{
	$nomeFileConfigurazione = empty($nomeFileConfigurazione) ?
								dirname(__FILE__) . "/config.inc.php" :
								$nomeFileConfigurazione;
	include $nomeFileConfigurazione;
	// inclusione del file contenente il driver specifico definito nel file
	// di configurazione
	include_once(dirname(__FILE__) . "/wadbdriver_$WADB_TIPODB.class.php");
	$driver = "waConnessioneDB_$WADB_TIPODB";
	$dbconn = new $driver($WADB_TIPODB, $WADB_HOST, $WADB_NOMEDB, 
							$WADB_NOMEUTENTE, $WADB_PASSWORD, $WADB_PORTA,
							$WADB_NOMELOG, $WADB_LOG_CALLBACK_FNC);
	$esito = $dbconn->connetti();
	return $dbconn;
	}

//*****************************************************************************
?>