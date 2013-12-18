//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe waselezione: select con possibilità di selezione singola
* 
* @class waselezione
* @extends wacontrollo
*/
var waselezione = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'selezione',
	
	//-------------------------------------------------------------------------
	/**
	 * svuota il controllo di tutte le sue opzioni
	 */
	svuota: function() 
		{
		var ultimaOptDaEliminare = 0;
		if (this.obj.options[0])
			{
			if (this.obj.options[0].value == '')
				ultimaOptDaEliminare = 1;
			}
		for (var i = this.obj.options.length - 1; i >= ultimaOptDaEliminare; i--)
			this.obj.options[i] = null;
		this.obj.selectedIndex = 0;
		
		return this.obj;
		},
		
	//-------------------------------------------------------------------------
	/**
	 * riempie il controllo con le opzioni date
	 * 
	 * @param {object} values dizionario delle opzioni; la chiave di ogni 
	 * elemento del dizionario sarà il valore dell'opzione; il valore di ogni
	 * elemento sarà il testo dell'opzione
	 * @param {string} toSelect eventuale valore dell'opzione da rendere attiva 
	 * (selezionata)
	 */
	riempi: function(values, toSelect) 
		{
		var toret = false;
		for (var key in values)
			{
			if (values[key] != undefined && values[key] != 'undefined')
				{
				this.obj.options[this.obj.options.length] = new Option(values[key], key);
				toret = values[key] == toSelect;
				}
			}
		this.obj.value = toSelect;
		return toret;
			
		}
		
	}
);
