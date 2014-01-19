<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_MODULO'))
{
/**
* @ignore
*/
define('_WA_MODULO',1);


//***************************************************************************
//****  classe waModulo **************************************************
//***************************************************************************
/**
* waModulo
*
* classe per la gestione di una form standard, con prelevamento, opzionale,
* dei dati da un record waRecord contenuto in un oggetto waRigheDB. 
* La form e' solo il contenitore che contiene
* i vari controlli definiti dalle specifiche classi.
* 
* Requires:
* - se utilizzata in associazione ai recordset, necessita di wadb
*
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waModulo
	{
	/**
	* foglio XSLT
	*
	* path del file XSLT che viene applicato all'XML generato dalla classe.
	* 
	* Se non valorizzato viene utilizzato il foglio XSLT di default della
	* classe (<b>uis/wa_default</b>)
	* @var string
	*/	
	var $xslt 			= '';
	
	/**
	* pagina di destinazione (action HTML) del modulo
	* @var string
	*/	
	var $paginaDestinazione		= '';
	
	/**
	* nome del modulo
	* @var string
	*/	
	var $nome 			= 'wamodulo';

	/**
	 * titolo del modulo che puo' essere utilizzato come si vuole nell'xslt
	 *
	 * @var string
	 */
	var $titolo;
		
	/**
	* posizionamento orizzontale del modulo nella pagina
	*
	* questa informazione viene utilizzata a piacere nella UI
	* @var mixed
	*/	
	var	$sinistra			= 'center';	
	
	/**
	* posizionamento verticale del modulo nella pagina
	*
	* questa informazione viene utilizzata a piacere nella UI
	* @var integer
	*/	
	var	$alto			= 0;
	
	/**
	* larghezza del modulo
	*
	* questa informazione viene utilizzata a piacere nella UI
	* @var integer
	*/	
	var $larghezza			= 400;
	
	/**
	* altezza del modulo
	 * 
	* questa informazione viene utilizzata a piacere nella UI
	*
	 * per default l'altezza del modulo e' data dalla posizione dell'ultimo
	 * controllo di input
	* @var integer
	*/	
	var $altezza			= 0;

	
	/**
	* recordset
	*
	* eventule oggetto di classe waRigheDB che contiene il record associato 
	* al modulo. Il recordset sara' generato dall'applicazione e passato alla
	* classe come proprieta'; il record preso in considerazione sara' sempre 
	* il primo della lista, qualora il recordset contenesse piu' record
	* @var waRigheDB
	*/	
	var $righeDB;
											
	/**
	* chiave univoca del record
	*
	* il valore di questo campo, se definito e se il record e' valorizzato, viene 
	* inviato nell'xml;  questa informazione viene utilizzata a piacere nella UI
	* @var string
	*/	
	var $nomeCampoRecId = '';

	/**
	* campo identificativo di editing per simulazione lock optimistic
	*
	* il valore di questo campo, se definito e se il record e' valorizzato, viene 
	* inviato nell'xml;  questa informazione viene utilizzata a piacere nella UI

	 * E' pensato per simulare un lock-optimistic: la pagina di
	* destinazione potra' cosi' controllare sul proprio record se
	* il campo e' stato modificato tra il momento in cui il record e'
	* stato letto e il momento in cui questo viene scritto, mediante chiamata al
	 * metoto {@link dammiModId}; ovvio che
	* l'applicazione deve provvedere a modificare questo campo su base dati
	* (tipicamente un timestamp) ogni volta che il record viene 
	* modificato
	* @var string
	*/	
	var $nomeCampoModId = '';

	/**
	* oggetto di classe non conosciuta da waModulo che contiene l'applicazione
	*
	* in fase di RPC, e' possibile chiamare un metodo di questo oggetto anziche'
	* una funzione procedurale
	* @var string
	*/	
	var $applicazione	= null;
	
	/**
	* - 
	*
	* array destinato a contenere i valori di input del modulo, valorizzato
	* a fronte di chiamata a {@link leggiInput}
	* @var array
	*/	
	var $input	= null;
	
	/**
	* insieme di tutti i controlli (di input e etichette) che compongono il
	 * modulo.
	 * 
	 * L'array e' di tipo numerico (non un dizionario, quindi) perche' al 
	 * proprio interno puo' contenere elementi di nome uguale (tipicamente una
	 * etichetta e un controllo di input) e perche' l'ordine di creazione dei
	 * controlli (e quindi l'indice numerico) e' quello che sara' passato 
	 * tramite XML al client per il rendering

	 * @var array 
	*/	
	var $controlli		= array();

	/**
	 * -
	 * 
	 * array associativo (dizionario) contenente l'insieme dei controlli di 
	 * input (non etichette e cornici, quindi) presenti nel modulo; ogni 
	 * elemento e' identificato dal nome del controllo di input
	 * 
	 * @var array
	 */
	var $controlliInput	= array();
	
	/**
	 * -
	 * 
	 * array associativo (dizionario) contenente l'insieme delle etichette (e cornici)
	 * presenti nel modulo; ogni elemento e' identificato dal nome dell'
	 * etichetta/cornice
	 * 
	 * @var array
	 */
	var $etichette = array();
	
	/**
	 * -
	 * 
	 * array a chiave numerica contenente l'insieme dei controlli di 
	 * input presenti nel modulo, in ordine di inserimento
	 * 
	 * @var array
	 */
	var $controlliInputNum	= array();
	
	/**
	 * -
	 * 
	 * array a chiave numerica contenente l'insieme delle etichette (e cornici)
	 * presenti nel modulo, in ordine di inserimento
	 * 
	 * @var array
	 */
	var $etichetteNum = array();
	
	/**
	 * -
	 * 
	 * oggetto di classe waRecord che contiene la prima riga dell'array
	 * waRigheDB::righe ; si presti attenzione al fatto che la proprietà risulterà
	 * valorizzata solo a fronte di chiamata a {@link leggiValoriIngresso}
	* @var waRecord
	*/	
	var $record;
	
	/**
	* buffer utilizzato per contenere l'output del modulo
	*
	* @ignore
	*/	
	var	$buffer; 
	
	/**
	* in fase di input viene valorizzato con quanto trovato nel campo {@link $nomeCampoModId}
	* al momento della creazione dell'output 
	*
	* @ignore
	*/	
	var	$valoreModId; 
	
	/**
	* identifica il tipo di operazione (aggiorna/elimina/abbandona) che viene 
	 * richiesta dalla UI; 
	*
	* @ignore
	*/	
	protected	$operazioneInputRichiesta; 
	
	/**
	 * -
	 * 
	* in fase di costruzione di un modulo, qualora vengano usati i metodi shortcut
	* forniti da questa classe per creare i controlli e le etichette,  questo 
	* valore viene utilizzato come default del posizionamento a sinistra di 
	* tutte le etichette del modulo
	*
	*/	
	var	$sinistraEtichette = 10; 
	
	/**
	 * -
	 * 
	* in fase di costruzione di un modulo, qualora vengano usati i metodi shortcut
	* forniti da questa classe per creare i controlli e le etichette,  questo 
	* valore viene utilizzato come default del posizionamento a sinistra di 
	* tutti i controlli del modulo
	*
	*/	
	var	$sinistraControlli = 220; 
	
	/**
	 * -
	 * 
	* in fase di costruzione di un modulo, qualora vengano usati i metodi shortcut
	* forniti da questa classe per creare i controlli e le etichette,  questo 
	* valore viene utilizzato come default dell'altezza di una linea utilizzata
	 * come sede del controllo (da non confondere con l'altezza del controllo)
	*
	*/	
	var	$altezzaLineaControlli = 20; 
	
	/**
	 * -
	 * 
	* in fase di costruzione di un modulo, qualora vengano usati i metodi shortcut
	* forniti da questa classe per creare i controlli e le etichette,  questo 
	* valore viene utilizzato come default dello spazio verticale tra i 
	 * controlli del modulo
	*
	*/	
	var	$interlineaControlli = 6; 
	
	//***************************************************************************
	//***************************************************************************
	//***************************************************************************
	/**
	* costruttore
	*
	* inizializza l modulo.
	* @param string $paginaDestinazione pagina di destinazione del modulo
	 * (action HTML); per
	* default e' la stessa pagina in cui il modulo viene costruito (compresi i
	* parametri di query-string)
	* @param object $applicazione oggetto di clase non predeterminata (ma
	* verosimilmente derivata dalla classe waApplicazione) all'interno del quale cercare i
	* metodi invocati con modalita' RPC qualora il metodo non fosse disponbile
	* proceduralmente. Con questo valore viene valorizzata la proprietà 
	 * {@link $applicazione applicazione)
	* @return void 
	*/
	function __construct($paginaDestinazione = null, $applicazione = null)
		{
		$this->applicazione = $applicazione;
		$this->paginaDestinazione = empty($paginaDestinazione) ? 
									$_SERVER['REQUEST_URI'] :
									$paginaDestinazione;
		$this->xslt = dirname(__FILE__) . "/uis/wa_default/xslt/wamodulo.xsl";
		}

	//***************************************************************************
	/**
	* mostra
	*
	* effettua la
	 * trasformazione mediante il foglio di stile {@link xslt} e ne produce 
	 * l'output. Deve essere ovviamente invocato al termine della costruzione del modulo.
	 * 
	* @param boolean $bufferizza se false, allora viene immediatamente effettuato
	* l'output del modulo; altrimenti la funzione ritorna il buffer di output 
	* del modulo stesso
	* @return void|string
	*/
	function mostra($bufferizza = false)
		{
		$this->costruisciXML();

		$html = $this->trasforma();
		
		// in alcuni casi e' comdo usare il metodo di output xhtml; questo pero'
		// crea una riga !DOCTYPE che non ha senso per il solo modulo (che e' solo una parte
		// della pagina e potrebbe anche essecene piu' d'uno); nel caso rimuoviamo
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
	* da usare in fase di debug per mostrare l'output XML anziche' l'HTML generato
	* dall'XSLT.
	 * 
	* @param boolean $bufferizza se false, allora viene immediatamente effettuato
	* l'output del modulo; altrimenti la funzione ritorna il buffer di output 
	* del modulo stesso
	* 
	* @return void|string
	*/
	function mostraXML($bufferizza = false)
		{
		$this->costruisciXML();
		if ($bufferizza)
			return $this->buffer;
			
		header("Content-Type: text/xml; charset=utf-8");			
		echo $this->buffer;
		}
		
	//***************************************************************************
	/**
	* dato l'xml in input trasforma secondo le regole dell'xslt 
	 * 
	* @access protected
	* @ignore
	*/
	function trasforma()
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
		$xml_doc->loadXML($this->buffer);
		
		// transform the XML into HTML using the XSL file
		if (!$out = $xp->transformToXML($xml_doc))
			trigger_error('XSL transformation failed.', E_USER_ERROR);

		// some browsers does not support empty div, iframe, script and textarea tags
		return preg_replace("!<(div|iframe|script|textarea)([^>]*?)/>!s", "<$1$2></$1>", $out);
		}

	//***************************************************************************
	/**
	* costruisce l'XML della tabella
	*
	* @access protected
	* @ignore
	*/
	function costruisciXML()
		{
		// per default l'altezza del modulo e' data dalla posizione dell'ultimo
		// controllo di input
		if (!$this->altezza)
			{
			if ($this->controlliInputNum)
				{
				$ctrlPrecedente = $this->controlliInputNum[count($this->controlliInputNum) - 1];
				$altezzaPrecedente = $ctrlPrecedente->altezza ? $ctrlPrecedente->altezza : $this->altezzaLineaControlli;
				$this->altezza = $ctrlPrecedente->alto + $altezzaPrecedente;
				}
			$this->altezza += $this->interlineaControlli;
			}		
			
	 	$this->buffer = "<?xml version='1.0' encoding='UTF-8'?>\n" .
					"<wamodulo>\n" .
						"\t<versione_librerie_xsl>" . LIBXSLT_VERSION . "</versione_librerie_xsl>\n" .
						"\t<nome>$this->nome</nome>\n" .
						"\t<titolo>$this->titolo</titolo>\n" .
						"\t<uri>" . htmlspecialchars($_SERVER['REQUEST_URI']) . "</uri>\n" .
						"\t<wamodulo_path>" . wamodulo_miaPath() . "</wamodulo_path>\n" .
						"\t<pagina_destinazione>" . htmlspecialchars($this->paginaDestinazione) . "</pagina_destinazione>\n" .
						"\t<sinistra>$this->sinistra</sinistra>\n" .
						"\t<alto>$this->alto</alto>\n" .
						"\t<larghezza>$this->larghezza</larghezza>\n" .
						"\t<altezza>$this->altezza</altezza>\n" .
						"\t<rec_id>\n".
							"\t\t<nome>$this->nomeCampoRecId</nome>\n" .
							"\t\t<valore>" . (!empty($this->record) && !empty($this->nomeCampoRecId) ?
											$this->record->valore($this->nomeCampoRecId) :
											'') . 
							"</valore>\n" .
						"\t</rec_id>\n".
						"\t<mod_id>\n".
							"\t\t<nome>$this->nomeCampoModId</nome>\n" .
							"\t\t<valore>" . (!empty($this->record) && !empty($this->nomeCampoModId) ?
											$this->record->valore($this->nomeCampoModId) :
											'') . 
							"</valore>\n" .
						"\t</mod_id>\n";
						
		// ciclo di visualizzazione dei controlli
		$this->buffer .= "\t<wamodulo_controlli>\n";
		foreach($this->controlli as $ctrl)
			$ctrl->mostra();
		$this->buffer .= "\t</wamodulo_controlli>\n";
		
		$this->buffer .= "</wamodulo>\n\n";
//		$this->buffer = str_replace("&", "&amp;", $this->buffer);
						
		}
		
	//**********************************************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	protected function eseguiRPCApplicativa()
		{			
		if (function_exists ($_POST['wamodulo_funzione_rpc']))
			$datiRisposta = call_user_func_array($_POST['wamodulo_funzione_rpc'], $_POST['wamodulo_dati_rpc'] ? $_POST['wamodulo_dati_rpc'] : array());
		elseif(method_exists($this->applicazione, $_POST['wamodulo_funzione_rpc']))
			$datiRisposta = call_user_func_array(array($this->applicazione, $_POST['wamodulo_funzione_rpc']), $_POST['wamodulo_dati_rpc'] ? $_POST['wamodulo_dati_rpc'] : array());
		else
			$this->rispostaRPC (WAMODULO_RPC_KO , "Funzione RPC non trovata: $_POST[wamodulo_funzione_rpc]");
		
		$this->rispostaRPC (WAMODULO_RPC_OK , "", $datiRisposta);
		}
		
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	protected function rispostaRPC($esito = WATBL_RPC_OK, $messaggio = '', $datiRisposta = null) 
		{
		
		$retval = "<wamodulo_esito_rpc>$esito</wamodulo_esito_rpc>\n" .
					"<wamodulo_messaggio_rpc>" . htmlspecialchars($messaggio) . "</wamodulo_messaggio_rpc>\n" .
					"<wamodulo_dati_rpc>\n";
		
		if (is_array($datiRisposta))
			{
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
		
		$retval .= "</wamodulo_dati_rpc>\n";
		
		header("Content-Type: text/xml; charset=UTF-8");
		$retval = "<wamodulo_rpc>\n$retval</wamodulo_rpc>\n";
		exit("<?xml version='1.0' encoding='UTF-8'?>\n$retval");
		
		}
		
	//***************************************************************************
	/**
	* -
	*
	* restituisce il tipo di operazione richiesta per la pagina (una delle defines
	* {@link WAMODULO_OPE_VIS_DETTAGLIO}, {@link WAMODULO_OPE_INSERIMENTO}, 
	* {@link WAMODULO_OPE_MODIFICA}, {@link WAMODULO_OPE_ELIMINA})
	*	
	 * @ignore 
	* @return integer
	*/
	function dammiOperazione()
		{
		return $_GET[WAMODULO_CHIAVE_OPERAZIONE];
		}

	//***************************************************************************
	/**
	* xmlValoriInput
	* @ignore
	*
	*/
	function dammiXmlValoriInput($array, $nomeArray = '')
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
	function costruisciXMLInput()
		{
	 	$this->buffer = "<?xml version='1.0' encoding='UTF-8'?>\n" .
					"<wamodulo.input>\n" .
					"\t<nome>$this->nome</nome>\n" .
					"\t<wamodulo_path>" . wamodulo_miaPath() . "</wamodulo_path>\n" .
					"\t<mod_id>\n".
						"\t\t<nome>$this->nomeCampoModId</nome>\n" .
					"\t</mod_id>\n" .
					$this->dammiXmlValoriInput($_POST, "post") .
					$this->dammiXmlValoriInput($_FILES, "files");
		
		// ciclo di visualizzazione dei controlli
		$this->buffer .= "\t<wamodulo_controlli.input>\n";
		foreach($this->controlli as $ctrl)
			$ctrl->xmlInput();
		$this->buffer .= "\t</wamodulo_controlli.input>\n";
		
		$this->buffer .= "</wamodulo.input>\n\n";			
		}
		
	//***************************************************************************
	/**
	*
	* @ignore
	*/
	function objectToArray($d) 
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
	* leggiValoriIngresso
	 * 
	 * e' il primo metodo da invocare una volta costruito il modulo;
	 *  invocando questo metodo, infatti,  e' possibile
	 * prendere decisioni sul comportamento che l'applicazione deve assumere, a 
	 * seconda dell'operativita' dell'utente. Tipicamente avremo una situazione 
	 * di questo tipo:
	* <code>
	 * // chiamata al metodo dell'applicazione che costruisce il modulo
	 * // popolandolo con i controlli e associandovi un record waDB
	 * $this->modulo = $this->creaModulo(); 
	 * 
	 * // chiamata al presente metodo che legge eventuali valori in input
	 * $this->modulo->leggiValoriIngresso();
	 * 
	 * // verifica se e' stato richiesto dall'utente l'abort dell'operazione
	 * // di editing del record 
	 * if ($this->modulo->daAnnullare())
	 *	   $this->ritorna();
	 * 
	 * // verifica se e' stato richiesto dall'utente la cancellazione del record
	 * // in editing 
	 * elseif ($this->modulo->daEliminare())
	 *	   $this->eliminaRecord();
	 * 
	 * // verifica se e' stato effettuato il submit da parte dell'utente, che 
	 * // quindi desidera consolidare (inserire/modificare)i dati inputati nel 
	 * // modulo; i dati, gia' normalizzati, potranno essere prelevati dall'
	 * // array associativo {@link input}, oppure tramite la proprieta' 
	 * // {@link waControllo::$valoreInput valoreInput} di ogni controllo, 
	 * // o ancora utilizzare il metodo {@link salva}
	 * // per copiare direttamente i dati del modulo nel record waRecord
	 * elseif ($this->modulo->daAggiornare())
	 *	   $this->aggiornaRecord();
	 * 
	 * // poiche' non e' stata effettuata alcuna azione di input da parte 
	 * // dell'utente, produciamo l'output del modulo
	 * else
	 *	   $this->mostraPagina();
	* </code>

	 * 
	 * @return void
	*
	*/
	function leggiValoriIngresso()
		{
		
		// valorizziamo il record, se esistente
		if ($this->righeDB)
			$this->record = $this->righeDB->righe[0];
		
		// dobbiamo definire il valore iniziale (di default o letto da DB) di
		// ogni controllo di input
		foreach ($this->controlliInput as $nome => $ctrl)
				{
				$ctrl->definisciValoreIniziale();
				}

		// i parametri dati arrivano sempre in post 
		if ($_POST["wamodulo_nome_modulo"] != $this->nome)
		// il post (se c'e') non e' relativo a questa istanza del modulo
			{
			return;
			}

			// eventuali parametri di una chiamata RPC non ha senso che vengano 
		// macinati dall'XSLT; se e' una chiamata RPC per questa istanza del
		// modulo, usiamo i parametri prelevandoli direttamente dal POST e
		// usciamo
		if ($_POST[WAMODULO_CHIAVE_OPERAZIONE] == WAMODULO_OPE_RPC)
			// e' una chiamata rpc del modulo; eseguiamo la funzione rpc ed usciamo.
			$this->eseguiRPCApplicativa();
		
		// non e' una chiamata RPC; andiamo a vedere se è una richiesta di input
		// x questa istanza del modulo
		$this->costruisciXMLInput();
//		header("Content-Type: text/xml; charset=utf-8");			
//		exit($this->buffer);
		$passo = $this->trasforma();
		$passo = simplexml_load_string($passo);
		$passo = $this->objectToArray($passo);
		
		// c'e' stata richiesta una modifica dati (potrebbe anche essere una
		// eliminazione del record; lo vediamo dopo); valorizziamo ogni
		// controllo di input con i dati ricevuti dalla UI
		$this->operazioneInputRichiesta = $passo["wamodulo_operazione"];
		$this->valoreModId = $passo["mod_id"] ? $passo["mod_id"] : $this->valoreModId;
		foreach($this->controlliInput as $ctrl)
			{
			$var = $ctrl->input2valoreInput($passo[$ctrl->nome]);
			if ($var !== null)
				$this->input[$ctrl->nome] = $var;
			}
		}
		
	//***************************************************************************
	/**
	 * metodo di debug: restituisce l'xml generato per ottenere i dati di
	 * input normalizzati tramite l'xslt 
	 * 
	* @param boolean $bufferizza se true, viene tornato il buffer 
	 * contenente l'xml; altrimenti manda in output l'xml
	* 
	* @return void|string
	*/
	function dammiXMLIngresso($bufferizza = false)
		{
		$this->costruisciXMLInput();
		if ($bufferizza)
			return $this->buffer;
			
		header("Content-Type: text/xml; charset=utf-8");			
		echo $this->buffer;
		}

	//***************************************************************************
	/**
	* -
	*
	 * verifica se i controlli definiti obbligatori in fase di costruzione del
	 * modulo sono stati tutti correttamente valorizzati
	 * 
	 * @return boolean
	*/
	function verificaObbligo()
		{
		foreach($this->controlli as $ctrl)
			{
			if (!$ctrl->verificaObbligo())
				return  false;
			}
		return true;
		}

	//***************************************************************************
	/**
	* -
	*
	 * qualora sia stato implementata la gestione del ModId (proprieta' 
	 * {@link nomeCampoModId} e relativa gestione all'interno della UI) 
	 * restituisce il valore che e' stato letto in fase di costruzione del 
	 * modulo all'interno del record. In questo modo e' possibile confrontarlo
	 * col valore presente all'interno del record al momento dell'aggiornamento
	 * e verificare cosi' se si e' verificata una violazione di lock
	 * 
	 * @return mixed (si consiglia di utilizzare un timestamp)
	*/
	function dammiModId()
		{
		return $this->valoreModId;
		}

	//***************************************************************************
	/**
	* -
	*
	* salva automaticamente tutti i valori di input dei controlli nel corrispondente
	* campo del record associato al modulo. I campi che che vengono valorizzati
	 * sono unicamente quelli che appartengono alla tabella a cui appartiene
	 * il primo campo trovato nel record che è chiave primaria
	 * 
	 * @param boolean $consolida : se true effettua anche il consolidamento
	 * sulla base dati; altrimenti si limita a valorizzare il waRecord
	 * 
	 * @return boolean|integer : false = errore; true = ok; se $consolida e
	 * il record e' frutto di un nuovo inserimento, torna l'identificativo
	 * del record inserito
	*/
	function salva($consolida = false)
		{
		// valorizziamo il record, se esistente
		if (!$this->righeDB)
			return false;
			
		 if (!$this->righeDB->righe[0])
		 	{
		 	$this->record = $this->righeDB->aggiungi();
		 	$new = true;
		 	}
		 else
			$this->record = $this->righeDB->righe[0];
			
		$tabellaPK = $this->righeDB->nomeTabella($this->righeDB->chiavePrimaria());
		if (!$tabellaPK)
			return false;
		
		foreach($this->controlli as $ctrl)
			{
			// lavoriamo solo sui campi che appartengono alla tabella della chiave primaria
			if ($this->righeDB->nomeTabella($ctrl->nome) == $tabellaPK)
				$ctrl->input2record();
			}
			
		if ($consolida)
			{
			$this->righeDB->salva();
			if ($this->righeDB->connessioneDB->nrErrore())
				return false;
			if ($new)
				return $this->righeDB->connessioneDB->ultimoIdInserito();
			}
			
		return true;
		}
		
	//***************************************************************************
	/**
	* provede all'eliminazione del record associato al modulo, se presente
	 * 
	 * @param boolean $consolida : se true effettua anche il consolidamento
	 * sulla base dati; altrimenti si limita a valorizzare il waRecord
	 * 
	 * attenzione: il record deve contenere un solo campo chiave primaria, 
	 * altrimenti il metodo non viene eseguito e viene tornato il valore false
	 * 
	 * @return boolean : false = errore ; true = ok ; 
	 * 
	*/
	function elimina($consolida = false)
		{
		// valorizziamo il record, se esistente
		if (!$this->righeDB || !$this->righeDB->righe[0])
			return false;

		// verifichiamo che stiano richiedendo l'eliminazione di un record con
		// una sola chiave primaria
		$nrPK = 0;
		foreach($this->righeDB->colonne as $col)
			{
			// lavoriamo solo sui campi che appartengono alla tabella della chiave primaria
			$nrPK += $col['chiavePrimaria'] ? 1 : 0;
			}
		if ($nrPK != 1)
			return false;
		
		$this->record = $this->righeDB->righe[0];
		$this->record->elimina();
		if ($consolida)
			{
			$this->righeDB->salva();
			if ($this->righeDB->connessioneDB->nrErrore())
				return false;
			}
			
		return true;
		}
		
	//***************************************************************************
	/**
	* verifica se e' stata richiesta da UI l'aggiornameto/inserimento del record
	 * 
	 * @return boolean
	 * 
	*/
	function daAggiornare()
		{
		return $this->operazioneInputRichiesta == WAMODULO_OPE_INSERIMENTO || 
					$this->operazioneInputRichiesta == WAMODULO_OPE_MODIFICA;
		}

	//***************************************************************************
	/**
	* verifica se e' stata richiesta l'eliminazione del record (da UI o da 
	 * programma)
	 * 
	 * @return boolean
	 * 
	*/
	function daEliminare()
		{
		return $this->dammiOperazione() == WAMODULO_OPE_ELIMINA || 
				$this->operazioneInputRichiesta == WAMODULO_OPE_ELIMINA;
		}

	//***************************************************************************
	/**
	* verifica se e' stata richiesto l'annullamento (abort) dell'editing
	 * 
	 * @return boolean
	 * 
	*/
	function daAnnullare()
		{
		return $this->operazioneInputRichiesta == WAMODULO_OPE_ANNULLA;
		}

		
	//*****************************************************************************
	/**
	 * giustifica un controllo di input, ossia fa in modo che la larghezza non
	 * superi quella del modulo
	 *  
	 * @param waControllo $ctrl
	 * @param boolean $check verifica su db, se possibile, la dimensione del campo
	 */
	function giustificaControllo(waControllo $ctrl, $controllaDB = true)
		{
		if ($this->righeDB && $controllaDB)
			$giustifica = $this->righeDB->lunghezzaMaxCampo($ctrl->nome) >= 50;
		else 
			$giustifica = true;
		
		if ($giustifica)
			$ctrl->larghezza = ($this->larghezza - $ctrl->sinistra) - $this->sinistraEtichette * 2;
		}
		
	//**************************************************************************
	/**
	 * aggiunge un controllo e relativa etichetta al modulo
	 * 
	 * @param string	$classe nome della classe del controllo che si intende creare
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento del controllo corrispondente
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 *
	 * @return object	oggetto controllo di input della classe data
	 */
	
	function aggiungiGenerico($classe, $nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{

		if ($altoControllo === false)
			{
			if ($this->controlliInputNum)
				{
				$ctrlPrecedente = $this->controlliInputNum[count($this->controlliInputNum) - 1];
				$altezzaPrecedente = $ctrlPrecedente->altezza ? $ctrlPrecedente->altezza : $this->altezzaLineaControlli;
				$altoControllo = $ctrlPrecedente->alto + $altezzaPrecedente;
				}
			$altoControllo += $this->interlineaControlli;
			}
		$altoEtichetta = $altoEtichetta === false ? $altoControllo : $altoEtichetta;
		$sinistraEtichetta = $sinistraEtichetta === false ? $this->sinistraEtichette : $sinistraEtichetta;
		$sinistraControllo = $sinistraControllo === false ? $this->sinistraControlli : $sinistraControllo;
		
		$lblctrl = new waEtichetta($this , $nome, $etichetta);
		$lblctrl->alto = $altoEtichetta;
		$lblctrl->sinistra = $sinistraEtichetta;
		$lblctrl->solaLettura = $solaLettura;
		$lblctrl->obbligatorio = $obbligatorio;
		
		$ctrl = new $classe($this, $nome);
		$ctrl->alto = $altoControllo;
		$ctrl->sinistra = $sinistraControllo;
		$ctrl->solaLettura = $solaLettura;
		$ctrl->obbligatorio = $obbligatorio;
		
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo AreaTesto, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waTesto
	 */
	function aggiungiAreaTesto($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waAreaTesto', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		$ctrl->altezza = 60;
		$this->giustificaControllo($ctrl, false);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo bottone all'interno del modulo
	 * 
	 * @param string	$nome nome del controllo che si intende creare
	 * @param string	$valore valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param int		$alto posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistra posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waBottone
	 */
	function aggiungiBottone($nome, $valore, 
									$solaLettura = false, 
									$alto = false, 
									$sinistra = false)
		{
		if ($alto === false)
			{
			if ($this->controlliInputNum)
				{
				$ctrlPrecedente = $this->controlliInputNum[count($this->controlliInputNum) - 1];
				$altezzaPrecedente = $ctrlPrecedente->altezza ? $ctrlPrecedente->altezza : $this->altezzaLineaControlli;
				$alto = $ctrlPrecedente->alto + $altezzaPrecedente;
				}
			$alto += $this->interlineaControlli;
			}
		$sinistra = $sinistra === false ? $this->sinistraControlli : $sinistra;
			
		$ctrl = new waBottone($this, $nome, $valore);
		$ctrl->alto = $alto;
		$ctrl->sinistra = $sinistra;
		$ctrl->solaLettura = $solaLettura;
		
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo captcha, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waCaptcha
	 */
	function aggiungiCaptcha($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = true, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waCaptcha', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo caricafile, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waCaricaFile
	 */
	function aggiungiCaricaFile($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waCaricaFile', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo cfpi, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waCFPI
	 */
	function aggiungiCFPI($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waCFPI', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo data, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waData
	 */
	function aggiungiData($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waData', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		$ctrl->annoDecrescente = TRUE;
		$ctrl->annoPartenza = date('Y') - 10;
		$ctrl->annoTermine = date('Y') + 1;
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo dataora, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waDataOra
	 */
	function aggiungiDataOra($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waDataOra', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		$ctrl->annoDecrescente = TRUE;
		$ctrl->annoPartenza = date('Y') - 10;
		$ctrl->annoTermine = date('Y') + 1;
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * -
	 * 
	 * alias di {@link aggiungiNonControllo}
	 */
	function aggiungiElemento($nome, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoControllo = false, 
									$sinistraControllo = false)
		{
		return $this->aggiungiNonControllo($nome, $solaLettura, $obbligatorio, $altoControllo, $sinistraControllo);
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo email, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waEmail
	 */
	function aggiungiEmail($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waEmail', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo intero, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waIntero
	 */
	function aggiungiIntero($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waIntero', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo logico, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waLogico
	 */
	function aggiungiLogico($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waLogico', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo multiselezione, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waMultiSelezione
	 */
	function aggiungiMultiSelezione($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waMultiSelezione', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		$ctrl->altezza = 60;
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo noncontrollo, senza alcuna etichetta, 
	 * all'interno del modulo
	 * 
	 * @param string	$nome nome del controllo 
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di input precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraControllo posizionamento orizzontale del controllo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waNonControllo
	 */
	function aggiungiNonControllo($nome, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoControllo = false, 
									$sinistraControllo = false)
		{
		if ($altoControllo === false)
			{
			if ($this->controlliInputNum)
				{
				$ctrlPrecedente = $this->controlliInputNum[count($this->controlliInputNum) - 1];
				$altezzaPrecedente = $ctrlPrecedente->altezza ? $ctrlPrecedente->altezza : $this->altezzaLineaControlli;
				$altoControllo = $ctrlPrecedente->alto + $altezzaPrecedente;
				}
			$altoControllo += $this->interlineaControlli;
			}
		$sinistraControllo = $sinistraControllo === false ? $this->sinistraControlli : $sinistraControllo;
		
		$ctrl = new waNonControllo($this, $nome);
		$ctrl->alto = $altoControllo;
		$ctrl->sinistra = $sinistraControllo;
		$ctrl->solaLettura = $solaLettura;
		$ctrl->obbligatorio = $obbligatorio;

		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo opzione, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waOpzione
	 */
	function aggiungiOpzione($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waOpzione', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo ora, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return pOra
	 */
	function aggiungiOra($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waOra', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo password, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waPassword
	 */
	function aggiungiPassword($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waPassword', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo selezione, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waSelezione
	 */
	function aggiungiSelezione($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waSelezione', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo testo, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waTesto
	 */
	function aggiungiTesto($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waTesto', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

	//**************************************************************************
	/**
	 * crea un controllo di tipo valuta, con relativa etichetta, all'interno
	 * del modulo
	 * 
	 * @param string	$nome nome del controllo e dell'etichetta che si intende creare
	 * @param string	$etichetta valore dell'etichetta del controllo
	 * @param boolean	$solaLettura true se il controllo va creato in sola lettura
	 * @param boolean	$obbligatorio true se il controllo va creato obbligatorio
	 * @param int		$altoEtichetta posizionamento verticale dell'etichetta; per default e' uguale al posizionamento dell'etichetta precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$altoControllo posizionamento verticale del controllo; per default e' uguale al posizionamento del controllo di oinput precedente a cui viene aggiunto il valore di {@link $interlineaControlli}
	 * @param int		$sinistraEtichetta posizionamento orizzontale dell'etichetta; per default e' uguale a {@link $sinistraEtichette}
	 * @param int		$sinistraControllo posizionamento orizzontale del controolo; per default e' uguale a {@link $sinistraControlli}
	 * 
	 * @return waValuta
	 */
	function aggiungiValuta($nome, $etichetta, 
									$solaLettura = false, 
									$obbligatorio = false, 
									$altoEtichetta = false, 
									$altoControllo = false, 
									$sinistraEtichetta = false, 
									$sinistraControllo = false)
		{
		$ctrl = $this->aggiungiGenerico('waValuta', $nome, $etichetta, 
											$solaLettura, $obbligatorio, 
											$altoEtichetta, $altoControllo, 
											$sinistraEtichetta, $sinistraControllo);
		return $ctrl;
		}

		
	//***********************************************************************
	/**
	* -
	*
	* magic method: restituisce, se esiste, il valore di input (!) del controllo
	 * individuato da $nomeControllo
	 * 
	 * è uno shortcut equivalente a invocare le proprietà
	 * 
	 * - waModulo::input[$nomeControllo] 
	 * - waModulo::controlliInput[$nomeControllo]->valoreInput
	 * 
	 * Attenzione a situazioni particolari in cui un modulo potrebbe contenere 
	 * controlli con lo stesso nome di una delle proprietà della classe (titolo, sinistra, ecc.):
	 * in questo caso ciò che viene restituito è il valore della proprietà,
	 * non del controllo
	 * 
	* @param string $nomeControllo nome del controllo di input contenuto nel modulo
	* @return mixed valore del controllo immesso in input
	*/ 
	public function __get($nomeControllo)
		{
		if ($this->controlliInput[$nomeControllo])
			{
			return $this->controlliInput[$nomeControllo]->valoreInput;
			}
			
		}
		
	}	// fine classe waModulo
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_MODULO'))
?>