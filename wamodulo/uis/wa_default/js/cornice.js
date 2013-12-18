//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wacornice: div HTML con bordi e un'etichetta che simula un box 
* all'interno del quale possono essere raggruppati (graficamente e basta!)
* diversi controlli
* 
* @class wacornice
* @extends wacontrollo
*/
var wacornice = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'cornice',
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.obj = document.getElementById(this.modulo.nome + "_"+ this.nome);
		},
		
	//-------------------------------------------------------------------------
	// inserisce un valore logico nel controllo
	mettiValore: function(valore) 
		{
		},
		
	//-------------------------------------------------------------------------
	// restituisce il valore logico del controllo
	dammiValore: function() 
		{
		},
		
	//-------------------------------------------------------------------------
	// a seconda dello stato definisce la classe css di un controllo
	renderizza: function() 
		{
		this.obj.style.visibility = this.visibile ? '' : 'hidden';
		this.obj.className = (this.solaLettura ? "wamodulo_disabilitato" : '') +
							(this.obbligatorio ? (this.solaLettura ? " " : '') + "wamodulo_obbligatorio" : '');
		}
			
	}
);
