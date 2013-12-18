//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wacontrollo: contiene attributi e metodi relativi a un controllo
* HTML generico.
* 
* Da questa classe derivano tutti gli altri tipi di controllo del package.
* 
* 
* @class wacontrollo
*/
var wacontrollo = new Class
(
	{
	//-------------------------------------------------------------------------
	// proprieta'
	
	/**
	 * oggetto di classe {@link wamodulo} a cui il controllo appartiene
	 * @type wamodulo
	 */
	modulo			: null,
	
	/**
	 * nome del controllo
	 * @type string
	 */
	nome			: '',
	
	/**
	 * tipo del controllo; viene definito da ogni sottoclasse
	 * @type string
	 */
	tipo			: '',
	
	/**
	 * valore del controllo
	 * @type string
	 */
	valore			: '',
	
	/**
	 * indica se il controllo deve essere visibile o meno
	 * @type boolean
	 */
	visibile		: false,
	
	/**
	 * indica se il controllo deve essere readonly o meno
	 * @type boolean
	 */
	solaLettura		: false,
	
	/**
	 * indica se il controllo deve essere obbligatorio o meno
	 * @type boolean
	 */
	obbligatorio	: false,
	
	/**
	 * ogetto HTML contenente il il controllo fisico (questa classe rappresenta
	 * il controllo logico/applicativo; qualche volta i due concetti coincidono,
	 * ma non sempre).
	 * @type HTML_input_object
	 */
	obj				: null,
	
	//-------------------------------------------------------------------------
	/**
	 * inizializzazione (costruttore)
	 * 
	 * @param {wamodulo} modulo valorizza la proprietà {@link modulo}
	 * @param {string} nome valorizza la proprietà {@link nome}
	 * @param {string} valore valorizza la proprietà {@link valore}
	 * @param {boolean} visibile valorizza la proprietà {@link visibile}
	 * @param {boolean} solaLettura valorizza la proprietà {@link solaLettura}
	 * @param {boolean} obbligatorio valorizza la proprietà {@link obbligatorio}
	 */
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.modulo = modulo;
		this.nome = nome;

		this.valore = valore;
		this.visibile = visibile * 1;
		this.solaLettura = solaLettura * 1;
		this.obbligatorio = obbligatorio * 1;
		this.obj = this.modulo.obj.elements[this.nome];
		if (this.tipo == 'etichetta' || this.tipo == 'cornice')
			this.modulo.etichette[this.nome] = this;
		else
			this.modulo.controlli[this.nome] = this;
		},
		
	//-------------------------------------------------------------------------
	/**
	* inserisce un valore applicativo nel controllo
	* 
	* @param {mixed} valore valore da inserire nel controllo
	*/
	mettiValore: function(valore) 
		{
		this.obj.value = valore;
		},
		
	//-------------------------------------------------------------------------
	/**
	* restituisce il  valore applicativo contenuto nel controllo
	*/
	dammiValore: function() 
		{
		return this.obj.value;
		},
		
	//-------------------------------------------------------------------------
	/**
	* rende visibile/invisibile un controllo
	* 
	* @param {boolean} siNo flag di visibilità
	* @param {boolean} ancheEtichetta flag che indica che il metodo deve agire 
	*	anche sull'eventuale etichetta associata al controllo
	*/
	mostra: function(siNo, ancheEtichetta) 
		{
		this.visibile = siNo;
		this.renderizza();
		if (ancheEtichetta && this.modulo.etichette[this.nome])
			this.modulo.etichette[this.nome].mostra(siNo, false);
		},
		
	//-------------------------------------------------------------------------
	/**
	* abilita/disabilita un controllo
	* 
	* @param {boolean} siNo flag di abilitazione
	* @param {boolean} ancheEtichetta flag che indica che il metodo deve agire 
	*	anche sull'eventuale etichetta associata al controllo
	*/
	abilita: function(siNo, ancheEtichetta) 
		{
		this.solaLettura = !siNo;
		this.renderizza();
		if (ancheEtichetta && this.modulo.etichette[this.nome])
			this.modulo.etichette[this.nome].abilita(siNo, false);
		},
		
	//-------------------------------------------------------------------------
	/**
	 * rende obbligatorio/non obbligatorio un controllo
	* 
	* @param {boolean} siNo flag di obbligatoreità
	* @param {boolean} ancheEtichetta flag che indica che il metodo deve agire 
	*	anche sull'eventuale etichetta associata al controllo
	 */
	obbliga: function(siNo, ancheEtichetta) 
		{
		this.obbligatorio = siNo;
		this.renderizza();
		if (ancheEtichetta && this.modulo.etichette[this.nome])
			this.modulo.etichette[this.nome].obbliga(siNo, false);
		},
		
	//-------------------------------------------------------------------------
	/**
	* a seconda dello stato /definisce la classe css di un controllo(visibile, 
	* solaLettura, obbligatorio) renderizza il controllo
	*/
	renderizza: function() 
		{
		this.obj.style.visibility = this.visibile ? '' : 'hidden';
		this.obj.disabled = this.solaLettura;
		this.obj.className = (this.obbligatorio ? "wamodulo_obbligatorio" : '');
		},
		
	//-------------------------------------------------------------------------
	/**
	 * verifica che un controllo obbligatorio sia stato valorizzato
	 */
	verificaObbligo: function() 
		{
		if (!this.obbligatorio)
			return true;
		return this.dammiValore() ? true : false;
		},
		
	//-------------------------------------------------------------------------
	/**
	 * verifica che un controllo sia valorizzato correttamente
	 */
	verificaForma: function() 
		{
		return true;
		},
		
	//-------------------------------------------------------------------------
	// se la classe ha degli eventi predefiniti, una volta chiamato l'evento
	// predefinito occorre innescare anche l'eventuale codice
	// definito dal programmatore dell'applicazione per l'evento 
	eventoApplicazione: function(event, nomeEvento, objEvento) 
		{
		if (this[nomeEvento])
			{
			var obj = objEvento ? objEvento : this.obj;
			obj[nomeEvento] = this[nomeEvento];
			if (event == undefined)
				var esito = obj[nomeEvento]();
			else
				var esito = obj[nomeEvento](event);
			obj[nomeEvento] = this["my" + nomeEvento];
			return esito;
			}
		},
		
	//-------------------------------------------------------------------------
	// associa un evento al controllo; se c'e' un evento predefinito lo
	// parcheggia nell'apposita variabile
	aggiungiEvento: function (nomeEvento, evento)
		{
		if (this["my" + nomeEvento])
			this[nomeEvento] = evento;
		else
			this.obj[nomeEvento] = evento;
		},
	
	//-------------------------------------------------------------------------
	/**
	 * verifica se un controllo fisico appartiene al controllo applicativo
	* 
	* @param {object} obj oggetto input HTML 
	 */
	miAppartiene: function (obj)
		{
		return obj.name == this.obj.name;
		},
	
	//-------------------------------------------------------------------------
	/**
	 * trim (che non sempre c'e'....)
	 */
	trim: function(str) 
		{
		return str.replace(/(^\s*)|(\s*$)/g, "");
		},
		
	//-------------------------------------------------------------------------
	// simula un evento sul controllo
	simulaEvento: function(tipoEvento) 
		{
		var event;
		if (document.createEvent) 
			{
			event = document.createEvent("HTMLEvents");
			event.initEvent(tipoEvento, true, true);
			this.obj.dispatchEvent(event);
			} 
		else 
			{
			event = document.createEventObject();
			event.eventType = tipoEvento;
			this.obj.fireEvent("on" + event.eventType, event);
			}
		
		}
		
		
	}
);
