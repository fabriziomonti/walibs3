//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wadataora: controllo composito per l'input di una data/ora
* 
* @class wadataora
* @extends wadata
*/
var wadataora = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wadata,

	//-------------------------------------------------------------------------
	// proprieta'
	tipo: 'dataora',
	
	/**
	 * oggetto HTML fisico utilizzato per selezionare l'ora
	 * @type HTML_object
	 */
	selezioneOra: null,
	
	/**
	 * oggetto HTML fisico utilizzato per selezionare i minuti
	 * @type HTML_object
	 */
	selezioneMin: null,
	
	/**
	 * oggetto HTML fisico utilizzato per selezionare i secondi
	 * @type HTML_object
	 */
	selezioneSec: null,
	
	//-------------------------------------------------------------------------
	//initialization
	initialize: function(modulo, nome, valore, visibile, solaLettura, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.parent(modulo, nome, valore, visibile, solaLettura, obbligatorio);
		this.selezioneOra = this.modulo.obj.elements["wamodulo_ora_" + this.nome];
		this.selezioneMin = this.modulo.obj.elements["wamodulo_min_" + this.nome];
		this.selezioneSec = this.modulo.obj.elements["wamodulo_sec_" + this.nome];
		},
		
	//-------------------------------------------------------------------------
	// restituisce il valore logico del controllo
	dammiValore: function() 
		{
		if (this.selezioneGiorno.value == '' || this.selezioneMese.value == '' || this.selezioneAnno == '' ||
			this.selezioneOra.value == '' || this.selezioneMin.value == '' ||
			(this.selezioneSec && this.selezioneSec.value == '')
			)
			return false;
//		return new Date(this.selezioneAnno.value, this.selezioneMese.value - 1, this.selezioneGiorno.value,
//						this.selezioneOra.value, this.selezioneMin.value, this.selezioneSec ? this.selezioneSec.value : 0);
		var retval = new Date();
		retval.setUTCFullYear(this.selezioneAnno.value, this.selezioneMese.value - 1, this.selezioneGiorno.value);
		retval.setUTCHours(this.selezioneOra.value, this.selezioneMin.value, this.selezioneSec ? this.selezioneSec.value : 0);
		return retval;
		},
		
	//-------------------------------------------------------------------------
	// inserisce un valore logico nel controllo
	mettiValore: function(valore) 
		{
		this.parent(valore);
		if (!valore || valore == 0 || valore == '')
			{
			this.selezioneOra.value = this.selezioneMin.value = '';
			if (this.selezioneSec)
				this.selezioneSec.value = '';
			}
		else
			{
			this.selezioneOra.value = valore.getUTCHours(); 
			this.selezioneMin.value = valore.getUTCMinutes(); 
			if (this.selezioneSec)
				this.selezioneSec.value = valore.getUTCSeconds();
			}
		},
		
	//-------------------------------------------------------------------------
	verificaForma: function() 
		{
		if (!this.dammiValore())
			return true;
			
//		var testDate = new Date(this.selezioneAnno.value, this.selezioneMese.value - 1, this.selezioneGiorno.value,
//						this.selezioneOra.value, this.selezioneMin.value, this.selezioneSec ? this.selezioneSec.value : 0);
		var testDate = new Date();
		testDate.setUTCFullYear(this.selezioneAnno.value, this.selezioneMese.value - 1, this.selezioneGiorno.value);
		testDate.setUTCHours(this.selezioneOra.value, this.selezioneMin.value, this.selezioneSec ? this.selezioneSec.value : 0);
		return this.selezioneGiorno.value == testDate.getUTCDate() &&
					this.selezioneMese.value == (testDate.getUTCMonth() + 1) &&
					this.selezioneAnno.value == testDate.getUTCFullYear() &&
					this.selezioneOra.value == testDate.getUTCHours() &&
					this.selezioneMin.value == testDate.getUTCMinutes() &&
					(this.selezioneSec ? this.selezioneSec.value == testDate.getUTCSeconds() : true);
		},
		
	//-------------------------------------------------------------------------
	// a seconda dello stato definisce la classe css di un controllo
	renderizza: function() 
		{
		this.parent();
		
		this.selezioneOra.style.visibility = this.selezioneMin.style.visibility = this.visibile ? '' : 'hidden';
		if (this.selezioneSec)
			this.selezioneSec.style.visibility = this.selezioneOra.style.visibility;
		
		this.selezioneOra.disabled = this.selezioneMin.disabled = this.solaLettura;
		if (this.selezioneSec)
			this.selezioneSec.disabled = this.selezioneOra.disabled;
		
		this.selezioneOra.className = this.selezioneMin.className = 
								(this.obbligatorio ? "wamodulo_obbligatorio" : '');
		if (this.selezioneSec)
			this.selezioneSec.className = this.selezioneOra.className;
		},
		
	//-------------------------------------------------------------------------
	// associa un evento al controllo
	aggiungiEvento: function (nomeEvento, evento)
		{
		this.parent(nomeEvento, evento);
		this.selezioneOra[nomeEvento] = evento;
		this.selezioneMin[nomeEvento] = evento;
		if (this.selezioneSec)
			this.selezioneSec[nomeEvento] = evento;
		},
	
	//-------------------------------------------------------------------------
	// verifica se un controllo fisico appartiene al controllo logico
	miAppartiene: function (obj)
		{
		if  (this.parent(obj) ||
				obj.name == this.selezioneOra.name ||
				obj.name == this.selezioneMin.name)
			return true;
		if (this.selezioneSec)
			return obj.name == this.selezioneSec.name;
		return false;
		}
	
	}
);
