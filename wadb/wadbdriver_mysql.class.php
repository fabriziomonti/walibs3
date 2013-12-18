<?php
/**
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

/**
* @ignore
*/
require_once(dirname(__FILE__ ). '/wadbdriver.class.php');

//***************************************************************************
//****  classe waConnessioneDB_mysql **********************************************
//***************************************************************************
/**
* waConnessioneDB_mysql
* 
* classe per la connessione fisica ad un database mysql
*
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waConnessioneDB_mysql extends waConnessioneDB 
	{
	
	/**
	* @access protected
	* @ignore
	*/ 
	var $DBConn = false;

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
		$this->DBConn = @mysql_connect($host, $this->WADB_NOMEUTENTE, $this->WADB_PASSWORD, true);
		if ($this->DBConn === false) 
			return false;
		if (@mysql_select_db($this->WADB_NOMEDB, $this->DBConn) === false) 
			return false;
		if ($this->esegui('SET AUTOCOMMIT=1') === false) 
			return false;
		mysql_set_charset("utf8", $this->DBConn);
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
		@mysql_close($this->DBConn);
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
		if ($this->DBConn === false)
			return @mysql_errno();
		return @mysql_errno($this->DBConn);
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
		if ($this->DBConn === false)
			return @mysql_error();
		return @mysql_error($this->DBConn);
		}
		
	//***************************************************************************
	/**
	* -
	*
	* Esegue un comando SQL sul database connesso.
	* @param string $sql SQL da eseguire
	* @return mixed i dati grezzi ottenuti dalla query o FALSE in caso di errore;
	* per conoscere l'esatto esito del metodo occorre invocare 
	* il metodo {@link nrErrore}
	* @ignore
	*/
	function esegui($sql)
		{
		$this->loggaQuery($sql);
		$qid = @mysql_query($sql, $this->DBConn);
		if ($qid === false)
			return false;
		$retval = array();
		while ($riga = @mysql_fetch_assoc($qid))
			$retval[] = $riga;
		@mysql_free_result($qid);	
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
		$this->esegui('SET AUTOCOMMIT=0');
		$this->esegui('BEGIN');
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
		$this->esegui('COMMIT');
		$this->esegui('SET AUTOCOMMIT=1');
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
		$this->esegui('ROLLBACK');
		$this->esegui('SET AUTOCOMMIT=1');
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
		return mysql_insert_id($this->DBConn);
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
		if (is_null($data) || $data === false || $data === '')
			return $this->nulloSql();
		$retval = date("'Y-m-d'", $data);
		if ($retval === false)
			return $this->nulloSql();
		return $retval;
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
		if (is_null($dataOra) || $dataOra === false || $dataOra === '')
			return $this->nulloSql();
		$retval = date("'Y-m-d H.i.s'", $dataOra);
		if ($retval === false)
			return $this->nulloSql();
		return $retval;
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
		return "'1980-01-01 " . date("H.i.s'", $ora);
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
		return "'" . mysql_real_escape_string($stringa, $this->DBConn) . "'";
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
		return ((int) $intero) . "";
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
		return ((float) $decimale) . "";
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
		if (is_null($campo))
			return $campo;
		switch ($tipoDB)
			{
			case 'DATE': 
				if (empty($campo))
					return null;
				if ($campo == "0000-00-00")
					return 0;
				list($anno, $mese, $giorno) = explode("-", $campo);
				return mktime(0,0,0, $mese * 1, $giorno * 1, $anno * 1);
			
			case 'TIME':
				list($ora, $min, $sec) = explode(":", $campo);
				return mktime($ora * 1, $min * 1, $sec * 1, 1, 1, 1980);
			
			case 'DATETIME':
			case 'TIMESTAMP': 
				if (empty($campo))
					return null;
				if ($campo == "0000-00-00 00:00:00")
					return 0;
				list($data, $ore) = explode(" ", $campo);
				list($anno, $mese, $giorno) = explode("-", $data);
				list($ora, $min, $sec) = explode(":", $ore);
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
				return $campo;
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
//echo "$sql<hr>";			
		if ($nrRighe !== null)
			{
			// modifichiamo la query, che ovviamente DEVE essere una select,
			// affinche' mysql possa tornare il nr di righe
			// che soddisfano la condizione, indipendentemente dal limit imposto
			$patternSelect="/(select )/i";
//			list($select, $params) = preg_split($patternSelect,trim($sql), 2, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			$elems = preg_split($patternSelect,trim($sql), 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			$select = array_shift($elems);
			$params = implode("", $elems);
			$sql = "$select SQL_CALC_FOUND_ROWS $params LIMIT $rigaIniziale, $nrRighe";
			}
		$qid = @mysql_query($sql, $this->DBConn);
		if ($qid === false)
			return false;
			
		// carichiamo le informazioni delle colonne
		$colInfos = array();
		$nrColonne = mysql_num_fields($qid);	
		for ($i = 0; $i < $nrColonne; $i++)
			$colInfos[] = $this->impostaAttributiColonne($qid, $i);
			
		// carichiamo le righe
		while ($rigaCruda = @mysql_fetch_array($qid, MYSQL_NUM))
			$righeCrude[] = $rigaCruda;
			
		@mysql_free_result($qid);	
		
		if ($nrRighe !== null)
			{
			// se e' stato richiesto il limit, allora andiamo anche a prelevare
			// il nr di righe che soddisfano la condizione
			$qid = mysql_query("SELECT FOUND_ROWS()", $this->DBConn);
			$nrRigheNoLimit = mysql_result($qid, 0);
			@mysql_free_result($qid);	
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
		$colInfo = mysql_fetch_field($queryId, $colIdx);
		$col['nome'] = $colInfo->name;
		$col['indice'] = $colIdx;
		$col['tipoDB'] = strtoupper($colInfo->type);
		$col['tabella'] = $colInfo->table;
		$col['chiavePrimaria'] = $colInfo->primary_key;
		$col['lunghezzaMax'] = mysql_field_len($queryId, $colIdx);
		$col['tipo'] = $this->dammiTipoCampoApplicativo($col['tipoDB']);

		// problema: se il campo e' di tipo longtext e lavoriamo in UTF8 c'e' 
		// evidentemente un problema di overflow da qualche parte (probabilmente 
		// in PHP, che non riesce a far stare in un intero 2^32*3) e viene 
		// ritornato -1 nella lunghezza massima del campo; in questo 
		// caso interveniamo a mano 
		if ($col['tipo'] == WADB_CONTENITORE && $col['lunghezzaMax'] == -1)
			$col['lunghezzaMax'] = pow(2, 32) - 1;
		// poiche' lavoriamo in utf8, per i campi stringa ci viene sempre
		// ritornata la lunghezza in bytes, non in caratteri; quindi e' da
		// dividere per 3
		elseif ($col['tipo'] == WADB_STRINGA || $col['tipo'] == WADB_CONTENITORE)
			$col['lunghezzaMax'] /= 3;
		$col['nrRipetizioni'] = 1;
		return $col;
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
//****  fine classe waConnessioneDB_mysql *****************************************
//***************************************************************************
	}	// fine classe 


?>