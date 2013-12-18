//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wacaptcha: controllo di input di un codice di controllo captcha
* 
* @class wacaptcha
* @extends wacontrollo
*/
var wacaptcha = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'captcha',
	imgObj: null,		// e' l'immagine contenent la chiave
		
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.imgObj = document.getElementById(this.modulo.nome + "_captcha_img_" + this.nome);
		
		},
		
	//-------------------------------------------------------------------------
	// a seconda dello stato definisce la classe css di un controllo
	renderizza: function() 
		{
		// oggetto principale
		this.parent();

		// immagine
		this.imgObj.style.visibility = this.visibile ? '' : 'hidden'; 
		}
		

	}
);
