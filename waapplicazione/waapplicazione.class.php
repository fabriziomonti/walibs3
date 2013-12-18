<?php
/**
* waApplicazione
*
* classe contenenente i metodi e le proprieta' comuni di una applicazione
* standard
* 
* @package waApplicazione
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_APPLICAZIONE'))
{
/**
* @ignore
*/
define('_WA_APPLICAZIONE',1);



/**
* waApplicazione
*
* classe contenenente i metodi e le proprieta' comuni di una applicazione
* standard
* 
* @package waApplicazione
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waApplicazione
	{
	/**
	* dominio http della'applicazione
	* salvo esigenze particolari la proprietà viene determinata automaticamente
	* @var string
	*/	
	var 	$dominio;
	
	/**
	* directory di lavoro del file system dell'applicazione
	* salvo esigenze particolari la proprietà viene determinata automaticamente
	* @var string
	*/	
	var 	$cwd;				// directory di lavoro del file system
	
	/**
	* directory di lavoro http dell'applicazione; 
	* salvo esigenze particolari la proprietà viene determinata automaticamente
	* @var string
	*/	
	var 	$httpwd = false;			// directory di lavoro http (non cancellare l'inizializzazione a false!!!)
	
	/**
	* directory file-system in cui risiedono le classi di controlli waModulo specifiche dell'applicazione
	* @var string
	*/	
	var 	$directoryClassiEsteseModulo;
	
	/**
	* directory del file-system da usare come temporanea dell'applicazione
	* @var string
	*/	
	var 	$directoryTmp;
	
	/**
	* file xslt dell'applicazione
	*  per default il foglio XSLT utilizzato è (all'interno del package waapplicazione) 
	* uis/wa_default/xslt/waapplicazione.xsl 
	* @var string
	*/	
	var 	$xslt;
	
	/**
	* nome programmatico dell'applicazione 
	* qualcosa di breve (max 10 caratteri) senza spazi ne' cartteri strani; 
	* l'idea e' quella di un nome di variabile da usare internamente
	* @var string
	*/	
	var 	$nome;
	
	/**
	* titolo dell'applicazione, formale da mostrare all'utente e non per uso interno
	* @var string
	*/	
	var 	$titolo;
	
	/**
	* versione dell'applicazione
	* @var string
	*/	
	var 	$versione;
	
	/**
	* data versione applicazione (formato since-the-epoch)
	* @var int
	*/	
	var 	$dataVersione;
	
	/**
	* server per invio della posta elettronica
	* @var string
	*/	
	var 	$serverSmtp;
	
	/**
	* utente smtp per invio della posta elettronica
	* @var string
	*/	
	var 	$utenteSmtp;
	
	/**
	* password smtp per invio della posta elettronica
	* @var string
	*/	
	var 	$passwordSmtp;
	
	/**
	* protocollo di sicurezza su smtp (ssl/tls)
	* se vuoto nessun encryption
	* @var string
	*/	
	var 	$sicurezzaSmtp;

	/**
	* porta smtp; se vuoto si usa il default (25)
	* @var string
	*/	
	var 	$portaSmtp;

	/**
	* indirizzo email per il supporto dell'applicazione
	* @var string
	*/	
	var 	$emailSupporto;
	
	/**
	* indirizzo email per le info sull'applicazione
	* @var string
	*/	
	var 	$emailInfo;
	
	/**
	* indica se l'applicazione usa la sessione di PHP
	* 
	* se true, allora
	* e' la classe waApplicazione stessa che si preoccupa di fare partire
	* la sessione tramite il proprio metodo {@link inizializza}.
	* @var boolean
	*/	
	var 	$usaSessione = true;
	
	/**
	* -
	* per la classe questa proprieta' non ha alcun significato; si limita
	* semplicemente a passare l'informazione all'xslt, il quale, sulla base
	* di questa informazione, puo' decidere in che modalita' far navigare
	* l'utente
	* @var string
	*/	
	var 	$modalitaNavigazione = WAAPPLICAZIONE_NAV_PAGINA;
	
	/**
	* flag che indica se la pagina deve essere chiusa al momento del ritorno
	* oppure deve continuare (verosimilmente con un data-entry)
	* @var string
	* @ignore
	*/	
	var 	$chiudiPagina = '';
	
	/**
	* valori che un pagina figlia ritorna alla mamma affinche' si allinei
	* alle modifiche effettuate (per il momento una stringa base64)
	* 
	* ha senso solo per modalita' navigazione != pagina
	* @ignore
	* @var array
	*/	
	var 	$valoriRitorno = '';
	
	/**
	* array degli oggetti (ma possono anche essere semplici stringhe)
	* che devono essere mandati in output (la sequenza la decide l'xslt)
	* @ignore
	* @var array
	*/	
	var 	$elementi = array();
	
	/**
	* buffer destinato a conterere l'xml
	* @ignore
	* @var string
	*/	
	var 	$buffer = '';

	/**
	* oggetto di classe waDocumentazione che provvede a creare la documentazione delle pagine visitate.
	* 
	* L'istanziazione dell'oggetto è a carico dell'applicazione; waApplicazione
	* si limita, qualora l'oggetto sia stato creato, ad effettuare le chiamate 
	* di richiesta documentazione delle pagine visitate.
	* <br/><br/>
	* Ovviamente l'oggetto deve essere istanziato solo in ambiente di sviluppo,
	* mai in produzione, per ovvi motivi di performance.
	* <br/><br/>
	* Si noti che in fase di creazione dell'istanza occorre passare al
	* costruttore un file di configurazione waDB: questo potrà essere il 
	* medesimo utilizzato dall'applicazione, ma in questo caso le tabelle di 
	* documentazione verranno creato all'interno del DB designato dal file di 
	* configurazione, ossia il medesimo dell'applicazione.
	* 
	* @var waDocumentazione
	*/
	var $waDoc = null;
	
	/**
	* -
	* proprietà che è possibile valorizzare qualora l'applicazione sia suddivisa
	* in più sezioni (es: gestione, valutazione, certificazione, calendari,
	* pagamenti, ecc.). Ogni sottoclasse di waApplicazione può definire tramite
	* questa proprietà il proprio titolo. 
	* <br/><br/>
	* La proprietà viene utilizzata dalla classe waDocumentazione per 
	* suddividere le pagine dell'applicazione all'interno della propria sezione.
	* <br/><br/>
	* Se non valorizzata, la proprietà assumerà il valore della proprietà
	* {@link $titolo}.
	* 
	* @var string
	*/	
	var 	$titoloSezione = '';
	
	/**
	* -
	* proprietà che è possibile valorizzare qualora l'applicazione sia suddivisa
	* in più sezioni (es: gestione, valutazione, certificazione, calendari,
	* pagamenti, ecc.). Ogni sottoclasse di waApplicazione può definire tramite
	* questa proprietà la propria sigla (nome breve). 
	* <br/><br/>
	* La proprietà viene utilizzata dalla classe waDocumentazione per 
	* suddividere le pagine dell'applicazione all'interno della propria sezione.
	* <br/><br/>
	* Se non valorizzata, la proprietà assumerà il valore della proprietà
	* {@link $nome}
	* 
	* @var string
	*/	
	var 	$siglaSezione = '';
	
	

	//*****************************************************************************
	/**
	* inizializza l'applicazione
	* 
	* effettua l'include delle classi standard
	* (waDB, waMenu, waModulo, waTabella) e fa eventualmente partire la sessione
	* (se {@link $usaSessione} = true).
	* <br/><br/>
	 * Di fatto questo metodo sostituisce il costruttore della classe (costruttore
	 * che la classe non possiede), in modo che la chiamata sia sempre esplicita
	 * e posizionata nel momento che il programmatore ritiene più opportuno.
	* <br/><br/>
	* Questo metodo deve essere sempre invocato esplicitamente dalla classe 
	* applicazione derivata, anche eventualmente nel proprio costruttore.
	* @return void
	*/
	function inizializza()
		{
		$this->dominio = $_SERVER['HTTP_HOST'];
		//definisce la working-directory http dell'applicazione; se per qualche 
		//motivo viene fuori un risultato sbagliato, allora il programmatore
		// dovrà valorizzare autonomamente la proprietà (prima o dopo la 
		// chiamata a inizializza() non fa differenza)
		$this->httpwd = $this->getHttpWd();
		$this->cwd = $this->getCwd();
		if ($this->usaSessione)
	        session_start();
		$this->includeClasses();	

		if (!$this->xslt)
			$this->xslt = dirname(__FILE__) . "/uis/wa_default/xslt/waapplicazione.xsl";
		}
	
	//***************************************************************************
	/**
	* mostra un messaggio e termina l'esecuzione dello script corrente
	* @param string $titolo intestazione del messaggio
	* @param string $messaggio testo del messaggio da mostrare
	* @param boolean $torna comanda alla UI di mostrare un bottone o analogo per tornare alla pagina precedente
	* @param boolean $chiudi comanda alla UI di mostrare un bottone o analogo per chiudere la finestra corrente
	* @return void
	*/
	function mostraMessaggio($titolo, $messaggio, $torna = true, $chiudi = false)
		{
		// eventuali elementi già aggiunti vanno eliminati
		$this->elementi = array();
		
		$this->aggiungiElemento($titolo, "titolo");
		$this->aggiungiElemento($messaggio, "messaggio");
		if ($torna)
			$this->aggiungiElemento('', "azione_torna");
		if ($chiudi)
			$this->aggiungiElemento('', "azione_chiudi");
		$this->mostra();
	    exit();
		}
		
	//***************************************************************************
	/**
	* mostra un messaggio di errore generato dal database e termina l'esecuzione dello script corrente
	* @param waConessioneDB $connessioneDB oggetto contenente la connessione al DB
	* @return void
	*/
	function mostraErroreDB($connessioneDB)
		{
	    $this->mostraMessaggio (
	    				"Errore di sistema",
						"Sono spiacente, ma hai trovato un errore di sistema e non riesco a" .
						" soddisfare la tua richiesta. Per cortesia, riprova piu' tardi, grazie.<P>[" .
						$connessioneDB->nrErrore() . "] " . 
						$connessioneDB->messaggioErrore());
		}
	
	//***************************************************************************
	/**
	* mostra un messaggio di errore a fronte della violazione di obbligatorietà e termina l'esecuzione dello script corrente
	* @return void
	*/
	function mostraErroreObbligatorieta()
		{
	    $this->mostraMessaggio (
				"Errore di compilazione",
				"Mancano campi obbligatori: non e' possibile eseguire la richiesta.<P>" .
				"I campi contrassegnati dall'asterisco devono essere " .
				"obbligatoriamente compilati, altrimenti la richiesta " .
				"non verra' eseguita");
		}
	
	
	//***************************************************************************
	/**
	* restiuisce una connessione al database 
	* 
	* in caso di errore, termina 
	* l'esecuzione dello script invocando il metodo {@link mostraErroreDB}
	* @param string $fileConfigurazione nome del file di configurazione del package waDB; 
	* se non specificato o se vuoto, verrà utilizzato il file di configurazione 
	* di default di waDB, che è vuoto: di fatto, quindi, il parametro deve 
	* essere sempre valorizzato correttamente
	* 
	* @return waConnessioneDB
	*/
	function dammiConnessioneDB($fileConfigurazione = null)
		{
		$connessioneDB = wadb_dammiConnessione($fileConfigurazione);
		if ($connessioneDB->nrErrore())
			$this->mostraErroreDB($connessioneDB);
		return $connessioneDB;
		}		
	
	//***************************************************************************
	/**
	* restituisce un oggetto di classe waRigheDB caricato con le righe ritornate dalla query
	* 
	* in caso di errore, termina l'esecuzione dello script
	* invocando il metodo {@link mostraErroreDB}
	* @param string $sql query sql da eseguire
	* @param waConnessioneDB $connessioneDB connessione al db; puo' essere vuoto: 
	* in questo caso viene valorizzato dal metodo tramite chiamata a {@link dammiConnessioneDB} 
	* e l'oggetto diventa utilizzabile anche dal chiamante
	* @param int $nrRighe numero max di righe che la query deve restituire
	* @param int $rigaIniziale numero di righe di cui effettuare lo skip (offset)
	* @return waRigheDB oggetto di classe waRigheDB caricato con le righe ritornate dalla query
	*/
	function dammiRigheDB($sql, $connessioneDB = null,	$nrRighe = null, $rigaIniziale = 0)
		{
		$connessioneDB = empty($connessioneDB) ? 
							$this->dammiConnessioneDB() : 
							$connessioneDB;
		$righeDB = new waRigheDB($connessioneDB);
		$righeDB->caricaDaSql($sql, $nrRighe, $rigaIniziale);
		if ($righeDB->nrErrore())
			$this->mostraErroreDB($connessioneDB);
		
		return $righeDB;
		}
		
	//*************************************************************************
	/**
	* funzione di esecuzione standard di una query (evidentemente di edit)
	* 
	* in caso di errore, termina l'esecuzione dello script
	* invocando il metodo {@link mostraErroreDB}
	* @param string $sql
	* @param waConnessioneDB $dbconn
	*/
	function eseguiDB($sql, waConnessioneDB $connessioneDB = null)
		{		
		$connessioneDB = empty($connessioneDB) ? 
							$this->dammiConnessioneDB() : 
							$connessioneDB;
		$connessioneDB->esegui($sql);
		if ($connessioneDB->nrErrore())
			$this->mostraErroreDB($connessioneDB);
		}
	
	//***************************************************************************
	/**
	* consolida su db le modifiche avvenute su un recordset waRigheDB
	* 
	* in caso di errore, termina l'esecuzione dello script
	* invocando il metodo {@link mostraErroreDB}
	 * @param waRigheDB $righeDB recordset da consolidare
	 */
	function salvaRigheDB(waRigheDB $righeDB)
		{
		$righeDB->salva();
		if ($righeDB->nrErrore())
			$this->mostraErroreDB($righeDB->connessioneDB);
		
		}
		
	
	//***************************************************************************
	/**
	* invia un messaggio email
	* @param mixed $to destinatari/o; puo' essere un solo indirizzo email 
	* (stringa) o 'n' indirizzi email (array di stringhe)
	* @param string $oggetto subject del messaggio
	* @param string $corpo body del messaggio
	* @param mixed $from mittente del messaggio; puo' essere una stringa 
	* (indirizzo email) o un array di stringhe in cui il primo elemento e'
	* l'indirizzo email ed il secondo il nome; se vuoto, viene utilizzato per
	* l'indirizzo il valore della proprieta' {@link emailSupporto} e per il nome
	* il valore della proprieta' {@link titolo}
	* @param mixed $cc destinatari/o  in carbon-copy; puo' essere un solo indirizzo email 
	* (stringa) o 'n' indirizzi email (array di stringhe)
	* @param mixed $bcc destinatari/o  in blind-carbon-copy; puo' essere un solo indirizzo email 
	* (stringa) o 'n' indirizzi email (array di stringhe)
	* @param mixed $allegati file allegato al messaggio; puo' essere un solo nome file
	* (stringa) o 'n' nomi file (array di stringhe)
	* @return boolean true = ok
	*/
	function mandaMail($to, 
						$oggetto, 
						$corpo = "", 
						$from = "", 
						$cc = "", 
						$bcc = "", 
						$allegati = "")
		{
		include_once dirname(__FILE__) . "/phpmailer/class.phpmailer.php";
		
		$mail = new phpmailer(true);
		$mail->CharSet = "UTF8";
		
		if (!empty($from))
			{
			if (is_array($from))
				{
				$mail->From = $from[0];
				$mail->FromName = $from[1];
				}
			else
				$mail->From = $from;
			}
		else
			{
			$mail->From = $this->emailSupporto;
			$mail->FromName = $this->titolo;
			}
			
		$mail->Subject = $oggetto;
		if (!empty($this->serverSmtp))
			{
			$mail->Mailer = "smtp";
			$mail->Host = $this->serverSmtp;
			if (!empty($this->utenteSmtp))
				{
				$mail->SMTPAuth = true;
				$mail->Username = $this->utenteSmtp;
				$mail->Password = $this->passwordSmtp;
				if (!empty($this->sicurezzaSmtp))
					$mail->SMTPSecure = $this->sicurezzaSmtp;
				if (!empty($this->portaSmtp))
					$mail->Port = $this->portaSmtp;
				}
			}
			
		$this->addMailAddr($mail, $to, 'Address');
		$this->addMailAddr($mail, $cc, 'CC');
		$this->addMailAddr($mail, $bcc, 'BCC');
		
		if (!empty($allegati))
			{
			if (is_array($allegati))
				{
				foreach($allegati as $allegato)
					$mail->AddAttachment($allegato);
				}
			else
				$mail->AddAttachment($allegati);
			}
		
		// TESTO DEL MESSAGGIO IN HTML
		//$mail->IsHTML(true);
		
		$mail->Body = $corpo;
		
		try 
			{
			return @$mail->Send();
			}
		catch (phpmailerException $e)
			{
//			echo $e->errorMessage();
			return false;
			}
		catch (Exception $e)
			{
			throw $e;
			}

		return $esito;
		}
		
	
	//***************************************************************************
	/**
	* crea la documentazione della pagina
	* 
	* se l'oggetto {@link $waDoc} è stato istanziato, allora genera la 
	* documentazione della pagina corrente, ivi compresi menu, moduli e tabelle
	* in questa compresi.
	* @return void
	*/
	function creaDocumentazione()
		{
		// possono darsi casi in cui un eventuale errore db richiama il metodo
		// mostraMessagio che chiama mostra che chiama creaDocumentazione,
		// generando loop;
		static $rientranza = false;
		
		if (!$this->waDoc || $rientranza)
			return;
		
		$rientranza = true;
		$this->siglaSezione = $this->siglaSezione ? $this->siglaSezione : $this->nome;
		$this->titoloSezione = $this->titoloSezione ? $this->titoloSezione : $this->titolo;
		$this->waDoc->documentaSezione($this->siglaSezione, $this->titoloSezione);
		
		foreach ($this->elementi as $elemento) 
			{
			if ($elemento['tipo'] == 'stringa' && $elemento['nome'] == 'titolo')
				$this->waDoc->documentaPagina($this->siglaSezione, $elemento['valore']);
			elseif (is_a($elemento['valore'], 'waTabella'))
				$this->waDoc->documentaTabella($this->siglaSezione, $elemento['valore']);
			elseif (is_a($elemento['valore'], 'waModulo'))
				$this->waDoc->documentaModulo($this->siglaSezione, $elemento['valore']);
			elseif (is_a($elemento['valore'], 'waMenu'))
				$this->waDoc->documentaMenu($this->siglaSezione, $elemento['valore']);
			}
			
		$rientranza = false;
		}
		
	//***************************************************************************
	/**
	* manda in output la pagina
	* 
	* manda in output la pagina, compresi gli elementi aggiunti alla pagina 
	* tramite il metodo {@link aggiungiElemento}, utilizzando il foglio di 
	* stile indicato nella proprietà {@link xslt}.
	* @param boolean $bufferizza se false, allora viene immediatamente effettuato 
	* l'output della pagina; altrimenti la funzione ritorna il buffer di output 
	* @return void|string
	*/
	function mostra($bufferizza = false)
		{
		// se e' stato istanziato l'oggetto waDoc, crea la documentazione della
		// pagina
		$this->creaDocumentazione();
		
		$this->costruisciXML();

		$html = $this->trasforma();
		
		if (stripos($html, "<html") !== false)
			// effettuiamo la correzione "strict" solo se stiamo
			// effettivamente mandando in output un'intera pagina HTML
			// (se non è una pagina intera - ad esempio il contenuto di un div -
			// o se l'output non è html, allora non si fa nessun controllo 
			// strict)
			$html = $this->correggiHTML($html);
		
		if ($bufferizza)
			return $html;
			
		header("Content-Type: text/html; charset=utf-8");			
		echo $html;

		}
		
	//***************************************************************************
	/**
	* manda in output l'XML  della pagina 
	* 
	* da usare in fase di debug per mostrare l'output XML anziche' l'output generato
	* dall'XSLT.
	* @param boolean $bufferizza se false, allora viene immediatamente effettuato
	* l'output della pagina; altrimenti la funzione ritorna il buffer di output 
	* della pagina stessa
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
	* ritorna alla pagina che ha chiamato la corrente
	* 
	* metodo da invocare quando una pagina ha terminato il proprio lavoro e 
	* deve tornare alla precedente. 
	* @param array $valoriRitorno array associativo in cui ogni coppia chiave/valore 
	* e' cio' che e' stato modificato dalla presente pagina e di cui si intende 
	* informare la pagina a cui si torna (ha senso solo per la modalita' di 
	* navigazione non su singola pagina)
	* @param boolean $chiudiPagina valore che viene passato all'xslt, sulla base del quale potra' prendere le opportune decisioni
	* @param boolean $soloXml anziche' produrre l'output attraverso l'xslt, mostra l'xml che verrebbe passato all'xslt
	* @param boolean $bufferizza anziche' mandare in output il risultato, ritorna il buffer contenente l'output
	* 
	*/
	function ritorna($valoriRitorno = false, $chiudiPagina = true, $soloXml = false, $bufferizza = false)
		{
					
		$this->chiudiPagina = $chiudiPagina ? 1 : 0;
		$valoriRitorno = is_array($valoriRitorno) ? $valoriRitorno : array(0);
		foreach ($valoriRitorno as $k => $v)
			$paramsToRet .= "$k=$v|||";
		$this->valoriRitorno = base64_encode($paramsToRet);
		if ($soloXml)
			return $this->mostraXML($bufferizza);
		else
			return $this->mostra($bufferizza);
		}
				
	//***************************************************************************
	/**
	* aggiunge un nuovo elemento da mandare in output
	* 
	* aggiunge un nuovo elemento da mandare in output al momento della chiamata 
	* a {@link mostra}.
	* <br/><br/>
	* L'elemento potra' essere uno qualsiasi degli oggetti predefiniti dalle
	* waLibs (waMenu, waModulo, waTabella), oppure una qualsiasi cosa che l'xslt
	* specifico dell'applicazione sia in grado di gestire. Ad esempio: se 
	* volete mandare in output un blocco di testo libero, anche con 
	* formattazione html, l'elemento sara' la stringa che contiene il testo e 
	* il nome dell'elemento potra' essere usato dall'xslt specifico 
	* dell'applicazione per metterlo in un div con classe "testolibero".
	* @param mixed $elemento l'oggetto da mandare in output; per gli oggetti
	*			predefiniti (waMenu, waModulo, waTabella) si passera' l'oggetto
	*			vero e proprio: sara' poi la classe waApplicazione a invocarne
	*			il metodo {@link mostra}. Per i restanti tipi di elementi sara'
	*			evidentemente una stringa, anche formattata, che l'xslt dovra'
	*			essere in grado di gestire
	* @param string $nome e' il nome dell'elemento che potra' essere 
	*			intercettato dall'xslt, che quindi si comportera' come da 
	*			istruzioni. Per gli oggetti conosciuti delle waLibs non e' 
	*			necessario specificare il parametro: sara' sempre utilizzato il 
	*			valore della proprieta' nome dell'oggetto.
	* @param string $tipo e' il tipo di elemento; per gli oggetti conosciuti 
	 *			delle waLibs non e' necessario specificare il parametro: sara' 
	 *			sempre utilizzato il nome della classe dell'oggetto (waModulo, 
	 *			waMenu, ecc.). Per gli elementi arbitrari il default è "stringa"
	 *			e il valore dell'elemento sarà per default definito CDATA. 
	 *			L'unico altro tipo gestito e' "xml": in questo caso il valore
	 *			non sarà definito CDATA
	* @return void
	*/
	function aggiungiElemento($elemento, $nome = '', $tipo = 'stringa')
		{
		// costruiamo la struttura da salvare negli elementi
		$tosav = array("nome" => '', "tipo" => '', "valore" => null);
		if (is_object($elemento))
			{
			$tosav['nome'] = $nome ? $nome : $elemento->nome;
			$tosav['tipo'] = strtolower(get_class($elemento));
			}
		else
			{
			$tosav['nome'] = $nome ;
			$tosav['tipo'] = $tipo;
			}
			
		$tosav['valore'] = $elemento;
		$this->elementi[] = $tosav;
		}
				
	//***************************************************************************
	/**
	* rimanda il browser ad una nuova pagina
	*
	* rimanda il browser ad una nuova pagina; questa funziona puo' essere invocata
	* solo se non e' stato effettuato alcun output, altrimenti verra' generato
	* un errore da parte dell'engine PHP
	* @param string $iURL indirizzo a cui ridirezionare il browser
	* @return void
	*/
	function ridireziona ($iURL)
		{
	    header("Status: 301 Redirect");
	    header("Location: $iURL");
	    exit();
		}
		
	//***************************************************************************
	/**
	* mostra il contenuto di un oggetto o di un array
	*
	* mostra il contenuto di un oggetto o di un array; da usare solo in fase di 
	* debug
	* @param mixed $obj oggetto o array da mostrare
	* @return void
	*/
	function mostraOggetto($obj)
		{
		echo "<pre>" . print_r($obj, true) . "</pre><hr>";
		}
	
				
	
	
	//****************************************************************************************
	//****************************************************************************************
	//****************************************************************************************
	//****************************************************************************************
	//******   funzioni protected                                                      
	//****************************************************************************************
	//****************************************************************************************
	//****************************************************************************************
	//****************************************************************************************
	
	//***********************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	protected function dammiMiaPath()
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
	
	//******************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	protected  function addMailAddr(&$MailObj, $Addr, $DestType)
		{
		if (!empty($Addr))
			{
			if (is_array($Addr))
				{
				for ($i=0; $i<count($Addr); $i++)
					{
					if ($Addr[$i])
						eval ("\$MailObj->Add$DestType('$Addr[$i]');");
					}
				}
			else 
				eval ("\$MailObj->Add$DestType('$Addr');");
			}
		}

	//****************************************************************************************
	/**
	* cerca di determinare quale e' la directory di lavoro http dell'applicazione
	* (la radice dell'applicazione rispetto alla document root, 
	* non di un'eventuale sottosezione), ricavandolo dalla directory del modulo 
	* che per primo contiene un'istanza di waApplicazione. Se per qualche motivo il risultato
	* fosse sbagliato (perche' il file che contiene la prima estensione di
	* waApplicazione non risiede nella radice dell'applicazione come le linee
	* guida prevedono, oppure perche' c'e' una pippa sistemistica), allora
	* il programmatore dovra' definirsi da solo il valore della proprieta'
	* httpwd
	* 
	* @ignore
	* @access protected
	*/
	protected function getHttpWd()
		{
		if ($this->httpwd !== false)
			// qualcuno ha già valorizzato la working directory; avra' avuto i 
			// suoi bravi motivi per farlo, quindi non tocchiamo nulla
			return $this->httpwd;
		
		$stacktrace = @debug_backtrace(false);
		if (!$stacktrace[1] || strtolower($stacktrace[1]['class']) != strtolower(get_class()))
			// non esiste neanche una estensione di waApplicazione????
			// che vuol dire? valorizziamo la working directory come se 
			// corrispondesse alla document-root, perche' non sappiamo che altro
			// fare
			return "";
			
		$dr = $_SERVER['DOCUMENT_ROOT'];
		if (strpos($dr, "\\") !== false)
			// siamo sotto windows
			$dr = strtolower(str_replace("\\", "/", $dr));

		// togliamo un eventuale slash finale dalla definizione della document-root
		if (substr($dr,-1) == "/")
			$dr = substr($dr, 0, -1);
		
		return str_replace("\\", "/", substr(dirname($stacktrace[1]['file']), strlen($dr)));

		}
	
	//****************************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	protected function getCwd()
		{
		$dr = $_SERVER['DOCUMENT_ROOT'];
		if (substr($dr, -1) == "\\" || 	substr($dr, -1) == "/")
			$dr = substr($dr, 0, -1);
		return "$dr$this->httpwd";
		}
	
	//****************************************************************************************
	/**
	* @ignore
	* @access protected
	*/
	protected function includeClasses()
		{
		if (!empty($this->directoryClassiEsteseModulo))
			define("WAMODULO_EXTENSIONS_DIR", $this->directoryClassiEsteseModulo);
		include_once dirname(__FILE__) . "/../wadb/wadb.inc.php";
		include_once dirname(__FILE__) . "/../wamodulo/wamodulo.inc.php";
		include_once dirname(__FILE__) . "/../watabella/watabella.inc.php";
		include_once dirname(__FILE__) . "/../wamenu/wamenu.inc.php";
		include_once dirname(__FILE__) . "/../wadocumentazione/wadocumentazione.class.php";
		}
		
	//***************************************************************************
	/**
	* trasformaXML
	*
	* trasforma un buffer xml in html tramite xslt.
	* 
	* @ignore
	* @access protected
	* @return string
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
		if (!$html = $xp->transformToXML($xml_doc))
			trigger_error('XSL transformation failed.', E_USER_ERROR);
			
//		return $this->correggiHTML($html);
		return $html;
		}
		
	//***************************************************************************
	/**
	* correggiHTML
	*
	* mettiamo a posto eventuali scorrettezze xhtml
	* 
	* @ignore
	* @access protected
	* @return string
	*/
	protected function correggiHTML($buffer)
		{
		// spostiamo i "<link ...>" nell'head
		list ($pre_head, $resto) = explode("<head>", $buffer, 2);
		list ($head, $resto) = explode("</head>", $resto, 2);
		list ($tra_head_e_body, $body) = explode("<body", $resto, 2);
		while (true)
			{
			list ($prima, $dopo) = explode("<link ", $body, 2);
			if (!$dopo)
				break;
			list ($link, $dopo) = explode(">", $dopo, 2);
			$head .= "<link $link" . (substr(rtrim($link), -1) == "/" ? '' : " /") . ">\n";
			$body = "$prima$dopo";
			}
		
		$buffer = "$pre_head\n<head>\n$head</head>$tra_head_e_body\n<body$body";
		
		return $buffer;
		}
		
	//***************************************************************************
	/**
	* @ignore
	* @access protected
	* @return void
	*/
   protected function costruisciXML()
		{
	 	$this->buffer = "<?xml version='1.0' encoding='UTF-8'?>\n" .
								"<waapplicazione>\n" .
 								"\t<versione_librerie_xsl>" . LIBXSLT_VERSION . "</versione_librerie_xsl>\n" .
 								"\t<waapplicazione_path>" . $this->dammiMiaPath() . "</waapplicazione_path>\n" .
 								"\t<dominio>$this->dominio</dominio>\n" .
 								"\t<nome>$this->nome</nome>\n" .
 								"\t<titolo>$this->titolo</titolo>\n" .
 								"\t<titolo_sezione>$this->titoloSezione</titolo_sezione>\n" .
 								"\t<sigla_sezione>$this->siglaSezione</sigla_sezione>\n" .
 								"\t<directory_lavoro>$this->httpwd</directory_lavoro>\n" .
 								"\t<modalita_navigazione>$this->modalitaNavigazione</modalita_navigazione>\n" .
 								"\t<pagina>\n" .
								"\t\t<uri>" . htmlspecialchars($_SERVER['REQUEST_URI']) . "</uri>\n" .
								"\t\t<uri_precedente>" . htmlspecialchars($_SERVER['HTTP_REFERER']) . "</uri_precedente>\n" .
 								"\t\t<nome>" . substr(basename($_SERVER['PHP_SELF']), 0, -4) . "</nome>\n";
 								
 		if ($this->elementi)
 			{
 			$this->buffer .= "\t\t<elementi>\n";
			foreach ($this->elementi as $elemento) 
				{
				$this->buffer .= "\t\t\t<elemento>\n";
				$this->buffer .= "\t\t\t\t<nome>$elemento[nome]</nome>\n";
				$this->buffer .= "\t\t\t\t<tipo>$elemento[tipo]</tipo>\n";
				$this->buffer .= "\t\t\t\t<valore>";
				if (strtoupper($elemento['tipo']) != "XML")
					$this->buffer .= "<![CDATA[";
				if (is_object($elemento['valore']))
					$this->buffer .= $elemento['valore']->mostra(true);
				else
					$this->buffer .= $elemento['valore'];
				if (strtoupper($elemento['tipo']) != "XML")
					$this->buffer .= "]]>";
				$this->buffer .= "</valore>\n";
				$this->buffer .= "\t\t\t</elemento>\n";
				}
			$this->buffer .= "\t\t</elementi>\n";
 			}
 		
 		if ($this->valoriRitorno)
 			{
 			$this->buffer .= "\t\t<ritorno>\n";
 			$this->buffer .= "\t\t\t<valori>$this->valoriRitorno</valori>\n";
 			$this->buffer .= "\t\t\t<chiudi>$this->chiudiPagina</chiudi>\n";
 			$this->buffer .= "\t\t</ritorno>\n";
 			}


		$this->buffer .="\t</pagina>" .
							"</waapplicazione>\n";
 								
		}
				
		
//***************************************************************************
	} 	// fine classe waApplicazione
	

//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_APPLICAZIONE'))

