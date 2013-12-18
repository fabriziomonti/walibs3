//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
 * classe che gestisce le proprietà e i metodi di un modulo waLibs.
 * 
 * 
* La struttura di questa classe prevede che:
*
* <ul>
* <li>
*	per ogni modulo venga creato un oggetto di classe wamodulo con nome 
* 	uguale a quello dell modulo (proprietà waModulo::nome in PHP) e aggiunto 
* 	come nuova proprietà dell'oggetto document. Questa operazione è svolta
* 	automaticamente dall'XSLT
*	<li>
*	l'XSLT non associa alcun evento ad alcun controllo (se non quello di
*	dei bottoni di submit implicitamente previsto dall'HTML). Sarà compito della
*	applicazione creare le funzioni di gestione degli eventi e associarli ai
*	controlli contenuti nel modulo. La UI di default di waApplicazione 
*	contiene già al suo interno un meccanismo automatico per fare ciò.
* </ul>
* 
* @class wamodulo
 */
var wamodulo = new Class
(
	{
	//-------------------------------------------------------------------------
	// proprieta'
	
	/**
	 * nome del modulo
	 * @type string
	 */
	nome				: '',
	
	/**
	 * flag deve essere valorizzato dai bottoni di submit quando si desidera 
	 * eliminare un record (bottoni con proprietà {@link wabottone#elimina} = 
	 * true); viene invocato il submit ma non vengono eseguiti 
	 * controlli formali o di obbligatorietà
	 * @type boolean
	 */
	tipoInvioElimina	: false,	
	
	/**
	 * flag deve essere valorizzato dai bottoni quando si desidera 
	 * abortire la sessione di editing (bottoni con proprietà {@link wabottone#annulla} = 
	 * true); non vengono eseguiti controlli formali o di obbligatorietà
	 * @type boolean
	 */
	tipoInvioAnnulla	: false,	// deve essere valorizzato dai bottoni di submit se si desidera abortire l'operazione e non consolidare nulla; non vengono eseguiti controlli formali o di obbligatorieta'

	/**
	 * dizionario (array associativo; in javascript è un oggetto, non un array)
	 * dei controlli di input (no etichette, no cornici) contenuti nel modulo.
	 * 
	 * La chiave di ogni elemento è il nome del controllo.
	 * 
	 * @type object
	 */
	controlli			: {},

	/**
	 * dizionario (array associativo; in javascript è un oggetto, non un array)
	 * dei controlli di tipo etichetta o cornice contenuti nel modulo.
	 * 
	 * La chiave di ogni elemento è il nome dell'etichetta.
	 * 
	 * @type object
	 */
	etichette			: {},

	/**
	 * oggetto form HTML fisico a cui fa riferimento il modulo
	 * 
	 * @type object
	 */
	obj					: null,
	
	/**
	 * se il flag è alzato, allora viene mostrato il contenutoi del messaggio
	 * RPC ricevuto dal server
	 * 
	 * @type boolean
	 */
	debugRPC					: false,
	
//	applicazione		: null,		// eventuale istanza dell'applicazione (e' lei stessa a valorizzare questa proprieta')
	http_request 		: false,
	errore_rpc 			: 'wamodulo_errore_rpc',
	
	//-------------------------------------------------------------------------
	// implements

	//-------------------------------------------------------------------------
	/**
	 * inizializzazione (costruttore)
	 * 
	 * @param {string} nome valorizza la proprietà {@link nome}
	 */
	initialize: function(nome) 
		{
		this.nome = nome;
		this.obj = document.getElementById(this.nome);
		this.obj.wamodulo = this;
		this.obj.onsubmit = function onsubmit(event) {return this.wamodulo.validaModulo();}
		},
		
	//-------------------------------------------------------------------------
	/**
	 *  verifica che tutti i controlli obbligatori siano stati valorizzati
	 */
	verificaObbligo: function() 
		{
		msg = '';
		for (nomeControllo in this.controlli)
			{
			if (!this.controlli[nomeControllo].verificaObbligo())
				msg += "Il campo " + 
						(this.etichette[nomeControllo] ? this.etichette[nomeControllo].dammiValore() : nomeControllo) +
						" e' obbligatorio e non e' stato valorizzato.\n";
			}
			
		if (msg == '')
			return true;

		alert(msg);
		return false;
		},
		
	//-------------------------------------------------------------------------
	/**
	 * verifica che tutti i controlli siano stati valorizzati con valori
	 * compatibili col tipo del controllo
	 */
	verificaForma: function() 
		{
		msg = '';
		for (nomeControllo in this.controlli)
			{
			if (!this.controlli[nomeControllo].verificaForma())
				msg += "Il campo " + 
						(this.etichette[nomeControllo] ? this.etichette[nomeControllo].dammiValore() : nomeControllo) +
						" non e' stato valorizzato correttamente.\n";
			}
			
		if (msg == '')
			return true;

		alert(msg);
		return false;
		},
		
	//-------------------------------------------------------------------------
	/**
	 * funzione di validazione standard di un modulo
	 */
	validaModulo: function() 
		{
		if (this.tipoInvioAnnulla)
			{
			this.obj.wamodulo_operazione.value = 5;
			return true;
			}
		if (this.tipoInvioElimina)
			{
			this.tipoInvioElimina = false;
			if (confirm('Confermi eliminazione?'))
				{
				this.obj.wamodulo_operazione.value = 4;
				return true;
				}
			return false;
			}
		if (this.verificaForma())
			{
			if(this.verificaObbligo())
				return confirm('Confermi operazione?');
			}
		return false;
		},
		
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	// restituisce una connessione rpc
	getHttpRequestObj: function() 
		{
		var hr = false;
		if (window.ActiveXObject) 
			{ 
			// IE
			try 
				{
				hr = new ActiveXObject("Msxml2.XMLHTTP");
				}
			catch (e) 
				{
				try 
					{
					hr = new ActiveXObject("Microsoft.XMLHTTP");
					}
				catch (e) 
					{}
				}			
			}
		else if (window.XMLHttpRequest) 
			{ 
			// Mozilla, Safari,...
			hr = new XMLHttpRequest();
//			if (hr.overrideMimeType) 
//				hr.overrideMimeType('text/plain');
			}
			
		
		return hr;
		},
		
	//-------------------------------------------------------------------------
	// mostra un eventuale errore durante rpc
	mostraErroreRPC: function(msg) 
		{
		alert(msg);
		return this.errore_rpc;
		},
		
	//-------------------------------------------------------------------------
	// cerca di leggere dalla struttura xml-rpc un valore
	leggiValoreXML: function(xmlDoc, chiave) 
		{
			
		var retval = '';
		
		if (xmlDoc.getElementsByTagName(chiave) && 
			xmlDoc.getElementsByTagName(chiave)[0] && 
			xmlDoc.getElementsByTagName(chiave)[0].firstChild)
			return xmlDoc.getElementsByTagName(chiave)[0].firstChild.nodeValue;

		return "";

		},
	
	//-------------------------------------------------------------------------
	/**
	 * effettua una chiamata RPC verso il server.
	 * 
	* gli argomenti da passare al metodo sono posizionali 
	* <ol>
	* <li>nome funzione/metodo PHP da richiamare lato server
	* <li>parametro 1 da passare alla funzione/metodo
	* <li>parametro 2 da passare alla funzione/metodo
	* <li>parametro n da passare alla funzione/metodo...
	* </ol>
	* 
	 */
	RPC: function() 
		{
		
		try
			{
			var http_request = this.getHttpRequestObj();
			
			// creiamo il messaggio da inviare (parametri della chiamata)
			// 6 e' il codice operazione rpc (vedi defines in wamodulo.inc.php)
			var post = 'wamodulo_operazione=6';
			// passiamo il nome del modulo che richiede RPC, in modo da
			// riconoscere, lato server, quale modulo ha richiesto l'operazione
			post += '&wamodulo_nome_modulo=' + escape(this.nome);
			// passiamo in wamodulo_funzionerpc il nome della funzione/metodo 
			// PHP da richiamare, che deve essere il primo argomento con cui e'
			// stato chiamato il presente metodo
			post += '&wamodulo_funzione_rpc=' + escape(arguments[0]);
			// passiamo i parametri coi quali il presente metodo e' stato 
			// richiamato nel medesimo ordine; li ricevera' nel medesimo ordine
			// posizionale anche la funzione/metodo PHP lato server
			for (var i = 1; i < arguments.length; i++)
					post += '&wamodulo_dati_rpc[' + (i - 1) + ']=' + escape(arguments[i]);

			// chiamiamo il server
			http_request.open('POST', document.location.href, false);
			http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			http_request.send(post);
			
			// verifica eventuali errori di sistema
			if (http_request.status != 200) 
				return this.mostraErroreRPC("Errore durante RPC: " + http_request.status);
				
			// gestione della risposta
			if (this.debugRPC)
				alert(http_request.responseText);
			var xmlDoc = http_request.responseXML;				
			var esito = this.leggiValoreXML(xmlDoc, 'wamodulo_esito_rpc');
			var messaggio = this.leggiValoreXML(xmlDoc, 'wamodulo_messaggio_rpc');
			if (esito != '__rpc_ok__')
				return this.mostraErroreRPC("Errore applicativo server durante chiamata RPC:\n" + messaggio);
			
			if (xmlDoc.getElementsByTagName('item')[0])
				{
				// l'esito e' un array; torniamo UN OGGETTO (dizionario!) perche' le 
				// chiavi non e' detto che siano necessariamente numeriche
				var item = null;
				var retval = {};
				if (xmlDoc.getElementsByTagName('item')[0].getAttribute("id"))
					{
					// l'array non e' vuoto	
					for (var i = 0;  i < xmlDoc.getElementsByTagName('item').length; i++)
						{
						item = xmlDoc.getElementsByTagName('item')[i];
						retval[item.getAttribute("id")] = item.childNodes[0] ? item.childNodes[0].nodeValue : '';
						}
					}					
				}
			else 
				var retval = this.leggiValoreXML(xmlDoc, 'wamodulo_dati_rpc');
						
			return retval;
			}
		catch(exception)
			{
			return this.mostraErroreRPC("Errore durante RPC: " + exception);
			}
		}
		
	}
);
