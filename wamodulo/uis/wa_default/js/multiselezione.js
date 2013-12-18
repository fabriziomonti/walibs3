//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wamultiselezione: select con possibilità di selezioni multiple
* 
* @class wamultiselezione
* @extends waselezione
*/
var wamultiselezione = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: waselezione,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'multiselezione',
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.obj = this.modulo.obj.elements[this.nome + "[]"];
		
		},
	
	//-------------------------------------------------------------------------
	dammiValore: function() 
		{
		var retval = new Array();
		var selCntr = 0;
		for (var i= 0; i < this.obj.options.length; i++)
			{
			if (this.obj.options[i].selected)
				{
				retval[selCntr] = this.obj.options[i].value;
				selCntr++;
				}
			}

		return retval;
		},
		
	//-------------------------------------------------------------------------
	// inserisce un valore logico nel controllo
	mettiValore: function(valore) 
		{
		for (var i= 0; i < this.obj.options.length; i++)
			this.obj.options[i].selected = false;
		for (var c = 0; c < valore.length; c++)
			{
			for (i= 0; i < this.obj.options.length; i++)
				this.obj.options[i].selected = this.obj.options[i].value == valore[c];
			}
		},
		
	//-------------------------------------------------------------------------
	verificaObbligo: function() 
		{
		if (!this.obbligatorio)
			return true;
		return this.dammiValore().length > 0;
		}
		
	
	}
);
