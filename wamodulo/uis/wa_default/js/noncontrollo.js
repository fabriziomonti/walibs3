//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wanoncontrollo: elemento arbitrario all'interno di un modulo
* 
* @class wanoncontrollo
* @extends wacontrollo
*/
var wanoncontrollo = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'noncontrollo',
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.obj = document.getElementById(nome);
		
		},
		
	//-------------------------------------------------------------------------
	/**
	* inserisce un valore applicativo nel controllo
	* 
	* @param {mixed} valore valore da inserire nel controllo
	*/
	mettiValore: function(valore) 
		{
		this.obj.innerHTML = valore;
		},
		
	//-------------------------------------------------------------------------
	/**
	* restituisce il  valore applicativo contenuto nel controllo
	*/
	dammiValore: function() 
		{
		return this.obj.innerHTML;
		},
		
	//-------------------------------------------------------------------------
	/**
	* a seconda dello stato /definisce la classe css di un controllo(visibile, 
	* solaLettura, obbligatorio) renderizza il controllo
	*/
	renderizza: function() 
		{
		this.parent();
		this.obj.className = "wamodulo_noncontrollo" + (this.obbligatorio ? " wamodulo_obbligatorio" : '');
		}
		
	
	
	}
);
