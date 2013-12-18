//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe walogico: input HTML di tipo si/no, renderizzato con un checkbox.
* 
* Il controllo applicativo assume sempre i valori 0/1
* 
* @class walogico
* @extends wacontrollo
*/
var walogico = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'logico',
	
	//-------------------------------------------------------------------------
	dammiValore: function() 
		{
		return this.obj.checked ? 1 : 0;
		},
		
	//-------------------------------------------------------------------------
	// inserisce un valore logico nel controllo
	mettiValore: function(valore) 
		{
		this.obj.checked = valore ? 1 : 0;
		},
		
	//-------------------------------------------------------------------------
	verificaObbligo: function() 
		{
		if (!this.obbligatorio)
			return true;
		return this.obj.checked;
		}
		
	}
);
