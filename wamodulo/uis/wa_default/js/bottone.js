//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wabottone: contiene attributi e metodi relativi a un bottone HTML
* 
* 
* @class wabottone
* @extends wacontrollo
*/
var wabottone = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo		: 'bottone',
	
	/**
	 * flag che indica che la pressione di questo bottone richiede abort
	 * della sessione di editing sul modulo
	 * @type boolean
	 */
	annulla		: false,	// e'un bottone di abort
	
	/**
	 * flag che indica che la pressione di questo bottone richiede al server
	 * l'eliminazione del record attualmente in editing
	 * @type boolean
	 */
	elimina		: false,	// e' un bottone che richiede l'eliminazione di un record
	myonclick	: function onclick(event) {this.form.wamodulo.controlli[this.name].alClick(event);},
	onclick		: false,
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		if (!this.obj)
			// pugnuetta di safari coi button....
			this.obj = this.modulo.obj.children(this.nome);
			
		this.annulla = this.obj.annulla;
		this.elimina = this.obj.elimina;
		this.obj.onclick = this.myonclick;
			
		},
		
	//-------------------------------------------------------------------------
	alClick: function(event) 
		{
		this.modulo.tipoInvioAnnulla = this.annulla == 1;
		this.modulo.tipoInvioElimina = this.elimina == 1;
		this.eventoApplicazione(event, "onclick");
		}
		
	
	}
);
