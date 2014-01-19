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
	 * @param {boolean} orderByKey per default il controllo viene riempito in 
	 * ordine alfabetico dei valori, non delle chiavi. Se questo parametro
	 * vale true, verrà mantenuto l'ordine delle chiavi
	 */
	riempi: function(values, toSelect, orderByKey) 
		{
		var toret = false;
		
		if (!orderByKey)
			{
			// cio' che arriva in input è un dizionario: per avere l'ordinamento
			// secondo i valori e non secondo le chiavi, lo trasformiamo in 
			// array
			var myValues = [];
			for (var key in values)
				{
				myValues[myValues.length] = {'key': key, 'value': values[key]};
				}
			myValues = this.sortByKey(myValues, "value");
			for (var li = 0; li < myValues.length; li++)
				{
				if (myValues[li]["value"] != undefined && myValues[li]["value"] != 'undefined')
					{
					this.obj.options[this.obj.options.length] = new Option(myValues[li]["value"], myValues[li]["key"]);
					toret = myValues[li]["key"] == toSelect;
					}
				}
			}
		else
			{
			for (var key in values)
				{
				if (values[key] != undefined && values[key] != 'undefined')
					{
					this.obj.options[this.obj.options.length] = new Option(values[key], key);
					toret = key == toSelect;
					}
				}
			}
			
		this.obj.value = toSelect;
		return toret;
		},
		
	//-------------------------------------------------------------------------
	/**
	 * @ignore
	 * 
	 * funzione di servizio per ordinare un array di oggetti
	 */
	 sortByKey : function (array, key) 
		{
		return array.sort(function(a, b) 
			{
			var x = a[key].toLowerCase();
			var y = b[key].toLowerCase();
			return ((x < y) ? -1 : ((x > y) ? 1 : 0));
			}
		);
		}	
	
		
	}
);
