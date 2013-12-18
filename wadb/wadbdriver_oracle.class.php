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

/* todo
- limit
 - record che soddisfano...
 - autocommit - gestione transazione
 - tabella di appartenenza della colonna
 - flag chiave primaria


*/

//***************************************************************************
//****  classe waConnessioneDB_oracle **********************************************
//***************************************************************************
/**
* waConnessioneDB_oracle
* 
* classe per la connessione fisica ad un database oracle
* 
* <b>SPERIMENTALE!</b>
*
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waConnessioneDB_oracle extends waConnessioneDB 
	{
	
	/**
	* @access protected
	* @ignore
	*/ 
	protected $DBConn = false;

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
		$dbConnString = "//$this->WADB_HOST";
		if ($this->WADB_PORTA != '')
			$dbConnString .= ":" . $this->WADB_PORTA;
		$dbConnString .= "/$this->WADB_NOMEDB";
			
		$this->DBConn = @oci_connect($this->WADB_NOMEUTENTE, $this->WADB_PASSWORD, $dbConnString);
		if ($this->DBConn === false) 
			return false;
//		if ($this->esegui('SET AUTOCOMMIT=1') === false) 
//			return false;

		$this->esegui("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'");
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
		@oci_close($this->DBConn);
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
		$retval = @oci_error($this->DBConn);
		if ($retval === false)
			return 0;
		return $retval['code'];
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
		$retval = @oci_error($this->DBConn);
		if ($retval === false)
			return '';
		return $retval['message'];

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
		$qid = @oci_parse($this->DBConn, $sql);
		if ($qid === false)
			return false;
		if (! @oci_execute($qid, OCI_DEFAULT))
			return false;
			
		$retval = array();
		while ($riga = @oci_fetch_assoc($qid))
			$retval[] = $riga;
			
		@oci_free_statement($qid);
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
		return oracle_insert_id($this->DBConn);
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
		return date("'Y-m-d'", $data);
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
		return date("'Y-m-d H.i.s'", $dataOra);
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
		return "'" . oracle_real_escape_string($stringa, $this->DBConn) . "'";
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
			case 'TIMESTAMP': 
				return $this->dataOraSql($valore);
			
			case 'NUMBER': 
			case 'NUMERIC': 
			case 'DEC': 
			case 'DECIMAL': 
			case 'BINARY_DOUBLE':
			case 'BINARY_FLOAT':
				// qui forse occorre fare una verifica dello scale...
				return $this->decimaleSql($valore);
			
			case 'CHAR':
			case 'NCHAR':
			case 'RAW':
			case 'VARCHAR2': 
			case 'NVARCHAR2': 
			case 'BLOB':
			case 'CLOB':
			case 'NCLOB':
			case 'BFILE':
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
			case 'TIMESTAMP': 
				list($data, $ore) = explode(" ", $campo);
				list($anno, $mese, $giorno) = explode("-", $data);
				list($ora, $min, $sec) = explode(":", $ore);
				return mktime($ora * 1, $min * 1, $sec * 1, $mese * 1, $giorno * 1, $anno * 1);
			
			case 'NUMBER': 
			case 'NUMERIC': 
			case 'DEC': 
			case 'DECIMAL': 
			case 'BINARY_DOUBLE':
			case 'BINARY_FLOAT':
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
		if ($nrRighe !== null)
			{
			// modifichiamo la query, che ovviamente DEVE essere una select,
			// affinche' oracle possa tornare il nr di righe
			// che soddisfano la condizione, indipendentemente dal limit imposto
			$sql = "SELECT * FROM (" .
							" SELECT  v1.*, rownum wadb_rn FROM (" .
									" SELECT  v0.*, count(*) over () wadb_found_rows FROM ($sql) v0" .
								") v1" .
			        			" WHERE rownum<=" . ($rigaIniziale + $nrRighe) . 
			        		") v2" .
						" WHERE v2.wadb_rn > $rigaIniziale" .
						" ORDER BY wadb_rn";
			}


		$qid = oci_parse($this->DBConn, $sql);
		if ($qid === false)
			return false;
		if (!oci_execute($qid, OCI_DEFAULT))
			return false;
			
		// carichiamo le informazioni delle colonne
		$colInfos = array();
		$nrColonne = oci_num_fields($qid) - ($nrRighe !== null ? 2 : 0);
		for ($i = 0; $i < $nrColonne; $i++)
			$colInfos[] = $this->impostaAttributiColonne($qid, $i);
			
		// carichiamo le righe
		while ($rigaCruda = oci_fetch_array($qid, OCI_NUM | OCI_RETURN_NULLS | OCI_RETURN_LOBS))
			{
			if ($nrRighe !== null)
				{
				$nrRigheNoLimit = $rigaCruda[$nrColonne];
				array_splice($rigaCruda, $nrColonne);
				}
			$righeCrude[] = $rigaCruda;
			}
			
		oci_free_statement($qid);
					
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
			case 'CHAR':
			case 'NCHAR':
			case 'RAW':
			case 'VARCHAR2': 
			case 'NVARCHAR2': 
				return WADB_STRINGA;
				
			case 'BLOB':
			case 'CLOB':
			case 'NCLOB':
			case 'BFILE':
				return WADB_CONTENITORE;
				
			case 'DATE': 
			case 'TIMESTAMP': 
				return WADB_DATAORA;
			
			case 'NUMBER': 
			case 'NUMERIC': 
			case 'DEC': 
			case 'DECIMAL': 
			case 'BINARY_DOUBLE':
			case 'BINARY_FLOAT':
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
		$col['nome'] = oci_field_name($queryId, $colIdx + 1);
		$col['indice'] = $colIdx;
		$col['tipoDB'] = strtoupper(oci_field_type($queryId, $colIdx + 1));
		
		$col['tabella'] = $colInfo->table;
		$col['chiavePrimaria'] = $colInfo->primary_key;
		$col['lunghezzaMax'] = oci_field_size($queryId, $colIdx + 1);
		$col['tipo'] = $this->dammiTipoCampoApplicativo($col['tipoDB']);
		if ($col['tipo'] == WADB_DECIMALE)
			{
			$scale = oci_field_scale($queryId, $colIdx + 1);
			if ($scale <= 0)
				$col['tipo'] = WADB_INTERO;
			}	
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
//****  fine classe waConnessioneDB_oracle *****************************************
//***************************************************************************
	}	// fine classe 


?>