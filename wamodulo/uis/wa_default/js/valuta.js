//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wavaluta: controllo per l'input di numero decimale
* 
* @class wavaluta
* @extends wacontrollo
*/
var wavaluta = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo		: 'valuta',
	nrDecimali	: 2,
	myonkeyup	: function onkeyup(event) {this.form.wamodulo.controlli[this.name].alTastoSu(event);},
	myonfocus	: function onfocus(event) {this.form.wamodulo.controlli[this.name].alFuoco(event);},
	myonblur	: function onblur(event) {this.form.wamodulo.controlli[this.name].allaPerditaFuoco(event);},
	onkeyup		: false,
	onfocus		: false,
	onblur		: false,
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.nrDecimali = this.obj.nrDecimali ? this.obj.nrDecimali : this.nrDecimali;
		this.obj.onkeyup = this.myonkeyup;
		this.obj.onfocus = this.myonfocus;
		this.obj.onblur = this.myonblur;
		
		},
		
	//-------------------------------------------------------------------------
	alTastoSu: function(event) 
		{
		var re = /^[0-9-'.'-',']*$/;
		if (!re.test(this.obj.value)) 
			this.obj.value = this.obj.value.replace(/[^0-9-'.'-',']/g,"");	
		this.eventoApplicazione(event, "onkeyup");
			
		},
		
	//-------------------------------------------------------------------------
	alFuoco: function(event) 
		{
		this.deformatta();
		this.eventoApplicazione(event, "onfocus");
		},
		
	//-------------------------------------------------------------------------
	allaPerditaFuoco: function(event) 
		{
		this.formatta();
		this.eventoApplicazione(event, "onblur");
		},
		
	//-------------------------------------------------------------------------
	// restituisce il valore logico del controllo
	dammiValore: function() 
		{
		var val = this.obj.value;
		val = val.replace(/\./g,"");
		val = val.replace(/\,/g,".");
		return this.roundFloat(val);
		},
		
	//-------------------------------------------------------------------------
	// inserisce un valore logico nel controllo
	mettiValore: function(valore) 
		{
		this.obj.value = this.roundFloat(valore);
		this.formatta();
		},
		
	//-------------------------------------------------------------------------
	roundFloat: function (nr)
		{
		var exp = Math.pow(10, this.nrDecimali);
		return Math.round(nr * exp) / exp;
		},
		
	//-------------------------------------------------------------------------
	// formatta il valore nel controllo
	formatta: function() 
		{
		if (this.obj.value == '')
			return;
			
		var elems = new Array();
		if (this.obj.value.indexOf(",") >= 0)
			elems = this.obj.value.split(",");
		else if (this.obj.value.indexOf(".") >= 0)
			elems = this.obj.value.split(".");
		else
			elems[0] = this.obj.value;
		var interi = elems[0] == '' ? 0 : elems[0];
		var decimali = elems[1] ? elems[1] : '';
		for (i = interi.length - 3; i > 0; i -= 3)
			interi = interi.substring (0 , i) + "." + interi.substring (i);
	    if (decimali.length > this.nrDecimali)
	    	decimali = decimali.substr(0, this.nrDecimali);
		for (i = decimali.length; i < this.nrDecimali; i++)
	      decimali += "0";
		this.obj.value = interi + "," + decimali;
		},
		
	//-------------------------------------------------------------------------
	// deformatta il valore nel controllo
	deformatta: function() 
		{
		this.obj.value = this.obj.value.replace(/\./g,"");
		},
		
	//-------------------------------------------------------------------------
	verificaObbligo: function() 
		{
		if (!this.obbligatorio)
			return true;
		return this.trim(this.obj.value) != '';
		}
		
	}
);




	