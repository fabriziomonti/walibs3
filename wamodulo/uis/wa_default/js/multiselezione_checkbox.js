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
		// in questo caso l'obj è il div che contiene i checkbox
		this.obj = document.getElementById(nome);
		
		},
	
	//-------------------------------------------------------------------------
	dammiValore: function() 
		{
		var retval = new Array();
		var selCntr = 0;
		
		for (var i= 0; i < this.modulo.obj.elements.length; i++)
			{
			if (this.modulo.obj.elements[i].name.substr(0, this.nome.length + 1) == this.nome + "[" &&
				this.modulo.obj.elements[i].checked)
				{
				retval[selCntr] = this.modulo.obj.elements[i].name.slice(this.nome.length + 1, -1);
				selCntr++;
				}
			}

		return retval;
		},
		
	//-------------------------------------------------------------------------
	// inserisce un valore logico nel controllo
	mettiValore: function(valore) 
		{
		for (var i= 0; i < this.modulo.obj.elements.length; i++)
			{
			if (this.modulo.obj.elements[i].name.substr(0, this.nome.length + 1) == this.nome + "[")
				this.modulo.obj.elements[i].checked = false;
			}
		for (var c = 0; c < valore.length; c++)
			{
			if (this.modulo.obj.elements[this.nome + "[" + valore[c] + "]"])
				this.modulo.obj.elements[this.nome + "[" + valore[c] + "]"].checked = true;
			}
		},
		
	//-------------------------------------------------------------------------
	/**
	 * svuota il controllo di tutte le sue opzioni
	 */
	svuota: function() 
		{
		this.obj.innerHTML = '';
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
				this.obj.innerHTML += "<div>\n" +
										"\t<input type='checkbox'" +
											" name='" + this.nome + "[" + key + "]'" +
											" id='" + this.nome + key + "'" + 
											(values[key] == toSelect ? " checked='checked'" : "") + " />\n" + 
										"\t<div>\n" + 
										"\t\t<label for='" + this.nome + key + "'>" + values[key] + "</label>\n" + 
										"\t</div>\n" + 
										"</div>\n";
				}
			}

		},
		
	//-------------------------------------------------------------------------
	verificaObbligo: function() 
		{
		if (!this.obbligatorio)
			return true;
		return this.dammiValore().length > 0;
		},
		
	
	//-------------------------------------------------------------------------
	/**
	* a seconda dello stato /definisce la classe css di un controllo(visibile, 
	* solaLettura, obbligatorio) renderizza il controllo
	*/
	renderizza: function() 
		{
		this.parent();
		
		for (var i= 0; i < this.modulo.obj.elements.length; i++)
			{
			if (this.modulo.obj.elements[i].name.substr(0, this.nome.length + 1) == this.nome + "[")
				this.modulo.obj.elements[i].disabled = this.solaLettura;
			}
		
		this.obj.className = "wamodulo_multiselezione_checkbox" + (this.obbligatorio ? " wamodulo_obbligatorio" : '');
		},
		
	//-------------------------------------------------------------------------
	// verifica se un controllo fisico appartiene al controllo logico
	miAppartiene: function (obj)
		{
		return obj.name.substr(0, this.nome.length + 1) == this.nome + "[";
		},
	
	//-------------------------------------------------------------------------
	// associa un evento al controllo
	aggiungiEvento: function (nomeEvento, evento)
		{
		for (var i= 0; i < this.modulo.obj.elements.length; i++)
			{
			if (this.modulo.obj.elements[i].name.substr(0, this.nome.length + 1) == this.nome + "[")
				this.modulo.obj.elements[i][nomeEvento] = evento;
			}
		}
	
	}
);
