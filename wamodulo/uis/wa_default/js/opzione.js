//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe waopzione: controllo di scelta mediante radio button
* 
* @class waopzione
* @extends wacontrollo
*/

var waopzione = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'opzione',
	
	//-------------------------------------------------------------------------
	// restituisce il valore logico del controllo
	dammiValore: function() 
		{
		for (var i = 0; i < this.obj.length; i++)
			{
			if (this.obj[i].checked)
				return this.obj[i].value;
			}
		return false;
		},
		
	//-------------------------------------------------------------------------
	// inserisce un valore logico nel controllo
	mettiValore: function(valore) 
		{
		for (var i = 0; i < this.obj.length; i++)
			this.obj[i].checked = this.obj[i].value == valore;
		},
		
	//-------------------------------------------------------------------------
	// associa un evento al controllo
	aggiungiEvento: function (nomeEvento, evento)
		{
		for (var i = 0; i < this.obj.length; i++)
			this.obj[i][nomeEvento] = evento;
		},
	
	//-------------------------------------------------------------------------
	// verifica se un controllo fisico appartiene al controllo logico
	miAppartiene: function (obj)
		{
		for (var i = 0; i < this.obj.length; i++)
			return obj.name == this.obj[i].name;
		},
	
	//-------------------------------------------------------------------------
	// a seconda dello stato definisce la classe css di un controllo
	renderizza: function() 
		{
			
		for (var i = 0; i < this.obj.length; i++)
			{
			this.obj[i].style.visibility = this.visibile ? '' : 'hidden';
			document.getElementById("wamodulo_lblradio_" + this.nome + "[" + i + "]").style.visibility = this.obj[i].style.visibility;
			}
			
		for (var i = 0; i < this.obj.length; i++)
			this.obj[i].disabled = this.solaLettura;
			
		var className = (this.obbligatorio ? "wamodulo_obbligatorio" : '');
		for (var i = 0; i < this.obj.length; i++)
			this.obj[i].className = className;
								
		}
	
	}
);
