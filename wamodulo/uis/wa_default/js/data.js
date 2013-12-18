//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wadata: controllo composito per l'input di una data
* 
* @class wadata
* @extends wacontrollo
*/
var wadata = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wacontrollo,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo			: 'data',
	
	/**
	 * oggetto HTML fisico utilizzato per contenere il calendario
	 * @type HTML_object
	 */
	divCalendario	: null,
	
	/**
	 * oggetto HTML fisico utilizzato per selezionare il giorno
	 * @type HTML_object
	 */
	selezioneGiorno	: null,
	
	/**
	 * oggetto HTML fisico utilizzato per selezionare il mese
	 * @type HTML_object
	 */
	selezioneMese	: null,
	
	/**
	 * oggetto HTML fisico utilizzato per selezionare l'anno
	 * @type HTML_object
	 */
	selezioneAnno	: null,
	
	/**
	 * oggetto HTML fisico utilizzato per mostrare il calendario mensile
	 * @type HTML_object
	 */
	bottoneCalMese	: null,
	
	/**
	 * oggetto HTML fisico utilizzato per mostrare il calendario annuale
	 * @type HTML_object
	 */
	bottoneCalAnno	: null,
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.selezioneGiorno = this.modulo.obj.elements["wamodulo_giorno_" + this.nome];
		this.selezioneMese = this.modulo.obj.elements["wamodulo_mese_" + this.nome];
		this.selezioneAnno = this.modulo.obj.elements["wamodulo_anno_" + this.nome];
		this.bottoneCalMese = this.modulo.obj.elements["wamodulo_mesecal_" + this.nome];
		this.bottoneCalAnno = this.modulo.obj.elements["wamodulo_annocal_" + this.nome];
		this.divCalendario = document.getElementById("overDiv");
		if (!this.divCalendario)
			{
			this.divCalendario = document.createElement('div');
   			this.divCalendario.setAttribute('id', "overDiv");
   			this.divCalendario.setAttribute('class', "calpop");
   			document.body.appendChild(this.divCalendario);
			}
		},
		
	//-------------------------------------------------------------------------
	// restituisce il valore logico del controllo
	dammiValore: function() 
		{
		if (this.selezioneGiorno.value == '' || this.selezioneMese.value == '' || this.selezioneAnno.value == '')
			return false;
//		return new Date(this.selezioneAnno.value, this.selezioneMese.value - 1, this.selezioneGiorno.value);
		var retval = new Date();
		retval.setUTCFullYear(this.selezioneAnno.value, this.selezioneMese.value - 1, this.selezioneGiorno.value);
		return retval;
		},
		
	//-------------------------------------------------------------------------
	// inserisce un valore logico nel controllo
	mettiValore: function(valore) 
		{
		if (!valore || valore == 0 || valore == '')
			this.selezioneGiorno.value = this.selezioneMese.value = this.selezioneAnno.value = '';
		else
			{
			this.selezioneGiorno.value = valore.getUTCDate(); 
			this.selezioneMese.value = valore.getUTCMonth() + 1; 
			this.selezioneAnno.value = valore.getUTCFullYear();
			}
		},
		
	//-------------------------------------------------------------------------
	verificaForma: function() 
		{
		if (!this.dammiValore())
			return true;
			
//		var testDate = new Date(this.selezioneAnno.value, this.selezioneMese.value - 1, this.selezioneGiorno.value);
		var testDate = new Date();
		testDate.setUTCFullYear(this.selezioneAnno.value, this.selezioneMese.value - 1, this.selezioneGiorno.value);
		return this.selezioneGiorno.value == testDate.getUTCDate() &&
					this.selezioneMese.value == (testDate.getUTCMonth() + 1) &&
					this.selezioneAnno.value == testDate.getUTCFullYear();
		},
		
	//-------------------------------------------------------------------------
	// a seconda dello stato definisce la classe css di un controllo
	renderizza: function() 
		{
		this.selezioneGiorno.style.visibility = this.selezioneMese.style.visibility = this.selezioneAnno.style.visibility = this.visibile ? '' : 'hidden';
		if (this.bottoneCalMese)
			this.bottoneCalMese.style.visibility = this.selezioneGiorno.style.visibility;
		if (this.bottoneCalAnno)
			this.bottoneCalAnno.style.visibility = this.selezioneGiorno.style.visibility;

		this.selezioneGiorno.disabled = this.selezioneMese.disabled = this.selezioneAnno.disabled = this.solaLettura;
		if (this.bottoneCalMese)
			this.bottoneCalMese.disabled = this.selezioneGiorno.disabled;
		if (this.bottoneCalAnno)
			this.bottoneCalAnno.disabled = this.selezioneGiorno.disabled;
			
		this.selezioneGiorno.className = 
			this.selezioneMese.className = 
			this.selezioneAnno.className = (this.obbligatorio ? "wamodulo_obbligatorio" : '');
								
		},
		
	//-------------------------------------------------------------------------
	// associa un evento al controllo
	aggiungiEvento: function (nomeEvento, evento)
		{
		this.selezioneGiorno[nomeEvento] = evento;
		this.selezioneMese[nomeEvento] = evento;
		this.selezioneAnno[nomeEvento] = evento;
		},
	
	//-------------------------------------------------------------------------
	// verifica se un controllo fisico appartiene al controllo logico
	miAppartiene: function (obj)
		{
		return obj.name == this.selezioneGiorno.name ||
				obj.name == this.selezioneMese.name ||
				obj.name == this.selezioneAnno.name;
		},
	
	//-------------------------------------------------------------------------
	// simula un evento sul controllo
	simulaEvento: function(tipoEvento) 
		{
		var event;
		if (document.createEvent) 
			{
			event = document.createEvent("HTMLEvents");
			event.initEvent(tipoEvento, true, true);
			this.selezioneGiorno.dispatchEvent(event);
			} 
		else 
			{
			event = document.createEventObject();
			event.eventType = tipoEvento;
			this.selezioneGiorno.fireEvent("on" + event.eventType, event);
			}
		
		}
		
	
	
	}
);
