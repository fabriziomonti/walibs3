<?php
/**
* @package waTabella
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_TABELLA'))
{
/**
* @ignore
*/
define('_WA_TABELLA',1);


//***************************************************************************
//****  classe waTabella **************************************************
//***************************************************************************
/**
* waTabella
*
* classe per la gestione di una tabella standard con alimentazione
* proveniente da una query SQL o da una matrice in memoria.
* 
* @package waTabella
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waTabella
	{
	/**
	* foglio XSLT
	*
	* e' il nome completo di path del file XSLT che deve essere applicato 
	 * all'XML generato dalla classe.
	* 
	* Se non valorizzato viene utilizzato il foglio XSLT di default della
	* classe (azioni su record in linea con bottoni a sinistra e uso di javascript)
	 * 
	* @var string
	*/	
	var $xslt 			= '';
	
	/**
	* nome della tabella
	*
	* E' buona norma che il nome
	 * segua le regole di naming della programmazione (no spazi, punteggiatura, 
	 * ecc.), perche' e' verosimile che lato client questa proprieta' venga 
	 * utilizzata per accedere ad un oggetto di programmazione.
	 * 
	* @var string
	*/	
	var $nome	= 'watabella';
	
	/**
	* nr. massimo di righe da mostrare all'interno di una pagina
	*
	* se posto a 0, allora non viene gestita la paginazione e vengono
	* mostrati tutti i record che soddisfano le condizioni impostate nella
	* query
	* @var integer
	*/	
	var $listaMaxRec = WATBL_LISTA_MAX_REC;
	
	/**
	 * flag di selezione mutuamente esclusiva
	 *
	 * indica, dal solo punto di vista della logica di programmazione, 
	 * se la selezione dei record e' mutuamente esclusiva, oppure se e'
	 * possibile selezionare piu' di un record alla volta, evidentemente ai fini 
	 * di azioni da compiere su piu' record contemporaneamente.
	 * 
	 * L'implementazione di <i>come</i> cio' avviene e' completamente a carico 
	 * del foglio di stile XSLT
	 * 
	 * @var boolean
	 */	
	var $selezioneEsclusiva	= true;
	
	/**
	 * titolo della tabella che puo' essere utilizzato come si vuole nell'XSLT
	 *
	 * @var string
	 */
	var $titolo;
		
	
	/**
	* modulo per azioni sui record
	*
	* Nome della pagina da invocare a fronte della pressione dei tasti
	* delle azioni standard sui record (vedi/modifica/Elimina) nonche' sull'azione,
	 * sempre standard, "Nuovo".
	* 
	* Quando alla tabella viene richiesta una azione standard su un record 
	* (Vedi, Nuovo, Modifica, Elimina), 
	* tipicamente questa azione viene svolta da una pagina contenente
	* un modulo, ossia un oggetto di classe waModulo. 
	 * 
	 * Come venga gestita l'apertura della pagina in questione e' completamente 
	 * a carico della UI.
	 * 
	 * @var string
	*/	
	var $paginaModulo = '';
	
	/**
	* eventuale funzione PHP da invocare prima della costruzione di ogni riga
	*
	* Alla funzione verra' passata l'intera tabella ($this).
	* se la funzione ritorna FALSE, allora la riga verra' omessa dalla lista.
	 * 
	 * E' possibile anche richiamare il metodo di un oggetto, anzichè una 
	 * funzione procedurale; in questo caso la proprietà assumerà il valore
	 * di un array, in cui il primo elemento è l'oggetto che contiene il metodo
	 * e il secondo elemento il nome del metodo.
	 * 
	* @var mixed string|array
	*/	
	var $funzionePrimaDiRiga = '';
	
	/**
	* eventuale funzione PHP da invocare dopo la costruzione di ogni riga
	*
	* Alla funzione verra' passata l'intera tabella ($this).
	 * 
	 * E' possibile anche richiamare il metodo di un oggetto, anzichè una 
	 * funzione procedurale; in questo caso la proprietà assumerà il valore
	 * di un array, in cui il primo elemento è l'oggetto che contiene il metodo
	 * e il secondo elemento il nome del metodo.
	 * 
	* @var mixed string|array
	*/	
	var $funzioneDopoDiRiga = '';
	
	/**
	* recordset
	*
	* oggetto di classe waRigheDB generato dalla query {@link sql} opportunamente
	* limitato per la corretta paginazione e filtrato dai filtri utente
	* @var waRigheDB
	*/	
	var	$righeDB; 
	
	/**
	* record
	*
	* oggetto di classe waRecord contente il record corrente durante il
	* ciclo di generazione del buffer di output
	* @var waRecord
	*/	
	var	$record; 
	
	/**
	* array delle azioni
	*
	* array associativo che contiene tutte le azioni 
	* applicabili alla tabella, siano esse su un insieme di record o su un 
	* singolo record. Ogni azione corrispondera'
	* ad un bottone (o a qualsiasi altro meccanismo deinito nel foglio {@link $xslt}, 
	* il quale inneschera', secondo le modalita' definite nalla UI,
	 *  la funzionalita' ad esso associata.
	* 
	* Ogni azione e' contenuta in
	* un oggetto di classe {@link waAzioneTabella}. Puo' essere indifferentente creato
	* invocando il metodo {@link aggiungiAzione}, oppure inizializzando direttamente
	* l'elemento dell'array.
	* 
	* L'array e' composto di un nr. di elementi a piacere, in cui la 
	* chiave dell'elemento e' il nome dell'azione.
	*
	* La classe fornisce di default le azioni standard:
	* - Nuovo 
	* - Vedi
	* - Modifica
	* - Elimina
	* dove <b>Nuovo</b> e' un'azione non {@link suRecord}, mentre le altre lo sono.
	* 
	* Inoltre, a fronte di determinate condizioni:
	* - Filtro // se almeno una delle colonne e' ordinabile/filtrabile
	* - noFiltro // se il filtro e' correntemente attivo
	*
	* E' possibile non mostrare uno qualsiasi dei bottoni di azione predefiniti
	* semplicemente eliminando l'elemento corrispondente. Ad esempio:
	*
	* <code>
	* $table = new waTabella("SELECT * FROM Fornitori");
	* $table->eliminaAzione('Nuovo');
	* </code>
	*
	* elimina dalla pagina la possibilita' di inserire un nuovo record.
	*
	* Analogamente e' possibile ridefinire la caption delle azioni standard.
	* Ad esempio:
	*
	* <code>
	* $table->azioni['Nuovo']->etichetta = 'Inserisci';
	* </code>
	*
	* cambia la caption (etichetta)
	* del bottone che per default del metodo {@link aggiungiAzione} 
	 * e' uguale al nome.
	* 
	* @var array
	*/	
	var $azioni	= array();

	/**
	* array delle colonne
	*
	* array associativo che contiene tutte le colonne sotto forma di oggetti
	* {@link waColonna}.
	 * 
	* Ogni colonna (elemento dell'array), puo' essere indifferentente creato
	* invocando il metodo {@link aggiungiColonna}, oppure inizializzando direttamente
	* l'elemento dell'array; si tenga pero' presente che essendo un array associativo, 
	* l'ordine delle colonne sara' quello di creazione degli elementi,
	* e che la prima colonna della tabella (non necessariamente il primo campo 
	* del record e non necessariamente visibile) DEVE sempre essere l'identificativo 
	* univoco della riga.
	 * 
	 * La chiave dell'array sara' corrispondente alla prorieta' {@link waColonna::$nome nome} della 
	 * classe {@link waColonna}
	* @var array
	*/	
	var $colonne	= array();		

	/**
	* flag di filtro attivo sulla vista
	*
	* readOnly; indica se c'e' un filtro attivo sulla vista (la tabella e' frutto di
	* una selezione effettuata col bottone "Filtro")
	* @var boolean
	*/	
	var $filtroAttivo = false;
	
	/**
	* matrice dei campi modificati da un'eventuale azione di input
	*
	* @var array
	*/	
	var $input = array();
	
	/**
	* istanza applicazione a cui la tabella appartiene
	 * 
	 * eventuale istanza di una applicazione (waApplicazione o sua estensione o 
	 * simili) all'interno della quale la tabella è stata generata.
	 * <br/><br/>
	 * La valorizzazione di questa proprietà ha senso solo quando si intende
	 * effettuare una chiamata RPC ad un metodo che appartiene all'applicazione
	*
	* @var object
	*/	
	var $applicazione = null;
	
	/**
	* orientazione di eventuale esportazione in pdf (P/L)
	*
	* @var string
	*/	
	var $pdf_orientazione = 'P';
	
	/**
	* classe personalizzata per la gestione del pdf (e' cosi' possibile 
	 * personalizzare hdr, ftr, font, ecc.). 
	 * 
	 * La classe deve mostrare la medesima interfaccia di watbl_pdf (se derivate
	 * andate sicuri...)
	*
	* @var string
	*/	
	var $pdf_nome_classe = 'watbl_pdf';
	
	/**
	* path completa del file che contiene la classe personalizzata per 
	 * l'esportazione in pdf
	*
	* @var string
	*/	
	var $pdf_file_classe = '';
	
	//*************************************************************************
	// protected

	/**
	* sql 
	*
	* query sql che alimenta la tabella. Il recordset waRigheDB
	* sara' generato dalla classe e restituito sotto forma di proprieta'
	* all'applicazione
	* @ignore
	* @access protected
	* @var string
	*/	
	protected	$_sql; 
	
	/**
	* file configurazione DB 
	*
	* @ignore
	* @access protected
	* @var string
	*/	
	protected	$_fileConfigurazioneDB; 
	
	/**
	* connessione DB
	*
	* oggetto di classe waconnessioneDB che gestisce la connessione
	* @var waconnessioneDB
	* @access protected
	* @ignore
	*/	
	protected	$connessioneDB; 
	
	/**
	* @ignore
	* @access protected
	* @var string
	*/	
	protected $_idName;				// nome del campo identificativo univoco del record
								// e' sempre il campo individuato dalla 1a colonna
	
	/**
	* @ignore
	* @access protected
	* @var string
	*/	
	protected $_colsByCntr;			// protected; colonne ad accesso sequenziale
	
	/**
	* @ignore
	* @access protected
	* @var string
	*/	
	protected $_currPath;				// path corrente della classe
	
	/**
	* @ignore
	* @access protected
	* @var string
	*/	
	protected $_recordCount;				// path corrente della classe
	
	/**
	* @ignore
	* @access protected
	* @var boolean
	*/	
	protected $_hasTotals;				// indica che almeno una colonna
									// deve essere totalizzata e quindi
									// viene prodotta la riga dei totali
	
	/**
	* clausola SELECT
	*
	* testo della clausola SELECT da invocare per l'accesso al db
	* @var string
	* @ignore
	* @access protected
	*/	
	protected	$SelectClause; 
	
	/**
	* clausola WHERE
	*
	* testo della clausola WHERE da invocare per l'accesso al db
	* (puo' essere raffinata da eventuali filtri richiesti 
	* dall'utente)
	* @access protected
	* @ignore
	* @var string
	*/	
	protected	$WhereClause; 
	
	/**
	* clausola FROM
	*
	* testo della clausola FROM da invocare per l'accesso al db
	* @access protected
	* @ignore
	* @var string
	*/	
	protected	$FromClause; 
	
	/**
	* clausola GROUP BY
	*
	* testo della clausola GROUP BY da invocare per l'accesso al db
	* @var string
	* @ignore
	* @access protected
	*/	
	protected	$GroupClause; 
	
	/**
	* clausola ORDER BY
	*
	* testo della clausola ORDER BY da invocare per l'accesso al db
	* (puo' essere ricoperta da eventuali ordinamenti richiesti 
	* dall'utente)
	* @access protected
	* @ignore
	* @var string
	*/	
	protected	$OrderClause; 
	
	/**
	* eventuale array che popola la tabella al posto della stringa sql
	*
	* @ignore
	* @access protected
	* @var array
	*/	
	protected	$matrice; 
	
	/**
	* buffer utilizzato per contenere l'output della tabella
	*
	* @access protected
	* @ignore
	* @var string
	*/	
	protected	$buffer; 
	
	/**
	* lista dei modi filtro applicabili alle colonne
	*
	* @access protected
	* @ignore
	* @var array
	*/	
	protected	$modiFiltro = array("lt" => array("operatore" => "<", "nome" => "minore"),
									"le" => array("operatore" => "<=", "nome" => "minore uguale"), 
									"eq" => array("operatore" => "=", "nome" => "uguale"), 
									"ge" => array("operatore" => ">=", "nome" => "maggiore uguale"), 
									"gt" => array("operatore" => ">", "nome" => "maggiore"), 
									"ne" => array("operatore" => "<>", "nome" => "diverso"), 
									"like"  => array("operatore" => " like ", "nome" => "contiene"));

	/**
	* lista dei modi ordinamento applicabili alle colonne
	*
	* @access protected
	* @ignore
	* @var array
	*/	
	protected	$modiOrdinamento = array("" => "",
										"asc" => "Crescente", 
										"desc" => "Decrescente");

	
	//***************************************************************************
	//***************************************************************************
	//***************************************************************************
	/**
	* waTabella (costruttore)
	*
	* inizializza la tabella.
	 * 
	* @param mixed $sqlOArray : se stringa e' la query sql che sara' passata al database; se e' un array, e' la matrice che verra' utilizzata per popolare la tabella; l'array potra' essere associativo o semplice, salvo che ci sia la corrispondenza tra la chiave/indice della colonna ed il nome del campo passato al metodo {@link aggiungiColonna}. In caso di array, la tabella non sara' paginata e non sara' possibile utilizzare le funzionalita' relative all'ordinamento e filtro.
	* @param string $fileConfigurazioneDB nome del file di configurazione della classe waDB da utilizzare per la connessione al DB
	*/
	function __construct($sqlOArray, $fileConfigurazioneDB = null)
		{
		if (is_array($sqlOArray))
			{
			$this->matrice = $sqlOArray;
			$this->listaMaxRec = 0;
			$this->_recordCount = count($this->matrice);
			}
		else
			$this->_sql = $sqlOArray;
		$this->_fileConfigurazioneDB = $fileConfigurazioneDB;

		$this->xslt = dirname(__FILE__) . "/uis/wa_azioni_sx_default/xslt/watabella.xsl";

		// creazione azioni standard
		$this->aggiungiAzione('Nuovo');
		$this->aggiungiAzione('Filtro');
		$this->aggiungiAzione('Vedi', true);
		$this->aggiungiAzione('Modifica', true);
		$this->aggiungiAzione('Elimina', true);
		
		$this->_currPath = watabella_miaPath();
		}

	//***************************************************************************
	/**
	* aggiunge una azione
	*
	* aggiunge una azione all'array {@link azioni}. 
	 * 
	* @param string $nome nome dell'azione ({@link waAzioneTabella::$nome})
	* @param boolean $suRecord definisce se l'azione e' suRecord o meno ({@link waAzioneTabella::$suRecord})
	* @param string $etichetta caption del bottone; per default uguale al nome ({@link waAzioneTabella::$etichetta})
	* @param mixed $funzioneAbilitazione callback function di verifica dell'abilitazione dell'azione
	* per la riga corrente ({@link waAzioneTabella::$funzioneAbilitazione})
	* @return waAzioneTabella oggetto {@link waAzioneTabella} che contiene i parametri dell'azione
	*/
	function aggiungiAzione($nome, 
							$suRecord = false, 
							$etichetta = '',
							$funzioneAbilitazione = '')
		{
		$etichetta = (empty($etichetta) ? $nome : $etichetta);
		$this->azioni[$nome] = new waAzioneTabella();
		$this->azioni[$nome]->nome = $nome;
		$this->azioni[$nome]->suRecord = $suRecord;
		$this->azioni[$nome]->etichetta = $etichetta;
		$this->azioni[$nome]->funzioneAbilitazione = $funzioneAbilitazione;
		return $this->azioni[$nome];
		}
	
	//***************************************************************************
	/**
	* elimina una azione
	*
	* elimina una azione dall'array {@link azioni}. Ha senso chiamare questo
	* metodo solo per le azioni di default (Nuovo/Vedi/Modifica/Elimina)
	* @param string $nome nome dell'azione ({@link waAzioneTabella::$nome})
	* @return void
	*/
	function eliminaAzione($nome)
		{
		unset($this->azioni[$nome]);
		}
	
	//***************************************************************************
	/**
	* Aggiunge una colonna alla tabella
	*
	* per la spiegazione approfondita dei parametri si veda le proprieta'
	* della classe {@link waColonna}.
	*
	* @param string $nome nome colonna
	* @param string $etichetta intestazione colonna
	* @param boolean $mostra flag di visualizzazione
	* @param boolean $ordina flag di ordinamento
	* @param boolean $filtra flag di filtro
	* @param integer $allineamento allineamento colonna
	* @param integer $formattazione formattazione contenuto cella
	* @param integer $maxCaratteri nr.massimo caratteri visualizzabili dalla cella
	* @param integer $nrDecimali nr. decimali da mostrare in caso di formattazione float
	* @param boolean $link indica che il contenuto della cella e' destinato a contenere un link
	* @param string $funzioneCalcolo funzione personalizzata di calcolo del valore da mostrare nella cella
	* @param boolean $totalizza flag di totalizzazione colonna
	* @param string $aliasDi eventuale nome campo di cui la colonna e' alias
	* @param boolean $noACapo indica se usare o meno il wrap all'interno delle celle della colonna
	* @param boolean $convertiHTML indica se convertire o meno il valore trovato in corrispondenza del campo affinche' venga mostrato, di eventuali sequenze HTML significative, il codice oppure il rendering
	* @return waColonna istanza dell'oggetto {@link waColonna} che viene aggiunta
	* all'array {@link colonne}; e' naturalmente possibile modificare gli attributi
	* di questa istanza a piacimento.
	*/
	function aggiungiColonna(	$nome, 
								$etichetta, 
								$mostra = true,
								$ordina = true, 
								$filtra = true, 
								$allineamento = WATBL_ALLINEA_SX, 
								$formattazione = '',
								$nrDecimali = 2,
								$maxCaratteri = WATBL_CELLA_MAX_CARATTERI,
								$link = false,
								$funzioneCalcolo = '',
								$totalizza = false,
								$aliasDi = '',
								$noACapo = false,
								$convertiHTML = true
							)

		{
		$index = count($this->colonne);
		$this->colonne[$nome] = new waColonna();
		$this->colonne[$nome]->nome = $nome;
		$this->colonne[$nome]->etichetta = $etichetta;
		$this->colonne[$nome]->mostra = $mostra;
		$this->colonne[$nome]->ordina = $this->daSql() ? $ordina : false;
		$this->colonne[$nome]->filtra = $this->daSql() ? $filtra : false;
		$this->colonne[$nome]->allineamento = $allineamento;
		$this->colonne[$nome]->formattazione = $formattazione;
		$this->colonne[$nome]->nrDecimali = $nrDecimali;
		$this->colonne[$nome]->maxCaratteri = $maxCaratteri;
		$this->colonne[$nome]->link = $link;
		$this->colonne[$nome]->funzioneCalcolo = $funzioneCalcolo;
		$this->colonne[$nome]->totalizza = $totalizza;
		$this->colonne[$nome]->aliasDi = $aliasDi;
		$this->colonne[$nome]->noACapo = $noACapo;
		$this->colonne[$nome]->convertiHTML = $convertiHTML;
                
		$this->_colsByCntr[$index] = &$this->colonne[$nome];
		return $this->colonne[$nome];
		}

	//***************************************************************************
	/**
	* trasforma il buffer xml con il foglio di stile associato
	*
	 * @ignore
	*/
	protected function trasforma()
		{
		// Create an XSLT processor
		$xp = new XsltProcessor();
		
		// create a DOM document and load the XSL stylesheet
		$xsl = new DomDocument;
		$xsl->load($this->xslt);
		
		// import the XSL styelsheet into the XSLT process
		$xp->importStylesheet($xsl);
		
		// create a DOM document and load the XML datat
		$xml_doc = new DomDocument;
		//  $xml_doc->load("test.xml");
		$xml_doc->loadXML($this->buffer);
		
		// transform the XML into HTML using the XSL file
		if (!$out = $xp->transformToXML($xml_doc))
			trigger_error('XSL transformation failed.', E_USER_ERROR);
			
		return $out;
		} 

	//***************************************************************************
	/**
	* Effettua la visualizzazione della tabella
	*
	* In pratica, e' l'ultimo metodo da invocare. Una volta chiamato questo metodo 
	* la tabella viene visualizzata e il compito della classe e' terminato
	*
	* @param boolean $bufferizza se false, allora viene immediatamente effettuato
	* l'output della tabella; altrimenti la funzione ritorna il buffer di output 
	* della tabella stessa
	* @return void|string
	*/
	function mostra($bufferizza = false)
		{
		$this->creaBuffer();
		$html = $this->trasforma();

		// in alcuni casi e' comodo usare il metodo di output xhtml; questo pero'
		// crea una riga !DOCTYPE che non ha senso per la sola tabella (che e' solo una parte
		// della pagina e potrebbe anche essecene piu' d'una); nel caso rimuoviamo
		// il tag
		while (true)
			{
			list ($prima, $dopo) = explode("<!DOCTYPE ", $html, 2);
			if (!$dopo)
				break;
			list ($dt, $dopo) = explode(">", $dopo, 2);
			$html = "$prima$dopo";
			}
		
		if ($bufferizza)
			return $html;
			
		echo $html;
		} 

	//***************************************************************************
	/**
	* mostraXML
	*
	* da usare in fase di debug per mostrare l'output XML anziche' l'HTML generato
	* dall'XSLT.
	* @param boolean $bufferizza se false, allora viene immediatamente effettuato
	* l'output del menu; altrimenti la funzione ritorna il buffer di output 
	* del menu stesso
	* @return void|string
	*/
	function mostraXML($bufferizza = false)
		{
		$this->creaBuffer();
		if ($bufferizza)
			return $this->buffer;
			
		header("Content-Type: text/xml; charset=utf-8");			
		echo $this->buffer;
		}
		
	//***************************************************************************
	/**
	* costruisce l'XML della tabella
	*
	* @access protected
	* @ignore
	*/
	protected function creaBuffer()
		{
	 	$this->buffer = "<?xml version='1.0' encoding='UTF-8'?>\n" .
								"<watabella>\n" .
 								"<versione_librerie_xsl>" . LIBXSLT_VERSION . "</versione_librerie_xsl>\n" .
 								"<nome>$this->nome</nome>\n" .
 								"<uri>" . $this->dammiURI() . "</uri>\n" .
 								"<watabella_path>$this->_currPath</watabella_path>\n" .
 								"<titolo>" . htmlspecialchars($this->titolo) . "</titolo>\n" .
 								"<selezione_esclusiva>" . ($this->selezioneEsclusiva ? 1 : 0) . "</selezione_esclusiva>\n" .
 								"<pagina_modulo>" . htmlspecialchars($this->paginaModulo) . "</pagina_modulo>\n";

		// encoding dei parametri da passare al modulo di ordinamento e filtro...
 		$this->creaXmlFiltro();
 		$this->creaXmlOrdinamento();
 									
		$this->buffer .= "\n\n";

		// crea la sezione delle azioni (sia su pagina che su record;
		// decidera' l'XSLT che farsene)
		$this->creaAzioni();
			
		$this->creaBarraNavigazione();
		
		// headers della tabella
		$this->creaIntestazioniTabella();
			
		// righe
		$this->creaRigheTabella();			
		
		// eventuale riga dei totali
		$this->creaRigaTotali();
		
		$this->buffer .= "</watabella>\n";
		
//		$this->buffer = str_replace("&", "&amp;", $this->buffer);
//		$this->buffer = str_replace("&amp;gt;", "&gt;", $this->buffer);
//		$this->buffer = str_replace("&amp;lt;", "&lt;", $this->buffer); 


		} 

	//***************************************************************************
	/**
	* crea il recordset {@link $righeDB} che verra' utilizzato per alimentare la tabella
	*
	* Il recordset sara' creato partendo dalle condizioni impostate dalla query
	* sql passata al costruttore successivamente raffinato/modificato mediante 
	* le selezioni effettuate dall'utente in fase di ordinamento/filtro.
	* 
	* @return boolean false nel caso si sia verificato un errore nell'esecuzione
	* della query
	*/
	function caricaRighe()
		{
		$this->connessioneDB = $this->connessioneDB ? $this->connessioneDB : wadb_dammiConnessione($this->_fileConfigurazioneDB);
		if ($this->connessioneDB->nrErrore())
			return false;
		
		// divide la query sql nelle varie clausole
		$this->_splitSql();
		// Applica eventuali filtri impostati con i criteri di ricerca
		$this->applicaFiltro();
		// Applica eventuali ulteriori filtri impostati con la ricerca rapida
		$this->applicaRicercaRapida();
		
		// Applica eventuali sort order impostati con i criteri di ricerca
		$this->applicaOrdinamento();
		
		// Applica eventuali ulteriori sort order impostati con l'ordinamento rapido
		$this->applicaOrdinamentoRapido();
		
		// se viene richiesta l'esportazione usiamo una paginazione forzata che
		// parte dalla prima riga (ovviamente) e arriva fino alla centesima;
		// in questo modo non rischiamo di sforare il memory limit; saranno poi
		// le procedura di esportazione che si preoccuperanno di leggere i 
		// restanti record
		if ($_GET["watbl_esporta_csv"][$this->nome])
			$this->esportaCSV();
		elseif ($_GET["watbl_esporta_xls"][$this->nome])
			$this->esportaXLS();
		elseif ($_GET["watbl_esporta_pdf"][$this->nome])
			$this->esportaPDF();
		
		list($nrRighe, $rigaIniziale) = $this->GetLimitClause();
		
		$this->righeDB = new waRigheDB($this->connessioneDB);
		$sql = "$this->SelectClause $this->FromClause $this->WhereClause $this->GroupClause $this->OrderClause";
		$this->righeDB->caricaDaSql($sql, $nrRighe, $rigaIniziale);
		if ($this->righeDB->nrErrore())
			return false;
		$this->_recordCount = $this->righeDB->nrRigheSenzaLimite();
		
		return true;
		}
		
	//***************************************************************************
	/**
	*
	*
	* divide la query sql nelle varie clausole
	* @access protected
	* @ignore
	*/	
	protected function _splitSql()
		{
		$this->SelectClause = $this->getClause($this->_sql, 'select');
		$this->FromClause = $this->getClause($this->_sql, 'from');
		$this->WhereClause = $this->getClause($this->_sql, 'where');
		$this->GroupClause = $this->getClause($this->_sql, 'group');
		$this->OrderClause = $this->getClause($this->_sql, 'order');
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
		$clauses = array("select", "from", "where", "group", "order", "limit");
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
	* @ignore
	* @access protected
	*/	
	protected function FormatVal($col, $val)
		{
		switch($col->formattazione)
			{
			case WATBL_FMT_DATA:
				return $this->GetDateS($val);
			case WATBL_FMT_DATAORA:
				return $this->GetDateTimeS($val);
			case WATBL_FMT_ORA:
				return $this->GetTimeS($val);
			case WATBL_FMT_DECIMALE:
				return $this->FormatCurrency($val, $col->nrDecimali);
			case WATBL_FMT_STRINGA:
				return $val;
			case WATBL_FMT_INTERO:
				return intval($val);
			case WATBL_FMT_CRUDO:
				return $val;
			default:
				if (!$this->daSql())
					return $val;
        		$Index = $this->righeDB->indiceCampo($col->nome);
			    switch ($this->righeDB->tipoCampo($col->nome))
			        {
			        case WADB_DECIMALE:
						return $this->FormatCurrency($val, $col->nrDecimali);
			        case WADB_DATA:
						return $this->GetDateS($val);
			        case WADB_DATAORA:
						return $this->GetDateTimeS($val);
			        case WADB_ORA:
						return $this->GetTimeS($val);
			        default:
						return $val;
			        }
			}
			
		}
		
	//***************************************************************************
	/**
	* @ignore
	*/	
	 function Format(waColonna $col)
		{
		$val = $this->daSql() ?
					$this->record->valore($col->nome) :
					$this->record[$col->nome];
		
		if ($col->convertiHTML)
			return htmlspecialchars($this->FormatVal($col, $val));
		return "<![CDATA[" . $this->FormatVal($col, $val) . "]]>";
		}
		
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	protected function GetDateS($tm)
		{
		if ($tm)
	    	return date("Y-m-d" ,$tm);
		}
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	protected function GetTimeS($tm)
		{
		if ($tm)
	    	return date("H:i:s" ,$tm);
		}
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	protected function GetDateTimeS($tm)
		{
		return trim($this->GetDateS($tm) . " " . $this->GetTimeS($tm));
		}
	
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	protected function FormatCurrency ($TheVar, $DecNr = 2)
		{
	    if ($TheVar === null || $TheVar === '') 
	    	return '';
	    $TheVar = (float) $TheVar;
	
	    $TheVar = number_format($TheVar, $DecNr, ".", '');
	
	    return $TheVar;
		}
		
	//*****************************************************************************
	// genera i controlli hidden che vengono associati ad ogni riga di una tabella
	/**
	* @ignore
	* @access protected
	*/	
	protected function creaQualificatoriRiga()
		{
		// scriviamo un controllo invisibile per ogni azione su record che puo'
		// essere abilitata/disabilitata
		$this->buffer .= "\t\t\t<azioni_abilitabili>\n";
		foreach ($this->azioni as $azione)
			{
			if ($azione->suRecord && $azione->funzioneAbilitazione)
				$this->buffer .= "\t\t\t\t<azione id='$azione->nome'>\n" . 
									"\t\t\t\t\t<nome>$azione->nome</nome>\n" . 
									"\t\t\t\t\t<abilitazione>" . (call_user_func($azione->funzioneAbilitazione, $this) ? 1 : 0) . "</abilitazione>\n" . 
									"\t\t\t\t</azione>\n";
			}
		$this->buffer .= "\t\t\t</azioni_abilitabili>\n";
		}
			
	//*****************************************************************************
	// costruisce il link da chiamare in caso di click su una intestazione di 
	// colonna, che provoca il sort rapido dei record in visualizzazione
	/**
	* @access protected
	* @ignore
	*/	
	protected function dammiDatiOrdinamentoRapido(waColonna $col)
		{
		return $col->ordina && $_GET["watbl_or"][$this->nome] == $col->nome ? $_GET["watbl_orm"][$this->nome] : 'no';
		}

	//*****************************************************************************
	// restituisce la uri della pagina corrente
	/**
	* @ignore
	* @access protected
	*/	
	protected function dammiURI()
		{
		// prendiamo tutti i parametri get e riportiamo solo quelli valorizzati
		$passo = explode("&", $_SERVER['QUERY_STRING']);
		$toret = '';
		$amp = '';
		foreach ($passo as $elem)
			{
			list($k, $v) = explode("=", $elem, 2);
			if (strlen($v))
				{
				$toret .= "$amp$k=$v";
				$amp = "&";
				}
			}
		
		return $toret ? htmlspecialchars("?$toret") : '';
			
		}

	//*****************************************************************************
	// dato un parametro per la costruzione del link, restituisce il valore
	// contenuto nel db/matrice se il parametro corrisponde ad un nome campo,
	// altrimenti il parametro e' una costante e quindi viene ritornato
	// esso stesso
	/**
	* @ignore
	* @access protected
	*/	
	protected function dammiValoreParametroLink($param)
		{
		if ($this->daSql())
			{
			$indiceCampo = $this->righeDB->indiceCampo($param);
			$valore = isset($indiceCampo) ? $this->record->valore($param) : $param;
			}
		else
			$valore = array_key_exists($param, $this->record) ? 
						$this->record[$param] : $param;
			
		return $valore;
		}
		
	//*****************************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	protected function creaAzioni()
		{
    	
		$this->GetControlloRicercaRapida();
		if ($this->filtroAttivo)
			$this->aggiungiAzione("noFiltro", false, 'No filtro');
							
		// aggiungiamo le azioni non "suRecord"
    	$this->buffer .= "\t<watabella_azioni_pagina>\n";
		foreach ($this->azioni as $azione)
			{
			if (!$azione->suRecord)
				$this->buffer .=  "\t\t<azione id='$azione->nome'>\n" .
									"\t\t\t<nome>$azione->nome</nome>\n" .
									"\t\t\t<etichetta>$azione->etichetta</etichetta>\n" .
									"\t\t</azione>\n";
			}
    	$this->buffer .= "\t</watabella_azioni_pagina>\n";
		$this->buffer .= "\n\n";
				
		// mostriamo le azioni "suRecord"
    	$this->buffer .= "\t<watabella_azioni_record>\n";
		foreach ($this->azioni as $azione)
			{
			if ($azione->suRecord)
				$this->buffer .=  "\t\t<azione id='$azione->nome'>\n" .
									"\t\t\t<nome>$azione->nome</nome>\n" .
									"\t\t\t<etichetta>$azione->etichetta</etichetta>\n" .
									"\t\t</azione>\n";
			}
    	$this->buffer .= "\t</watabella_azioni_record>\n";
		$this->buffer .= "\n\n";
	
		}
	
	//*****************************************************************************
	/**
	 * 
	 * trasforma il valore ricevuto da un controllo di ricerca (filtro o rapida)
	 * a seconda di questi casi:
	 * o se la ricerca e' per 'like' aggiunge i caratteri jolly prima e dopo
	 * o se viene riconosciuta una data, la data viene formattata secondo le regole del db
	 * o in tutti gli altri casi ritorna il valore passato
	 * 
	 * comunque e' da migliorare....
	 * 
	* @ignore
	* @access protected
	*/	
	protected function dammiSqlValore(waColonna $col, $valore, $operatore)
		{
		// non sappiamo a priori quale e' il tipo del valore che ci stanno
		// passando, perche' non avendo ancora acceduto al db non sappiamo di 
		// che tipo e' la colonna data; il client e' pero' tenuto a formattare 
		// secondo le regole standard le date e i decimali; per i decimali non
		// c'e' problema: cosi' come vengono passati vanno bene anche per PHP e
		// quindi per il db; per le date/ore, invece, occorre cercare di 
		// riconoscerle e trasformarle di conseguenza
		if (preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $valore, $parts)) 
			{
			$time = mktime($parts[4], $parts[5], $parts[6], $parts[2], $parts[3], $parts[1]);
			// ci caviamo gli apicetti che poi glieli rimettiamo
			$valore = substr(substr($this->connessioneDB->dataOraSql($time), 1), 0, -1);
			}
		elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $valore, $parts)) 
			{
			$time = mktime(0,0,0, $parts[2], $parts[3], $parts[1]);
			// ci caviamo gli apicetti che poi glieli rimettiamo
			$valore = substr(substr($this->connessioneDB->dataSql($time), 1), 0, -1);
			}
		elseif (preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $valore, $parts)) 
			{
			$time = mktime($parts[1], $parts[2], $parts[3], 1, 1, 1980);
			// ci caviamo gli apicetti che poi glieli rimettiamo
			$valore = substr(substr($this->connessioneDB->oraSql($time), 1), 0, -1);
			}
			
		$valore = $operatore == ' like ' ? "%$valore%" : $valore;
		return $this->connessioneDB->stringaSql($valore);
		}
		
	//*****************************************************************************
	/**
	 * 
	 * dato l'indice di un filtro ritorna l'operatore utilizzato per l'ordinamento
	 * 
	* @ignore
	* @access protected
	*/	
	protected function dammiModoOrdinamentoColonna($idx)
		{
		if (!is_int($idx))
			return;
		if ($this->modiOrdinamento[$_GET["watbl_om"][$this->nome][$idx]])
			return  $_GET["watbl_om"][$this->nome][$idx];

		}
	
	//*****************************************************************************
	/**
	 * 
	 * verifica se una colonna e' stata selezionata per l'ordinamento e se del caso
	 * ritorna l'indice dell'ordinamento
	 * 
	 * attenzione che potrebbe ritornare false e 0, che sono due cose diverse
	 * 
	* @ignore
	* @access protected
	*/	
	protected function dammiIndiceOrdinamentoColonna(waColonna $col)
		{
		if (!is_array( $_GET["watbl_oc"][$this->nome]))
			return false;
			
		foreach( $_GET["watbl_oc"][$this->nome] as $idx => $nomeCampo)
			{
			if ($col->nome == $nomeCampo && $col->ordina)
				return $idx;
			}
			
		return false;
		}

	//*****************************************************************************
	/**
	 * 
	 * 
	* @ignore
	* @access protected
	*/	
	protected function creaXmlOrdinamento()
		{
 		$this->buffer .= "<modi_ordinamento>\n";
 		foreach ($this->modiOrdinamento as $valore => $nome)
 			$this->buffer .= "\t<item><valore>$valore</valore><nome>$nome</nome></item>\n";
 		$this->buffer .= "</modi_ordinamento>\n";
 		
		$this->buffer .= "<ordinamento>\n";
		if (is_array( $_GET["watbl_oc"][$this->nome]))
			{
			foreach( $_GET["watbl_oc"][$this->nome] as $idx => $nomeCampo)
				{
				if ($this->colonne[$nomeCampo] && $this->colonne[$nomeCampo]->ordina)
					$this->buffer .= "\t<item>\n" .
										"\t\t<indice>$idx</indice>\n" . 
										"\t\t<campo>$nomeCampo</campo>\n" . 
										"\t\t<modo>" . $this->dammiModoOrdinamentoColonna($idx) . "</modo>\n" . 
										"\t</item>\n" ;
				}
			}
			
		$this->buffer .= "</ordinamento>\n";
		
		}
	
	//*****************************************************************************
	/**
	 * 
	 * 
	* @ignore
	* @access protected
	*/	
	protected function creaXmlFiltro()
		{
 		$this->buffer .= "<modi_filtro>\n";
 		foreach ($this->modiFiltro as $valore => $modo)
 			$this->buffer .= "\t<item><valore>$valore</valore><nome>$modo[nome]</nome></item>\n";
 		$this->buffer .= "</modi_filtro>\n";
 		
		$this->buffer .= "<filtro>\n";
		if (is_array( $_GET["watbl_fc"][$this->nome]))
			{
			foreach( $_GET["watbl_fc"][$this->nome] as $idx => $nomeCampo)
				{
				if ($this->colonne[$nomeCampo] &&
						$this->colonne[$nomeCampo]->filtra &&
						($modo = $this->dammiModoFiltroColonna($idx, true)) &&
						($valore = $this->dammiValoreFiltroColonna($idx, true)))
					$this->buffer .= "\t<item>\n" .
										"\t\t<indice>$idx</indice>\n" . 
										"\t\t<campo>$nomeCampo</campo>\n" . 
										"\t\t<modo>$modo</modo>\n" . 
										"\t\t<valore>$valore</valore>\n" . 
										"\t</item>\n" ;
				}
			}
			
		$this->buffer .= "</filtro>\n";
		
		}
	
	//*****************************************************************************
	/**
	 * 
	 * dato l'indice di un filtro ritorna l'operatore utilizzato per il filtro
	 * 
	* @ignore
	* @access protected
	*/	
	protected function dammiModoFiltroColonna($idx, $mask = false)
		{
		if (!is_int($idx))
			return;
		if ($operatore = $this->modiFiltro[ $_GET["watbl_fm"][$this->nome][$idx]]['operatore'])
			{
			if ($mask)
				return  $_GET["watbl_fm"][$this->nome][$idx];
			else
				return $operatore;
			}

		}
	
	//*****************************************************************************
	/**
	 * 
	 * dato l'indice di un filtro ritorna il valore utilizzato per il filtro
	 * 
	* @ignore
	* @access protected
	*/	
	protected function dammiValoreFiltroColonna($idx, $mask = false)
		{
		if (!is_int($idx))
			return;
		if ($mask)
			return htmlentities( $_GET["watbl_fv"][$this->nome][$idx]);
		return  $_GET["watbl_fv"][$this->nome][$idx];
		}
	
	//*****************************************************************************
	/**
	 * 
	 * verifica se una colonna e' stata selezionata per il filtro e se del caso
	 * ritorna l'indice del filtro
	 * 
	 * attenzione che potrebbe ritornare false e 0, che sono due cose diverse
	 * 
	* @ignore
	* @access protected
	*/	
	protected function dammiIndiceFiltroColonna(waColonna $col)
		{
		if (!is_array( $_GET["watbl_fc"][$this->nome]))
			return false;
			
		foreach( $_GET["watbl_fc"][$this->nome] as $idx => $nomeCampo)
			{
			if ($col->nome == $nomeCampo &&
					$col->filtra &&
					$this->dammiModoFiltroColonna($idx) &&
					$this->dammiValoreFiltroColonna($idx))
				return $idx;
			}
			
		return false;
		}
	
	//*****************************************************************************
	// aggiunge alle condizioni di where di una lista ulteriori coindizioni,
	// in base agli eventuali criteri di ricerca impostati dalle funzioni di filtro
	// e ordinamento (sortfilter.php e pagine relative)
	/**
	* @ignore
	* @access protected
	*/	
	protected function applicaFiltro()
		{
		if (!is_array( $_GET["watbl_fc"][$this->nome]))
			return;
			
		$where = "";
		foreach( $_GET["watbl_fc"][$this->nome] as $idx => $nomeCampo)
			{
			$col = $this->colonne[$nomeCampo];
			if (!$col || $this->dammiIndiceFiltroColonna($col) !== $idx)
				continue;

			$nomeCampo = $col->aliasDi ? $col->aliasDi : $col->nome;
			$operatore = $this->dammiModoFiltroColonna($idx);
			$valore = $this->dammiSqlValore($col, $this->dammiValoreFiltroColonna($idx), $operatore);
			$where .= ($where ? " AND " : '') . 
						($col->aliasDi ? $col->aliasDi : $col->nome) .
						$operatore . $valore;
			}

		if (!$where)
			return;
			
		$this->filtroAttivo = true;
		$this->WhereClause = (!$this->WhereClause ? "WHERE" : "$this->WhereClause AND") . " $where";
			
		}

	//*****************************************************************************
	// Apllica ulteriori filtri alla query impostati con la ricerca rapida
	/**
	* @ignore
	* @access protected
	*/	
	protected function applicaRicercaRapida()
		{
		if (!$rr = trim( $_GET["watbl_rr"][$this->nome]))
			return;
			
		$where = "";
		foreach ($this->colonne as $nomeCol => $col)		
			{
			if (!$col->filtra)
				continue;
			
			$where .= ($where ? " OR " : '') . 
						($col->aliasDi ? $col->aliasDi : $col->nome) .
						" LIKE " . $this->dammiSqlValore($col,  $_GET["watbl_rr"][$this->nome], ' like ');
			}
			
		if ($where)
			$this->WhereClause = (!$this->WhereClause ? "WHERE" : "$this->WhereClause AND") . " ($where)";
		
		}

	//*****************************************************************************
	// restituisce eventuali sort order impostati con i criteri di ricerca 
	// impostati dalle funzioni di filtro e ordinamento (sortfilter.php e pagine 
	// relative)
	/**
	* @ignore
	* @access protected
	*/	
	protected function applicaOrdinamento()
		{
	
		if (!is_array( $_GET["watbl_oc"][$this->nome]))
			return;
			
		$myOrder = '';
		foreach( $_GET["watbl_oc"][$this->nome] as $idx => $nomeCampo)
			{
			$col = $this->colonne[$nomeCampo];
			if (!$col || $this->dammiIndiceOrdinamentoColonna($col) !== $idx)
				continue;

			$operatore = $this->dammiModoOrdinamentoColonna($idx);
			$myOrder .= ($myOrder ? ', ' : ' ORDER BY ') . 
								"$nomeCampo $operatore";
			}
			
		if ($myOrder)
			$this->OrderClause = $myOrder;
			
		}
	
	//*****************************************************************************
	// restituisce un (uno solo!) eventuale sort order impostato con l'ordinamento
	// rapido, ossia con il click su una intestazione di colonna laddove abilitato
	// OCCHIO: viene in coda ad eventuali ordinamenti fatti col modulo
	// ordinamento/filtro
	/**
	* @ignore
	* @access protected
	*/	
	protected function applicaOrdinamentoRapido()
		{
		$colonnaOrdinamentoRichiesta =  $_GET["watbl_or"][$this->nome];
		if (!$this->colonne[$colonnaOrdinamentoRichiesta] || ! $this->colonne[$colonnaOrdinamentoRichiesta]->ordina)
			return;
			
		$this->OrderClause = " ORDER BY $colonnaOrdinamentoRichiesta" .
								(strtolower( $_GET["watbl_orm"][$this->nome]) == "desc" ? " DESC" : '');
			
		}

	//*****************************************************************************
	// restituisce la stringa sql da utilizzare con condizione di limit per la 
	// visualizzazione tabellare
	/**
	* @ignore
	* @access protected
	*/	
	protected function GetLimitClause()
		{
		if ($this->listaMaxRec == 0)
			return array();
		return array($this->listaMaxRec, 
						 $_GET["watbl_pg"][$this->nome] * $this->listaMaxRec);
		
		}
		
	//*****************************************************************************
	// costruisce la barra di navigazione dei record delle pagine delle viste
	/**
	* @ignore
	* @access protected
	*/	
	protected function creaBarraNavigazione()
		{
	
		
		$currPage =  $_GET["watbl_pg"][$this->nome]?  $_GET["watbl_pg"][$this->nome] : 0; 		// in  $_GET["watbl_pg'] c'e' il nr di pagina
																	// corrente della navigazione dei record
		$FirstRec = ($this->_recordCount  ? ($currPage * $this->listaMaxRec) + 1 : 0);
		if ($this->listaMaxRec == 0)
			{
			$LastRec = $this->_recordCount;
			$totalePagine = 1;
			}
		else
			{
			$LastRec = 	(($currPage * $this->listaMaxRec) + $this->listaMaxRec) > $this->_recordCount ?
						$this->_recordCount :
						($currPage * $this->listaMaxRec) + $this->listaMaxRec;
			$totalePagine = intval($this->_recordCount / $this->listaMaxRec) + 
							($this->_recordCount % $this->listaMaxRec > 0 ? 1 : 0);
			}
						
		$this->buffer .= "\t<watabella_barra_navigazione>\n";
		$this->buffer .= "\t\t<nr_pagina_corrente>$currPage</nr_pagina_corrente>\n";
		$this->buffer .= "\t\t<totale_pagine>$totalePagine</totale_pagine>\n";
		$this->buffer .= "\t\t<primo_record>$FirstRec</primo_record>\n";
		$this->buffer .= "\t\t<ultimo_record>$LastRec</ultimo_record>\n";
		$this->buffer .= "\t\t<totale_record>$this->_recordCount</totale_record>\n";
		
		$this->buffer .= "\t</watabella_barra_navigazione>\n";
		$this->buffer .= "\n\n";
		}
		
	//*****************************************************************************
	// restituisce il textbox per la ricerca rapida
	/**
	* @ignore
	* @access protected
	*/	
	protected function GetControlloRicercaRapida()
		{
		$this->buffer .= "<watabella_ricerca_rapida>\n" .
							"<valore>" . $_GET["watbl_rr"][$this->nome] . "</valore>\n" . 
							"</watabella_ricerca_rapida>\n";
		}
		
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	protected function GetRowID()
		{
		// l'dentificativo della riga e' sempre il campo della prima colonna
		return $this->daSql() ?
				$this->record->valore($this->_colsByCntr[0]->nome) :
				$this->record[$this->_colsByCntr[0]->nome];
		}
		
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	protected function daSql()
		{
		return isset($this->_sql);
		}
		
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	protected function creaIntestazioniTabella()
		{
		$this->buffer .= "\t<watabella_intestazioni>\n";
		foreach ($this->colonne as $col)
			{
			$col->tipoCampo = !$col->tipoCampo && $this->daSql() && $this->righeDB ? 
									$this->righeDB->tipoCampo($col->nome) :
									$col->tipoCampo;
			$col->lunghezzaMaxCampo = !$col->lunghezzaMaxCampo && $this->daSql() && $this->righeDB ? 
									$this->righeDB->lunghezzaMaxCampo($col->nome) :
									$col->lunghezzaMaxCampo;
			
			$this->buffer .= "\t\t<intestazione>\n" .
								"\t\t\t<nome>$col->nome</nome>\n" .
								"\t\t\t<etichetta>$col->etichetta</etichetta>\n" .
								"\t\t\t<allineamento>$col->allineamento</allineamento>\n" .
								"\t\t\t<mostra>$col->mostra</mostra>\n" .
								"\t\t\t<ordina>$col->ordina</ordina>\n" .
								"\t\t\t<filtra>$col->filtra</filtra>\n" .
								"\t\t\t<tipo_campo>$col->tipoCampo</tipo_campo>\n" .
								"\t\t\t<formattazione>$col->formattazione</formattazione>\n" .
								"\t\t\t<max_caratteri>$col->maxCaratteri</max_caratteri>\n" .
								"\t\t\t<converti_html>" . ($col->convertiHTML ? 1 : 0) ."</converti_html>\n" .
								"\t\t\t<link>" . ($col->link ? 1 : 0) . "</link>\n" .
								"\t\t\t<ordinamento_rapido>" . $this->dammiDatiOrdinamentoRapido($col) ."</ordinamento_rapido>\n";
			
			if ($col->inputTipo)
				{
				$this->buffer .= "\t\t\t<input>\n" .
									"\t\t\t\t<tipo>$col->inputTipo</tipo>\n" .
									"\t\t\t\t<obbligatorio>" . ($col->inputObbligatorio ? 1 : 0) ."</obbligatorio>\n" .
									"\t\t\t\t<lunghezza_max_campo>$col->lunghezzaMaxCampo</lunghezza_max_campo>\n";
				if (is_array($col->inputOpzioni))
					{
					$this->buffer .= "\t\t\t\t<opzioni>\n";
					foreach ($col->inputOpzioni as $val => $text)
						$this->buffer .= "\t\t\t\t\t<opzione val='$val'>" . htmlspecialchars($text). "</opzione>\n";
					$this->buffer .= "\t\t\t\t</opzioni>\n";
					}
				$this->buffer .= "\t\t\t</input>\n";
				}
								
			$this->buffer .= "\t\t</intestazione>\n";
			}
		
		$this->buffer .= "\t</watabella_intestazioni>\n";
		$this->buffer .= "\n\n";
		}
		
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	protected function creaRigheTabella()
		{
		$this->buffer .= "\t<watabella_righe>\n";
		$iteraSu = $this->daSql() ? $this->righeDB->righe : $this->matrice;
		foreach ($iteraSu as $this->record)
			{
			if (!empty($this->funzionePrimaDiRiga))
				{
				$p = $this->funzionePrimaDiRiga;
				if ($p($this) === false)
					continue;
				}
				
			$this->buffer .= "\t\t<riga id='" . $this->GetRowID() . "'>\n";
			$this->creaQualificatoriRiga();
			$this->creaCelleRiga();
			$this->buffer .= "\t\t</riga>\n";
				
			if (!empty($this->funzioneDopoDiRiga))
				{
				$p = $this->funzioneDopoDiRiga;
				$p($this);
				}
			}
			
		$this->buffer .= "\t</watabella_righe>\n";
		$this->buffer .= "\n\n";
		}
		
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	protected function creaCelleRiga()
		{
		foreach ($this->colonne as $col)
			{
			$this->buffer .= "\t\t\t<cella>\n";
			if (!empty($col->funzioneCalcolo))
				// e' una colonna che viene valorizzata dal chiamante;
				// gli passiamo la tabella e ilrecord corrente, sa lui cosa farsene
				$this->buffer .= "\t\t\t\t<valore>" . call_user_func($col->funzioneCalcolo, $this) . "</valore>\n";
			else
				$this->buffer .= "\t\t\t\t<valore>" . $this->Format($col) . "</valore>\n";
				
			if ($col->totalizza)
				{
				if (empty($col->totalizzatore))
					$col->totalizzatore = 0;
				$col->totalizzatore += ($this->daSql() ?
											$this->record->valore($col->nome) :
											$this->record[$col->nome]);
				$this->_hasTotals = true;
				}
			$this->buffer .= "\t\t\t</cella>\n";
			}
		}
		
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	protected function creaRigaTotali()
		{
		if (!$this->_hasTotals)
			{
			// se la tabella è vuota, non sappiamo se vuole la riga dei totali o meno
			foreach ($this->colonne as $col)
				{
				if ($col->totalizza)
					{
					$this->_hasTotals = true;
					break;
					}
				}
			
			}
			
		if (!$this->_hasTotals)
			return;
			
		$this->buffer .= "\t<watabella_riga_totali>\n";
		
		foreach ($this->colonne as $col)
			{
			$this->buffer .= "\t\t<cella>\n";
			if ($col->totalizza)
				$this->buffer .= "\t\t\t<valore>" . $this->FormatVal($col, $col->totalizzatore) . "</valore>\n";
			else
					$this->buffer .= "\t\t\t<valore></valore>\n";
			$this->buffer .= "\t\t</cella>\n";
			}
		$this->buffer .= "\t</watabella_riga_totali>\n";
		$this->buffer .= "\n\n";
			
		}
		
	//***************************************************************************
	/**
	 * DEPRECATED!!! rimane solamente per compatibilita' siaper Lisa
	 * serve per l'input
	 * @ignore
	*/
	function dammiValoreInput($nomeCampo, $id, $metodo = 'POST')
		{
		if (strtoupper($metodo == 'GET'))
			$req = &$_GET;
		else 
			$req = &$_POST;
			
		return $this->convertiValoreInput($nomeCampo, $req[$nomeCampo][$id]);
		}
		
	//***************************************************************************
	/**
	 * legge eventuali valori in input quando la tabella e' usata come modulo
	*/
	function leggiValoriIngresso()
		{
		if ($_POST["watabella_nome_tabella"] != $this->nome)
			// il post (se c'e') non e' relativo a questa istanza della tabella
			return;
		
		// eventuali parametri di una chiamata RPC applicativa non ha senso che 
		// vengano macinati dall'XSLT; se e' una chiamata RPC applicativa per 
		// questa istanza della tabella, usiamo i parametri prelevandoli
		// direttamente dal POST e usciamo
		if ($_POST["watabella_rpc"] &&
			$_POST["watabella_funzione_rpc"] != "watabella_rpc_aggiornamento_immediato")
			// e' una chiamata rpc applicativa; eseguiamo la funzione rpc ed usciamo.
			$this->eseguiRPCApplicativa();
		
		
		// non e' una chiamata RPC; andiamo a vedere se è una richiesta di input
		// x questa istanza della tabella
		$this->costruisciXMLInput();
//		header("Content-Type: text/xml; charset=utf-8");			
//		exit($this->buffer);
		$passo = $this->trasforma();
		$passo = simplexml_load_string($passo);
		$passo = $this->objectToArray($passo);
		unset($passo["watabella_nome_tabella"]);
		
		// ci salviamo in una variabile se e' stato richiesto l'aggiornamento 
		// immediato o meno
		$aggiornameno_immediato = $_POST["watabella_funzione_rpc"] == "watabella_rpc_aggiornamento_immediato";
		
		// se viene richiesto l'inserimento al volo (evidentemente da 
		// quick-edit), valorizziamo l'array di input con un semplice true: sara'
		// il segnale per la creazione del record, anzichè ciclare sulle righe
		// per la modifica/cancellazione
		if ($passo["watabella_inserisci"])
			{
			$this->input = true;
			if ($aggiornameno_immediato)
				// non vedo come possa essere possibile diversamente, 
				// ma si sa mai...
				$this->aggiornamentoImmediato();
			return;
			}
		
		if (!$passo["watabella_righe"]) return;
		
		// se e' stata modificata piu' di una riga l'xml produce un array di array di array
		// se invece e' stata modificata una riga sola, lxml produce un array di array
		if ($passo["watabella_righe"]["riga"][0])
			$passo = $passo["watabella_righe"]["riga"];
		else
			{
			$passo[0] = $passo["watabella_righe"]["riga"];
			unset($passo["watabella_righe"]);
			}
			
		// dall'xml le righe ci arrivano racchiuse in un elemento con nome
		// riga e attributo [id]; l'attributo diventa la chiave del nostro 
		// array
		foreach ($passo as $elem)
			{
			$id = $elem["@attributes"]["id"];
			unset($elem["@attributes"]);
			$passo2[$id] = $elem;
			}
		$passo = &$passo2;

		
		// converte tutti i valori ricevuti in input nel formato utilizzabile
		// dall'applicazione e li mette nella proprieta' $input
		foreach ($passo as $id_riga => $valori)
			{
			foreach ($valori as $nomeCampo => $valore)
				{
				if ($nomeCampo == "watabella_elimina")
					$this->input[$id_riga][$nomeCampo] = true;
				else
					{
					$funzione_conversione = "convertiInput_" . $this->colonne[$nomeCampo]->inputTipo;
					if (method_exists($this, $funzione_conversione))
						$this->input[$id_riga][$nomeCampo] = call_user_func (array($this, $funzione_conversione), $valore);
					else
						$this->input[$id_riga][$nomeCampo] = $this->convertiInput_testo($valore);
					}
				}
			}
			
		// se e' stato richiesto l'aggiornamento immediato via rpc, cerchiamo di 
		// consolidare quanto richiesto e torniamo la palla al client
		if ($aggiornameno_immediato)
			$this->aggiornamentoImmediato ();
			
		}
		
	//***************************************************************************
	/**
	 * -
	 * 
	 * metodo richiamato in POST/RPC: consolida immediatamente l'edit effettuato
	 * su un controllo di input
	 * 
	* @access protected
	 * @ignore
	*/
	protected function aggiornamentoImmediato()
		{
		$esito = $this->salva (true) ? WATBL_RPC_OK : WATBL_RPC_KO;
		
		if ($esito == WATBL_RPC_OK && $this->input === true)
			// significa che hanno richiesto un inserimento ed e' andato tutto ok;
			// proviamo a restituire anche l'ultimo id inserito...
			$datiRisposta = $this->righeDB->connessioneDB->ultimoIdInserito ();
		elseif($esito == WATBL_RPC_KO)
			$messaggio = "Errore su db: " . $this->righeDB->nrErrore() . " - " . $this->righeDB->messaggioErrore();
		
		$this->rispostaRPC ($esito, $messaggio, $datiRisposta);
		}
		
	//***************************************************************************
	/**
	 * -
	 * 
	 * restituisce true se il recordset della tabella e' da aggiornare a fronte
	 * di eventuale input
	*/
	function daAggiornare()
		{
		return $this->input ? true : false;
		}
		
	//***************************************************************************
	/**
	 * salva nel recordset inuovi valori derivanti da eventuale input
	 * 
	* I campi che che vengono valorizzati
	 * sono unicamente quelli che appartengono alla tabella a cui appartiene
	 * il campo chiave primaria
	 * 
	 * @param boolean $consolida : se true effettua anche il consolidamento
	 * sulla base dati; altrimenti si limita a valorizzare i waRecord
	 * 
	 * @return boolean : false = errore; true = ok
	*/
	function salva($consolida = false)
		{
		// se non e' gia' stato creato il recordset della tabella, ne creiamo 
		// comunque uno vuoto; questo ci serve per avere informazioni sulle
		// colonne e quindi sulla chiave primaria
		if (!$this->righeDB)
			{
			if (!$this->daSql())
				return false;
			$this->connessioneDB = $this->connessioneDB ? $this->connessioneDB :
								wadb_dammiConnessione($this->_fileConfigurazioneDB);
			if ($this->connessioneDB->nrErrore())
				return false;
			$this->righeDB = new waRigheDB($this->connessioneDB);
			$this->righeDB->caricaDaSql($this->_sql, 0);
			}
		
		// per regola la prima colonna della waTabella e' sempre LA chiave 
		// primaria	(la doppia indirezione e' per avere il case corretto del
		// nome campo)
		$pk = $this->righeDB->nomeCampo($this->righeDB->indiceCampo($this->_colsByCntr[0]->nome));
		$tabellaPK = $this->righeDB->colonne[strtoupper($pk)]["tabella"];
		
		// creiamo la stringa sql per creare il recordset
		$sql = "SELECT * FROM $tabellaPK";
		
		// se la proprieta' input e' un semplice boolean che vale true, anziche'
		// un array, allora significa che e' stato richiesta la creazione di un
		// record da quick-edit: proviamo a crearlo e torniamo.
		// Naturalmente sulla query data deve essere possibile creare un record 
		// vuoto; non vengono tornati eventuali valori di default creati dal
		// db-engine
		if (is_bool($this->input) && $this->input)
			{
			$rs = new waRigheDB($this->connessioneDB);
			$rs->caricaDaSql($sql, 0);
			if ($rs->nrErrore())
				return false;
			$riga = $rs->aggiungi();
			// questo serve per fregare righedb, altrimenti se non trova nessun 
			// dato cambiato non effettua neppure l'insert...
			$riga->inserisciValore($pk, '');
			return $consolida ? $rs->salva() : true;
			}
		
		// ciclo delle modifiche/cancellazioni; cerchiamo di racchiudere tutto 
		// in una transazione, anche se non e' detto che la tabella sul db le 
		// supporti
		$this->connessioneDB->iniziaTransazione();
		foreach ($this->input as $id_riga => $valori)
			{
			$rs = new waRigheDB($this->connessioneDB);
			// leggiamo il record relativo alla sola chiave primaria
			$rs->caricaDaSql("$sql WHERE $pk=" . $this->connessioneDB->stringaSql($id_riga), 1);
			if (!$rs->righe)
				return false;
			if ($valori["watabella_elimina"])
				$rs->righe[0]->elimina();
			else
				{
				foreach($valori as $nome_campo => $valore)
					// non facciamo controllisull'esistenza del campo, perche'
					// se il nome campo non esiste nel recordset di destinazione
					// la scrittura non ha nessun effetto
					{
					$rs->righe[0]->inserisciValore($nome_campo, $valore);
					}
				}
			if ($consolida && !$rs->salva())
				return false;
			}
		$this->connessioneDB->confermaTransazione();
			
		return true;
		}
		
	//***************************************************************************
	/**
	* converte un valore di tipo testo cosi' come arrivato dal post nel formato 
	 * utilizzabile dall'applicazione
	*
	* @ignore
	*/
	protected function convertiInput_testo($valore) 
		{
		return $valore;
		}
		
	//***************************************************************************
	/**
	* converte un valore di tipo data cosi' come arrivato dal post nel formato 
	 * utilizzabile dall'applicazione
	*
	* @ignore
	*/
	protected function convertiInput_data($valore) 
		{
		if (@preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $valore, $parts)) 
			return mktime(0,0,0, $parts[2], $parts[3], $parts[1]);

		return false;
		}
		
	//***************************************************************************
	/**
	* converte un valore di tipo dataora cosi' come arrivato dal post nel formato 
	 * utilizzabile dall'applicazione
	*
	* @ignore
	*/
	protected function convertiInput_dataora($valore) 
		{
		if (@preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $valore, $parts)) 
			return mktime($parts[4], $parts[5], $parts[6], $parts[2], $parts[3], $parts[1]);

		return false;
		}
		
	//***************************************************************************
	/**
	* converte un valore di tipo ora cosi' come arrivato dal post nel formato 
	 * utilizzabile dall'applicazione
	*
	* @ignore
	*/
	protected function convertiInput_ora($valore) 
		{
		if (@preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $valore, $parts)) 
			return mktime($parts[1], $parts[2], $parts[3], 1, 1, 1980);

		return false;
		}
		
	//***************************************************************************
	/**
	* converte un valore di tipo valuta cosi' come arrivato dal post nel formato 
	 * utilizzabile dall'applicazione
	*
	* @ignore
	*/
	protected function convertiInput_valuta($valore) 
		{
		if ($valore === '')
			return null;
		return floatval($valore);
		}
		
	//***************************************************************************
	/**
	* converte un oggetto in un array 8evidentemente solo le proprietà...)
	*
	* @ignore
	*/
	protected function objectToArray($d) 
		{
		if (is_object($d)) 
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
 
		if (is_array($d)) 			
			{
			if ($d)
				return array_map(array($this, "objectToArray"), $d);
			else
				return '';
			}
			
		// Return array
		return $d;
		}
	
	//***************************************************************************
	/**
	* xmlValoriInput
	* @ignore
	*
	*/
	protected function dammiXmlValoriInput($array, $nomeArray = '')
		{
		if ($nomeArray)
			$toret = "\t<$nomeArray>\n";
		foreach($array as $k => $v)
			{
			$toret .= "\t\t<item id='$k'>";
			if (is_array($v))
				$toret .= $this->dammiXmlValoriInput($v);
			else
				$toret .= htmlspecialchars($v);
			$toret .= "</item>\n";
			}
		if ($nomeArray)
			$toret .= "\t</$nomeArray>\n";
		return $toret;
			
		}
		
	//***************************************************************************
	/**
	* costruisciXMLInput
	* @ignore
	*
	*/
	protected function costruisciXMLInput()
		{
	 	$this->buffer = "<?xml version='1.0' encoding='UTF-8'?>\n" .
					"<watabella.input>\n" .
					"\t<nome>$this->nome</nome>\n" .
					"\t<watabella_path>$this->_currPath</watabella_path>\n" .
					$this->dammiXmlValoriInput($_POST, "post") .
					$this->dammiXmlValoriInput($_FILES, "files");

		$this->creaIntestazioniTabella();
		$this->buffer .= "</watabella.input>\n\n";			
		}
		
	//***************************************************************************
	/**
	* esportaCSV
	* @ignore
	*
	*/
	protected function esportaCSV()
		{
		$buffer = $comma = '';
		foreach ($this->colonne as $col)
			{
			if ($col->mostra)
				{
				$buffer .= $comma . '"' . $col->etichetta . '"';
				$comma = ',';
				}
			}
		$buffer .= "\n";
							
		// si legge da db a blocchi di 100 righe, in modo da non sforare il 
		// memory limit
		while ($this->leggiBloccoSuccessivoEsportazione())
			{
			foreach ($this->righeDB->righe as $this->record)
				{
				$comma = '';
				foreach ($this->colonne as $col)
					{
					if ($col->mostra)
						{
						$buffer .= $comma;
						$buffer .= '"';
						if (!empty($col->funzioneCalcolo))
							$tocat = call_user_func($col->funzioneCalcolo, $this);
						elseif ($this->record->righeDB->tipoCampo($col->nome) == WADB_DATA &&
							$this->record->valore($col->nome) != 0)
							$tocat = date("Y-m-d", $this->record->valore($col->nome));
						elseif ($this->record->righeDB->tipoCampo($col->nome) == WADB_DATAORA &&
							$this->record->valore($col->nome) != 0)
							$tocat = date("Y-m-d H:i:s", $this->record->valore($col->nome));
						else
							$tocat = $this->record->valore($col->nome);
						$buffer .= str_replace('"', '""', $tocat) . '"';
						$comma = ',';
						}

					}
				$buffer .= "\n";
				}
			}
			
		$nomefile = $this->soloLettereNumeri($this->titolo) . date("_YmdHis") . ".csv";
		header("Pragma: ");
		header("Expires: Fri, 15 Aug 1980 18:15:00 GMT");
		header("Last-Modified: ".date("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0, false");
		header('Content-Length: '. strlen($buffer));
		header("Content-Disposition: attachment; filename=\"$nomefile\";" );
		header("Content-Type: application/force-download");
		header("Content-Transfer-Encoding: binary");
			
		exit($buffer);		
		
		}
		
	//***************************************************************************
	/**
	* esportaXLS 
	* @ignore
	*
	*/
	protected function esportaXLS()
		{
		// a seconda delle librerie che messe a disposizione dal sistema
		// chiamiamo la migliore funzione disponibile
		if (($found = @include ('PHPExcel/Classes/PHPExcel.php')))
			{
			$this->esportaXLS_5();
			}
		elseif (($found = @include ('ExcelWriterXML/ExcelWriterXML.php')))
			{
			$this->esportaXLS_XML();
			}
		elseif (($found = @include ('Spreadsheet/Excel/Writer.php')))
			{
			// in realta' e' una 5 taroccata che da dei problemi...
			$this->esportaXLS_8();
			}
			
		}
		
	
	//***************************************************************************
	/**
	* esportaXLS_11 (PHPExcel)
	* @ignore
	*
	*/
	protected function esportaXLS_5()
		{
		date_default_timezone_set('UTC');
		
		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		
		$colIdx = 0;
		foreach ($this->colonne as $col)
			{
			if ($col->mostra)
				{
				$sheet->setCellValueByColumnAndRow($colIdx, 1, $col->etichetta);
				$sheet->getStyleByColumnAndRow($colIdx, 1)->getFont()->setBold(true);
				$colIdx++;
				}
			}

		$rowIdx = 2;
		// si legge da db a blocchi di 100 righe, in modo da non sforare il 
		// memory limit
		while ($this->leggiBloccoSuccessivoEsportazione())
			{
			foreach ($this->righeDB->righe as $this->record)
				{
				$colIdx = 0;
				foreach ($this->colonne as $col)
					{
					if ($col->mostra)
						{
						if (!empty($col->funzioneCalcolo))
							{
							$sheet->setCellValueExplicitByColumnAndRow($colIdx, $rowIdx, call_user_func($col->funzioneCalcolo, $this));
							}
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_DATA && $this->record->valore($col->nome))
							{
							$sheet->setCellValueByColumnAndRow($colIdx, $rowIdx,  PHPExcel_Shared_Date::PHPToExcel($this->record->valore($col->nome)));
							$sheet->getStyleByColumnAndRow($colIdx, $rowIdx)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
							}
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_DATAORA && $this->record->valore($col->nome))
							{
							$sheet->setCellValueByColumnAndRow($colIdx, $rowIdx,  PHPExcel_Shared_Date::PHPToExcel($this->record->valore($col->nome)));
							$sheet->getStyleByColumnAndRow($colIdx, $rowIdx)->getNumberFormat()->setFormatCode('dd/mm/yyyy h:mm');
							}
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_INTERO)
							{
							$sheet->setCellValueByColumnAndRow($colIdx, $rowIdx, $this->record->valore($col->nome));
							}
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_DECIMALE)
							{
							$sheet->setCellValueByColumnAndRow($colIdx, $rowIdx, $this->record->valore($col->nome));
							}
						else
							{
							$sheet->setCellValueExplicitByColumnAndRow($colIdx, $rowIdx, $this->record->valore($col->nome));
							}

						$colIdx++;
						}
					}
				$rowIdx++;
				}
			}

		// Redirect output to a client’s web browser (Excel5)
		$filename = $this->soloLettereNumeri($this->titolo) . date("_YmdHis") . ".xls";
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename=\"$filename\"");
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');

		exit();
		}		
		
	//***************************************************************************
	/**
	* esportaXLS_XML (con ExcelWriterXML)
	* @ignore
	*
	*/
	protected function esportaXLS_XML()
		{
		$xml = new ExcelWriterXML($this->soloLettereNumeri($this->titolo) . date("_YmdHis") . ".xls");

		$sheet = $xml->addSheet('Foglio 1');

		$arraglio = array();
		
		$colIdx = 0;
		$format = $xml->addStyle('StyleHeader');
		$format->fontBold();
		foreach ($this->colonne as $col)
			{
			if ($col->mostra)
				{
				$sheet->writeString(1, $colIdx, $col->etichetta, 'StyleHeader');
				$colIdx++;
				}
			}

		$dateFormat = $xml->addStyle('data');
		$dateFormat->numberFormat("dd/mm/yyyy");
		$dateTimeFormat = $xml->addStyle('data_ora');
		$dateTimeFormat->numberFormat("dd/mm/yyyy\ hh:mm:ss");
		$rowIdx = 2;
		// si legge da db a blocchi di 100 righe, in modo da non sforare il 
		// memory limit
		while ($this->leggiBloccoSuccessivoEsportazione())
			{
			foreach ($this->righeDB->righe as $this->record)
				{
				$colIdx = 0;
				foreach ($this->colonne as $col)
					{
					if ($col->mostra)
						{
						if (!empty($col->funzioneCalcolo))
							{
							$sheet->writeString($rowIdx, $colIdx, call_user_func($col->funzioneCalcolo, $this));
							}
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_DATA)
							{
							$sheet->writeDateTime($rowIdx, $colIdx, $sheet->convertMysqlDate($this->record->valore($col->nome, 1)), "data");
							}
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_DATAORA)
							{
							$sheet->writeDateTime($rowIdx, $colIdx, $sheet->convertMysqlDatetime($this->record->valore($col->nome, 1)), "data_ora");
							}
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_INTERO)
							{
							$sheet->writeNumber($rowIdx, $colIdx, $this->record->valore($col->nome));
							}
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_DECIMALE)
							{
							$sheet->writeNumber($rowIdx, $colIdx, $this->record->valore($col->nome));
							}
						else
							{
							$sheet->writeString($rowIdx, $colIdx, $this->record->valore($col->nome));
							}

						$colIdx++;
						}
					}
				$rowIdx++;
				}
			}

		$xml->sendHeaders();
		$xml->writeData();			

			
		exit();
		}		
		
	//***************************************************************************
	/**
	* esportaXLS_8 (usa vecchio modulo PEAR, che sembra dare problemi....)
	* @ignore
	*
	*/
	protected function esportaXLS_8()
		{
		
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setVersion(8);
		$worksheet = $workbook->addWorksheet('Foglio 1');
		$align = array(WATBL_ALLINEA_CENTRO => 'center', WATBL_ALLINEA_DX => 'right', WATBL_ALLINEA_SX => 'left');
		
		$colIdx = 0;
		foreach ($this->colonne as $col)
			{
			if ($col->mostra)
				{
				$format = $workbook->addFormat();
				$format->setBold();
				$format->setAlign($align[$col->allineamento]);
				$worksheet->write(0, $colIdx, $col->etichetta, $format);
				$colIdx++;
				}
			}
		
		$rowIdx = 1;
		// si legge da db a blocchi di 100 righe, in modo da non sforare il 
		// memory limit
		while ($this->leggiBloccoSuccessivoEsportazione())
			{
			foreach ($this->righeDB->righe as $this->record)
				{
				$colIdx = 0;
				foreach ($this->colonne as $col)
					{
					if ($col->mostra)
						{
						$format = $workbook->addFormat();
						$format->setAlign($align[$col->allineamento]);
						if (!empty($col->funzioneCalcolo))
							$towrite = call_user_func($col->funzioneCalcolo, $this);
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_DATA)
							{
							$format->setNumFormat('DD/MM/YYYY');
							$towrite = 25569 + $this->record->valore($col->nome) / 86400;	
							$format->setAlign('center');
							}
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_DATAORA)
							{
							$format->setNumFormat('DD/MM/YYYY hh:mm');
							$towrite = (25569 + ($this->record->valore($col->nome)  + 7200) / 86400);	
							$format->setAlign('center');
							}
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_DECIMALE)
							{
							$format->setNumFormat('#,##0.00;-#,##0.00');
							$towrite = $this->record->valore($col->nome);
							$format->setAlign('right');
							}
						elseif ($this->righeDB->tipoCampo($col->nome) == WADB_INTERO)
							{
							$towrite = $this->record->valore($col->nome);
							$format->setAlign('right');
							}
						else
							$towrite = $this->record->valore($col->nome);

						$worksheet->write($rowIdx, $colIdx, $towrite, $format);
						$colIdx++;
						}
					}
				$rowIdx++;
				}
			}
		
		$workbook->send($this->soloLettereNumeri($this->titolo) . date("_YmdHis") . ".xls");
		$workbook->close();
		exit();
		}		
		
	//***************************************************************************
	/**
	* esportaPDF
	* @ignore
	*
	*/
	protected function esportaPDF()
		{
		if (!$this->pdf_file_classe)
			require_once dirname(__FILE__) . '/watbl_pdf.php';
		else
			require_once $this->pdf_file_classe;

		$pdf = new $this->pdf_nome_classe($this);
		$pdf->esporta();
		}
		
	//***************************************************************************
	/**
	 * in fase di esportazione, legge il blocco di record successivo (non li 
	 * leggiamo tutti in una volta altrimenti rischiamo l'overflow)
	 * @ignore
	*/
	function leggiBloccoSuccessivoEsportazione()
		{
		static $indiceBloccoRecord = 0;

		unset($this->righeDB);
		$this->righeDB = new waRigheDB($this->connessioneDB);
		$sql = "$this->SelectClause $this->FromClause $this->WhereClause $this->GroupClause $this->OrderClause";
		$this->righeDB->caricaDaSql($sql, WATBL_NR_REC_BLOCCO_ESPORTAZIONE, $indiceBloccoRecord * WATBL_NR_REC_BLOCCO_ESPORTAZIONE);
		if ($this->righeDB->nrErrore())
			exit("errore db");	// non dovrebbe avere senso....
		$indiceBloccoRecord++;
		
		return $this->righeDB->righe;
		
		}
		
	//***************************************************************************
	/**
	 * @ignore
	*/
	function soloLettereNumeri($inputString)
		{
		$toret = '';
		for ($i = 0; $i < strlen($inputString); $i++)
			{
			$ord = ord(substr($inputString, $i, 1));
			if (($ord >= ord('a') && $ord <= ord('z')) ||
				($ord >= ord('A') && $ord <= ord('Z')) ||
				// vengono accettati nel nome anche meno 
				// e undescore
				($ord >= ord('0') && $ord <= ord('9')) ||
				$ord == ord('-') ||
				$ord == ord('_'))
				$toret .= chr($ord);
			}
		return $toret;
		}
		
	//**********************************************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	protected function eseguiRPCApplicativa()
		{			
		if (function_exists ($_POST['watabella_funzione_rpc']))
			$datiRisposta = call_user_func_array($_POST['watabella_funzione_rpc'], $_POST['watabella_dati_rpc'] ? $_POST['watabella_dati_rpc'] : array());
		elseif(method_exists($this->applicazione, $_POST['watabella_funzione_rpc']))
			$datiRisposta = call_user_func_array(array($this->applicazione, $_POST['watabella_funzione_rpc']), $_POST['watabella_dati_rpc'] ? $_POST['watabella_dati_rpc'] : array());
		else
			$this->rispostaRPC (WATBL_RPC_KO , "Funzione RPC non trovata: $_POST[watabella_funzione_rpc]");
		
		$this->rispostaRPC (WATBL_RPC_OK , "", $datiRisposta);
		}
		
	//***************************************************************************
	/**
	* invia al client una risposta a RPC in formato  XML
	*
	* @ignore
	*/
	function rispostaRPC($esito = WATBL_RPC_OK, $messaggio = '', $datiRisposta = null) 
		{
		
		$retval = "<watabella_esito_rpc>$esito</watabella_esito_rpc>\n" .
					"<watabella_messaggio_rpc>" . htmlspecialchars($messaggio) . "</watabella_messaggio_rpc>\n" .
					"<watabella_dati_rpc>";
		
		if (is_array($datiRisposta))
			{
			$retval .= "\n";
			if ($datiRisposta)
				{
				foreach ($datiRisposta as $id => $item)
					$retval .= "<item id='$id'>" . htmlspecialchars($item) . "</item>\n";
				}
			else
				//l'array e' vuoto
				$retval .= "<item/>";
			}
		elseif ($datiRisposta)
			$retval .= htmlspecialchars($datiRisposta);
		
		$retval .= "</watabella_dati_rpc>\n";
		
		header("Content-Type: text/xml; charset=UTF-8");
		$retval = "<watabella_rpc>\n$retval</watabella_rpc>\n";
		exit("<?xml version='1.0' encoding='UTF-8'?>\n$retval");
		
		}
		
//***************************************************************************
	}	// fine classe waTabella
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_TABELLA'))
?>