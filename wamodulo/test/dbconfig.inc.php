<?php
/**
* 
* Tipo database; si vedano le defines WADB_TIPODB_* in {@link wadb.inc.php}
*/
$WADB_TIPODB = WADB_TIPODB_MYSQL;

/**
* 
* Nome o indirizzo IP host di residenza del db
*/
$WADB_HOST = 'localhost';

/**
 
* Nome utente per l'accesso al db
*/
$WADB_NOMEUTENTE = 'watest';

/**
* 
* Password utente per l'accesso al db
*/
$WADB_PASSWORD = 'watest';

/**
* 
* Nome del db
*/
$WADB_NOMEDB = 'testwalibs';

/**
* 
* Porta sui cui viene condiviso il db
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