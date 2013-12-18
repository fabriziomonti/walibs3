//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe waetichetta: label HTML tipicamente associata ad un controllo di
* input col medesimo nome
* 
* @class waetichetta
* @extends wacontrollo
*/
var waetichetta = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'etichetta',
	helpLink: false,
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.obj = document.getElementById(this.modulo.nome + "_"+ this.nome);
		this.helpLink = document.getElementById("hlplink_" + this.modulo.nome + "_"+ this.nome);
		},
		
	//-------------------------------------------------------------------------
	// inserisce un valore logico nel controllo
	mettiValore: function(valore) 
		{
		if (this.helpLink)
			this.helpLink.innerHTML = valore;
		else
			this.obj.innerHTML = valore;
		},
		
	//-------------------------------------------------------------------------
	// restituisce il valore logico del controllo
	dammiValore: function() 
		{
		if (this.helpLink)
			return this.helpLink.innerHTML;
		return this.obj.innerHTML;
		},
		
	//-------------------------------------------------------------------------
	// a seconda dello stato definisce la classe css di un controllo
	renderizza: function() 
		{
		this.obj.style.visibility = this.visibile ? '' : 'hidden';
		this.obj.disabled = this.solaLettura;
		this.obj.className = (this.solaLettura ? "wamodulo_disabilitato" : '') +
							(this.obbligatorio ? (this.solaLettura ? " " : '') + "wamodulo_obbligatorio" : '');
		}
		
	
	}
);
