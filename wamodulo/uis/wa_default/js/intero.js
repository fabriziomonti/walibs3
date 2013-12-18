//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe waintero: input HTML destinato a contenere un numero intero
* 
* @class waintero
* @extends wacontrollo
*/

var waintero = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'intero',
	myonkeyup	: function onkeyup(event) {this.form.wamodulo.controlli[this.name].alTastoSu(event);},
	onkeyup		: false,
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.obj.onkeyup = this.myonkeyup;
		
		},
		
	//-------------------------------------------------------------------------
	alTastoSu: function(event) 
		{
		var re = /^[0-9]*$/;
		if (!re.test(this.obj.value)) 
			this.obj.value = this.obj.value.replace(/[^0-9]/g,"");	
		this.eventoApplicazione(event, "onkeyup");
		},
		
	//-------------------------------------------------------------------------
	verificaObbligo: function() 
		{
		if (!this.obbligatorio)
			return true;
		return this.trim(this.obj.value) != '';
		},
		
	//-------------------------------------------------------------------------
	verificaForma: function() 
		{
		if ((this.obj.value * 2 / 2) == (this.obj.value + ''))
			return true;
		return false;
		}
		
	}
);
