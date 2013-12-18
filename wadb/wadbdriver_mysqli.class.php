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
//****  classe waConnessioneDB_mysqli ***************************************
//***************************************************************************
/**
* waConnessioneDB_mysqli
* 
* classe per la connessione fisica ad un database mysql-improved
*
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waConnessioneDB_mysqli extends waConnessioneDB 
	{
	
	/**
	* @access protected
	* @ignore
	*/ 
	var $DBConn = null;

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
		$this->DBConn = @mysqli_connect($this->WADB_HOST, $this->WADB_NOMEUTENTE, $this->WADB_PASSWORD, $this->WADB_NOMEDB, $this->WADB_PORTA ? $this->WADB_PORTA : null);
		if (!$this->DBConn) 
			return false;
		if (! @mysqli_autocommit($this->DBConn, true)) 
			return false;
		mysqli_set_charset($this->DBConn, "utf8");
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
		if (!$this->DBConn) 
			return;
		@mysqli_close($this->DBConn);
		$this->DBConn = null;
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
		if (!$this->DBConn)
			return @mysqli_errno();
		return @mysqli_errno($this->DBConn);
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
		if (!$this->DBConn)
			return @mysqli_error();
		return @mysqli_error($this->DBConn);
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
		$qid = @mysqli_query($this->DBConn, $sql);
		if ($qid === false)
			return false;
		$retval = array();
		while ($riga = @mysqli_fetch_assoc($qid))
			$retval[] = $riga;
		@mysqli_free_result($qid);	
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
		@mysqli_autocommit($this->DBConn, false);
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
		@mysqli_commit($this->DBConn);
		@mysqli_autocommit($this->DBConn, true);
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
		@mysqli_rollback($this->DBConn);
		@mysqli_autocommit($this->DBConn, true);
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
		return mysqli_insert_id($this->DBConn);
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
		return "'" . mysqli_real_escape_string($this->DBConn, $stringa) . "'";
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
			case MYSQLI_TYPE_DATE: 
				return $this->dataSql($valore);
			
			case MYSQLI_TYPE_TIME:
				return $this->oraSql($valore);
			
			case MYSQLI_TYPE_DATETIME:
			case MYSQLI_TYPE_TIMESTAMP: 
				return $this->dataOraSql($valore);
			
			case MYSQLI_TYPE_TINY:
			case MYSQLI_TYPE_SHORT: 
			case MYSQLI_TYPE_INT24:
			case MYSQLI_TYPE_LONG: 
			case MYSQLI_TYPE_LONGLONG:
				return $this->interoSql($valore);
			
			case MYSQLI_TYPE_FLOAT: 
			case MYSQLI_TYPE_DOUBLE:
			case MYSQLI_TYPE_DECIMAL:
			case MYSQLI_TYPE_NEWDECIMAL:
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
			case MYSQLI_TYPE_DATE: 
				if (empty($campo))
					return null;
				if ($campo == "0000-00-00")
					return 0;
				list($anno, $mese, $giorno) = explode("-", $campo);
				return mktime(0,0,0, $mese * 1, $giorno * 1, $anno * 1);
			
			case MYSQLI_TYPE_TIME:
				list($ora, $min, $sec) = explode(":", $campo);
				return mktime($ora * 1, $min * 1, $sec * 1, 1, 1, 1980);
			
			case MYSQLI_TYPE_DATETIME:
			case MYSQLI_TYPE_TIMESTAMP: 
				if (empty($campo))
					return null;
				if ($campo == "0000-00-00 00:00:00")
					return 0;
				list($data, $ore) = explode(" ", $campo);
				list($anno, $mese, $giorno) = explode("-", $data);
				list($ora, $min, $sec) = explode(":", $ore);
				return mktime($ora * 1, $min * 1, $sec * 1, $mese * 1, $giorno * 1, $anno * 1);
			
			case MYSQLI_TYPE_TINY:
			case MYSQLI_TYPE_SHORT: 
			case MYSQLI_TYPE_INT24:
			case MYSQLI_TYPE_LONG: 
			case MYSQLI_TYPE_LONGLONG:
			case MYSQLI_TYPE_FLOAT: 
			case MYSQLI_TYPE_DOUBLE:
			case MYSQLI_TYPE_DECIMAL:
			case MYSQLI_TYPE_NEWDECIMAL:
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
		$qid = @mysqli_query($this->DBConn, $sql);
		if ($qid === false)
			return false;
			
		// carichiamo le informazioni delle colonne
		$colInfos = array();
		$nrColonne = mysqli_num_fields($qid);	
		for ($i = 0; $i < $nrColonne; $i++)
			$colInfos[] = $this->impostaAttributiColonne($qid, $i);
			
		// carichiamo le righe
		while ($rigaCruda = @mysqli_fetch_array($qid, MYSQLI_NUM))
			$righeCrude[] = $rigaCruda;
			
		@mysqli_free_result($qid);	
		
		if ($nrRighe !== null)
			{
			// se e' stato richiesto il limit, allora andiamo anche a prelevare
			// il nr di righe che soddisfano la condizione
			$qid = mysqli_query($this->DBConn, "SELECT FOUND_ROWS()");
			list($nrRigheNoLimit) = mysqli_fetch_row($qid);
			@mysqli_free_result($qid);	
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
			case MYSQLI_TYPE_STRING: 
			case MYSQLI_TYPE_CHAR:
			case MYSQLI_TYPE_VAR_STRING: 
			case MYSQLI_TYPE_ENUM: 
			case MYSQLI_TYPE_SET: 
				return WADB_STRINGA;
				
			case MYSQLI_TYPE_TINY_BLOB: 
			case MYSQLI_TYPE_BLOB:
			case MYSQLI_TYPE_MEDIUM_BLOB:
			case MYSQLI_TYPE_LONG_BLOB: 
				return WADB_CONTENITORE;
				
			case MYSQLI_TYPE_DATE: 
				return WADB_DATA;
			
			case MYSQLI_TYPE_TIME:
				return WADB_ORA;
			
			case MYSQLI_TYPE_DATETIME:
			case MYSQLI_TYPE_TIMESTAMP: 
				return WADB_DATAORA;
			
			case MYSQLI_TYPE_TINY:
			case MYSQLI_TYPE_SHORT: 
			case MYSQLI_TYPE_INT24:
			case MYSQLI_TYPE_LONG: 
			case MYSQLI_TYPE_LONGLONG:
				return WADB_INTERO;
				
			case MYSQLI_TYPE_FLOAT: 
			case MYSQLI_TYPE_DOUBLE:
			case MYSQLI_TYPE_DECIMAL:
			case MYSQLI_TYPE_NEWDECIMAL:
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
		$colInfo = mysqli_fetch_field_direct($queryId, $colIdx);
		$col['nome'] = $colInfo->name;
		$col['indice'] = $colIdx;
		$col['tipoDB'] = strtoupper($colInfo->type);
		$col['tabella'] = $colInfo->table;
		$col['chiavePrimaria'] = $colInfo->flags & 2 ? 1 : 0;
		$col['lunghezzaMax'] = $colInfo->length;
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
//****  fine classe waConnessioneDB_mysqli **********************************
//***************************************************************************
	}	// fine classe 


?>