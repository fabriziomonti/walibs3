//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
 * classe di base delle procedure di controllo di una applicazione waLibs.
 * 
 * L'XSLT di default, una volta caricato questo file,  cercherà di caricare un 
 * modulo (file) javascript con nome [nome_pagina].js. Dopodichè verificherà se
 * esiste già un oggetto in document.wapagina . Se non esiste, creerà quell'oggetto
 * instanziando la presente classe.
 * 
 * E' così possibile per il programmatore estendere la presente classe nel file 
 * [nome_pagina].js e sempre all'interno del file istanziarne l'estensione in 
 * document.wapagina.
 * 
 * I compiti principali della classe sono:
 * 
 * <ul>
 * <li>gestire la navigazione tra le pagine (apertura/chiusura/aggiornamento)
 * <li>collegare le azioni di eventuali oggetti waTabella presenti nella pagina
 *		con metodi implementati  all'interno dell'estensione della classe
 *		(ossia in [nome_pagina].js). Si veda al proposito la nota in 
 *		{@link collegaAzioniTabella}
 * <li>collegare i link di eventuali oggetti waTabella presenti nella pagina
 *		con metodi implementati  all'interno dell'estensione della classe
 *		(ossia in [nome_pagina].js). Si veda al proposito la nota in 
 *		{@link collegaLinkTabella}
 * <li>collegare gli eventi di eventuali oggetti contenuti nei waModuli presenti 
 *		nella pagina
 *		con metodi implementati  all'interno dell'estensione della classe
 *		(ossia in [nome_pagina].js). Si veda al proposito la nota in 
 *		{@link collegaEventiModulo}.
 *	</ul>
 *	
 *	Naturalmente è poi possibile che [nome_pagina].js dichiari una classe che
 *	non deriva direttamente da waapplicazione, ma da una classe intermedia che
 *	derivi da waapplicazione, e che implementi metodi personalizzati per il 
 *	funzionamento della propria applicazione (calcolo codice fiscale, gestione
 *	help online, ecc.). Difatti l'XSLT di default cercherà anche di caricare un
 *	modulo (file) javascript con nome [nome_applicazione].js
 *	
 *	@class waapplicazione
 */
