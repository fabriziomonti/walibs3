<?php
/**
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WADB_RIGHE'))
{
/**
* @ignore
*/
define('_WADB_RIGHE',1);


//***************************************************************************
//****  classe waRigheDB **********************************************
//***************************************************************************
/**
* waRigheDB
*
* Classe per la gestione di un insieme di righe (recordset).
*
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waRigheDB
	{
	/**
	* -
	* Oggetto di classe {@link waConnessioneDB} che mantiene la connessione al db
	* @var waConnessioneDB
	*/
	var $connessioneDB = null;

	/**
	* -
	* Array di oggetti di classe {@link waRecord} che compongono il recordset
	* @var array
	*/
	var $righe = array();

	/**
	* -
	* Matrice ad ordinale numerico dei campi convertiti per l'utilizzo da PHP
	* la proprieta' e' pubblica, ma non documentata, perche' ha senso che
	* venga interrogata solo dalla classe waRecord
	* @var array
	* @ignore
	*/
	var $valori = array();

	/**
	* -
	* Matrice ad ordinale numerico dei campi cosi' come ritornati dal db
	* la proprieta' e' pubblica, ma non documentata, perche' ha senso che
	* venga interrogata solo dalla classe waRecord
	* @var array
	* @ignore
	*/
	var $valoriCrudi = array();

	/**
	* -
	* Matrice ad ordinale numerico dei campi convertiti per PHP, ma contenente
	* solo i valori originali tornati dal db
	* la proprieta' e' pubblica, ma non documentata, perche' ha senso che
	* venga interrogata solo dalla classe waRecord
	* @var array
	* @ignore
	*/
	var $valoriOriginali = array();

	/**
	* -
	* Array associativo delle informazioni relative alle colonne
	* la proprieta' e' pubblica, ma non documentata, perche' ha senso che
	* venga interrogata solo dalla classe waRecord
	* @var array
	* @ignore
	*/
	var $colonne = array();

	/**
	* -
	* Array numerico delle informazioni relative alle colonne
	* la proprieta' e' pubblica, ma non documentata, perche' ha senso che
	* venga interrogata solo dalla classe waRecord
	* @var array
	* @ignore
	*/
	var $colonneOrd = array();

	/**
	* -
	* Array numerico contenente lo stato dei record presenti nel recordset
	* la proprieta' e' pubblica, ma non documentata, perche' ha senso che
	* venga interrogata solo dalla classe waRecord
	* @var array
	* @ignore
	*/
	var $statiRecord = array();

	/**
	* -
	* Nr. di record che soddisfano la condizione di una query indipendetemente
	* dal limit impostato
	* @ignore
	* @var int
	*/
	var $_nrRigheSenzaLimite = 0;

	//***************************************************************************
	/**
	* -
	*
	* Costruttore.
	* @param waConnessioneDB $connessioneDB istanza della classe per la
	*  connessione al database
	*/
	function __construct($connessioneDB)
		{
		$this->connessioneDB = $connessioneDB;
		}

	//***************************************************************************
	/**
	* -
	*
	* tbd...
	 * @ignore
	*/
	function carica($tabella,
						$arrayRichieste,
						$arrayOrdinamenti,
						$nrRighe = '',
						$rigaIniziale = '')
		{
		}

	//***************************************************************************
	/**
	* -
	*
	* Esegue una query SQL, costruisce le strutture dati relative alle colonne
	* e alle righe e restituisce un array di righe waRecord.
	*
	* @param string $sql SQL da eseguire
	* @param int $nrRighe numero max di righe che la query deve restituire
	* @param int $rigaIniziale numero di righe di cui effettuare lo skip (offset)
	* @return mixed (boolean | array) : se esito positivo ritorna l'array delle righe restituite
	* dalla query; se esito negativo torna false; per conoscere l'esatto esito
	* della query occorre invocare il metodo {@link nrErrore}
	*/
	function caricaDaSql($sql,
						$nrRighe = null,
						$rigaIniziale = 0)
		{

		$esito = $this->connessioneDB->eseguiEstesa($sql, $nrRighe, $rigaIniziale);
		if ($esito === false)
			return false;

		list($this->colonneOrd, $this->valoriCrudi, $this->_nrRigheSenzaLimite) = $esito;

		// carichiamo le informazioni delle colonne
		for ($i = 0; $i < count($this->colonneOrd); $i++)
			$this->colonne[strtoupper($this->colonneOrd[$i]['nome'])] = &$this->colonneOrd[$i];

		// carichiamo le righe
		for ($i = 0; $i < count($this->valoriCrudi); $i++)
			{
			$this->valori[] = $this->convertiRiga($this->valoriCrudi[$i]);
			$this->statiRecord[] = WADB_RECORD_INALTERATO;
			$this->righe[] = new waRecord($this, $i);
			}
		$this->valoriOriginali = $this->valori;

		return $this->righe;
		}

	//***************************************************************************
	/**
	* -
	*
	* Ritorna l'ultimo codice di errore restituito dal database.
	* @return string
	*/
	function nrErrore()
		{
		return $this->connessioneDB->nrErrore();
		}

	//***************************************************************************
	/**
	* -
	*
	* Ritorna l'ultimo messaggio di errore restituito dal database.
	* @return string
	*/
	function messaggioErrore()
		{
		return $this->connessioneDB->messaggioErrore();
		}

	//***************************************************************************
	/**
	* -
	*
	* Ritorna il numero di campi (colonne) restituiti dalla query
	* @return int
	*/
	function nrCampi()
		{
		return count($this->colonne);
		}

	//***************************************************************************
	/**
	* -
	*
	* Ritorna il numero di righe che soddisfano la condizione impostata dalla
	* query, indipendentemente da una eventuale richiesta di limitazione delle
	* righe da restituire
	* @return int
	*/
	function nrRigheSenzaLimite()
		{
		return $this->_nrRigheSenzaLimite;
		}

	//***************************************************************************
	/**
	* -
	*
	* Dato il nome di un campo tra quelli ritornati dalla query, restituisce
	* l'ordinale della colonna
	* @param string $nomeCampo nome del campo (case-insensitive)
	* @return int
	*/
	function indiceCampo($nomeCampo)
		{
		return $this->colonne[strtoupper($nomeCampo)]['indice'];
		}

	//***************************************************************************
	/**
	* -
	*
	* Dato l'indice di una colonna tra quelle ritornate dalla query, restituisce
	* il nome del campo
	* @param int $indiceCampo indice del campo
	* @return string
	*/
	function nomeCampo($indiceCampo)
		{
		return $this->colonneOrd[$indiceCampo]['nome'];
		}


	//***************************************************************************
	/**
	* -
	*
	* Dato, indifferentemente, il nome o l'indice numerico di un campo tra quelli
	* ritornati dalla query, restituisce la lunghezza massima che il campo puo'
	* ospitare
	* @param mixed (string | int) $nomeOIndiceCampo nome o indice del campo
	* @return int
	*/
	function lunghezzaMaxCampo($nomeOIndiceCampo)
		{
		$colInfo = $this->dammiInfoColonnaDaNomeOIndice($nomeOIndiceCampo);
		return $colInfo['lunghezzaMax'];
		}

	//***************************************************************************
	/**
	* -
	*
	* Dato, indifferentemente, il nome o l'indice numerico di un campo tra quelli
	* ritornati dalla query, restituisce il tipo del campo nativo sul DB
	* @param mixed (string | int) $nomeOIndiceCampo nome o indice del campo
	* @return string
	*/
	function tipoCampoDB($nomeOIndiceCampo)
		{
		$colInfo = $this->dammiInfoColonnaDaNomeOIndice($nomeOIndiceCampo);
		return $colInfo['tipoDB'];
		}

	//***************************************************************************
	/**
	* -
	*
	* Dato, indifferentemente, il nome o l'indice numerico di un campo tra quelli
	* ritornati dalla query, restituisce il tipo del campo applicativo (si vedano
	* le define relative ai tipi campo applicativi contenute in
	* {@link wadb.inc.php}).
	* @param mixed (string | int) $nomeOIndiceCampo nome o indice del campo
	* @return string
	*/
	function tipoCampo($nomeOIndiceCampo)
		{
		$colInfo = $this->dammiInfoColonnaDaNomeOIndice($nomeOIndiceCampo);
		return $colInfo['tipo'];
		}

	//***************************************************************************
	/**
	* -
	*
	* Ritorna il nome della tabella a cui appartiene un campo
	* @return string
	*/
	function nomeTabella($nomeOIndiceCampo)
		{
		$colInfo = $this->dammiInfoColonnaDaNomeOIndice($nomeOIndiceCampo);
		return $colInfo['tabella'];
		}

	//***************************************************************************
	/**
	* -
	*
	* Ritorna il nome del campo che e' chiave primaria del record
	* attenzione: potrebbero darsi casi in cui esistono piu' chiavi
	* primarie (viste) o in cui non sia stata selezionata nessuna chiave
	* primaria; questo metodo ritorna il nome del primo campo che e'
	* chiave primaria, se esistente. Sta al programmatore calare correttamente
	* la chiamata a questo metodo nel contesto in cui viene chiamato
	* @return string
	*/
	function chiavePrimaria()
		{
		foreach($this->colonne as $info)
			{
			if ($info['chiavePrimaria'])
				return $info['nome'];
			}
		}

	//***************************************************************************
	/**
	* -
	*
	* Inserisce una nuova riga vuota nel recordset e ritorna il relativo oggetto
	* di classe waRecord
	* @return waRecord
	*/
	function aggiungi()
		{
		$this->valoriCrudi[] =
			$this->valori[] =
			$this->valoriOriginali[] = array_fill(0, $this->nrCampi(), $niente);
		$this->statiRecord[] = WADB_RECORD_NUOVO;
		return ($this->righe[] = new waRecord($this, count($this->righe)));
		}

	//***************************************************************************
	/**
	* -
	*
	* Esegue tutte le operazioni di inseRimento, modifica, cancellazione
	* pendenti sull'intero insieme di oggetti di classe waRecord che compongono
	* l'array {@link righe}.
	* @return boolean : true = ok; false = errore; per conoscere l'esatto esito
	* dell'operazione occorre invocare il metodo {@link nrErrore}
	*/
	function salva()
		{
		$retval = true;
		$righeDaAllineare = array();

		for ($i = 0; $i < count($this->righe); $i++)
			{
			if ($this->statiRecord[$i] == WADB_RECORD_MODIFICATO)
				$this->aggiornaRecord($i);
			elseif ($this->statiRecord[$i] == WADB_RECORD_NUOVO)
				$this->inserisciRecord($i);
			elseif ($this->statiRecord[$i] == WADB_RECORD_DA_CANCELLARE)
				$this->eliminaRecord($i);
			if ($this->nrErrore())
				{
				$retval = false;
				break;
				}
			else
				$righeDaAllineare[] = $i;
			}

		// dobbiamo allineare lo stato delle righe; possiamo farlo solo dopo
		// che tutte le operazioni sono state eseguite, altrimenti il client
		// perde l'allineamento dell'indice
		foreach ($righeDaAllineare as $indiceRiga)
			$this->allineaStatiRiga($indiceRiga);

		return $retval;
		}

	//***************************************************************************
	/**
	* -
	*
	* Dato, indifferentemente, il nome o l'indice di un campo tra quelli ritornati
	* dalla query, restituisce l'ordinale della colonna
	* il metodo non e' documentato perche' ha senso se chiamato solo dalla
	* classe waRecord
	* @param mixed (string | int)  $nomeOIndiceCampo nome o indice del campo (case-insensitive)
	* @return int
	* @ignore
	*/
	function dammiOrdinaleColonnaDaNomeOIndice($nomeOIndiceCampo)
		{
		return is_numeric($nomeOIndiceCampo) ?
				$nomeOIndiceCampo :
				$this->colonne[strtoupper($nomeOIndiceCampo)]['indice'];
		}

	//***************************************************************************
	//*****  inizio metodi protected  *********************************************
	//***************************************************************************

	//***************************************************************************
	/**
	* -
	*
	* Una volta eseguite le operazioni pendenti sul recordset con esito positivo,
	* allinea gli stati interni di una riga
	* @param int $indiceRiga indice della riga all'intrno del recordset
	* @return void
	* @access protected
	* @ignore
	*/
	protected function allineaStatiRiga($indiceRiga)
		{
		switch ($this->statiRecord[$indiceRiga])
			{
			case WADB_RECORD_NUOVO:
			case WADB_RECORD_MODIFICATO:
				$this->valoriOriginali[$indiceRiga] = $this->valori[$indiceRiga];
				// qui manca l'aggiornamento dei valori crudi... come facciamo?
				$this->statiRecord[$indiceRiga] = WADB_RECORD_INALTERATO;
				break;
			case WADB_RECORD_DA_CANCELLARE:
				array_splice($this->valoriOriginali, $indiceRiga, 1);
				array_splice($this->valoriCrudi, $indiceRiga, 1);
				array_splice($this->statiRecord, $indiceRiga, 1);
				array_splice($this->righe, $indiceRiga, 1);
				break;
			}
		}

	//***************************************************************************
	/**
	* -
	*
	* Data una riga, compone la stringa SQL per l'eliminazione del record
	* e la esegue
	* @param int $indiceRiga indice della riga all'interno del recordset
	* @return boolean
	* @access protected
	* @ignore
	*/
	protected function eliminaRecord($indiceRiga)
		{
		// stabiliamo quali sono le tabelle da cui eliminare il record
		$tabelle = array();
		$riga = & $this->valori[$indiceRiga];
		$originale = & $this->valoriOriginali[$indiceRiga];

		for ($i = 0; $i < $this->nrCampi(); $i++)
			$tabelle[$this->colonneOrd[$i]['tabella']] = true;

		// per ognuna delle tabelle da cui eliminare il record, creiamo
		// una query sql di DELETE
		foreach ($tabelle as $tabella => $vero)
			{
			if ($condizione = $this->dammiCondizioneChiavePrimaria($tabella, $indiceRiga))
				{
				$sql = "DELETE FROM $tabella  WHERE $condizione";
				if ($this->connessioneDB->esegui($sql) === false)
					return false;
				}
			}

		return true;
		}

	//***************************************************************************
	/**
	* -
	*
	* Data una riga, compone la stringa SQL per l'inserimento effettivo del record
	* nel db e la esegue
	* @param int $indiceRiga indice della riga all'interno del recordset
	* @return boolean
	* @access protected
	* @ignore
	*/
	protected function inserisciRecord($indiceRiga)
		{
		// stabiliamo quali sono le tabelle da aggiornare
		$tabelle = array();
		$riga = & $this->valori[$indiceRiga];
		for ($i = 0; $i < $this->nrCampi(); $i++)
			{
			if (is_array($riga[$i]))
				{
				for ($j = 0; $j < count($riga[$i]); $j++)
					{
					if ($riga[$i][$j] !== $niente)
						$tabelle[$this->colonneOrd[$i]['tabella']][$this->colonneOrd[$i]['nome']][$j] = $riga[$i][$j];
					}
				}
			else
				{
				if ($riga[$i] !== $niente)
					$tabelle[$this->colonneOrd[$i]['tabella']][$this->colonneOrd[$i]['nome']] = $riga[$i];
				}
			}

		// per ognuna delle tabelle che riportano un campo modificato, creiamo
		// una query sql di INSERT
		foreach ($tabelle as $tabella => $campi)
			{
			$sql = "INSERT INTO $tabella (";
			$primoGiro = true;
			foreach ($campi as $nome => $valore)
				{
				$sql .= ($primoGiro ? '' : ", ");
				$primoGiro = false;
				if (is_array($valore))
					{
					$primoGiro = true;
					foreach($valore as $indiceRipetizione => $valoreVero)
						{
						$sql .= ($primoGiro ? '' : ", ") . $nome . "[" . ($indiceRipetizione + 1) ."]";
						$primoGiro = false;
						}
					}
				else
					$sql .= "$nome";
				}
			$sql .= ") VALUES (";
			$primoGiro = true;
			foreach ($campi as $nome => $valore)
				{
				$sql .= ($primoGiro ? '' : ", ");
				$primoGiro = false;
				if (is_array($valore))
					{
					$primoGiro = true;
					foreach($valore as $indiceRipetizione => $valoreVero)
						{
						$sql .= ($primoGiro ? '' : ", ") .
								$this->connessioneDB->valoreSql($valoreVero, $this->colonne[strtoupper($nome)]['tipoDB']);
						$primoGiro = false;
						}
					}
				else
					$sql .= $this->connessioneDB->valoreSql($valore, $this->colonne[strtoupper($nome)]['tipoDB']);
				}
			$sql .= ")";
			if ($this->connessioneDB->esegui($sql) === false)
				return false;

			// ci facciamo restituire l'ultimo id eventualmente generato da una
			// chiave primaria autoincrementale, e la andiamo ad inserire
			// nel valore corrispondente, in modo da avere il piu' possibile allineato
			// la riga del recordset con quanto c'e' sul db
			foreach ($this->colonne as $nome => $infos)
				{
				if ($infos['tabella'] == $tabella &&
					$infos['chiavePrimaria'] &&
					empty($riga[$infos['indice']]))
					// questa colonna e' evidentemente una chiave primaria
					// autoincrementale della tabella in esame, altrimenti
					// non avremmo potuto creare il record senza specificarla...
					$riga[$infos['indice']] = $this->connessioneDB->ultimoIdInserito();
				}

			}

		return true;
		}

	//***************************************************************************
	/**
	* -
	*
	* Data una riga, compone la stringa SQL per l'update del record
	* e la esegue
	* @param int $indiceRiga indice della riga all'interno del recordset
	* @return boolean
	* @access protected
	* @ignore
	*/
	protected function aggiornaRecord($indiceRiga)
		{
		// stabiliamo quali sono le tabelle da aggiornare
		$tabelle = array();
		$riga = & $this->valori[$indiceRiga];
		$originale = & $this->valoriOriginali[$indiceRiga];

		for ($i = 0; $i < $this->nrCampi(); $i++)
			{
			if (is_array($riga[$i]))
				{
				for ($j = 0; $j < count($riga[$i]); $j++)
					{
					if ($riga[$i][$j] !== $originale[$i][$j])
						$tabelle[$this->colonneOrd[$i]['tabella']][$this->colonneOrd[$i]['nome']][$j] = $riga[$i][$j];
					}
				}
			else
				{
				if ($riga[$i] !== $originale[$i])
					$tabelle[$this->colonneOrd[$i]['tabella']][$this->colonneOrd[$i]['nome']] = $riga[$i];
				}
			}

		// per ognuna delle tabelle che riportano un campo modificato, creiamo
		// una query sql di UPDATE
		foreach ($tabelle as $tabella => $campi)
			{
			$sql = "UPDATE $tabella SET ";
			$primoGiro = true;
			foreach ($campi as $nome => $valore)
				{
				$sql .= ($primoGiro ? '' : ", ");
				$primoGiro = false;

				if (is_array($valore))
					{
					$primoGiro = true;
					foreach($valore as $indiceRipetizione => $valoreVero)
						{
						$sql .= ($primoGiro ? '' : ", ") .
								$nome . "[" . ($indiceRipetizione + 1) . "]=" .
								$this->connessioneDB->valoreSql($valoreVero, $this->colonne[strtoupper($nome)]['tipoDB']);
						$primoGiro = false;
						}
					}
				else
					$sql .= "$nome=" . $this->connessioneDB->valoreSql($valore, $this->colonne[strtoupper($nome)]['tipoDB']);
				}
			$sql .= " WHERE " . $this->dammiCondizioneChiavePrimaria($tabella, $indiceRiga);
			if ($this->connessioneDB->esegui($sql) === false)
				return false;
			}

		return true;
		}

	//***************************************************************************
	/**
	* -
	*
	* Data una riga, compone la clausola SQL di WHERE per la ricerca di un record
	* @param string $tabella tabella per cui creare la condizione di where
	* @param int $indiceRiga indice della riga all'interno del recordset
	* @return string
	* @access protected
	* @ignore
	*/
	protected function dammiCondizioneChiavePrimaria($tabella, $indiceRiga)
		{
		// costruisce la condizione di where di una update
		$sql = '';

		$primoGiro = true;
		foreach($this->colonne as $info)
			{
			if ($info['tabella'] == $tabella && $info['chiavePrimaria'])
				{
				if (!$primoGiro)
					$sql .= " AND ";
				$primoGiro = false;
				$sql .= $info['nome'] . "=" .
						$this->connessioneDB->valoreSql($this->valoriOriginali[$indiceRiga][$info['indice']], $info['tipoDB']);
				}
			}

		return $sql;
		}


	//***************************************************************************
	/**
	* -
	*
	* Dato, indifferentemente, il nome o l'indice di un campo tra quelli ritornati
	* dalla query, restituisce le informazioni relative alla colonna
	* @param mixed (string | int) $nomeOIndiceCampo nome del campo o indice della colonna
	* @return array
	* @access protected
	* @ignore
	*/
	protected function dammiInfoColonnaDaNomeOIndice($nomeOIndiceCampo)
		{
		return is_numeric($nomeOIndiceCampo) ?
				$this->colonneOrd[$nomeOIndiceCampo] :
				$this->colonne[strtoupper($nomeOIndiceCampo)];
		}

	//***************************************************************************
	/**
	* -
	*
	* Data una riga cosi' come arriva dal db, la converte nel formato per
	* essere utilizzata da PHP
	* @param array $riga
	* @return array
	* @access protected
	* @ignore
	*/
	protected function convertiRiga($riga)
		{
		$retval = array();
		for ($i = 0; $i < count($riga); $i++)
			$retval[] = $this->connessioneDB->convertiCampo($riga[$i], $this->colonneOrd[$i]['tipoDB']);
		return $retval;
		}


	}

//*****************************************************************************
} //  if (!defined('_WADB_RIGHE'))
?>