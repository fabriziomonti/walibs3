//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
// classe wapagina
var wapagina = new Class
(
	{
	//-------------------------------------------------------------------------
	// proprieta'
	tabelle		: {},
	moduli		: {},
	tabelleIdx	: [],
	moduliIdx	: [],
	// proprieta' valorizzate e utilizzabili nel caso in cui la pagina gestisca una sola tabella/modulo
	tabella 	: null,		// prima o unica tabella passata al costruttore
	modulo 		: null,		// primo o unico modulo passato al costruttore

	//-------------------------------------------------------------------------
	//initialization
	initialize: function(tabelle, moduli) 
		{
		// definizione iniziale delle proprieta'
		if (tabelle)
			{
			if (tabelle instanceof Array)	
				{
				this.tabelleIdx = tabelle;
				for (var i = 0; i < tabelle.length; i++)
					{
					this.tabelle[tabelle[i].nome] = tabelle[i];
					this.tabelle[tabelle[i].nome].applicazione = this;
					}
				this.tabella = this.tabelleIdx[0];
				}
			else
				this.tabella = this.tabelleIdx[0] = this.tabelle[tabelle.nome] = tabelle;
			}

		if (moduli)
			{
			if (moduli instanceof Array)	
				{
				this.moduliIdx = moduli;
				for (var i = 0; i < moduli.length; i++)
					{
					this.moduli[moduli[i].nome] = moduli[i];
					this.moduli[moduli[i].nome].applicazione = this;
					}
				this.modulo = this.moduliIdx[0];
				}
			else
				this.modulo = this.moduliIdx[0] = this.moduli[moduli.nome] = moduli;
			}
		
		// collegamento tra il modulo e le azioni non comprese da standard
		// pone gli eventi nello scope dell'applicazione 
		this.collegaEventiModulo();
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
			return this.wamodulo.applicazione[nomeMetodo + this.name + "_" + this.name](event);

		var nomeControllo = '';
		for (nomeControllo in this.form.wamodulo.controlli)
			{
			if (this.form.wamodulo.controlli[nomeControllo].miAppartiene(this))
				break;
			}
			
		nomeMetodo += this.form.wamodulo.nome + "_" + this.form.wamodulo.controlli[nomeControllo].nome;
		return this.form.wamodulo.applicazione[nomeMetodo](event);
		},
		
	//-------------------------------------------------------------------------
	// collegamento tra il modulo e le azioni non comprese da standard
	// pone gli eventi nello scope dell'applicazione 
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
	// verifica se un oggetto dizionario ha elementi al suo interno oppure no
	dizionarioVuoto: function (dizionario)
		{
		for (var li in dizionario)
			return false;
		
		return true;
		},
		
	//-------------------------------------------------------------------------
	evento_onclick_modulo0_id_amministrazione: function (event)
		{
		
		var esito = this.moduli.modulo0.RPC("fammiUnaRPC", this.moduli.modulo0.controlli["id_amministrazione"].dammiValore());
		if (this.dizionarioVuoto(esito))
			alert("L'RPC dice che non esistono corsi per l'id_amministrazione " + 
					this.moduli.modulo0.controlli["id_amministrazione"].dammiValore());
		else
			{
			var msg = "Lista dei corsi gestiti dall' id_amministrazione " + 
						this.moduli.modulo0.controlli["id_amministrazione"].dammiValore() + 
						" ottenuta tramite RPC" + "\n\n";
			for (var li in esito)
				msg += li + " - " + esito[li] + "\n";
		
			alert(msg);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo0_cmdAbilita: function (event)
		{
		for (var li in this.moduli.modulo0.controlli)
			{
			if (this.moduli.modulo0.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo0.controlli[li].abilita(true, true);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo0_cmdDisabilita: function (event)
		{
		for (var li in this.moduli.modulo0.controlli)
			{
			if (this.moduli.modulo0.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo0.controlli[li].abilita(false, true);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo0_cmdObbligatorio: function (event)
		{
		for (var li in this.moduli.modulo0.controlli)
			{
			if (this.moduli.modulo0.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo0.controlli[li].obbliga(true, true);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo0_cmdNonObbligatorio: function (event)
		{
		for (var li in this.moduli.modulo0.controlli)
			{
			if (this.moduli.modulo0.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo0.controlli[li].obbliga(false, true);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo0_cmdVisualizza: function (event)
		{
		for (var li in this.moduli.modulo0.controlli)
			{
			if (this.moduli.modulo0.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo0.controlli[li].mostra(true, true);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo0_cmdNascondi: function (event)
		{
			
		for (var li in this.moduli.modulo0.controlli)
			{
			if (this.moduli.modulo0.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo0.controlli[li].mostra(false, true);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo1_id_amministrazione: function (event)
		{
		
		var esito = this.moduli.modulo1.RPC("fammiUnaRPC", this.moduli.modulo1.controlli["id_amministrazione"].dammiValore());
		if (this.dizionarioVuoto(esito))
			alert("L'RPC dice che non esistono corsi per l'id_amministrazione " + 
					this.moduli.modulo1.controlli["id_amministrazione"].dammiValore());
		else
			{
			var msg = "Lista dei corsi gestiti dall' id_amministrazione " + 
						this.moduli.modulo1.controlli["id_amministrazione"].dammiValore() + 
						" ottenuta tramite RPC" + "\n\n";
			for (var li in esito)
				msg += li + " - " + esito[li] + "\n";
		
			alert(msg);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo1_cmdAbilita: function (event)
		{
		for (var li in this.moduli.modulo1.controlli)
			{
			if (this.moduli.modulo1.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo1.controlli[li].abilita(true, true);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo1_cmdDisabilita: function (event)
		{
		for (var li in this.moduli.modulo1.controlli)
			{
			if (this.moduli.modulo1.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo1.controlli[li].abilita(false, true);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo1_cmdObbligatorio: function (event)
		{
		for (var li in this.moduli.modulo1.controlli)
			{
			if (this.moduli.modulo1.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo1.controlli[li].obbliga(true, true);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo1_cmdNonObbligatorio: function (event)
		{
		for (var li in this.moduli.modulo1.controlli)
			{
			if (this.moduli.modulo1.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo1.controlli[li].obbliga(false, true);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo1_cmdVisualizza: function (event)
		{
		for (var li in this.moduli.modulo1.controlli)
			{
			if (this.moduli.modulo1.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo1.controlli[li].mostra(true, true);
			}
		},
	
	//-------------------------------------------------------------------------
	evento_onclick_modulo1_cmdNascondi: function (event)
		{
		for (var li in this.moduli.modulo1.controlli)
			{
			if (this.moduli.modulo1.controlli[li].nome.substr(0, 3) != "cmd")
				this.moduli.modulo1.controlli[li].mostra(false, true);
			}
		}
	
			
		
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
var moduli = [];
moduli.push(document.modulo0);
moduli.push(document.modulo1);
var pagina = new wapagina(null, moduli);




