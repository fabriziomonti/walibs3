<?php
/**
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

//***************************************************************************
/**
* classe astratta per la connessione fisica ad un database 
 * 
 * Questa classe non fa assolutamente niente, quindi è perfettamente inutile 
 * istanziarne un oggetto; fornisce unicamente l'interfaccia programmatica
 * verso i driver dei rispettivi database.
 * <br><br>
 * Per istanziare una vera connessione alla base dati è necessario utilizzare
 * la funzione procedurale {@link wadb_dammiConnessione}, la quale restituirà
 * un oggetto che implementerà l'interfaccia documentata da questa classe.
*
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waConnessioneDB
	{
	/**
	* 
	* Tipo database; si vedano le defines WADB_TIPODB_* in {@link wadb.inc.php}
	*/
	var $WADB_TIPODB = '';
	
	/**
	* 
	* Nome o indirizzo IP host di residenza del db
	*/
	var $WADB_HOST = '';
	
	/**
	* 
	* Nome utente per l'accesso al db
	*/
	VAR $WADB_NOMEUTENTE = '';
	
	/**
	* 
	* Password utente per l'accesso al db
	*/
	var $WADB_PASSWORD = '';
	
	/**
	* 
	* Nome del db
	*/
	var $WADB_NOMEDB = '';
	
	/**
	* 
	* Porta sui cui viene condiviso il db
	*/
	var $WADB_PORTA = '';
	
	/**
	* 
	* Nome di un file sequenziale dove vengono loggati tutti gli acessi in scrittura al db 
	* (anonimi, salvo l'ip di provenienza)
	*/
	var $WADB_NOMELOG = '';
	
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
	var $WADB_LOG_CALLBACK_FNC = '';
	
	
	//***************************************************************************
	/**
	* -
	* 
	* costruttore.
	 * 
	 * @param integer $WADB_TIPODB vedi parametri file di configurazione {@link config.inc.php}
	 * @param string $WADB_HOST vedi parametri file di configurazione {@link config.inc.php}
	 * @param string $WADB_NOMEDB vedi parametri file di configurazione {@link config.inc.php}
	 * @param string $WADB_NOMEUTENTE vedi parametri file di configurazione {@link config.inc.php}
	 * @param string $WADB_PASSWORD vedi parametri file di configurazione {@link config.inc.php}
	 * @param string $WADB_NOMEDB vedi parametri file di configurazione {@link config.inc.php}
	 * @param string $WADB_PORTA vedi parametri file di configurazione {@link config.inc.php}
	 * @param string $WADB_NOMELOG vedi parametri file di configurazione {@link config.inc.php}
	 * @param string|array $WADB_LOG_CALLBACK_FNC vedi parametri file di configurazione {@link config.inc.php}
	*/ 
	function __construct($WADB_TIPODB, $WADB_HOST, $WADB_NOMEDB, 
							$WADB_NOMEUTENTE, $WADB_PASSWORD, $WADB_PORTA,
							$WADB_NOMELOG, $WADB_LOG_CALLBACK_FNC)
		{
		$this->WADB_TIPODB = $WADB_TIPODB;
		$this->WADB_HOST = $WADB_HOST;
		$this->WADB_NOMEUTENTE = $WADB_NOMEUTENTE;
		$this->WADB_PASSWORD = $WADB_PASSWORD;
		$this->WADB_NOMEDB = $WADB_NOMEDB;
		$this->WADB_PORTA = $WADB_PORTA;
		$this->WADB_NOMELOG = $WADB_NOMELOG;
		$this->WADB_LOG_CALLBACK_FNC = $WADB_LOG_CALLBACK_FNC;
		}
		
	//***************************************************************************
	/**
	* Connette il database.
	 * 
	* @return boolean per conoscere l'esatto esito del metodo occorre invocare
	* il metodo {@link nrErrore}
	*/ 
	function connetti()
		{
		}
		
	//***************************************************************************
	/**
	* -
	* 
	* disconnette il database (abortisce eventuali transazioni non committate).
	* @return void
	*/ 
	function disconnetti()
		{
		}
		
		
	//***************************************************************************
	/**
	* -
	* 
	* ritorna l'ultimo codice di errore restituito dal database.
	* @return string
	*/ 
	function nrErrore()
		{
		}
		
	//***************************************************************************
	/**
	* -
	* 
	* ritorna l'ultimo messaggio di errore restituito dal database.
	* @return string
	*/ 
	function messaggioErrore()
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* Esegue un comando SQL sul database connesso.
	* 
	* @param string $sql SQL da eseguire
	* @return mixed i dati grezzi ottenuti dalla query o FALSE in caso di errore;
	* per conoscere l'esatto esito del metodo occorre invocare 
	* il metodo {@link nrErrore}
	*/
	function esegui($sql)
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* Inizia una transazione.
	* @return void
	*/ 
	function iniziaTransazione()
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* Conferma una transazione aperta in precedenza con {@link iniziaTransazione}.
	* @return void
	*/ 
	function confermaTransazione()
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* Annulla una transazione aperta in precedenza con {@link iniziaTransazione}.
	* @return void
	*/ 
	function annullaTransazione()
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* ritorna l'ultimo identificativo univoco inserito nel database a fronte di INSERT
	* (posto che la tabella sia dotata di una chiave primaria autoincrementale).
	* @return integer
	*/ 
	function ultimoIdInserito()
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma una data "since-the-epoch" nel formato SQL DATE richiesto dal database.
	* @param integer $data data in formato "since-the-epoch"
	* @return string la data in formato SQL
	*/ 
	function dataSql($data)
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma una data/ora "since-the-epoch" nel formato SQL DATETIME richiesto
	* dal database.
	* @param integer $dataOra data/ora in formato "since-the-epoch"
	* @return string la data/ora in formato SQL
	*/ 
	function dataOraSql($dataOra)
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma una ora "since-the-epoch" (ignorando anno, mese, giorno) nel 
	* formato SQL TIME richiesto dal database.
	* @param integer $ora ora in formato "since-the-epoch"
	* @return string l'ora in formato SQL
	*/ 
	function oraSql($ora)
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma una stringa nel formato SQL richiesto dal database.
	* @param string $stringa  stringa da convertire
	* @return string  stringa convertita
	*/ 
	function stringaSql($stringa)
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma un intero nel formato SQL richiesto dal database.
	* @param integer $intero  intero da convertire
	* @return string  stringa convertita
	*/ 
	function interoSql($intero)
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma un numero decimale nel formato SQL richiesto dal database.
	* @param float $decimale  idecimale da convertire
	* @return string  stringa convertita
	*/ 
	function decimaleSql($decimale)
		{
		}
		
		
	//***************************************************************************
	/**
	* -
	*
	* restituisce il valore NULL come richiesto dal db.
	* @return string  
	*/ 
	function nulloSql()
		{
		}
		
	//***************************************************************************
	//******* inizio metodi semi-protected ****************************************
	//***************************************************************************
	
	//***************************************************************************
	/**
	* -
	*
	* prende un campo come arriva da PHP e lo converte nel formato SQL
	* il metodo non e' documentato perche' ha senso se chiamato solo dalla
	* classe waRigheDB
	* @param mixed $dato dato in formato PHP  da convertire
	* @param string $tipoDB tipo del campo sul database
	* @return string valore da inserire nella query SQL
	* @ignore
	*/ 
	function valoreSql($valore, $tipoDB)
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* prende un campo come arriva da db e lo converte nel formato usabile da PHP
	* il metodo non e' documentato perche' ha senso se chiamato solo dalla
	* classe waRigheDB
	* @param mixed $dato dato in formato DB  da convertire
	* @param string $tipoDB tipo del campo sul database
	* @return mixed  valore PHP
	* @ignore
	*/ 
	function convertiCampo($campo, $tipoDB)
		{
		}
	
	//***************************************************************************
	/**
	* - 
	*
	* in pratica e' un duplicato di @{link esegui}, salvo che viene utilizzata
	* dal recordset per passare e ricevere piu' informazioni
	* il metodo non e' documentato perche' ha senso se chiamato solo dalla
	* classe waRigheDB
	* @param string $sql query sql
	* @param int $nrRighe numero max di righe che la query deve restituire
	* @param int $rigaIniziale numero di righe di cui effettuare lo skip (offset)
	* @return array : 
	* - nel primo elemento array info colonne; 
	* - nel secondo elemento array dei valori cosi' come ritornati dal db (crudi)
	* - nel terzo elemento il nr. di righe che soddisfano le condizioni, 
	*   indipendentemente dal limit imposto
	* @ignore
	*/ 
	function eseguiEstesa($sql, $nrRighe = null, $rigaIniziale = 0)
		{
		}
		
	//***************************************************************************
	//******* inizio metodi protected *********************************************
	//***************************************************************************
	
	//***************************************************************************
	/**
	* -
	*
	* restituisce il meta-tipo (ossia il tipo applicativo) di un campo del db
	* @param string $tipoDB tipo del campo sul database
	* @return string  meta-tipo
	* @access protected
	* @ignore
	*/ 
	protected function dammiTipoCampoApplicativo($tipoDB)
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* restituisce le info di una colonna
	* @return array info colonna
	* @access protected
	* @ignore
	*/ 
	protected function impostaAttributiColonne($queryId, $colIdx)
		{
		}
		
	//***************************************************************************
	/**
	* -
	*
	* logga una query in scrittura su db; se valorizzata, chiama anche 
	* la callback function
	* @access protected
	* @ignore
	*/ 
	protected function loggaQuery($sql)
		{

		if (!$this->WADB_NOMELOG && !$this->WADB_LOG_CALLBACK_FNC)
			return;
			
		list($comando, $resto) = explode(" ", strtoupper(trim($sql)), 2);
		if ($comando != "INSERT" && $comando != "UPDATE" && $comando != "DELETE" && $comando != "DROP" && $comando != "TRUNCATE")
			return;

		// verifichiamo se c'e' una rientranza
		$stack = debug_backtrace();
		for ($i = 1; $i < count($stack); $i++)
			{
			if ($stack[$i]['class'] == __CLASS__ && $stack[$i]['function'] == __FUNCTION__)
				return;
			}

		if ($this->WADB_NOMELOG)
			{
		    $riga = date("Y-m-d H:i:s") . " ||| " .
		    		str_pad($_SERVER['REMOTE_ADDR'], 15) . " ||| " .
		    		str_pad($_SERVER['PHP_SELF'], 40) . " ||| " .
		    		$sql . "\r\n";
		    $fp = fopen($this->WADB_NOMELOG, "a");
		    fwrite($fp, $riga);
		    fclose($fp);
			}			
			
		if ($this->WADB_LOG_CALLBACK_FNC)
			{ 
			if (is_string($this->WADB_LOG_CALLBACK_FNC) && function_exists($this->WADB_LOG_CALLBACK_FNC))
				call_user_func($this->WADB_LOG_CALLBACK_FNC, $sql);
			elseif (is_array($this->WADB_LOG_CALLBACK_FNC))
				{
				list($applClass, $varClassInst, $method) = $this->WADB_LOG_CALLBACK_FNC;
				$applInstance = eval("return $applClass::$varClassInst;");
				if (method_exists($applInstance, $method))
					call_user_func(array($applInstance, $method), $sql);
				}
			}
			
		}
		
	//***************************************************************************
	/**
	* -
	*
	* distruttore
	 * @ignore
	*/ 
	function __destruct()
		{
		}

//***************************************************************************
//****  fine classe waConnessioneDB *****************************************
//***************************************************************************
	}	// fine classe 


?>