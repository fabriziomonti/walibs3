<?php
/**
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

/**
* @ignore
*/
require_once(dirname(__FILE__ ). '/wadbdriver.class.php');

//***************************************************************************
//****  classe waConnessioneDB_mssql **********************************************
//***************************************************************************
/**
* waConnessioneDB_mssql
* 
* classe per la connessione fisica ad un database MSSQL
*
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waConnessioneDB_mssql extends waConnessioneDB 
	{
	
	/**
	* @access protected
	* @ignore
	*/ 
	protected $DBConn = false;

	/**
	* @access protected
	* @ignore
	*/ 
	protected $lastError = 0;

	/**
	* @access protected
	* @ignore
	*/ 
	protected $lastErrorMsg = "";

	/**
	* @access protected
	* @ignore
	*/ 
	protected $primaryKeyIdx = -1;

	/**
	* @access protected
	* @ignore
	*/ 
	protected $mainTable = "";

	//***************************************************************************
	/**
	* -
	* 
	* connette il database.
	* @return boolean per conoscere l'esatto esito del metodo occorre invocare 
	* il metodo {@link nrErrore}
	* @ignore
	*/ 
	function connetti()
		{
		$host = $this->WADB_HOST;
		if ($this->WADB_PORTA != '')
			$host .= ":" . $this->WADB_PORTA;
		$this->DBConn = @mssql_connect($host, $this->WADB_NOMEUTENTE, $this->WADB_PASSWORD, true);
		if ($this->DBConn === false) 
			return $this->alzaFlagErrore();
		if (@mssql_select_db($this->WADB_NOMEDB, $this->DBConn) === false) 
			return $this->alzaFlagErrore();
		return true;
		}
		
	//***************************************************************************
	/**
	* -
	* 
	* disconnette il database (abortisce eventuali transazioni non committate).
	* @return void
	* @ignore
	*/ 
	function disconnetti()
		{
		if ($this->DBConn === false) 
			return;
		@mssql_close($this->DBConn);
		$this->DBConn = false;
		}
		
		
	//***************************************************************************
	/**
	* -
	* 
	* ritorna l'ultimo codice di errore restituito dal database.
	* @return string
	* @ignore
	*/ 
	function nrErrore()
		{
		return $this->lastError;
		}
		
	//***************************************************************************
	/**
	* -
	* 
	* ritorna l'ultimo messaggio di errore restituito dal database.
	* @return string
	* @ignore
	*/ 
	function messaggioErrore()
		{
		return $this->lastErrorMsg;
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
	* @ignore
	*/ 
	function esegui($sql)
		{
		$qid = @mssql_query($sql, $this->DBConn);
		if ($qid === false)
			return $this->alzaFlagErrore();
			
		$retval = array();
		while ($riga = @mssql_fetch_assoc($qid))
			$retval[] = $riga;
		@mssql_free_result($qid);	
		return $retval;
		}
		
	//***************************************************************************
	/**
	* -
	*
	* Inizia una transazione.
	* @return void
	* @ignore
	*/ 
	function iniziaTransazione()
		{
		$this->esegui('BEGIN TRAN');
		}
		
	//***************************************************************************
	/**
	* -
	*
	* Conferma una transazione aperta in precedenza con {@link iniziaTransazione}.
	* @return void
	* @ignore
	*/ 
	function confermaTransazione()
		{
		$this->esegui('COMMIT TRAN');
		}
		
	//***************************************************************************
	/**
	* -
	*
	* Annulla una transazione aperta in precedenza con {@link iniziaTransazione}.
	* @return void
	* @ignore
	*/ 
	function annullaTransazione()
		{
		$this->esegui('ROLLBACK TRAN');
		}
		
	//***************************************************************************
	/**
	* -
	*
	* ritorna l'ultimo identifica univoco inserito nel database a fronte di INSERT
	* (posto che la tabella sia dotata di una chiave primaria autoincrementale).
	* @return integer
	* @ignore
	*/ 
	function ultimoIdInserito()
		{
		$result = @mssql_query("select @@IDENTITY", $this->DBConn);
		if ($result === false)
			return $this->alzaFlagErrore();
		$riga = @mssql_fetch_array($result, MSSQL_NUM);
		@mssql_free_result($result);	
		return $riga[0];
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma una data "since-the-epoch" nel formato SQL DATE richiesto dal database.
	* @param integer $data data in formato "since-the-epoch"
	* @return string la data in formato SQL
	* @ignore
	*/ 
	function dataSql($data)
		{
		if (is_null($data))
			return $this->nulloSql();
		return date("'Ymd'", $data);
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma una data/ora "since-the-epoch" nel formato SQL DATETIME richiesto
	* dal database.
	* @param integer $dataOra data/ora in formato "since-the-epoch"
	* @return string la data/ora in formato SQL
	* @ignore
	*/ 
	function dataOraSql($dataOra)
		{
		if (is_null($dataOra))
			return $this->nulloSql();
		return date("'Ymd H:i:s'", $dataOra);
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma una ora "since-the-epoch" (ignorando anno, mese, giorno) nel 
	* formato SQL TIME richiesto dal database.
	* @param integer $ora ora in formato "since-the-epoch"
	* @return string l'ora in formato SQL
	* @ignore
	*/ 
	function oraSql($ora)
		{
		if (is_null($ora))
			return $this->nulloSql();
		return "'1980-01-01 " . date("H:i:s'", $ora);
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma una stringa nel formato SQL richiesto dal database.
	* @param string $stringa  stringa da convertire
	* @return string  stringa convertita
	* @ignore
	*/ 
	function stringaSql($stringa)
		{
		if (is_null($stringa))
			return $this->nulloSql();
		return "'" . str_replace("'", "''", $stringa) . "'";
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma un intero nel formato SQL richiesto dal database.
	* @param integer $intero  intero da convertire
	* @return string  stringa convertita
	* @ignore
	*/ 
	function interoSql($intero)
		{
		if (is_null($intero) || $intero === '')
			return $this->nulloSql();
		return $intero . "";
		}
		
	//***************************************************************************
	/**
	* -
	*
	* trasforma un numero decimale nel formato SQL richiesto dal database.
	* @param float $decimale  idecimale da convertire
	* @return string  stringa convertita
	* @ignore
	*/ 
	function decimaleSql($decimale)
		{
		if (is_null($decimale) || $decimale === '')
			return $this->nulloSql();
		return $decimale . "";
		}
		
		
	//***************************************************************************
	/**
	* -
	*
	* restituisce il valore NULL come richiesto dal db.
	* @return string  
	* @ignore
	*/ 
	function nulloSql()
		{
		return 'NULL';
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
			
		switch ($tipoDB)
			{
			case 'DATE': 
				return $this->dataSql($valore);
			
			case 'TIME':
				return $this->oraSql($valore);
			
			case 'DATETIME':
			case 'TIMESTAMP': 
				return $this->dataOraSql($valore);
			
			case 'INT': 
			case 'INTEGER':
			case 'BIGINT':
			case 'TINYINT':
			case 'MEDIUMINT':
			case 'SMALLINT': 
				return $this->interoSql($valore);
			
			case 'FLOAT': 
			case 'DOUBLE':
			case 'DECIMAL':
			case 'REAL':
				return $this->decimaleSql($valore);
			
			default:
				return $this->stringaSql($valore);
			}
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
		$mesiStr = array("jan" => 1, "feb" => 2, "mar" => 3,
							"apr" => 4, "may" => 5, "jun" => 6,
							"jul" => 7, "aug" => 8, "sep" => 9,
							"oct" => 10, "nov" => 11, "dec" => 12);
							
		if (is_null($campo))
			return $campo;
		switch ($tipoDB)
			{
			case 'DATE': 
				list($anno, $mese, $giorno) = explode("-", $campo);
				return mktime(0,0,0, $mese * 1, $giorno * 1, $anno * 1);
			
			case 'TIME':
				list($ora, $min, $sec) = explode(":", $campo);
				return mktime($ora * 1, $min * 1, $sec * 1, 1, 1, 1980);
			
			case 'DATETIME':
			case 'TIMESTAMP': 
				list($mese, $giorno, $anno, $ore) = explode(" ", $campo);
				$mese = $mesiStr[strtolower($mese)];
				list($ora, $min, $sec, $resto) = explode(":", $ore);
				$ampm = strtoupper(substr($ore, -2)); 
				if ($ampm == "AM" && $ora == 12)
					$ora = 0;
				elseif ($ampm == "PM" && $ora == 12)
					{}
				elseif ($ampm == "PM")
					$ora += 12;
				return mktime($ora * 1, $min * 1, $sec * 1, $mese * 1, $giorno * 1, $anno * 1);
			
			case 'INT': 
			case 'INTEGER':
			case 'BIGINT':
			case 'TINYINT':
			case 'MEDIUMINT':
			case 'SMALLINT': 
			case 'FLOAT': 
			case 'DOUBLE':
			case 'DECIMAL':
			case 'REAL':
				return ($campo * 1);
			
			default:
				return utf8_encode($campo);
			}
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
		// cerchiamo l'indice del campo chiave primaria che deve assolutamente
		// esistere nella tabella principale
		$this->mainTable = $this->getMainTableName($sql);
		$this->primaryKeyIdx = $this->getTablePKeyIdx($this->mainTable);

		// la paginazione su sql server e' un po' incasinata...
		if ($rigaIniziale != 0)
			{
			// creiamo la query complessa per poter gestire la paginazione
			$primaryKeyName = $this->getTablePKeyName($this->mainTable, $this->primaryKeyIdx);
			$sqlElems = $this->splitSql($sql);
			$complSql = $this->addTopToSelect($sqlElems['select'], $nrRighe) .
							$sqlElems['from'] . " " .
							$sqlElems['where'] . " " .
							(empty($sqlElems['where']) ? "WHERE " : "AND ") .
							"$primaryKeyName NOT IN " .
								"(SELECT $primaryKeyName FROM " .
									"(" . $this->addTopToSelect($sqlElems['select'], $rigaIniziale) .
									$sqlElems['from'] . " " .
									$sqlElems['where'] . " " .
									$sqlElems['group'] . " " .
									$sqlElems['order'] . ") " .
								"AS Tbl1) " .
							$sqlElems['group'] . " " .
							$sqlElems['order'];
			}
		elseif ($nrRighe !== null)
			{
			$sqlElems = $this->splitSql($sql);
			$complSql = $this->addTopToSelect($sqlElems['select'], $nrRighe) .
							$sqlElems['from'] . " " .
							$sqlElems['where'] . " " .
							$sqlElems['group'] . " " .
							$sqlElems['order'];
			}
		else 
			$complSql = $sql;			

//echo "$complSql<hr>";
			
		$qid = @mssql_query($complSql, $this->DBConn);
		if ($qid === false)
			return $this->alzaFlagErrore();

		// carichiamo le informazioni delle colonne
		$colInfos = array();
		$nrColonne = @mssql_num_fields($qid);	
		for ($i = 0; $i < $nrColonne; $i++)
			$colInfos[] = $this->impostaAttributiColonne($qid, $i);
			
		// carichiamo le righe
		while($rigaCruda = @mssql_fetch_array($qid, MSSQL_NUM))
			$righeCrude[] = $rigaCruda;
		@mssql_free_result($qid);	

		if ($nrRighe !== null)
			{
			// se e' stato richiesto il limit, allora andiamo anche a prelevare
			// il nr di righe che soddisfano la condizione
			$complSql = $sqlElems['select'] . " " . 
							$sqlElems['from'] . " " .
							$sqlElems['where'] . " " .
							$sqlElems['group'];
			$complSql = "SELECT COUNT(*) AS cntr FROM ($complSql) AS Tbl1";
//echo "$complSql<hr>";
			$result = mssql_query($complSql, $this->DBConn);
			$riga = mssql_fetch_array($result, MSSQL_NUM);
			@mssql_free_result($result);	
			$nrRigheNoLimit = $riga[0];
			}
		else
			$nrRigheNoLimit = count($righeCrude);
		
		return array($colInfos, $righeCrude, $nrRigheNoLimit);
		
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
		switch ($tipoDB)
			{
			case 'STRING': 
			case 'CHAR':
			case 'VARCHAR': 
			case 'ENUM': 
			case 'SET': 
				return WADB_STRINGA;
				
			case 'TINYTEXT': 
			case 'TINYBLOB': 
			case 'TEXT':
			case 'LONGTEXT': 
			case 'MEDIUMTEXT':
			case 'IMAGE':
			case 'LONGBLOB': 
			case 'BLOB':
			case 'MEDIUMBLOB':
				return WADB_CONTENITORE;
				
			case 'DATE': 
				return WADB_DATA;
			
			case 'TIME':
				return WADB_ORA;
			
			case 'DATETIME':
			case 'TIMESTAMP': 
				return WADB_DATAORA;
			
			case 'INT': 
			case 'INTEGER':
			case 'BIGINT':
			case 'TINYINT':
			case 'MEDIUMINT':
			case 'SMALLINT': 
				return WADB_INTERO;
				
			case 'FLOAT': 
			case 'DOUBLE':
			case 'DECIMAL':
			case 'REAL':
				return WADB_DECIMALE;
				
			default: 
				return WADB_TIPO_SCONOSCIUTO;
			}
			
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
		$colInfo = mssql_fetch_field($queryId, $colIdx);
		$col['nome'] = $colInfo->name;
		$col['indice'] = $colIdx;
		$col['tipoDB'] = strtoupper($colInfo->type);
		$col['tabella'] = $this->mainTable;
		$col['chiavePrimaria'] = ($this->primaryKeyIdx == $colIdx ? 1 : 0);
		if (strtolower($colInfo->type == 'int'))
			$col['lunghezzaMax'] = 10;
		else
			$col['lunghezzaMax'] = mssql_field_length($queryId, $colIdx);
		$col['tipo'] = $this->dammiTipoCampoApplicativo($col['tipoDB']);
		$col['nrRipetizioni'] = 1;
		return $col;
		}
		
	//***************************************************************************
	/**
	* -
	*
	* alza il flag di errore e torna false
	* @access private
	* @ignore
	*/ 
	private function alzaFlagErrore()
		{
		$this->lastError = $this->dammiCodiceErrore();
		return false;
		}

	//***************************************************************************
	/**
	* -
	*
	* chiede all'engine il codice dell'ultimo errore verificatosi
	 * @ignore
	* @return int il codice di errore
	*/
	protected function dammiCodiceErrore()
		{
		if ($this->DBConn === false) 
			return -1;
			
		$this->lastErrorMsg = @mssql_get_last_message();
		$result = mssql_query("select @@error", $this->DBConn);
		if ($result === false)
			return -1;
		$riga = @mssql_fetch_array($result, MSSQL_NUM);
		@mssql_free_result($result);	
		return $riga[0];
		}
		
	//***************************************************************************
	/**
	*
	*
	* aggiunge la clausola top ad una istruzione select
	* @access protected
	* @ignore
	*/	
	protected function addTopToSelect($select, $top)
		{
		// eliminiamo tutti i doppi spazi in input
		$select = trim($select);
		while(strpos($select, "  ") !== false)
			$select = str_replace("  ", " ", $select);
		if (strtoupper(substr($select, 0, strlen("SELECT DISTINCT "))) == "SELECT DISTINCT ")
			$selectStmt = "SELECT DISTINCT ";
		else
			$selectStmt = "SELECT ";

		$selectParams = substr($select, strlen($selectStmt));
		return "$selectStmt TOP($top) $selectParams ";
		}
		
	//***************************************************************************
	/**
	*
	*
	* restituisce il nome della tabella principale di una query (si suppone la 
	* prima dopo il from)
	* @access protected
	* @ignore
	*/	
	protected function getMainTableName($sql)
		{
		$pattern="/( from )/i";
		list($select, $params) = preg_split($pattern, trim($sql), 2, PREG_SPLIT_NO_EMPTY);
		$pattern="/( |,)/i";
		list($mainTable, $resto) = preg_split($pattern, trim($params), 2, PREG_SPLIT_NO_EMPTY);
		return $mainTable;
		}
		
	//***************************************************************************
	/**
	*
	*
	* restituisce l'indice del campo chiave primaria di una tabella. Una chiave 
	* primaria univoca deve per forza esistere (e' un requisito di ogni tabella 
	* delle walibs).
	* @access protected
	* @ignore
	*/	
	protected function getTablePKeyIdx($table)
		{
		$result = mssql_query("SELECT sysindexkeys.colid FROM sysobjects INNER JOIN sysindexkeys ON sysobjects.id = sysindexkeys.id WHERE sysobjects.name ='$table'", $this->DBConn);
		$riga = @mssql_fetch_array($result, MSSQL_NUM);
		@mssql_free_result($result);	
		return $riga[0] - 1;
		}
		
	//***************************************************************************
	/**
	*
	*
	* restituisce il nome del campo chiave primaria di una tabella
	* @access protected
	* @ignore
	*/	
	protected function getTablePKeyName($table, $keyIdx)
		{
		$result = mssql_query("SELECT TOP(0) * FROM $table", $this->DBConn);
		$colInfo = mssql_fetch_field($result, $keyIdx);
		return $colInfo->name;
		}
		
	//***************************************************************************
	/**
	*
	*
	* divide la query sql nelle varie clausole
	* @access protected
	* @ignore
	*/	
	protected function splitSql($sql)
		{
		$toret = array();
		$toret['select'] = $this->getClause($sql, 'select');
		$toret['from'] = $this->getClause($sql, 'from');
		$toret['where'] = $this->getClause($sql, 'where');
		$toret['group'] = $this->getClause($sql, 'group');
		$toret['order'] = $this->getClause($sql, 'order');
		return $toret;
		}
		
	//***************************************************************************
	/**
	* @access protected
	* @ignore
	*/	
	protected function getClause($sql, $keyword)
		{
		$keyword = strtolower($keyword);
		if ($keyword == "select")
			$inizio = 0;
		else
			$inizio = stripos($sql, " $keyword ");
		if ($inizio === false) 
			return '';
		$clauses = array("select", "from", "where", "group", "order");
		for ($i = 0; $i < count($clauses); $i++)
			{
			if ($keyword == $clauses[$i])
				break;
			}
		for ($i += 1; $i < count($clauses); $i++)
			{
			$fine = stripos($sql, " $clauses[$i] ");
			if ($fine)
				break;
			}
		if ($fine)
			$quanti = $fine - $inizio;
		else 
			$quanti = strlen($sql) - $inizio;
		return trim(substr($sql, $inizio, $quanti));
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
		$this->disconnetti();
		}

//***************************************************************************
//****  fine classe waConnessioneDB_mssql *****************************************
//***************************************************************************
	}	// fine classe 


?>