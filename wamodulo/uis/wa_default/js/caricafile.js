//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wacaricafile: controllo composito per la gestione di caricamento,
* visualizzazione, eliminazione di un file
* 
* @class wacaricafile
* @extends wacontrollo
*/
var wacaricafile = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'caricafile',
	
	/**
	 * oggetto HTML fisico utilizzato per mandare in visualizzazione il file
	 * @type HTML_object
	 */
	bottoneVedi: null,
	
	/**
	 * oggetto HTML fisico utilizzato come etichetta in corrispondenza del
	 * checkbox di richiesta eliminazione del file
	 * @type HTML_object
	 */
	etichettaElimina: null,

	/**
	 * oggetto HTML fisico (checkbox) utilizzato per com unicare al server che è 
	 * stata richiesta l'eliminazione del file
	 * @type HTML_object
	 */
	logicoElimina: null,
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.bottoneVedi = this.modulo.obj.elements["wamodulo_mostrafile_" + this.nome];
		this.etichettaElimina = document.getElementById("wamodulo_etichettaeliminafile_" + this.modulo.nome + "_"+ this.nome);
		this.logicoElimina = this.modulo.obj.elements["wamodulo_logicoeliminafile_" + this.modulo.nome + "[" + this.nome + "]"];
		this.logicoElimina.onclick = function onclick(event) 
			{
			var nome = this.name.substr(this.name.indexOf("[") + 1); 
			nome = nome.substr(0, nome.length - 1);
			this.form.wamodulo.controlli[nome].renderizza();
			}
		
		},
		
	//-------------------------------------------------------------------------
	dammiValore: function() 
		{
		if (this.logicoElimina.checked)
			return '';
		if (this.obj.value)
			return this.obj.value;
			
		return this.valore;
		},

		
	//-------------------------------------------------------------------------
	// inserisce un valore logico nel controllo
	mettiValore: function(valore) 
		{
		},
		
	//-------------------------------------------------------------------------
	// a seconda dello stato definisce la classe css di un controllo
	renderizza: function() 
		{
		// oggetto principale
		this.parent();
		this.obj.disabled = this.solaLettura || this.logicoElimina.checked; 

		// bottone visualizzazione file esistente
		this.bottoneVedi.style.visibility = this.visibile && this.valore ? '' : 'hidden'; 
		
		// etichetta elimina file
		// la funzione di eliminazione e' visibile solo se:
		// - l'intero controllo e' visibile
		// - se il controllo non e' solo lettura
		// - se il controllo non e' obbligatorio
		// - se il controllo e' stato valorizzato in partenza
		var visibileElimina = this.visibile && (!this.solaLettura) && (!this.obbligatorio) && this.valore;
		this.etichettaElimina.style.visibility = visibileElimina ? '' : 'hidden';
			
		// check elimina file
		this.logicoElimina.style.visibility = visibileElimina ? '' : 'hidden';
		this.logicoElimina.disabled = this.solaLettura; 
		}
		

	//-------------------------------------------------------------------------
	// metodo chiamato quando viene selezionato/deselezionato il checkbox di
	// richiesta eliminazione
//	premutoLogicoEliminazione: function() 
//		{
//		this.renderizza();
//		}
		
	
	
	}
);