var waapplicazione = new Class
(
	{
	//-------------------------------------------------------------------------
	// proprieta'
	
	/**
	 * dizionario (array associativo: in javascript è un oggetto) degli oggetti
	 * watabella trovati all'interno della pagina, valorizzato automaticamente 
	 * dalla classe
	 */
	tabelle		: {},
	
	/**
	 * dizionario (array associativo: in javascript è un oggetto) degli oggetti
	 * wamodulo trovati all'interno della pagina, valorizzato automaticamente 
	 * dalla classe
	 */
	moduli		: {},
	
	/**
	 * array degli oggetti watabella trovati all'interno della pagina, 
	 * valorizzato automaticamente dalla classe in ordine di creazione
	 */
	tabelleIdx	: [],
	
	/**
	 * array degli oggetti wamodulo trovati all'interno della pagina, 
	 * valorizzato automaticamente dalla classe in ordine di creazione
	 */
	moduliIdx	: [],
	
	/**
	 * primo oggetto watabella trovato all'interno della pagina; corrisponde
	 * al primo elemento di {@link tabelle} e  {@link tabelleIdx}.  
	 */
	/**
	 * primo oggetto watabella trovato all'interno della pagina; corrisponde
	 * al primo elemento di {@link tabelle} e  {@link tabelleIdx}.  
	 */
	tabella 	: null,		// prima o unica tabella passata al costruttore
	
	/**
	 * primo oggetto wamodulo trovato all'interno della pagina; corrisponde
	 * al primo elemento di {@link moduli} e  {@link moduliIdx}.  
	 */
	modulo 		: null,		// primo o unico modulo passato al costruttore
	
	/**
	 * codice della modalità di navigazione da utilizzare, così come passato 
	 * dalla corrispondente classe PHP
	 */
	modalitaNavigazione	: '',		// codice della modalita' di navigazione da utilizzare (vedi waapplicazione.inc.php)
	
	finestraFiglia		: null,		// handle della finestra che contiene la pagina figlia quando si naviga in modalita' finestra
	iframeFiglia		: null,		// oggetto iframe che contiene la pagina figlia quando si naviga in modalita' interna
	
	/**
	 * metodo da richiamare da parte della pagina figlia in modo che la pagina 
	 * parent si allinei alle modifiche apportate dalla figlia.
	 * 
	 * Per default questa proprietà punta al metodo {@link ricaricaPagina}, che,
	 * appunto, si ricarica completamente per riflettere eventuali cambiamenti
	 * apportati dalla pagina figlia. E' ben possibile utilizzare un altro 
	 * metodo che, ad esempio, decida di recepire i cambiamenti tramite chiamate 
	 * RPC.
	 * 
	 * al metodo viene passato un dizionario di tutti gli elementi che hanno
	 * subito input.
	 */
	metodoAllineamento	: null,	
	
	ultimoSpiazzamentoY	: 0,		// posizione di scroll verticale della pagina al momento in cui si apre una pagina figlia (serve per riposizionarsi sul riallineamento in modalita' navigazione interna)

	modalitaNavigazionePagina		: 1,	// vedi defines in waapplicazione.inc.php
	modalitaNavigazioneFinestra 	: 2,
	modalitaNavigazioneInterna	 	: 3,
	
	// quando si naviga con finestra interna (iframe) non e' possibile chiamare
	// immediatamente il metodoAllineamento di una eventuale nonna, perche'
	// se metodoAllineamento prevede il ricaricamento della pagina (ed e' assai
	// probabile) cio' farebbe scomparire la mamma; per questo motivo salviamo
	// in un flag che quando la mamma (che ha ricevuto la chiamata a metodoAllineamento)
	// si chiude, allora quello e' il momento di chiamare il metodoAllineamento
	// della propria mamma (ossia la nonna della pagina che ha chiamato per 
	// prima metodoAllineamento, che si deve trasmettere a cascata a tutte le
	// mamme)
	flagRicaricaNonnaNavigazioneInterna: false,
	
	// vedi defiles in wamodulo.inc.php
	// nome del parametro tramite cui comunicare al modulo quale operazione e' 
	// stata richiesta dall'utente
	chiaveOperazione	: 'waope',	
	// valore del parametro tramite cui comunicare al modulo che l'utente ha 
	// richiesto la visualizzazione in dettaglio di un record
	opeDettaglio		: 1, 
	// valore del parametro tramite cui comunicare al modulo che l'utente ha 
	// richiesto la creazione di un nuovo record
	opeInserimento		: 2, 
	// valore del parametro tramite cui comunicare al modulo che l'utente ha 
	// richiesto la modifica di un record
	opeModifica			: 3, 
	// valore del parametro tramite cui comunicare al modulo che l'utente ha 
	// richiesto l'eliminazione di un record
	opeElimina			: 4, 

	

	//-------------------------------------------------------------------------
	/**
	 * inizializzazione (costruttore)
	 * 
	 */
	initialize: function() 
		{
		// definizione iniziale delle proprieta'
		this.metodoAllineamento = this.ricaricaPagina;
		this.iframeFiglia = document.getElementById("waapplicazione_iframe_figlia");
		
		// cerca tutti gli oggetti di classe watabella e wamodulo presenti nella pagina
		// per associarli all'applicazione
		for (var li in document.forms)
			this.aggiungiOggetto(document.forms[li]);
			
		// collegamento tra la tabella e le azioni non comprese da standard; pone
		// le azioni nello scope dell'applicazione anziche' della tabella.
		this.collegaAzioniTabella();
		
		// collegamento tra la tabella e le azioni non comprese da standard; pone
		// le azioni nello scope dell'applicazione anziche' della tabella.
		this.collegaLinkTabella();
		
		// dice alle tabelle che funzione utilizzare per aprire le finestre figlie
		this.collegaFinestreTabelle();

		// collegamento tra il modulo e le azioni non comprese da standard
		// pone gli eventi nello scope dell'applicazione 
		this.collegaEventiModulo();
		
		// se ci vien passato un parametro di posizionamento verticale proviamo a
		// rimettere la pagina nella posizione in cui l'utente l'ha lasciata
		// quando ha aperto una pagina figlia che evidentemente ci chiede di allinearci
		// alle sue modifiche
		var applRetY = this.dammiParametroQS("wa_pry"); 
		if (applRetY)
			{
			try {window.scrollTo(0, applRetY);}
			catch (exception) {}		
			}
				
		},
	
	//-------------------------------------------------------------------------
	// data una form, va a vedere se e' una di quelle associate a wamodulo o
	// watabella; se del caso aggiunge gli oggetti a quelli gestiti
	// dall'applicazione
	aggiungiOggetto: function(form) 
		{
		var oggetto = '';
		var tipo;
		if (form.watabella)
			{
			tipo = "tabelle";
			oggetto = form.watabella;
			}
		else if (form.wamodulo)
			{
			tipo = "moduli";
			oggetto = form.wamodulo;
			}
		else
			return;			
			
		oggetto.applicazione = this;
		this[tipo][oggetto.nome] = oggetto;
		this[tipo + "Idx"][this[tipo + "Idx"].length] = oggetto;
		
		if (oggetto.obj.watabella && !this.tabella)
			this.tabella = oggetto;
		else if (oggetto.obj.wamodulo && !this.modulo)
			this.modulo = oggetto;
		
		},
		
	//-------------------------------------------------------------------------
	/**
	 * metodo documentato a soli fini illustrativi, ma che non ha alcuna 
	 * necessità di essere invocato dal programmatore, in quanto richiamato
	 * dalla classe in fase di inizializzazione.
	 * 
	 * Questo metodo cerca eventuali metodi definiti all'interno della classe
	 * (e naturalmente sue derivate) che abbiano una corrispondenza con una
	 * delle azioni definite all'interno di un oggetto watabella. Se trova la
	 * corrispondenza, associa all'azione della tabella il metodo della classe,
	 * tale per cui quando l'utente richiede l'azione viene invocato il metodo
	 * della classe. Inoltre pone lo scope del metodo all'interno della classe
	 * derivata da waapplicazione, anzichè di watabella.
	 * <p>
	 * Ad esempio: supponiamo di avere nella pagina un oggetto watabella il cui
	 * nome è <b>tabella_ordini</b>, e che per quella tabella sia stato richiesto di
	 * implementare l'azione su riga <b>archivia</b>. 
	 * </p>
	 * Supponiamo ancora che abbiate istanziato in document.wapagina un oggetto
	 * di classe derivata da waapplicazione e che implementi il metodo
	 * </p>
	 * <pre>
	 * azione_tabella_ordini_archivia : function(idRiga)
	 * </pre>
	 * <p>
	 * Ebbene, ogni volta che l'utente premerà il bottone (o qualsiasi altro
	 * meccanismo) "archivia" verrà invocato il metodo da voi definito in 
	 * document.wapagina, e lo scope del metodo sarà quello di document.wapagina,
	 * ossia <b>this</b> corrisponderà a document.wapagina e non a
	 * document.tabella_ordini.
	 * </p>
	 * <p>
	 * A questo punto voi potrete chiamare il vostro server avvertendolo che
	 * l'utente ha richiesto l'archiviazione della riga; ad esempio (supponendo
	 * che la riga ordine abbia anche una colonna con nome <b>titolo</b>):
	 * </p>
	 * <pre>
	 * {
	 * if (confirm("Confermi archiviazione ordine " + this.tabelle["tabella_ordini"].righe[idRiga].campi.titolo + "?"))
	 *	location.href = "?archivia=1&idRiga=" + idRiga;
	 * }
	 * </pre>
	 */
	collegaAzioniTabella: function ()
		{
		var nomeMetodo;
		var nomeTabella;
		
		for (nomeMetodo in this)
			{
			if (typeof(this[nomeMetodo]) == 'function' && nomeMetodo.substr(0, ("azione_").length) == "azione_")
				{
				for (nomeTabella in this.tabelle)
					{
					if (nomeMetodo.substr(0, ("azione_" + nomeTabella + "_").length) == "azione_" + nomeTabella + "_")
						this.tabelle[nomeTabella][nomeMetodo] = new Function ("id", "return this.applicazione." + nomeMetodo + "(id)");
					}
				}
			}
		},

	//-------------------------------------------------------------------------
	/**
	 * metodo documentato a soli fini illustrativi, ma che non ha alcuna 
	 * necessità di essere invocato dal programmatore, in quanto richiamato
	 * dalla classe in fase di inizializzazione.
	 * 
	 * Questo metodo cerca eventuali metodi definiti all'interno della classe
	 * (e naturalmente sue derivate) che abbiano una corrispondenza con uno
	 * dei link definiti all'interno di un oggetto watabella. Se trova la
	 * corrispondenza, associa al link della tabella il metodo della classe,
	 * tale per cui quando l'utente segue il link viene invocato il metodo
	 * della classe. Inoltre pone lo scope del metodo all'interno della classe
	 * derivata da waapplicazione, anzichè di watabella.
	 * <p>
	 * Ad esempio: supponiamo di avere nella pagina un oggetto watabella il cui
	 * nome è <b>tabella_ordini</b>, e che per quella tabella sia stato richiesto di
	 * implementare un link sulla colonna <b>referente</b>. 
	 * </p>
	 * Supponiamo ancora che abbiate istanziato in document.wapagina un oggetto
	 * di classe derivata da waapplicazione e che implementi il metodo
	 * </p>
	 * <pre>
	 * link_tabella_ordini_referente : function(idRiga)
	 * </pre>
	 * <p>
	 * Ebbene, ogni volta che l'utente seguirà il link nella cella della colonna
	 * <b>referente</b> verrà invocato il metodo da voi definito in 
	 * document.wapagina, e lo scope del metodo sarà quello di document.wapagina,
	 * ossia <b>this</b> corrisponderà a document.wapagina e non a
	 * document.tabella_ordini.
	 * </p>
	 * <p>
	 * A questo punto voi potrete, ad esempio, invocare il client di posta 
	 * elettronica passandogli l'indirizzo email del referente (che avrete 
	 * ovviamente inserito in una delle colonne della tabella, magari non visibile):
	 * </p>
	 * <pre>
	 * {
	 * location.href = "mailto:" + this.tabelle["tabella_ordini"].righe[id].campi.EmailReferente;
	 * }
	 * </pre>
	 */
	collegaLinkTabella: function ()
		{
		var nomeMetodo;
		var nomeTabella;
		
		for (nomeMetodo in this)
			{
			if (typeof(this[nomeMetodo]) == 'function' && nomeMetodo.substr(0, ("link_").length) == "link_")
				{
				for (nomeTabella in this.tabelle)
					{
					if (nomeMetodo.substr(0, ("link_" + nomeTabella + "_").length) == "link_" + nomeTabella + "_")
						this.tabelle[nomeTabella][nomeMetodo] = new Function ("id", "return this.applicazione." + nomeMetodo + "(id)");
					}
				}
			}
		},

	//-------------------------------------------------------------------------
	// funzione richiamata automaticamente dalla collegaEventiModulo
	eventoApplicazione: function (event)
		{
		// siamo nello scope dell'oggetto fisico che ha innescato l'evento;
		// vogliamo andare nello scope di questo (this) oggetto;
		// cerchiamo qual'e' il controllo a cui appartiene l'oggetto che ha
		// scatenato l'evento, in modo da ricostruire il nome del metodo da
		// richiamare
		
		var nomeMetodo = "evento_on" + (window.event || event).type + "_";
		
		// verifichiamo se non sia un evento del modulo
		if (this.elements)
			return this.wamodulo.applicazione[nomeMetodo + this.id + "_" + this.id](event);

		var nomeControllo = '';
		for (nomeControllo in this.form.wamodulo.controlli)
			{
			if (this.form.wamodulo.controlli[nomeControllo].miAppartiene(this))
				break;
			}
			
		nomeMetodo += this.form.id + "_" + this.form.wamodulo.controlli[nomeControllo].nome;
		return this.form.wamodulo.applicazione[nomeMetodo](event);
		},
		
	//-------------------------------------------------------------------------
	/**
	 * metodo documentato a soli fini illustrativi, ma che non ha alcuna 
	 * necessità di essere invocato dal programmatore, in quanto richiamato
	 * dalla classe in fase di inizializzazione.
	 * 
	 * Questo metodo cerca eventuali metodi definiti all'interno della classe
	 * (e naturalmente sue derivate) che abbiano una corrispondenza con un
	 * evento e uno dei controlli definiti all'interno di un oggetto wamodulo. Se trova la
	 * corrispondenza, associa l'evento al metodo della classe,
	 * tale per cui quando l'utente genera l'evento viene invocato il metodo
	 * della classe. Inoltre pone lo scope del metodo all'interno della classe
	 * derivata da waapplicazione, anzichè di wamodulo/wacontrollo.
	 * <p>
	 * Ad esempio: supponiamo di avere nella pagina un oggetto wamodulo il cui
	 * nome è <b>modulo_ordini</b>, e che per quel modulo vogliate controllare
	 * l'evento onblur sul controllo <b>importo</b>
	 * </p>
	 * Supponiamo ancora che abbiate istanziato in document.wapagina un oggetto
	 * di classe derivata da waapplicazione e che implementi il metodo
	 * </p>
	 * <pre>
	 * evento_onblur_modulo_ordini_importo: function (event)
	 * </pre>
	 * <p>
	 * Ebbene, ogni volta che l'utente genererà l'evento verrà invocato il metodo 
	 * da voi definito in 
	 * document.wapagina, e lo scope del metodo sarà quello di document.wapagina,
	 * ossia <b>this</b> corrisponderà a document.wapagina e non a
	 * document.modulo_ordini.
	 * </p>
	 * <p>
	 * A questo punto voi potrete, ad esempio, verificare il superamento di una 
	 * data soglia:
	 * </p>
	 * <pre>
	 * {
	 * if (this.moduli["modulo_ordini"].controlli.importo.dammiValore() > 1000)
	 *	alert("Attenzione: superata soglia!");
	 * }
	 * </pre>
	 */
	collegaEventiModulo: function ()
		{
		var elems = [];
		var nomeEvento = '';
		var nomeControllo = '';
		var nomeMetodo = '';
		var nomeModulo = '';
			
		for (nomeMetodo in this)
			{
			if (typeof(this[nomeMetodo]) == 'function' && nomeMetodo.substr(0, ("evento_").length) == "evento_")
				{
				elems = nomeMetodo.split("_", 3);
				nomeEvento = elems[1];
				for (nomeModulo in this.moduli)
					{
					if (nomeMetodo.substr(0, ("evento_" + nomeEvento + "_" + nomeModulo + "_").length) == 
												"evento_" + nomeEvento + "_" + nomeModulo + "_")
						{
						nomeControllo = nomeMetodo.substr(("evento_" + nomeEvento + "_" + nomeModulo + "_").length);
						if (this.moduli[nomeModulo].controlli[nomeControllo])
							this.moduli[nomeModulo].controlli[nomeControllo].aggiungiEvento(nomeEvento, this.eventoApplicazione);
						else if (nomeModulo == nomeControllo)
							// e' stato richiesto un evento sull'intero modulo (verosimilmente l'overload di onsubmit)
							this.moduli[nomeModulo].obj[nomeEvento] = this.eventoApplicazione;
						}
					}
				}
			}
		},

	//-------------------------------------------------------------------------
	// dice alle tabelle che funzione utilizzare per aprire le finestre figlie
	collegaFinestreTabelle: function ()
	    {
	    // ricordati che la classe tabella ha un reference all'applicazione
	    for (var li in this.tabelle)
	    	this.tabelle[li].apriPagina = function (pagina, w, h) {return this.applicazione.apriPagina(pagina, w, h);}
	    },
	    
	//-------------------------------------------------------------------------
	/**
	 * apre una pagina figlia
	 * 
	 * @param {string} pagina URL della pagina di destinazione
	 * @param {int} winW larghezza della finestra di destinazione; naturalmente
	 * ha senso solo per la modalità di navigazione a finestre; default 1024
	 * @param {int} winW altezza della finestra di destinazione; naturalmente
	 * ha senso solo per la modalità di navigazione a finestre; default 768
	 */
	apriPagina: function (pagina, winW, winH)
	    {
	    if (this.modalitaNavigazione == this.modalitaNavigazioneFinestra)
	    	return this.apriPaginaFinestra(pagina, winW, winH);
	    else if (this.modalitaNavigazione == this.modalitaNavigazioneInterna)
			return this.apriPaginaInterna(pagina);
		else
		    {
			var qs = this.rimuoviParametroDaQS("wa_pry");
		    var qoe = qs.substr(0, 1) == "?" ? "&" : "?";
		    var paginaRitorno = encodeBase64(document.location.pathname + 
		    					qs + 
		    					qoe + 
		    					"wa_pry=" + this.dammiSpiazzamentoY());
		    qoe = pagina.indexOf("?") != -1 ? "&" : "?";
		    location.href = pagina + qoe + "wa_pr=" + paginaRitorno;
		    }
		
	    },
	    
	//-------------------------------------------------------------------------
	apriPaginaFinestra: function (pagina, winW, winH)
	    {
		if (!winW) winW = 1024;
		if (!winH) winH = 768;
		
		this.chiudiFiglia();
		this.finestraFiglia = window.open(pagina,"",
								"width=" + winW +
								",height=" + winH +
								",screenX=0,screenY=0,top=0,left=0" + 
								",bgcolor=white,scrollbars=yes,resizable=yes,status=yes");
	    },
	    
	//-----------------------------------------------------------------------------
	apriPaginaInterna: function (pagina)
		{
		this.ultimoSpiazzamentoY = this.dammiSpiazzamentoY();
		scroll(0,0);
		document.body.style.overflow = 'hidden';
		this.iframeFiglia.style.visibility ='';
	    if (this.iframeFiglia.contentWindow)
	   		this.iframeFiglia.contentWindow.document.location.href = pagina;
	    else if (document.all)
			document.all[this.iframeFiglia.id].src = pagina;  
	
	    },
	    
	//-------------------------------------------------------------------------
	/**
	 * chiude la pagina corrente
	 */
	chiudiPagina: function ()
	    {
	    if (this.modalitaNavigazione == this.modalitaNavigazioneFinestra)
	    	self.close();
	    else if (this.modalitaNavigazione == this.modalitaNavigazioneInterna)
	    	{
			// se una figlia di questo frame ha richiesto il caricamento a 
			// cascata delle mamme, questo e' il momento per dire alla mamma
			// di ricaricarsi
			if (parent.document.wapagina.flagRicaricaNonnaNavigazioneInterna && parent.document.wapagina.metodoAllineamento)
				parent.document.wapagina.metodoAllineamento();
			parent.document.body.style.overflow = '';
			parent.document.wapagina.iframeFiglia.style.visibility ='hidden';
		   	document.location.href = "about:blank";
			if (parent.document.wapagina.ultimoSpiazzamentoY)
				{
				try {parent.scrollTo(0, parent.document.wapagina.ultimoSpiazzamentoY);}
				catch (exception) {}		
				}
	    	}
		else
		    {
			var paginaRitorno = this.dammiParametroQS("wa_pr"); 
			if (paginaRitorno)
				return location.href = decodeBase64(paginaRitorno);
			this.msgErrore("pagina di ritorno non trovata");
		    }
	    	
	    },
    
	//-------------------------------------------------------------------------
	// ritna il valore di un parametro contenuto nella query-string dell'url corrente
	dammiParametroQS: function (nomeParametro)
		{
		var coppie = (document.location.search.substr(1)).split('&');
		for (var i = coppie.length - 1; i >= 0; i--)
			{
			var kv = coppie[i].split('=');
			if (kv[0] == nomeParametro)
				return kv[1];
			}
		
		return false;
		},
		
	//-------------------------------------------------------------------------
	// restituisce lo posizione di scroll verticale della pagina, in modo che 
	// quando ne viene chiamato il riallineamento possa posizionarsi nella 
	// posizione dove l'utente l'aveva lasciata
	dammiSpiazzamentoY: function ()
		{
		var y = 0;
		try
			{
			if (document.documentElement && document.documentElement.scrollTop)
				y = document.documentElement.scrollTop;
			else if (document.body && document.body.scrollTop)
				y = document.body.scrollTop;
			else if (window.pageYOffset)
				y = window.pageYOffset;
			}
		catch(exception){}
		return y;
		},
	
	//-------------------------------------------------------------------------
	// metodo richiamato da una finestra figlia quando vuole comunicare alla mamma
	// di allinearsi alle modifiche da lei effettuate
	allineaGenitore: function (valoriInput)
	    {
	    if (this.modalitaNavigazione == this.modalitaNavigazionePagina)
	    	return;
	    	
		var valoriInput = decodeBase64(valoriInput);
		var elems = valoriInput.split("|||");
		var param;
		valoriInput = {};
		for (var i = 0; i < elems.length; i++)
			{
			 param = elems[i].split("=", 2);
			 if (param[0] != "")
			 	valoriInput[param[0]] = param[1];
			}
		var mamma = opener ? opener : parent;
		mamma.document.wapagina.metodoAllineamento(valoriInput);
	    },
    
	//-------------------------------------------------------------------------
	// il default delle funzioni di sincronizzazione che una finestra child 
	// chiama per far si che la mamma si allinei; nel caso piu' semplice
	// (questo), praticamente si ricarica. se avete bisogno di qualcosa di
	// piu' complesso, definite una vostra "metodoAllineamento"
	ricaricaPagina: function (datiInputFiglia)
		{
		if (opener)
			{
			if (opener.document.wapagina && opener.document.wapagina.metodoAllineamento)
				opener.document.wapagina.metodoAllineamento(datiInputFiglia);
			}			
		else 
			parent.document.wapagina.flagRicaricaNonnaNavigazioneInterna = true;
			
		// in caso di ricaricamento di finestra a causa di inserimento/modifica record
		// non dobbiamo chiudere  ne' la finestra di help, ne' quella service, quindi
		// dobbiamo ridefinire l'evento onUnload del documento
		if (document.layers)
			window.captureEvents(Event.ONUNLOAD);
		window.onunload = '';
		
		// ricarichiamo la finestra... sarebbe facile fare una reload; e invece no
		// perche' senno' il browser ci chiede l'autorizzazione in caso di precedente
		// submit. 
		// OCCHIO! si perdono eventuali parametri POST! se servono, fatevi 
		// un metodoAllineamento apposta
		var qs = this.rimuoviParametroDaQS("wa_pry");
			
		// aggiungiamo al ricaricamnto il parametro per lo spiazzamento verticale,
		// cosi' l'utente ritrova la pagina nella posizione dove l'ha lasciata
	    if (this.modalitaNavigazione == this.modalitaNavigazioneFinestra)
			qs += (qs == '' ? '?' : '&') + "wa_pry=" + this.dammiSpiazzamentoY();
	    else if (this.modalitaNavigazione == this.modalitaNavigazioneInterna)
			qs += (qs == '' ? '?' : '&') + "wa_pry=" + this.ultimoSpiazzamentoY;
		
		document.location.href = document.location.pathname + qs;
		},
	

	//-------------------------------------------------------------------------
	// toglie un parametro dalla query string (location.search) perchè
	// altrimenti sarebbe ripetuto e a lungo andare rischia di intasare
	// la QS stessa
	rimuoviParametroDaQS: function (param)
	    {
		var qs = '';
		if (document.location.search.length)
			{
			var kv = new Array();
			var coppie = (document.location.search.substr(1)).split('&');
			for (var i = 0; i < coppie.length; i++)
				{
				kv = coppie[i].split('=');
				if (kv[0] != param)
					qs += (qs == '' ? '?' : '&') + kv[0] + "=" + (kv[1] ? kv[1] : '');
				}
			}
			
		return qs;
			
		},
		
	//-------------------------------------------------------------------------
	// metodo invocato sulla unload del documento; se la navigazione e' a 
	// finestre chiude una eventuale finestra figlia (e a cascata le figlie
	// chiuderanno le figlie)
	chiudiFiglia: function ()
	    {
		if (this.finestraFiglia && !this.finestraFiglia.closed)
			{
			this.finestraFiglia.document.wapagina.chiudiPagina();
			this.finestraFiglia = null;
			}
	    },

	//-------------------------------------------------------------------------
	/**
	 * mostra un messaggio di errore e torna false
	 */
	msgErrore: function (msg)
	    {
	    alert(msg);
	    return false;
	    },

	//-------------------------------------------------------------------------
	/**
	 * verifica se un oggetto dizionario ha elementi al suo interno oppure no
	 */
	dizionarioVuoto: function (dizionario)
		{
		for (var li in dizionario)
			return false;
		
		return true;
		},
		
	//-------------------------------------------------------------------------
	/**
	 * mostra il contenuto di un oggetto (utile in fase di debug per i dizionari)
	 */
	mostraOggetto: function (obj)
		{
		var msg = '';
		for (var li in obj)
			msg += li + "=" + obj[li] + "\n";
		alert(msg);
		}

		
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
