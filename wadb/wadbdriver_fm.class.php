<?php
/**
* @package waDB
* @version 3.0
* @author A. Tosi, G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

/**
* @ignore
*/
require_once(dirname(__FILE__ ). '/wadbdriver.class.php');

//***************************************************************************
// inclusione del package FX
// determiniamo se per caso non sia stata gia' caricata una copia
// del package FX; se e' gia' stata caricata, simuliamo un fatal error
// nel caso in cui la versione di FX sia differente rispetto a
// quella che deve usare il presente package. Se la versione e'
// la stessa, semplicemente, non includiamo il file
if (defined("FX_VERSION"))
	{
	if (FX_VERSION != '4.2')
		exit("ATTENZIONE: " . __FILE__ . " non puo' essere incluso perche'" .
			 " non puo' caricare la propria versione (4.2) della classe FX;" .
			 " l'applicazione ha gia' incluso una classe FX versione " .
			 FX_VERSION . ". L'intero caricamento dell'applicazione e' stato" .
			 " abortito!");
	}
else
	/**
	* @ignore
	*/
	require_once(dirname(__FILE__ ). '/FX/FX.php');

//***************************************************************************
//****  classe waConnessioneDB_fm_fm **********************************************
//***************************************************************************
/**
* waConnessioneDB_fm
*
* Classe per la connessione fisica ad un database filemaker 7/8/9
*
* @author A.Tosi, G.Gaiba, F.Monti
* @package waDB
* @version 3.0
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waConnessioneDB_fm extends waConnessioneDB
	{
	/**
	 * 
	 * Driver per la gestione del colloquio con database FileMaker.
	 * 
	* Per FileMaker, le query supportate sono le seguenti (case-insensitive):<ul>
	* <li>SELECT * FROM formato_scheda [WHERE campo1='valore1'[, campo2='valore2'...]][ ORDER BY campo1[desc][, campo2[desc]],...][ LIMIT primaRiga,numeroRighe]
	* <li>INSERT INTO formato_scheda (campo1[, campo2...]) VALUES ('valore1'[, 'valore2',...])
	* <li>UPDATE formato_scheda SET campo1='valore1'[, campo2='valore2'...][WHERE campo1='valore1'[, campo2='valore2'...]] </ul>
	 */

	/**
	* -
	* Codici di errore di FileMaker 9
	* @var array
	* @access protected
	* @ignore
	*/
	protected $erroriFM = array(
		-1 => "Errore sconosciuto",
		0 => "Nessun errore",
		1 => "Azione annullata dall'utente",
		2 => "Errore di memoria",
		3 => "Comando non disponibile (ad esempio, sistema operativo non appropriato, modo errato e così via)",
		4 => "Comando sconosciuto",
		5 => "Comando non valido (ad esempio, un'istruzione di script Definisci campo priva di calcolo)",
		6 => "File di sola lettura",
		7 => "Memoria esaurita",
		8 => "Risultato vuoto",
		9 => "Privilegi insufficienti",
		10 => "Dati richiesti non disponibili",
		11 => "Nome non valido",
		12 => "Il nome esiste già",
		13 => "File o oggetto in uso",
		14 => "Fuori intervallo",
		15 => "Impossibile dividere per zero",
		16 => "Operazione non riuscita, nuovo tentativo richiesta (ad esempio una query dell'utente)",
		17 => "Tentativo di convertire il set di caratteri stranieri in UTF-16 non riuscito",
		18 => "Il client deve fornire le informazioni sull'account per procedere",
		19 => "La stringa contiene caratteri diversi da A-Z, a-z, 0-9 (ASCII)",
		100 => "Manca un file",
		101 => "Manca un record",
		102 => "Manca un campo",
		103 => "Manca una relazione",
		104 => "Manca uno script",
		105 => "Manca un formato",
		106 => "Manca una tabella",
		107 => "Manca un indice",
		108 => "Manca una lista valori",
		109 => "Manca un set di privilegi",
		110 => "Mancano tabelle correlate",
		111 => "Ripetizione campo non valida",
		112 => "Manca una finestra",
		113 => "Manca una funzione",
		114 => "Manca un riferimento al file",
		115 => "Il set di menu specificato non è presente",
		116 => "L'oggetto formato specificato non è presente",
		117 => "La sorgente dati indicata non è presente",
		130 => "File danneggiati o non presenti; reinstallarli",
		131 => "Impossibile trovare i file del supporto per la lingua (come i file modello)",
		200 => "Accesso al record negato",
		201 => "Impossibile modificare il campo",
		202 => "Accesso al campo negato",
		203 => "Nel file non c'è nessun record da stampare o la password non consente l'accesso alla stampa",
		204 => "Nessun accesso ai campi nei criteri di ordinamento",
		205 => "L'utente non dispone dei privilegi di accesso per creare nuovi record; l'importazione sovrascriverà i dati esistenti",
		206 => "L'utente non dispone del privilegio per cambiare la password o il file non è modificabile",
		207 => "L'utente non ha privilegi sufficienti per cambiare lo schema del database, oppure il file non è modificabile",
		208 => "La password non contiene abbastanza caratteri",
		209 => "La nuova password deve essere diversa da quella esistente",
		210 => "L'account utente è inattivo",
		211 => "La password è scaduta",
		212 => "Nome utente e/o password non validi. Riprovare",
		213 => "Il nome utente e/o la password non esistono",
		214 => "Troppi tentativi di accesso",
		215 => "I privilegi di amministratore non possono essere duplicati",
		216 => "L'account Ospite non può essere duplicato",
		217 => "L'utente non dispone di privilegi sufficienti per modificare l'account Admin",
		300 => "File bloccato o in uso",
		301 => "Record usato da un altro utente",
		302 => "Tabella usata da un altro utente",
		303 => "Schema database usato da un altro utente",
		304 => "Formato usato da un altro utente",
		306 => "ID modifica del record non corrispondente",
		400 => "Criteri di ricerca vuoti",
		401 => "Nessun record soddisfa la richiesta",
		402 => "Il campo selezionato non è un campo di confronto per un riferimento",
		403 => "Limite massimo di record per la versione di prova di FileMaker Pro superato",
		404 => "Criterio di ordinamento non valido",
		405 => "Il numero di record specificato supera il numero di record che possono essere omessi",
		406 => "Criteri di sostituzione/riserializzazione non validi",
		407 => "Manca uno o entrambi i campi di confronto (relazione non valida)",
		408 => "Tipo di dati associato al campo specificato non valido per questa operazione",
		409 => "Ordine di importazione non valido",
		410 => "Ordine di esportazione non valido",
		412 => "Per recuperare il file è stata usata una versione errata di FileMaker Pro",
		413 => "Tipo di campo non valido",
		414 => "Il formato non può visualizzare il risultato",
		415 => "Uno o più record correlati richiesti non sono disponibili",
		416 => "E' richiesta una chiave primaria dalla tabella della sorgente dati",
		417 => "Impossibile operare via ODBC, database non supportato",
		500 => "Il valore della data non soddisfa le opzioni di verifica",
		501 => "Il valore dell'ora non soddisfa le opzioni di verifica",
		502 => "Il valore del numero non soddisfa le opzioni di verifica",
		503 => "Il valore nel campo non è compreso nell'intervallo specificato nelle opzioni di verifica",
		504 => "Il valore del campo non è univoco come richiesto dalle opzioni di verifica",
		505 => "Il valore del campo non esiste nel file di database come richiesto dalle opzioni di verifica",
		506 => "Il valore nel campo non è elencato nella lista di valori specificata nelle opzioni di verifica",
		507 => "Il valore nel campo non ha superato il test del calcolo dell'opzione di verifica",
		508 => "Valore non valido immesso in modo Trova",
		509 => "Il campo richiede un valore valido",
		510 => "Valore correlato vuoto o non disponibile",
		511 => "Il valore immesso nel campo supera il numero massimo di caratteri consentiti",
		512 => "Il record è stato già modificato da un altro utente",
		513 => "Il record non può essere creato vuoto",
		600 => "Errore di stampa",
		601 => "La combinazione di intestazione e più di pagina supera una pagina",
		602 => "Il corpo non rientra in una pagina per l'impostazione della colonna corrente",
		603 => "Connessione di stampa interrotta",
		700 => "Tipo di file errato per l'importazione",
		706 => "File EPSF privo di immagine di anteprima",
		707 => "Impossibile trovare traduttore per immagini",
		708 => "Impossibile importare il file. è necessario un computer a colori",
		709 => "Non è riuscita l'importazione del filmato QuickTime",
		710 => "Impossibile aggiornare il riferimento al file QuickTime. Il file di database è di sola lettura.",
		711 => "Impossibile trovare il traduttore per l'importazione",
		714 => "Operazione non consentita dai privilegi della password",
		715 => "è stato specificato un foglio di lavoro di Excel o un intervallo con nome mancante",
		716 => "Una query SQL che impiega istruzioni DELETE, INSERT o UPDATE non è consentita per l'importazione ODBC",
		717 => "Informazioni XML/XSL insufficienti per procedere con l'importazione o l'esportazione",
		718 => "Errore di analisi del file XML (da Xerces)",
		719 => "Errore di conversione XML usando XSL (da Xalan)",
		720 => "Errore durante l'esportazione; il formato desiderato non supporta i campi multipli",
		721 => "Errore sconosciuto nel parser o nel convertitore",
		722 => "Impossibile importare dati in un file che non ha campi",
		723 => "Non si dispone dell'autorizzazione per aggiungere o modificare record nella tabella di destinazione",
		724 => "Non si dispone dell'autorizzazione per aggiungere record alla tabella di destinazione",
		725 => "Non si dispone dell'autorizzazione per modificare record nella tabella di destinazione",
		726 => "Vi sono più record nel file di importazione che nella tabella di destinazione. Non tutti i record sono stati importati",
		727 => "Vi sono più record nella tabella di destinazione che nel file di importazione. Non tutti i record sono stati aggiornati",
		729 => "Errori durante l'importazione. Impossibile importare i record",
		730 => "Versione Excel non supportata. (Convertire il file in formato Excel 7.0 (Excel 95), Excel 97, 2000 o XP e riprovare)",
		731 => "Il file da importare non contiene dati",
		732 => "Questo file non può essere inserito perché contiene altri file",
		733 => "Una tabella non può essere importata in se stessa",
		734 => "I file di questo tipo non possono essere visualizzati come immagine",
		735 => "I file di questo tipo non possono essere visualizzati come immagine. Verranno inseriti e visualizzati come file",
		736 => "Troppi dati da esportare in questo formato. Sarà troncato",
		800 => "Impossibile creare il file su disco",
		801 => "Impossibile creare il file temporaneo sul disco di sistema",
		802 => "Impossibile aprire il file",
		803 => "Il file è per un singolo utente oppure non è stato possibile trovare l'host",
		804 => "Impossibile aprire il file.",
		805 => "Usare il comando Recupera",
		806 => "Impossibile aprire il file con questa versione di FileMaker Pro",
		807 => "Il file non è un file FileMaker Pro oppure è gravemente danneggiato",
		808 => "Impossibile aprire il file. I privilegi di accesso sono danneggiati",
		809 => "Il disco o il volume è pieno",
		810 => "Il disco o il volume è protetto",
		811 => "Impossibile aprire il file temporaneo come file di FileMaker Pro",
		813 => "Errore di sincronizzazione del record in rete",
		814 => "Impossibile aprire i file. è già aperto il numero massimo",
		815 => "Impossibile aprire il file di riferimento",
		816 => "Impossibile convertire il file",
		817 => "Impossibile aprire il file poiché non fa parte di questa soluzione",
		819 => "Impossibile salvare una copia locale di un file remoto",
		820 => "File in fase di chiusura",
		821 => "L'host ha forzato una disconnessione",
		822 => "File FMI non trovati; reinstallare i file non presenti",
		823 => "Impossibile impostare il file su utente singolo; alcuni ospiti sono connessi",
		824 => "Il file è danneggiato o non è un file FileMaker",
		900 => "Errore generico del modulo di gestione del controllo ortografico",
		901 => "Dizionario principale non installato",
		902 => "Impossibile avviare la Guida",
		903 => "Impossibile usare il comando in un file condiviso",
		905 => "Non è selezionato nessun campo attivo; il comando può essere usato solo se un campo è attivo",
		906 => "Il file deve essere condiviso per utilizzare questo comando",
		920 => "Impossibile inizializzare il modulo di gestione del controllo ortografico",
		921 => "Impossibile caricare il dizionario utente per la modifica",
		922 => "Impossibile trovare il dizionario utente",
		923 => "Il dizionario utente è di sola lettura",
		951 => "Errore imprevisto (*)",
		954 => "Grammatica XML non supportata (*)",
		955 => "Nessun nome per il database (*)",
		956 => "è stato superato il numero massimo di sessioni del database (*)",
		957 => "Conflitto tra i comandi (*)",
		958 => "Parametro mancante (*)",
		1200 => "Errore di calcolo generico",
		1201 => "Troppi pochi parametri nella funzione",
		1202 => "Troppi parametri nella funzione",
		1203 => "Fine calcolo non previsto",
		1204 => "Sono previsti un numero, una costante di testo, un nome di campo o una \"(",
		1205 => "Il commento non termina con \"*/\"",
		1206 => "La costante di testo deve terminare con un punto interrogativo",
		1207 => "Parentesi mancante",
		1208 => "Operatore mancante, funzione non trovata o \"(\" non prevista",
		1209 => "Nome (come nome campo o nome formato) mancante",
		1210 => "La funzione del plug-in è già stata registrata",
		1211 => "Utilizzo della lista valori non consentito in questa funzione",
		1212 => "Qui è previsto un operatore (ad esempio, +, -, *)",
		1213 => "Questa variabile è già stata definita nella funzione Consenti",
		1214 => "MEDIO, CONTEGGIO, ESTENSO, RICAVARIPETIZIONI, MAX, MIN, VPN, DEVST, SOMMA e RICAVARIASSUNTO: espressione trovata dove è necessario un campo solo",
		1215 => "Questo parametro è un parametro non valido per la funzione Get",
		1216 => "Solo i campi Riassunto sono consentiti come primo argomento in RICAVARIASSUNTO",
		1217 => "Il campo di separazione non è valido",
		1218 => "Impossibile valutare il numero",
		1219 => "Non è possibile usare un campo nella propria formula",
		1220 => "Il campo deve essere di tipo normale o Calcolo",
		1221 => "I dati devono essere di tipo Numero, Data, Ora o Indicatore data e ora",
		1222 => "Impossibile memorizzare il calcolo",
		1223 => "La funzione non è implementata",
		1224 => "La funzione non è definita",
		1225 => "La funzione non è supportata in questo contesto",
		1300 => "Il nome specificato non può essere utilizzato",
		1400 => "Errore nell'inizializzazione del driver ODBC; assicurarsi che i driver ODBC siano installati correttamente",
		1401 => "Allocazione ambiente fallita (ODBC)",
		1402 => "Impossibile liberare l'ambiente (ODBC)",
		1403 => "Impossibile disconnettersi (ODBC)",
		1404 => "Impossibile allocare la connessione (ODBC)",
		1405 => "Impossibile liberare la connessione (ODBC)",
		1406 => "Controllo SQL API (ODBC) fallito",
		1407 => "Impossibile allocare l'istruzione (ODBC)",
		1408 => "Errore esteso (ODBC)",
		1409 => "Errore (ODBC)",
		1413 => "Impossibile connettersi (ODBC)",

		// errori custom
		10000 => "Errore FX",
		20000 => "Errore dati in input"
		);

	/**
	* -
	* istanza della classe FX utilizzata per accedere tramite http a FileMaker Server Advanced
	* @var FX
	* @access protected
	* @ignore
	*/
	protected $DBConn = null;

	/**
	* -
	* id dell'ultimo record inserito
	* @var int
	* @access protected
	* @ignore
	*/
	protected $_ultimoIdInserito = null;

	/**
	* -
	* Codice di errore dell'ultima query eseguita
	* @var int
	* @ignore
	* @access protected
	*/
	protected $codErrore = null;

	//***************************************************************************
	/**
	* -
	*
	* Connette il database.
	* @return boolean per conoscere l'esatto esito del metodo occorre invocare
	* il metodo {@link nrErrore}
	* @ignore
	*/
	function connetti()
		{
		$this->DBConn = new FX($this->WADB_HOST, $this->WADB_PORTA, 'FMPro7');
		if (!is_a($this->DBConn,'FX'))
			return false;
			
		$nomeUtente = $this->WADB_NOMEUTENTE;
		if (defined('WADB_MULTIUSER'))
			{
			$cntr = @file_get_contents(WADB_MULTIUSER_FILE);
			if (empty($cntr) || $cntr > WADB_MULTIUSER_MAX)
				$cntr = 1;
			$nomeUtente .= $cntr;
			@file_put_contents(WADB_MULTIUSER_FILE, ++$cntr);
			}
			
		$this->DBConn->SetDBPassword($this->WADB_PASSWORD, $nomeUtente);
		return true;

		}

	//***************************************************************************
	/**
	* -
	*
	* Disconnette il database
	* @return void
	* @ignore
	*/
	function disconnetti()
		{
		unset($this->DBConn);
		}


	//***************************************************************************
	/**
	* -
	*
	* Ritorna l'ultimo codice di errore restituito dal database.
	* @return string
	* @ignore
	*/
	function nrErrore()
		{
		return $this->codErrore;
		}

	//***************************************************************************
	/**
	* -
	*
	* Ritorna l'ultimo messaggio di errore restituito dal database.
	* @return string
	* @ignore
	*/
	function messaggioErrore()
		{
		return $this->erroriFM[$this->codErrore];
		}

	//***************************************************************************
	/**
	* -
	*
	* Esegue un comando SQL sul database connesso.
	* Per i database che supportano nativamente il linguaggio SQL, la query va
	* espressa nel dialetto SQL supportato dal particolare database.
	*
	* Per FileMaker, le query supportate sono le seguenti (case-insensitive):<ul>
	* <li>SELECT * FROM formato_scheda [WHERE campo1='valore1'[, campo2='valore2'...]][ ORDER BY campo1[desc][, campo2[desc]],...][ LIMIT primaRiga,numeroRighe]
	* <li>INSERT INTO formato_scheda (campo1[, campo2...]) VALUES ('valore1'[, 'valore2',...])
	* <li>UPDATE formato_scheda SET campo1='valore1'[, campo2='valore2'...][WHERE campo1='valore1'[, campo2='valore2'...]] </ul>
	* @param string $sql SQL da eseguire
	* @return mixed i dati grezzi ottenuti dalla query o FALSE in caso di errore;
	* per conoscere l'esatto esito del metodo occorre invocare
	* il metodo {@link nrErrore}
	* @ignore
	*/
	function esegui($sql)
		{
		$this->codErrore = -1;
		if (empty($this->WADB_HOST) || empty($this->WADB_NOMEDB) || empty($this->WADB_NOMEUTENTE))
			return $this->ErroreDatiInput("Errore: Non sono stati inseriti tutti i dati del server");

		$sql=trim($sql);
		$comando = strtolower(substr($sql,0,6));
		if ($comando == 'select')
			$result = $this->eseguiSelect($sql);
		elseif ($comando == 'insert')
			$result = $this->eseguiInsert($sql);
		elseif ($comando == 'update')
			$result = $this->eseguiUpdate($sql);
		elseif ($comando == 'delete')
			$result = $this->eseguiDelete($sql);
		else
			return $this->ErroreDatiInput("Errore: Query SQL non supportata");
		if ($result === false)
			return false;

		if (!is_array($result))
			{
			$this->codErrore = 10000;	// e' un errore di FX, che torna un oggetto errore
			return false;
			}
		$this->codErrore = $result['errorCode'];
		return $result;
		}

	//***************************************************************************
	/**
	* -
	*
	* Inizia una transazione.
	* Siccome FileMaker non supporta la transazione questo metodo non fa nulla in
	* caso di database FileMaker.
	* @return void
	* @ignore
	*/
	function iniziaTransazione()
		{
		}

	//***************************************************************************
	/**
	* -
	*
	* Conferma una transazione aperta in precedenza con {@link iniziaTransazione}.
	* Siccome FileMaker non supporta la transazione questo metodo non fa nulla in
	* caso di database FileMaker.
	* @return void
	* @ignore
	*/
	function confermaTransazione()
		{
		}

	//***************************************************************************
	/**
	* -
	*
	* Annulla una transazione aperta in precedenza con {@link iniziaTransazione}.
	* Siccome FileMaker non supporta la transazione questo metodo non fa nulla in
	* caso di database FileMaker.
	* @return void
	* @ignore
	*/
	function annullaTransazione()
		{
		}

	//***************************************************************************
	/**
	* -
	*
	* Ritorna l'ultimo identifica univoco inserito nel database a fronte di INSERT
	* (posto che la tabella sia dotata di una chiave primaria autoincrementale).
	* Per FileMaker ritorna sempre il recId del record inserito.
	* @return integer
	* @ignore
	*/
	function ultimoIdInserito()
		{
		return $this->_ultimoIdInserito;
		}

	//***************************************************************************
	/**
	* -
	*
	* Trasforma una data/ora "since-the-epoch" nel formato SQL DATETIME richiesto dal database.
	* @param integer $dataOra  data/ora in formato "since-the-epoch"
	* @return string
	* @ignore
	*/
	function dataOraSql($dataOra)
		{
		if ($dataOra == null)
			$result = '';
		else
			$result= date('m/d/Y H:i:s', $dataOra);
		return "'$result'";
		}

	//***************************************************************************
	/**
	* -
	*
	* Trasforma una data "since-the-epoch" nel formato SQL DATE richiesto dal database.
	* @param integer $data  data in formato "since-the-epoch"
	* @return string
	* @ignore
	*/
	function dataSql($data)
		{
		if ($data != null)
			$result = date('m/d/Y', $data);
		else $result='';
		return "'$result'";
		}

	//***************************************************************************
	/**
	* -
	*
	* Trasforma una ora "since-the-epoch" (ignorando anno, mese, giorno) nel formato SQL TIME richiesto dal database.
	* @param integer $ora ora in formato "since-the-epoch"
	* @return string
	* @ignore
	*/
	function oraSql($ora)
		{
		if ($ora != null)
			$result = date('H:i:s',$ora);
		return "'$result'";
		}

	//***************************************************************************
	/**
	* -
	*
	* Trasforma un numero decimale nel formato SQL richiesto dal database.
	* @param integer $intero intero da convertire da convertire
	* @return string
	* @ignore
	*/
	function interoSql($intero)
		{
		//In FileMaker non c'è differenza tra intero e decimale
		return $this->decimaleSql($intero);
		}

	//***************************************************************************
	/**
	* -
	*
	* Trasforma un numero decimale nel formato SQL richiesto dal database.
	* @param float $decimale decimale da convertire
	* @return string
	* @ignore
	*/
	function decimaleSql($decimale)
		{
		//Sostituisco la virgola col punto nel caso che qualcuno usasse in maniera impropria questo metodo
		//passandogli una stringa invece che un float
		$result = str_replace(',','.',$decimale);
		return "'" . ((float) $result) . "'";
		}

	//***************************************************************************
	/**
	* -
	*
	* Trasforma una stringa nel formato SQL richiesto dal database.
	* @param string $stringa stringa da convertire
	* @return string
	* @ignore
	*/
	function stringaSql($stringa)
		{
		$result= utf8_encode(str_replace("'","\\'",$stringa));
		return "'$result'";
		}


	//***************************************************************************
	/**
	* -
	*
	* Restituisce il valore NULL come richiesto dal db.
	* @return string
	* @ignore
	*/
	function nulloSql()
		{
		return '';
		}


	//***************************************************************************
	/**
	* -
	*
	* Restituisce l'array delle liste valori assegnate ai campi sul formato
	* facendo una connessione al database. 
	* @param string $formato formato scheda da cui prendere le liste valori
	* @return array l'array di liste valori
	*/
	function listeValori($formato)
		{
		//Creo una connessione al DB per effettuare le modifiche
		if (empty($this->WADB_HOST) || empty($formato))
			return $this->ErroreDatiInput("Errore: impossibile ottenere le liste valori, non sono stati assegnati i parametri per la connessione al database");

		$this->DBConn->SetDBData($this->WADB_NOMEDB, $formato, 1);
		$result=$this->DBConn->FMView();
		return $result['valueLists'];
		}

	//***************************************************************************
	/**
	* -
	*
	* restituisce il vero contenuto (non l'URL del file) di un campo contenitore
	* @param string $valoreContenitore valore del campo contenitore (indirizzo http)
	* @return string contenuto binario del contenitore
	*/
	function dammiContenutoContenitore($valoreContenitore)
		{
		if (empty($valoreContenitore))
			return false;
		$url = "http://$this->WADB_NOMEUTENTE:$this->WADB_PASSWORD@$this->WADB_HOST:$this->WADB_PORTA$valoreContenitore";
		$fp = fopen($url, "rb");
		if (!$fp)
			return false;
        while (!feof($fp)) 
            $buffer .= fread($fp, 4096);
        fclose($fp);
        return $buffer;
		}

	//***************************************************************************
	//******* inizio metodi semi-protected ****************************************
	//***************************************************************************

	//***************************************************************************
	/**
	* -
	*
	* Prende un campo come arriva da PHP e lo converte nel formato SQL
	* il metodo non e' documentato perche' ha senso se chiamato solo dalla
	* classe waRigheDB
	* @param mixed $dato dato in formato PHP  da convertire
	* @param string $tipoDB tipo del campo sul database
	* @return string valore da inserire nella query SQL
	* @ignore
	*/
	function valoreSql($dato, $tipoDB)
		{

		switch ($tipoDB)
			{
			case 'CONTAINER':
				$this->ErroreDatiInput("Errore: Impossibile inserire il campo contenitore \"$dato\".");
				$result=$this->stringaSQL($dato);
				break;
			case 'TEXT':
				$result=$this->stringaSQL($dato);
				break;
			case 'NUMBER':
				$result=$this->decimaleSQL($dato);
				break;
			case 'DATE':
				$result=$this->dataSQL($dato);
				break;
			case 'TIME':
				$result=$this->oraSQL($dato);
				break;
			case 'TIMESTAMP':
				$result=$this->dataOraSQL($dato);
				break;
			}
		return $result;
		}

	//***************************************************************************
	/**
	* -
	*
	* Prende un campo come arriva da db e lo converte nel formato usabile da PHP
	* il metodo non e' documentato perche' ha senso se chiamato solo dalla
	* classe waRigheDB
	* @param mixed $dato dato in formato DB  da convertire
	* @param string $tipoDB tipo del campo sul database
	* @return mixed  valore PHP
	* @ignore
	*/
	function convertiCampo($dato, $tipoDB)
		{

		$result=null;
		if (is_array($dato))
			{
			foreach($dato as $elemento)
				$result[] = $this->convertiSingoloCampo($elemento, $tipoDB);
			}
		else
			$result = $this->convertiSingoloCampo($dato, $tipoDB);

		return $result;
		}

	//***************************************************************************
	/**
	* -
	*
	* In pratica e' un duplicato di {@link esegui}, salvo che viene utilizzata
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
			$sql .= " LIMIT $rigaIniziale, $nrRighe";
		$result = $this->esegui($sql);
		if ($result === false)
			return false;

		if ($this->codErrore != 0 	&&  //Ok
			$this->codErrore != 101 && //Manca il record (?)
			$this->codErrore != 401)   //Nessun record trovato
			return false;

		// determiniamo la tabella (formato) perche' dopo ci servira'...
		$patternSelect = "/(select)\s+\*\s+from\s+([^\s]+)\s*(where)?\s*(.*)?/i";
		list($prima, $tabella) = preg_split($patternSelect,$sql,-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		if ($this->codErrore == 101 || //Manca il record (?)
			$this->codErrore == 401)   //Nessun record trovato
			{
			//Nel caso non vengano trovati record faccio una seconda query da 0 record per scaricare i nomi dei campi
			$result = $this->esegui("SELECT * FROM $tabella LIMIT 0,0");
//echo "<pre>" . print_r($result,1). "</pre><hr>";
			}

		// carichiamo le informazioni delle colonne
		$colInfos = array();
		// creiamo la colonna fittizia del recid; e' un finto campo, sempre
		// presente e read-only, che viene restituito in testa ad ogni riga
		$colInfos[] = $this->impostaAttributiColonne('', 0, $tabella, '-recid');

		foreach ($result['fields'] as $i => $colInfo)
			$colInfos[] = $this->impostaAttributiColonne($colInfo, $i + 1, $tabella);

		// carichiamo le righe
		$righeCrude=array();
		foreach($result['data'] as $ids => $valori)
			{
			list($recid, $modid) = explode(".", $ids, 2);
			$rigaCruda = array();
			$rigaCruda[0] = $recid;
			foreach ($valori as $nomecampo => $valore)
				{
				if (count($valore) == 1)
					$rigaCruda[] = html_entity_decode($valore[0], ENT_COMPAT, "UTF-8");
				else
					$rigaCruda[] = $valore;
				}
			$righeCrude[] = $rigaCruda;
			}

		return array($colInfos, $righeCrude, $result['foundCount']);

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
			case 'TEXT':
				$result=WADB_STRINGA;
				break;
			case 'NUMBER':
				$result=WADB_DECIMALE;
				break;
			case 'DATE':
				$result=WADB_DATA;
				break;
			case 'TIME':
				$result=WADB_ORA;
				break;
			case 'TIMESTAMP':
				$result=WADB_DATAORA;
				break;
			case 'CONTAINER':
				$result=WADB_CONTENITORE;
				break;
			default:
				$result=WADB_TIPOSCONOSCIUTO;
			}
		return $result;

		}

	//***************************************************************************
	/**
	* -
	*
	* prende un campo come arriva da db e lo converte nel formato usabile da PHP
	* @param mixed $dato dato in formato DB  da convertire
	* @param string $tipoDB tipo del campo sul database
	* @return mixed  valore PHP
	* @access protected
	* @ignore
	*/
	protected function convertiSingoloCampo($dato, $tipoDB)
		{
		$result=null;
		switch ($tipoDB)
			{
			case 'CONTAINER':
			case 'TEXT':
				$result=$dato;
				break;
			case 'NUMBER':
				if ($dato === '')
			         $result = '';
			    elseif (preg_match('/^(\d{1,3}(\,\d{3})*|(\d+))(\.\d+)?$/',$dato))
			           $result = ((float) str_replace(',','',$dato));
			    elseif (preg_match('/^(\d{1,3}(\.\d{3})*|(\d+))(\,\d+)?$/',$dato))         
				       $result = ((float) str_replace(',','.',str_replace('.','',$dato)));
				else $result = '*ERRORE*';
				break;
			case 'DATE':
				if (empty($dato)) $result='';
				else {
					list($mese,$giorno,$anno)=explode('/',$dato);
					$result=mktime(0,0,0,$mese,$giorno,$anno);
				}
				break;
			case 'TIME':
				if ($dato=='') $result='';
				else {
					list($ore,$minuti,$secondi)=explode(':',$dato);
					$result=mktime($ore,$minuti,$secondi,1,1,1980);
				}
				break;
			case 'TIMESTAMP':
				if ($dato=='') $result='';
				else {
				list($data,$ora)=explode(' ',$dato);
				list($mese,$giorno,$anno)=explode('/',$data);
					list($ore,$minuti,$secondi)=explode(':',$ora);
					$result=mktime($ore,$minuti,$secondi,$mese,$giorno,$anno);
				}
				break;
			}
		return $result;
		}

	//***************************************************************************
	/**
	* -
	*
	* Estrae i campi, i valori e gli operatori dalla stringa "SQL-like" e passa i valori alla FX
	* @param string $condizione stringa "SQL-like" da convertire e passare alla FX
	* @return void
	* @access protected
	* @ignore
	*/
	protected function buildWhereClause($condizione) {
		$pattern="/([^>=<\*\^\$]+)[\s\r\n]*(>=|<=|<>|==|=|>|<|LIKE|\*\*|\^\^|\$\$)[\s\r\n]*'(.+?)'[\s\r\n]*(and|or|$)[\s\r\n]*/si";

		// castroncino di bicio per evitare il problema delle condizioni su valore vuoto (''); 13/02/2008
	// castroncino di bicio per evitare il problema delle condizioni su valore vuoto (''); 13/02/2008
	$condizione = str_replace("\\''", "______", $condizione);
	$condizione = str_replace("''", "'='", $condizione);
	$condizione = str_replace("______", "\\''", $condizione);
	$elementi=preg_split($pattern,$condizione,-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    if ((count($elementi)-3)%4!=0) {
			$this->ErroreDatiInput("Errore: numero errato di parametri nella condizione, forse ti sei dimenticato gli apici nei valori dei campi");
			return false;
		}
		$mode='';
		$i=0;
		while (isset($elementi[$i])) {
			$campo=trim($elementi[$i++]);
			$op=$elementi[$i++];
			$valore=$elementi[$i++];
			$valore=str_replace("\\'","'",$valore);
			$valore= str_replace("@",'\@',$valore);
			switch (strtoupper($op)) {
					case '>=':
						$this->DBConn->AddDBParam ($campo, $valore, 'gte');
						break;
					case '<=':
						$this->DBConn->AddDBParam ($campo, $valore, 'lte');
						break;
					case '<>':
						$this->DBConn->AddDBParam ($campo, $valore, 'neq');
						break;
					case 'LIKE':
						if (substr($valore, 0, 1) == "%" && substr($valore, -1) == "%")
							{
							$fm_operator = "cn";
							$valore = substr($valore, 1, -1);
							}
						elseif (substr($valore, 0, 1) == "%")
							{
							$fm_operator = "ew";
							$valore = substr($valore, 1);
							}
						elseif (substr($valore, -1) == "%")
							{
							$fm_operator = "bw";
							$valore = substr($valore, 0, -1);
							}
						else
							$fm_operator = "eq";
						$this->DBConn->AddDBParam ($campo, $valore, $fm_operator);
						break;
//					case '$$':
//						$this->DBConn->AddDBParam ($campo, $valore, 'ew');
//						break;
//					case '^^':
//						$this->DBConn->AddDBParam ($campo, $valore, 'bw');
//						break;
					case '==':
						$this->DBConn->AddDBParam ($campo, "=".$valore, 'eq');
						break;
					case '=':
						// modifica del 09/07/2013; l'operatore di default è
						// l'uguale, e diventa dannoso specificarlo quando si 
						// usa il -recid in FM 12 (restituisce errore 4)
						$this->DBConn->AddDBParam ($campo, $valore);
						break;
					case '>':
						$this->DBConn->AddDBParam ($campo, $valore, 'gt');
						break;
					case '<':
						$this->DBConn->AddDBParam ($campo, $valore, 'lt');
						break;
					default:
						$this->ErroreDatiInput("Errore: operatore sconosciuto nella condizione \"".$campo.$op.$valore."\".");
				}
				$op_logico=isset($elementi[$i])?strtolower(trim($elementi[$i])):'';
				if ($op_logico=='and' && $mode=='') $mode='and';
			elseif ($op_logico=='or' && $mode=='') $mode='or';
			elseif (($op_logico=='and' || $op_logico=='or') && $mode!=$op_logico)
					$this->ErroreDatiInput('Errore: impossibile utilizzare AND e OR nella stessa condizione. In questo caso verrà utilizzato '.strtoupper($mode));
				if ($mode=='or') $this->DBConn->AddDBParam ('-lop', 'or');
				$i++;
		}
		return true;
	}

	//***************************************************************************
	/**
	* -
	*
	* Passa i nomi dei campi per l'ordinamento alla FX
	* @param array $arrayOrdinamenti array contenente i nomi dei campi per cui fare l'ordinamento, in caso di ordinamento decrescente aggingere ' desc' dopo il nome del campo.
	* @return void
	* @access protected
	* @ignore
	*/
	function buildSortOrder($arrayOrdinamenti) {
		foreach($arrayOrdinamenti as $nomeCampo) {
			$elementi=preg_split("[( )]",  $nomeCampo, 2, PREG_SPLIT_NO_EMPTY);
			if (count($elementi)==2) {
				if (strtolower(trim($elementi[1]))=='desc') {
					$nomeCampo=$elementi[0];
					$mode='descend';
				}	else {
					$mode='';
				}
			} else {
				$mode='';
			}
			$this->DBConn->AddSortParam ($nomeCampo, $mode);
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
	protected function impostaAttributiColonne($colInfo, $colIdx, $tabella, $name = '')
		{
		$col['nome'] = $name == '-recid' ? $name : $colInfo['name'];
		$col['indice'] = $colIdx;
		$col['tipoDB'] = $name == '-recid' ? 'NUMBER' : strtoupper($colInfo['type']);
		$col['tabella'] = $tabella;
		$col['chiavePrimaria'] = $name == '-recid' ? 1 : 0;
		$col['lunghezzaMax'] = $this->lunghezzaMaxCampo($col['tipoDB']);
		$col['tipo'] = $this->dammiTipoCampoApplicativo($col['tipoDB']);
		$col['nrRipetizioni'] = $name == '-recid' ? 1 : $colInfo['maxrepeat'];
		return $col;
		}

	//***************************************************************************
	/**
	* -
	*
	* Restituisce la lunghezza massima in byte di un campo del tipo indicato
	* @param mixed $tipo tipo del campo come ottenuto dal database
	* @return int
	* @access protected
	* @ignore
	*/
	protected function lunghezzaMaxCampo($tipoDB)
		{
		switch ($tipoDB)
			{
			case 'TEXT':
// 				 return 2147483648; // 2GB
				 return 1024; // 2GB
			case 'NUMBER':
//				 return 800; // 800 cifre
				 return 20; // 800 cifre
			case 'DATE':
				return 10; // gg/mm/aaaa
			case 'TIME':
				 return 11; // hh:mm:ss AM/PM
			case 'TIMESTAMP':
				return 22; // DATE e TIME concatenati con uno spazio
			case 'CONTAINER':
				 return 4294967296; // 4GB
			default:
				return null;
			}
		}

	//***************************************************************************
	/**
	* -
	*
	* Esegue un comando select SQL sul database connesso:
	* SELECT * FROM formato_scheda [WHERE campo1='valore1'[, campo2='valore2'...]][ ORDER BY campo1[desc][, campo2[desc]],...][ LIMIT primaRiga,numeroRighe]
	* Case-insensitive
	* @param string $sql SQL da eseguire
	* @return mixed i dati grezzi ottenuti dalla query o FALSE in caso di errore
	* @access protected
	* @ignore
	*/
	function eseguiSelect($sql)
		{
			
		$patternSelect="/(select)[\s\r\n]+\*[\s\r\n]+from[\s\r\n]+([^\s\n\r]+)[\s\r\n]*(where)?[\s\r\n]*(.*)/si";
		$elementi=preg_split($patternSelect,trim($sql),-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		if (count($elementi)==1)
			return $this->ErroreDatiInput("Errore: Query SQL non corretta");
		$formato = $elementi[1];
		$arrayOrdinamenti=array();
		$arrayLimit=array(0,999999);
		$stringa = isset($elementi[3])?$elementi[3]:$elementi[2];
		//Controllo se c'è il limit
		if (stripos($stringa,'limit ')!==false)
			{
			list ($stringa,$limit)=preg_split('/limit[\s\r\n]/si',$stringa);
			$patternLimit="/[\s\r\n]*,[\s\r\n]*/si";
			$arrayLimit=preg_split($patternLimit,trim($limit),-1,PREG_SPLIT_NO_EMPTY);
			if (count($arrayLimit)!=2)
				return $this->ErroreDatiInput("Errore: Query SQL non corretta, sintassi per istruzione LIMIT errata. Usare \" LIMIT rigaIniziale,numeroRighe\"");
			}
		//Controllo se c'è order by
		if (stripos($stringa,'order by')===false)
			$condizione=trim($stringa);
		else
			{
			list($condizione,$ordinamenti)=preg_split('/order by/si',$stringa);
			$patternOrder="/[\s\r\n]*,[\s\r\n]*/i";
			$arrayOrdinamenti=preg_split($patternOrder,trim($ordinamenti),-1,PREG_SPLIT_NO_EMPTY);
			$condizione=trim($condizione);
			}

		//Passo i valori alla FX
		$this->DBConn->SetDBData($this->WADB_NOMEDB, $formato, $arrayLimit[1]);
		if (!empty($arrayLimit[0]))
			$this->DBConn->FMSkipRecords($arrayLimit[0]);
		$ok=true;
		if (!empty($condizione))
			$ok=$this->buildWhereClause($condizione);
		if (!$ok)
			return false;
		if (!empty($arrayOrdinamenti))
			$this->buildSortOrder($arrayOrdinamenti);
		//Eseguo la query
		$this->DBConn->FMUseCURL(false);
		if (!empty($condizione))
			$result=$this->DBConn->FMFind();
		else
			$result=$this->DBConn->FMFindAll();

		return $result;

		}

	//***************************************************************************
	/**
	 * -
	 *
	 * Esegue un comando delete SQL sul database connesso:
	 * DELETE FROM formato_scheda [WHERE campo1='valore1'[, campo2='valore2'...]]
	 * Case-insensitive
	 * @param string $sql SQL da eseguire
	 * @return mixed i dati grezzi ottenuti dalla query o FALSE in caso di errore
	 * @access protected
	 * @ignore
	 */
	protected function eseguiDelete($sql)
		{

		$patternSelect = "/(delete)[\s\n\r]+from[\s\n\r]+([^\s\n\r]+)[\s\n\r]*(where)?[\s\n\r]*(.*)/si";
		$elementi = preg_split($patternSelect, trim($sql), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		if (count($elementi) == 1) return $this->ErroreDatiInput("Errore: Query SQL non corretta");
		$formato = $elementi[1];
		$stringa = isset($elementi[3]) ? $elementi[3] : $elementi[2];
		$condizione = trim($stringa);

		//Passo i valori alla FX
		$this->DBConn->SetDBData($this->WADB_NOMEDB, $formato, 999999);
		$ok = true;
		if (!empty($condizione)) $ok = $this->buildWhereClause($condizione);
		if (!$ok)
			{
			$this->ErroreDatiInput('Errore nella condizione WHERE');
			return false;
			}
		//Eseguo la query
		$this->DBConn->FMUseCURL(false);
		
		// per la cancellazione di un record è necessario specificare il recid; 
		// ma se la condizione impostata è già sul -recid è inutile andare a 
		// rileggere il record per rilevare il -recid ...
		if (strtolower(substr($condizione, 0, strlen("-recid='"))) == "-recid='")
			return $this->DBConn->FMDelete(true);
				
		if (!empty($condizione)) $risultatoRicerca = $this->DBConn->FMFind();
		else $risultatoRicerca = $this->DBConn->FMFindAll();

		//Controllo che il risultato non sia un FX_Error
		if (is_a($risultatoRicerca, 'FX_Error'))
			{
			return false;
			}

		if ($risultatoRicerca['errorCode'] != 0)
			{
			$this->codErrore = $risultatoRicerca['errorCode'];
			return false;
			}

		//Eseguo le query di delete
		foreach ($risultatoRicerca['data'] as $id => $rec)
			{
			list ($recid, $modid) = explode(".", $id);
			$this->DBConn->AddDBParam('-recid', $recid);
			$result = $this->DBConn->FMDelete(true);
			}

		return $result;
		}

	//***************************************************************************
	/**
	* -
	*
	* Esegue un comando insert SQL sul database connesso:
	* INSERT INTO formato_scheda (campo1[, campo2...]) VALUES ('valore1'[, 'valore2',...])
	* Case-insensitive
	* @param string $sql SQL da eseguire
	* @return mixed i dati grezzi ottenuti dalla query o FALSE in caso di errore
	* @access protected
	* @ignore
	*/
	protected function eseguiInsert($sql)
		{
		$patternInsert="/(insert)[\s\n\r]+into\s+([^\s\n\r]+)[\s\n\r]+\([\s\n\r]*([^\)]+)[\s\n\r]*\)[\s\n\r]+(values)[\s\n\r]+/si";
		$elementi=preg_split($patternInsert,trim($sql),-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		if (count($elementi)!=5)
			return $this->ErroreDatiInput("Errore: Query SQL non corretta");
		$formato=$elementi[1];
		$patternCampi="/[\s\n\r]*,[\s\n\r]*/si";
		$campi=preg_split($patternCampi,$elementi[2],-1,PREG_SPLIT_NO_EMPTY);
		$patternValori="/[\s\n\r]*(?U)('.*')[\s\n\r]*(?:,|$)/si";
		$valori=preg_split($patternValori,trim(substr($elementi[4],1,-1)),-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		if (count($campi) > count($valori))
			return $this->ErroreDatiInput("Errore: il numero di campi è maggiore del numero dei valori.");
		if (count($campi) < count($valori))
			return $this->ErroreDatiInput("Errore: il numero di valori è maggiore del numero dei campi, ti sei dimenticato dei valori oppure gli apici nei valori");
		//Passo i valori alla FX
		$this->DBConn->SetDBData($this->WADB_NOMEDB, $formato, 1);
		for ($i=0;$i<count($campi);$i++)
			{
			$valore=substr($valori[$i],1,-1);
			$valore=str_replace("\\'","'",$valore);
			$this->DBConn->AddDBParam(trim($campi[$i]), utf8_decode($valore));
			}
		$result = $this->DBConn->FMNew();

		// se l'esito e' ok, valorizziamo l'ultimo idInserito
		if ($result['errorCode'] == 0)
			{
			$key = key($result['data']);
			list($this->_ultimoIdInserito, $modId) = explode(".", $key);
			}

		return $result;
		}

	//***************************************************************************
	/**
	* -
	*
	* Esegue un comando update SQL sul database connesso:
	* UPDATE formato_scheda SET campo1='valore1'[, campo2='valore2'...][WHERE campo1='valore1'[, campo2='valore2'...]]
	* Case-insensitive
	* @param string $sql SQL da eseguire
	* @return mixed i dati grezzi ottenuti dalla query o FALSE in caso di errore
	* @access protected
	* @ignore
	*/
	protected function eseguiUpdate($sql)
		{
		$patternUpdate="/(update)[\s\r\n]+([^\s\r\n]+)[\s\r\n]+set[\s\r\n]+(.*)/si";
		$elementi=preg_split($patternUpdate,$sql,-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		if (count($elementi)!=3)
			return $this->ErroreDatiInput("Errore: Query SQL non corretta");
		$formato=$elementi[1];
		if (stripos($elementi[2],'where')!==false)
			{
			$patternWhere="/(where[\s\r\n])/i";
 			list ($stringaParametri,$condizione) = preg_split($patternWhere,$elementi[2],-1,PREG_SPLIT_NO_EMPTY );
			}
		else
			{
			$stringaParametri=$elementi[2];
			$condizione='';
			}
		$patternParametri="/[\s\r\n]*([^=]+)[\s\r\n]*=[\s\r\n]*(?U)('.*')[\s\r\n]*(?:,|$)/si";
		$parametri=preg_split($patternParametri,trim($stringaParametri),-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	    if (count($parametri)%2!=0)
			return $this->ErroreDatiInput("Errore: Nella query manca un nome di un campo o un valore");
		
		$this->DBConn->SetDBData($this->WADB_NOMEDB, $formato, 999999);
		//OTTIMIZZAZIONE
		//Se nella condizione viene indicato solo il recid non mi serve fare una ricerca, uso
		//quello specificato nella condizione	
		if (preg_match("/-recid='([0-9]+)'/",$condizione,$match)==0) {
			//Passo i valori per la ricerca alla FX
			$ok=true;
			if (!empty($condizione))
				$ok=$this->buildWhereClause($condizione);
			if (!$ok) 
				return $this->ErroreDatiInput('Errore nella condizione WHERE');
			
			//Eseguo la query di ricerca
			$this->DBConn->FMUseCURL(false);
			if (!empty($condizione))
				$risultatoRicerca=$this->DBConn->FMFind();
			else
				$risultatoRicerca=$this->DBConn->FMFindAll();
	
	        //Controllo che il risultato non sia un FX_Error
	        if (is_a($risultatoRicerca,'FX_Error')) {
	          return false;
	        }
	
			if ($risultatoRicerca['errorCode']!=0) {
	            $this->codErrore=$risultatoRicerca['errorCode'];
				return false;
	        }
		} else {
			//E' stato specificato solo il recid nella condizione
			$recid=$match[1];
			//Creo una struttura dati identica a quella ottenuta da una ricerca
			$risultatoRicerca['data'][$recid.'.0']='dummy record';
		}
		//Eseguo le query di update
		foreach ($risultatoRicerca['data'] as $id=>$rec)
			{
			list ($recid,$modid)=explode(".",$id);
			$this->DBConn->AddDBParam('-recid', $recid);
			$i=0;
			while (isset($parametri[$i]))
				{
				$campo=trim($parametri[$i++]);
				$valore=str_replace("\\'","'",substr(trim($parametri[$i++]),1,-1));
				$this->DBConn->AddDBParam($campo, utf8_decode($valore));
				}
			$result = $this->DBConn->FMEdit();

			if (is_a($result,'FX_Error')) {
                $result=false;
            }

			if ($result['errorCode']!='0') {
                $this->codErrore=$result['errorCode'];
				$result=false;
			}
		}

	  return $result;

	}

	//***************************************************************************
	/**
	* -
	*
	* fa un giochino: usa sempre il medesimo codice di errore ma sovrascrive
	 * ogni volta il messaggio di errore, in modo da dare al programmatore
	 * qualche informazione in piu'
	* @param string $riga testo delmessaggio di errore
	* @return boolean sempre false
	* @access protected
	* @ignore
	*/
	protected function ErroreDatiInput($riga)
		{
		$this->erroriFM[20000] = $riga;
		return false;
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
//****  fine classe waConnessioneDB_fm *****************************************
//***************************************************************************
	}	// fine classe


?>