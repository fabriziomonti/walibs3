<?php
/**
* @package waDocumentazione
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_DOCUMENTAZIONE'))
{
/**
* @ignore
*/
define('_WA_DOCUMENTAZIONE',1);

//*****************************************************************************
/**
* waDocumentazione
*
 * Classe per la creazione automatica di documentazione di applicazioni waLibs.
 * 
* @package waDocumentazione
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waDocumentazione
	{
	/**
	 * directory di destinazione della documentazione
	 * @ignore
	 */
	private $dirDest;

	/**
	 * oggetto di classe waApplicazione si cui si appoggia la classe per la
	 * gestione
	 * @var waApplicazione
	 * @ignore
	*/
	protected $app;
	
	/**
	 * oggetto di classe waConnessioneDB per l'accesso al db della documentazione
	* @var waConnessioneDB
	 * @ignore
	*/
	protected $dbconn;
	
	/**
	 * file di configurazione dell'oggetto di classe waConnessioneDB per 
	 * l'accesso al db della documentazione
	* @var string
	* @ignore 
	*/
	protected $fileConfigurazioneDB;
	
	//***************************************************************************
	/**
	* costruttore
	*
	* @param waApplicazione $app oggetto applicazione all'interno della quale gestire la documentazione
	* @param string $fileConfigurazioneDB opzionale; nome del file di configurazione 
	 * della classe waDB che sara' utilizzata per leggere/scrivere la documentazione. 
	 * Attenzione: non deve necessariamente essere il file del db dell'applicazione
	 * che si andra' a documentare; nel caso lo fosse, le tabelle contenenti le
	 * informazioni di documentazione saranno create all'interno dello stesso DB 
	 * dell'applicazione
	* @param string $nomeFileConfigurazione nome del file di configurazione della classe;
	 * se lasciato vuoto verrà utilizzato il file di configurazione di default
	* @return void 
	*/
	function __construct(waApplicazione $app, $fileConfigurazioneDB = '', $nomeFileConfigurazione = '')
		{
		$nomeFileConfigurazione = empty($nomeFileConfigurazione) ?
									dirname(__FILE__) . "/wadocumentazione.config.inc.php" :
									$nomeFileConfigurazione;
		include($nomeFileConfigurazione);
		
		$this->dirDest = $WADOC_DIR_DEST;
		$this->app = $app;
		$this->fileConfigurazioneDB = $fileConfigurazioneDB;
		$this->dbconn = $this->app->dammiConnessioneDB($fileConfigurazioneDB);
		$this->_creaTabelleDB();
		
		// salviamo in sessione (sperando che qualcuno l'abbia fatta partire)
		// i nomi dei file di configurazione: l'applicazione di gestione 
		// documentazione, se invocata, li preleverà da lì
		$_SESSION["waDocumentazione"]["fileConfigurazioneDB"] = $fileConfigurazioneDB;
		$_SESSION["waDocumentazione"]["nomeFileConfigurazione"] = $nomeFileConfigurazione;
		$_SESSION["waDocumentazione"]["datiApplicazione"]["titolo"] = $this->app->titolo;
		$_SESSION["waDocumentazione"]["datiApplicazione"]["versione"] = $this->app->versione;
		$_SESSION["waDocumentazione"]["datiApplicazione"]["dataVersione"] = $this->app->dataVersione;
		
//		$this->_svuotaTutto();exit();
		}
		
	//***************************************************************************
	/**
	 * fa partire il programma di gestione della documentazione
	 * 
	 */
	function avviaGestione ()
		{
		$index = $this->dammiMiaPath() . "/wadocapp/index.php";
		if (stripos($_SERVER["SERVER_SOFTWARE"], "IIS") !== false)
			// vai a sapere perchè, IIS vuole il dominio...
			$index = "http://" . $this->app->dominio . "/$index";
		
		$this->app->ridireziona($index);
		}
		
	//***************************************************************************
	/**
	* crea la documentazione automatica di una sezione dell'applicazione
	*
	* @param string $siglaSezione sigla della sezione che si intende documentare
	* @param string $nomeSezione nome esteso della sezione che si intende documentare
	* @return void 
	 * @ignore
	*/
	function documentaSezione($siglaSezione, $nomeSezione)
		{
		
		$sql = "SELECT * FROM wadoc_sezioni WHERE sigla=" . $this->dbconn->stringaSql($siglaSezione);
		$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
		$riga = $rs->righe[0];
		if (empty($riga))
			{
			$riga = $rs->aggiungi();
			$riga->inserisciValore("sigla", $siglaSezione);
			$riga->inserisciValore("nome", $nomeSezione);
			$this->_salvaRigheDB($rs);
			}
			
		// gia' che siamo in piedi censiamo anche gli allegati
		$this->documentaAllegati();
		}
		
	//***************************************************************************
	/**
	* crea la documentazione automatica degli allegati della documentazione. Il
	* metodo viene gia' invocato automaticamente ogni volta che si documenta una
	* sezione mediante {@link documentaSezione}; e' lasciato public unicamente
	* per autodocumentazione ed eventuale overloading
	* 
	* Per essere documentati gli allegati devono risiedere in
	* 
	* <b><i>[directory_destinazione_documentazione]</i>/allegati</b>
	*
	 * @ignore
	* @return void 
	*/
	function documentaAllegati()
		{
		$files = glob("$this->dirDest/allegati/*");
		if (empty($files))
			return ;
			
		foreach ($files as $file)
			{
			$file = basename($file);
			$sql = "SELECT * FROM wadoc_allegati WHERE nome=" . $this->dbconn->stringaSql($file);
			$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
			$riga = $rs->righe[0];
			if (empty($riga))
				{
				$riga = $rs->aggiungi();
				$riga->inserisciValore("nome", $file);
				$riga->inserisciValore("titolo", $file);
				$this->_salvaRigheDB($rs);
				}
			}
		}
		
	//***************************************************************************
	/**
	* crea la documentazione automatica di una pagaina dell'applicazione
	*
	* @param string $siglaSezione sigla della sezione a cui la pagina appartiene
	* @param string $titolo titolo della pagina
	 * @ignore
	* @return void 
	*/
	function documentaPagina($siglaSezione, $titolo)
		{
		if (isset($_GET['wadoc_act']))
			return;
			
		$idSezione = $this->_dammiIdSezione($siglaSezione);
		$nomePagina = basename($_SERVER['SCRIPT_NAME']);
		$sql = "SELECT * FROM wadoc_pagine WHERE idSezione=" . $this->dbconn->interoSql($idSezione) .
				" AND nome=" . $this->dbconn->stringaSql($nomePagina);
		$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
		$riga = $rs->righe[0];
		if (empty($riga))
			{
			$riga = $rs->aggiungi();
			$riga->inserisciValore("idSezione", $idSezione);
			$riga->inserisciValore("nome", $nomePagina);
			$riga->inserisciValore("titolo", $titolo);
			$this->_salvaRigheDB($rs);
			}
		}
		
	//***************************************************************************
	/**
	* crea la documentazione automatica di un menu dell'applicazione
	*
	* @param string $siglaSezione sigla della sezione a cui il menu appartiene
	* @param wamenu $menu oggetto di classe waMenu che si intende documentare
	 * 
	 * @ignore
	* @return void 
	*/
	function documentaMenu($siglaSezione, wamenu $menu)
		{
		$this->dbconn->iniziaTransazione();
		
		$idSezione = $this->_dammiIdSezione($siglaSezione);
		$sql = "SELECT * FROM wadoc_menu WHERE idSezione=" . $this->dbconn->interoSql($idSezione) .
					" AND nome=" . $this->dbconn->stringaSql($menu->nome);
		$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
		$riga = $rs->righe[0];
		if (empty($riga))
			{
			$riga = $rs->aggiungi();
			$riga->inserisciValore("idSezione", $idSezione);
			$riga->inserisciValore("nome", $menu->nome);
			$riga->inserisciValore("titolo", $menu->titolo ? $menu->titolo : $menu->nome);
			$this->_salvaRigheDB($rs);
			$idMenu = $rs->connessioneDB->ultimoIdInserito();
			}
		else 
			$idMenu = $riga->valore("idMenu");
			
		foreach ($menu->voci as $indice => $voce)
			{
			if (strpos($voce['url'], "wadoc_act=") !== false)
				continue;
			$sql = "SELECT * FROM wadoc_vociMenu WHERE idMenu=" . $this->dbconn->interoSql($idMenu) .
						" AND etichetta=" . $this->dbconn->stringaSql($voce['etichetta']) .
						" AND destinazione=" . $this->dbconn->stringaSql(basename($voce['url']));
			$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
			$riga = $rs->righe[0];
			if (empty($riga))
				$riga = $rs->aggiungi();
			$riga->inserisciValore("idMenu", $idMenu);
			$riga->inserisciValore("etichetta", $voce['etichetta']);
			$riga->inserisciValore("destinazione", basename($voce['url']));
			$riga->inserisciValore("livello", $voce['livello']);
			$riga->inserisciValore("posizione", $indice);
			$this->_salvaRigheDB($rs);
			}
			
		$this->dbconn->confermaTransazione();
		}
		
	//***************************************************************************
	/**
	* crea la documentazione automatica di una tabella dell'applicazione.
	* 
	* Se la tabella e' associata ad una tabella DB, viene anche creata la
	* documentazione della tabella del DB; in particolare:
	* - se la tabella DB e' di tipo MYSQL viene automaticamente creata la documentazione dell'intero DB
	* - se la tabella DB e' di tipo FM viene creata la documentazione del solo formato invocato
	*
	* @param string $siglaSezione sigla della sezione a cui la tabella appartiene
	* @param waTabella $table oggetto di classe waTabella che si intende documentare
	*
	 * @ignore
	* @return void 
	*/
	function documentaTabella($siglaSezione, waTabella $table)
		{
		$this->dbconn->iniziaTransazione();
		if (!empty($table->righeDB))
			$this->documentaDB($table->righeDB);
		
		$idPagina = $this->_dammiIdpagina($siglaSezione);
		$sql = "SELECT * FROM wadoc_tabelle WHERE idPagina=" . $this->dbconn->interoSql($idPagina) .
					" AND nome=" . $this->dbconn->stringaSql($table->nome);
		$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
		$riga = $rs->righe[0];
		if (empty($riga))
			{
			$riga = $rs->aggiungi();
			$riga->inserisciValore("idPagina", $idPagina);
			$riga->inserisciValore("nome", $table->nome);
			$riga->inserisciValore("titolo", $table->titolo ? $table->titolo : $table->nome);
			$this->_salvaRigheDB($rs);
			$idTabella = $rs->connessioneDB->ultimoIdInserito();
			}
		else 
			$idTabella = $riga->valore("idTabella");
			
		$indice = 0;
		foreach ($table->colonne as $col)
			{
			$sql = "SELECT * FROM wadoc_colonne WHERE idTabella=" . $this->dbconn->interoSql($idTabella) .
						" AND nome=" . $this->dbconn->stringaSql($col->nome);
			$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
			$riga = $rs->righe[0];
			if (empty($riga))
				$riga = $rs->aggiungi();
			$riga->inserisciValore("idTabella", $idTabella);
			$riga->inserisciValore("etichetta", $col->etichetta);
			$riga->inserisciValore("nome", $col->nome);
			$riga->inserisciValore("idCampo", $this->_dammiCampoDBDaTabella($table, $col));
			$riga->inserisciValore("mostra", $col->mostra);
			$riga->inserisciValore("ordina", $col->ordina);
			$riga->inserisciValore("filtra", $col->filtra);
			$riga->inserisciValore("totalizza", $col->totalizza);
			$riga->inserisciValore("aliasDi", $col->aliasDi);
			$riga->inserisciValore("posizione", $indice);
			$this->_salvaRigheDB($rs);
			$indice++;
			}
			
		$indice = 0;
		foreach ($table->azioni as $azione)
			{
			$sql = "SELECT * FROM wadoc_azioni WHERE idTabella=" . $this->dbconn->interoSql($idTabella) .
						" AND etichetta=" . $this->dbconn->stringaSql($azione->etichetta);
			$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
			$riga = $rs->righe[0];
			if (empty($riga))
				$riga = $rs->aggiungi();
			$riga->inserisciValore("idTabella", $idTabella);
			$riga->inserisciValore("etichetta", $azione->etichetta);
			$riga->inserisciValore("suRecord", $azione->suRecord ? 1 : 0);
			$riga->inserisciValore("condizionata", $azione->funzioneAbilitazione ? 1 : 0);
			$riga->inserisciValore("posizione", $indice);
			$this->_salvaRigheDB($rs);
			$indice++;
			}
			
		$this->dbconn->confermaTransazione();
		}
		
	//***************************************************************************
	/**
	* crea la documentazione automatica di un modulo dell'applicazione.
	* 
	* Se il modulo e' associato ad una tabella DB, viene anche creata la
	* documentazione della tabella del DB; in particolare:
	* - se la tabella DB e' di tipo MYSQL viene automaticamente creata la documentazione dell'intero DB
	* - se la tabella DB e' di tipo FM viene creata la documentazione del solo formato invocato
	*
	* @param string $siglaSezione sigla della sezione a cui il modulo appartiene
	* @param waModulo $modulo oggetto di classe waModulo che si intende documentare
	*
	 * @ignore
	* @return void 
	*/
	function documentaModulo($siglaSezione, waModulo $modulo)
		{
		$this->dbconn->iniziaTransazione();
		if (!empty($modulo->righeDB))
			$this->documentaDB($modulo->righeDB);
		
		$idPagina = $this->_dammiIdpagina($siglaSezione);
		$sql = "SELECT * FROM wadoc_moduli WHERE idPagina=" . $this->dbconn->interoSql($idPagina) .
					" AND nome=" . $this->dbconn->stringaSql($modulo->nome);
		$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
		$riga = $rs->righe[0];
		if (empty($riga))
			{
			$riga = $rs->aggiungi();
			$riga->inserisciValore("idPagina", $idPagina);
			$riga->inserisciValore("nome", $modulo->nome);
			$riga->inserisciValore("titolo", $modulo->titolo ? $modulo->titolo : $modulo->nome);
			$this->_salvaRigheDB($rs, $this->app);
			$idModulo = $rs->connessioneDB->ultimoIdInserito();
			}
		else 
			$idModulo = $riga->valore("idModulo");
			
		$indice = 0;
		foreach ($modulo->controlli as $ctrl)
			{
			if (is_a($ctrl, "waEtichetta"))
				continue;
			$sql = "SELECT * FROM wadoc_controlli WHERE idModulo=" . $this->dbconn->interoSql($idModulo) .
						" AND nome=" . $this->dbconn->stringaSql($ctrl->nome);
			$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
			$riga = $rs->righe[0];
			if (empty($riga))
				$riga = $rs->aggiungi();
			$riga->inserisciValore("idModulo", $idModulo);
			$riga->inserisciValore("etichetta", $this->_cercaEtichetta($ctrl));
			$riga->inserisciValore("nome", $ctrl->nome);
			$riga->inserisciValore("idCampo", $this->_dammiCampoDBDaModulo($ctrl));
			$riga->inserisciValore("tipo", $ctrl->tipo);
			$riga->inserisciValore("visibile", $ctrl->visibile);
			$riga->inserisciValore("solaLettura", $ctrl->solaLettura);
			$riga->inserisciValore("obbligatorio", $ctrl->obbligatorio);
			$riga->inserisciValore("posizione", $indice);
			$this->_salvaRigheDB($rs);
			$indice++;
			}
			
		$this->dbconn->confermaTransazione();
		}
		
	//***************************************************************************
	/**
	* crea la documentazione automatica del DB o di una tabella del DB
	* 
	* - se $righeDB e' di tipo MYSQL viene automaticamente creata la documentazione dell'intero DB
	* - se $righeDB e' di tipo FM viene creata la documentazione del solo formato invocato
	*
	* @param waRigheDB $righeDB oggetto di classe waRigheDB da cui desumere le informazioni che si intende documentare
	*
	 * @ignore
	* @return void 
	*/
	function documentaDB(waRigheDB $righeDB)
		{
		if (empty($righeDB))
			return;
			
		$dbconn2Doc = $righeDB->connessioneDB;
		if ($dbconn2Doc->WADB_TIPODB == WADB_TIPODB_MYSQL)
			{
			$sql = "SHOW TABLES";
			$rs = $this->app->dammiRigheDB($sql, $dbconn2Doc);
			
			foreach($rs->righe as $riga)
				$this->documentaTabellaDB($dbconn2Doc, $riga->valore(0));
			}
		elseif ($dbconn2Doc->WADB_TIPODB == WADB_TIPODB_FM)
			{
			// per FM non riusciamo a fare tutto il db in un colpo solo:
			// occorre che facciamo una tabella per volta.
			
			// recuperiamo il nome tabella (in realta': formato) dal primo campo
			$this->documentaTabellaDB($dbconn2Doc, $righeDB->colonneOrd[0]['tabella']);
			}
		}
		
	//***************************************************************************
	/**
	* crea la documentazione automatica di una tabella/formato del DB
	* 
	* @param waConnessioneDB $dbconn2Doc oggetto di classe waConnessioneDB a cui la tabella appartiene
	* @param string $nomeTabellaDB nome della tabella/formato da documentare
	*
	 * @ignore
	* @return void 
	*/
	function documentaTabellaDB(waConnessioneDB $dbconn2Doc, $nomeTabellaDB)
		{
		if (substr($nomeTabellaDB, 0, strlen("wadoc_")) == "wadoc_")
			return ;
			
		//$this->dbconn->iniziaTransazione();
		$sql = "SELECT * FROM wadoc_tabelleDB WHERE tipo=" . $this->dbconn->stringaSql($dbconn2Doc->WADB_TIPODB) .
					" AND nomeDB=" . $this->dbconn->stringaSql($dbconn2Doc->WADB_NOMEDB) .
					" AND nome=" . $this->dbconn->stringaSql($nomeTabellaDB);
		$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
		$riga = $rs->righe[0];
		if (empty($riga))
			{
			$riga = $rs->aggiungi();
			$riga->inserisciValore("tipo", $dbconn2Doc->WADB_TIPODB);
			$riga->inserisciValore("nomeDB", $dbconn2Doc->WADB_NOMEDB);
			$riga->inserisciValore("nome", $nomeTabellaDB);
			$this->_salvaRigheDB($rs, $this->app);
			$idTabellaDB = $rs->connessioneDB->ultimoIdInserito();
			}
		else 
			$idTabellaDB = $riga->valore("idTabellaDB");
			
		$sql = "SELECT * FROM $nomeTabellaDB";
		$rs2Doc = $this->app->dammiRigheDB($sql, $dbconn2Doc, 0);
		
		for($i = 0; $i < $rs2Doc->nrCampi(); $i++)
			{
			$sql = "SELECT * FROM wadoc_campi WHERE idTabellaDB=" . $this->dbconn->interoSql($idTabellaDB) .
						" AND nome=" . $this->dbconn->stringaSql($rs2Doc->nomeCampo($i));
			$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
			$riga = $rs->righe[0];
			if (empty($riga))
				$riga = $rs->aggiungi();
			$riga->inserisciValore("idTabellaDB", $idTabellaDB);
			$riga->inserisciValore("nome", $rs2Doc->nomeCampo($i));
			$riga->inserisciValore("tipo", $rs2Doc->tipoCampo($i));
			$riga->inserisciValore("tipoDB", $rs2Doc->tipoCampoDB($i));
			$maxLen = ($dbconn2Doc->WADB_TIPODB == WADB_TIPODB_MYSQL && 
						($rs2Doc->tipoCampo($i) == WADB_CONTENITORE || $rs2Doc->tipoCampo($i) == WADB_STRINGA) ?
						$rs2Doc->lunghezzaMaxCampo($i) / 3 : $rs2Doc->lunghezzaMaxCampo($i));
			$riga->inserisciValore("lunghezza", $maxLen);
			$riga->inserisciValore("chiavePrimaria", $rs2Doc->nomeCampo($i) == $rs2Doc->chiavePrimaria() ? 1 : 0);
			$riga->inserisciValore("posizione", $i);
			$this->_salvaRigheDB($rs);
			}
			
		//$this->dbconn->confermaTransazione();

		}
		
	//***************************************************************************
	/**
	* crea le tabelle sul db della waDocumentazione
	*
	* @return void
	* @ignore
	*/
	protected function _creaTabelleDB()
		{
		$queries = file_get_contents(dirname(__FILE__) . "/db/wadoc.sql");
		$queries = explode(";", $queries);
		foreach($queries as $query)
			{
			if (trim($query))
				$this->app->eseguiDB ($query, $this->dbconn);
			}
		}
		
	//***************************************************************************
	/**
	* ritorna l'id di una pagina
	*
	* @return integer
	* @ignore
	*/
	protected function _dammiIdPagina($siglaSezione)
		{
		$idSezione = $this->_dammiIdSezione($siglaSezione);
		$nomePagina = basename($_SERVER['SCRIPT_NAME']);
		$sql = "SELECT idPagina FROM wadoc_pagine" .
				" WHERE idSezione=" . $this->dbconn->interoSql($idSezione) .
				" AND nome=" . $this->dbconn->stringaSql($nomePagina);
		$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
		$riga = $rs->righe[0];
		if (empty($riga))
			return 0;
		else
			return $riga->valore('idPagina');
		}
		
	//***************************************************************************
	/**
	* ritorna l'id di una sezione dato una sigla sezione
	*
	* @return integer
	* @ignore
	*/
	protected function _dammiIdSezione($siglaSezione)
		{
		
		static $ids = array();
		
		if (!isset($ids[$siglaSezione]))
			{
			$sql = "SELECT idSezione FROM wadoc_sezioni" .
					" WHERE sigla=" . $this->dbconn->stringaSql($siglaSezione);
			$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
			$riga = $rs->righe[0];
			if (empty($riga))
				$ids[$siglaSezione] = 0;
			else
				$ids[$siglaSezione] = $riga->valore('idSezione');
			}
			
		return $ids[$siglaSezione];
		}
		
	//***************************************************************************
	/**
	* cerchiamo di capire il nome di una tabella di appartenenza di un campo
	*
	* @return void 
	* @ignore 
	*/
	protected function _dammiCampoDBDaTabella(waTabella $table, waColonna $col)
		{
		if (empty($table->righeDB))
			return ;
		$dbconn2Doc = $table->righeDB->connessioneDB;
		$infoCampoDB = $table->righeDB->colonne[strtoupper($col->nome)];
		
		return $this->_dammCampoDB($table->righeDB->connessioneDB, $infoCampoDB);
		}
		
	//***************************************************************************
	/**
	* cerchiamo di capire il nome di una tabella di appartenenza di un campo
	*
	* @return void 
	* @ignore 
	*/
	protected function _dammiCampoDBDaModulo(waControllo $ctrl)
		{
		if (is_a($ctrl, "waBottone"))
			return ;
		if (empty($ctrl->modulo->righeDB) || (!$ctrl->corrispondenzaDB))
			return ;
		$infoCampoDB = $ctrl->modulo->righeDB->colonne[strtoupper($ctrl->nome)];
		return $this->_dammCampoDB($ctrl->modulo->righeDB->connessioneDB, $infoCampoDB);
		}
		
	//***************************************************************************
	/**
	* cerchiamo di capire il nome di una tabella di appartenenza di un campo
	*
	* @return void 
	* @ignore 
	*/
	protected function _dammCampoDB(waConnessioneDB $dbconn2Doc, $infoCampoDB)
		{
		if (empty($infoCampoDB) || empty($infoCampoDB['tabella']))
			return;
			
		$sql = "SELECT wadoc_campi.idCampo" .
				" FROM wadoc_campi" .
				" INNER JOIN wadoc_tabelleDB ON wadoc_campi.idTabellaDB=wadoc_tabelleDB.idTabellaDB" .
				" WHERE wadoc_tabelleDB.tipo=" . $this->dbconn->stringaSql($dbconn2Doc->WADB_TIPODB) .
				" AND wadoc_tabelleDB.nomeDB=" . $this->dbconn->stringaSql($dbconn2Doc->WADB_NOMEDB) .
				" AND wadoc_tabelleDB.nome=" . $this->dbconn->stringaSql($infoCampoDB['tabella']) .
				" AND wadoc_campi.nome=" . $this->dbconn->stringaSql($infoCampoDB['nome']);
						
		$rs = $this->app->dammiRigheDB($sql, $this->dbconn, 1);
		$riga = $rs->righe[0];
		if (empty($riga))
			return ;
		return $riga->valore("idCampo");
			
		}
		
	//***************************************************************************
	/**
	*
	* @return string
	* @ignore 
	*/
	protected function _cercaEtichetta(waControllo $ctrl)
		{
		if (is_a($ctrl, "waBottone"))
			return $ctrl->valore;
			
		foreach ($ctrl->modulo->controlli as $etichetta)
			{
			if (!is_a($etichetta, "waEtichetta"))
				continue;
			if ($etichetta->nome == $ctrl->nome)
				return $etichetta->valore;	
			}
		}
		
	//*************************************************************************
	// funzione di salvataggio standard di un recordset
	/**
	* @ignore
	*/
	protected function _salvaRigheDB(waRigheDB $righeDB)
		{		
		$righeDB->salva();
		if ($righeDB->nrErrore())
			$this->app->mostraErroreDB($righeDB->connessioneDB);
		}
	
	//*************************************************************************
	/**
	* @ignore
	*/
	protected function _eseguiDB($sql)
		{		
		$this->dbconn->esegui($sql);
		if ($this->dbconn->nrErrore())
			$this->app->mostraErroreDB($this->dbcon);
		}
	
	//*************************************************************************
	// funzione di salvataggio standard di un recordset
	/**
	* @ignore
	*/
	protected function _svuotaTutto()
		{		
		$sql = "TRUNCATE TABLE wadoc_azioni";
		$this->dbconn->esegui($sql);
		$sql = "TRUNCATE TABLE wadoc_campi";
		$this->dbconn->esegui($sql);
		$sql = "TRUNCATE TABLE wadoc_colonne";
		$this->dbconn->esegui($sql);
		$sql = "TRUNCATE TABLE wadoc_controlli";
		$this->dbconn->esegui($sql);
		$sql = "TRUNCATE TABLE wadoc_menu";
		$this->dbconn->esegui($sql);
		$sql = "TRUNCATE TABLE wadoc_moduli";
		$this->dbconn->esegui($sql);
		$sql = "TRUNCATE TABLE wadoc_sezioni";
		$this->dbconn->esegui($sql);
		$sql = "TRUNCATE TABLE wadoc_pagine";
		$this->dbconn->esegui($sql);
		$sql = "TRUNCATE TABLE wadoc_tabelle";
		$this->dbconn->esegui($sql);
		$sql = "TRUNCATE TABLE wadoc_tabelleDB";
		$this->dbconn->esegui($sql);
		$sql = "TRUNCATE TABLE wadoc_vociMenu";
		$this->dbconn->esegui($sql);
		if ($this->dbconn->nrErrore())
			$this->app->mostraErroreDB($this->dbconn);
		}
	
			
	//*************************************************************************
	/**
	 * 
	 * @ignore
	 */
	function dammiMiaPath()
		{
		if (strpos(__FILE__, "\\") !== false)
			{
			// siamo sotto windows
			$thisFile = strtolower(str_replace("\\", "/", __FILE__));
			$dr = strtolower(str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']));
			}
		else
			{
			$thisFile = __FILE__;
			$dr = $_SERVER['DOCUMENT_ROOT'];
			}

		if (substr($dr,-1) == "/")
			$dr = substr($dr, 0, -1);
		if ($dr != substr($thisFile, 0, strlen($dr)))
			// quando la document root non e' in comune con la path del file corrente, 
			// allora significa che siamo in ambiente di sviluppo, e si includono
			// i file da un link simbolico; in questo caso la libreria deve essere 
			// posta immediatamente al di sotto della document root; se non si puo'
			// fare, occorre copiare la lib dove si ritiene opportuno
			$toret = "/" . basename(dirname(dirname($thisFile))) . "/" . basename(dirname($thisFile));
		else
			$toret = substr(dirname($thisFile), strlen($dr));

		return $toret;		
		}

	//***************************************************************************
	}

	
//***************************************************************************
} //  if (!defined('_WA_DOCUMENTAZIONE'))
?>