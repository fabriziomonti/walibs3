<?php
/**
 * file di configurazione tipo di una connessione DB
 * 
 * Il file deve essere copiato all'interno della directory della propria 
 * applicazione e valorizzato secondo le proprie esigenze.
 * <br/><br/>
 * Il significato dei parametri da valorizzare è il seguente:
 * 
 * <b>$WADB_TIPODB</b><br/>
 * Tipo database; si vedano le defines WADB_TIPODB_* in {@link wadb.inc.php}
 * <br/><br/>
 * <b>$WADB_HOST</b><br/>
 * Nome o indirizzo IP host di residenza del db
 * <br/><br/>
 * <b>$WADB_NOMEUTENTE</b><br/>
 * Nome utente per l'accesso al db
 * <br/><br/>
 * <b>$WADB_PASSWORD</b><br/>
 * Password utente per l'accesso al db
 * <br/><br/>
 * <b>$WADB_NOMEDB</b><br/>
 * Nome del db
 * <br/><br/>
 * <b>$WADB_PORTA</b><br/>
 * Porta sui cui viene condiviso il db (vuoto per porta di default del RDBMS)
 * <br/><br/>
 * <b>$WADB_NOMELOG</b><br/>
* Nome di un file sequenziale dove vengono loggati tutti gli acessi in scrittura al db 
* (anonimi, salvo l'ip di provenienza)
 * <br/><br/>
 * <b>$WADB_LOG_CALLBACK_FNC</b><br/>
* Nome di una funzione callback invocata ad ogni accesso al db in scrittura.
* Alla funzione, se esistente, viene passato come parametro la stringa sql in esecuzione. E' cosi'
* possibile per una applicazione definire un proprio logging, che riporti eventuali dati dell'utente
* che ha invocato la scrittura su db. La variabile puo' anche contenere un metodo: in questo caso sara'
* un array di tre elementi:
* o nome della classe che contiene il metodo
* o nome di una proprieta' statica della classe che restituisce un' istanza della classe
* o nome del metodo da invocare
 * 
 * Si presti attenzione al fatto che le variabili in questione, essendo incluse
 * all'interno di una funzione, non hanno scope globale, ma locale alla funzione
 * che include il file.
 * 
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

/**
* 
* Tipo database; si vedano le defines WADB_TIPODB_* in {@link wadb.inc.php}
*/
$WADB_TIPODB = '';

/**
* 
* Nome o indirizzo IP host di residenza del db
*/
$WADB_HOST = '';

/**
 
* Nome utente per l'accesso al db
*/
$WADB_NOMEUTENTE = '';

/**
* 
* Password utente per l'accesso al db
*/
$WADB_PASSWORD = '';

/**
* 
* Nome del db
*/
$WADB_NOMEDB = '';

/**
* 
* Porta sui cui viene condiviso il db (vuoto per porta di default del RDBMS)
*/
$WADB_PORTA = '';


/**
* 
* Nome di un file sequenziale dove vengono loggati tutti gli acessi in scrittura al db 
* (anonimi, salvo l'ip di provenienza)
*/
$WADB_NOMELOG = '';

/**
* 
* Nome di una funzione callback invocata ad ogni accesso al db in scrittura.
* Alla funzione, se esistente, viene passato come parametro la stringa sql in esecuzione. E' cosi'
* possibile per una applicazione definire un proprio logging, che riporti eventuali dati dell'utente
* che ha invocato la scrittura su db. La variabile puo' anche contenere un metodo: in questo caso sara'
* un array di tre elementi:
* o nome della classe che contiene il metodo
* o nome di una proprieta' statica della classe che restituisce un' istanza della classe
* o nome del metodo da invocare
*/
$WADB_LOG_CALLBACK_FNC = '';

?>