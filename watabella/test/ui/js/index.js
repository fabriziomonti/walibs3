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
						
		// collegamento tra la tabella e le azioni non comprese da standard; pone
		// le azioni nello scope dell'applicazione anziche' della tabella.
		this.collegaAzioniTabella();
		
		// collegamento tra la tabella e le azioni non comprese da standard; pone
		// le azioni nello scope dell'applicazione anziche' della tabella.
		this.collegaLinkTabella();
		
		},
	
	//-------------------------------------------------------------------------
	// collegamento tra la tabella e i link che possono essere presentinelle colonne; pone
	// i metodi nello scope dell'applicazione anziche' della tabella.
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
	// collegamento tra la tabella e i link che possono essere presentinelle colonne; pone
	// i metodi nello scope dell'applicazione anziche' della tabella.
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
	mostraOggetto: function (obj)
		{
		var msg = '';
		for (var li in obj)
			msg += li + "=" + obj[li] + "\n";
		alert(msg);
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
	azione_tabella0_Custom: function (id)
		{
		var link = "<a href='javascript:alert(\"" + 
							this.tabelle.tabella0.righe[id].campi['importo'] + "\")'>" +
							this.tabelle.tabella0.righe[id].dammiContenutoCella("rifpa") + 
							"</a>";
							
		this.tabelle.tabella0.righe[id].modificaContenutoCella("rifpa", link);
		},

	//-------------------------------------------------------------------------
	azione_tabella0_RPC: function (id)
		{
		var id_organismo = this.tabelle.tabella0.righe[id].campi['id_organismo'];
		var esito = this.tabelle.tabella0.RPC("fammiUnaRPC", id_organismo);
		if (this.dizionarioVuoto(esito))
			alert("L'RPC dice che non esistono corsi per l'id_organismo " + id_organismo);
		else
			{
			var msg = "Lista dei corsi gestiti dall' id_organismo " + id_organismo +
						" ottenuta tramite RPC" + "\n\n";
			for (var li in esito)
				msg += li + " - " + esito[li] + "\n";
		
			alert(msg);
			}
		
		},
		
	//-------------------------------------------------------------------------
	azione_tabella0_wa_azioni_sx_default: function ()
		{
		location.href = "?type=wa_azioni_sx_default";
		},

	//-------------------------------------------------------------------------
	azione_tabella0_wa_azioni_dx: function ()
		{
		location.href = "?type=wa_azioni_dx";
		},

	//-------------------------------------------------------------------------
	azione_tabella0_wa_azioni_context: function ()
		{
		location.href = "?type=wa_azioni_context";
		},

	//-------------------------------------------------------------------------
	azione_tabella0_wa_azioni_sx_edit: function ()
		{
		location.href = "?type=wa_azioni_sx_edit";
		},

	//-------------------------------------------------------------------------
	azione_tabella0_wa_azioni_sx_quick_edit: function ()
		{
		location.href = "?type=wa_azioni_sx_quick_edit";
		},

	//-------------------------------------------------------------------------
	azione_tabella0_wa_usabile: function ()
		{
		location.href = "?type=wa_usabile";
		},
		
	//-------------------------------------------------------------------------
	link_tabella0_sigla: function (id)
		{
		alert(this.tabelle.tabella0.righe[id].campi.nome);
		},
		
	//-------------------------------------------------------------------------
	azione_tabella1_Custom: function (id)
		{
		var link = "<a href='javascript:alert(\"" + 
							this.tabelle.tabella1.righe[id].campi['importo'] + "\")'>" +
							this.tabelle.tabella1.righe[id].dammiContenutoCella("rifpa") + 
							"</a>";
							
		this.tabelle.tabella1.righe[id].modificaContenutoCella("rifpa", link);
		},

	//-------------------------------------------------------------------------
	azione_tabella1_RPC: function (id)
		{
		var id_organismo = this.tabelle.tabella1.righe[id].campi['id_organismo'];
		var esito = this.tabelle.tabella1.RPC("fammiUnaRPC", id_organismo);
		if (this.dizionarioVuoto(esito))
			alert("L'RPC dice che non esistono corsi per l'id_organismo " + id_organismo);
		else
			{
			var msg = "Lista dei corsi gestiti dall' id_organismo " + id_organismo +
						" ottenuta tramite RPC" + "\n\n";
			for (var li in esito)
				msg += li + " - " + esito[li] + "\n";
		
			alert(msg);
			}
		
		},
		
	//-------------------------------------------------------------------------
	azione_tabella1_wa_azioni_sx_default: function ()
		{
		location.href = "?type=wa_azioni_sx_default";
		},

	//-------------------------------------------------------------------------
	azione_tabella1_wa_azioni_dx: function ()
		{
		location.href = "?type=wa_azioni_dx";
		},

	//-------------------------------------------------------------------------
	azione_tabella1_wa_context: function ()
		{
		location.href = "?type=wa_azioni_context";
		},

	//-------------------------------------------------------------------------
	azione_tabella1_wa_azioni_sx_edit: function ()
		{
		location.href = "?type=wa_azioni_sx_edit";
		},

	//-------------------------------------------------------------------------
	azione_tabella1_wa_azioni_sx_quick_edit: function ()
		{
		location.href = "?type=wa_azioni_sx_quick_edit";
		},

	//-------------------------------------------------------------------------
	azione_tabella1_wa_usabile: function ()
		{
		location.href = "?type=wa_usabile";
		},
		
	//-------------------------------------------------------------------------
	link_tabella1_sigla: function (id)
		{
		alert(this.tabelle.tabella1.righe[id].campi.nomeamm);
		}
		
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
var tabelle = [];
tabelle.push(document.tabella0);
tabelle.push(document.tabella1);
var pagina = new wapagina(tabelle);