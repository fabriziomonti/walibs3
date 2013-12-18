/**
* @package waTabella
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe wacolonna: contiene attributi e metodi relativi a una colonna di una
* tabella di classe {@link watabella}.
* 
* I metodi di questa classe vengono di fatto usati solo internamente e solo
* in caso di editing 
* 
* @class wacolonna
*/
var wacolonna = new Class
(
	{
	//-------------------------------------------------------------------------
	// proprieta'
	
	/**
	 * oggetto di classe {@link watabella} a cui la colonna appartiene
	 * @type watabella
	 */
	tabella			: null,
	
	/**
	 *nome della colonna
	 * @type string
	 */
	nome			: '',
	
	/**
	 * intestazione della colonna
	 * @type  string
	 */
	etichetta			: '',
	
	/**
	 * tipo del campo; corrisponde alla proprietà tipoCampo della
	 * classe waColonna in PHP, la quale a sua volta discende (se la tabella è
	 * associata a un recordset) dal risultato del emtodo tipoCampo di waRigheDB 
	 * @type string
	 */
	tipo_campo			: '',
	
	/**
	 * tipo del controllo di input; corrisponde alla proprietà inputTipo della
	 * classe waColonna in PHP
	 * @type string
	 */
	tipo_input			: '',

	/**
	 * indica se la colonna, in fase di input, debba essere considerata readonly
	 * (in questa versione della classe non gestito)
	 * @type boolean
	 */
	solaLettura		: false,

	/**
	 * indica se la colonna, in fase di input, debba essere considerata mandatory
	 * @type boolean
	 */
	obbligatorio	: false,
	
	//-------------------------------------------------------------------------
	/**
	 * inizializzazione (costruttore)
	 * 
	 * @param {watabella} tabella valorizza la proprietà {@link tabella}
	 * @param {string} nome valorizza la proprietà {@link nome}
	 * @param {string} etichetta valorizza la proprietà {@link etichetta}
	 * @param {string} tipo_campo valorizza la proprietà {@link tipo_campo}
	 * @param {string} tipo_input valorizza la proprietà {@link tipo_input}
	 * @param {boolean} obbligatorio valorizza la proprietà {@link obbligatorio}
	 */
	initialize: function(tabella, nome, etichetta, tipo_campo, tipo_input, obbligatorio) 
		{
		// definizione iniziale delle proprieta'
		this.tabella = tabella;
		this.nome = nome;
		this.etichetta = etichetta;
		this.tipo_campo = tipo_campo;
		this.tipo_input = tipo_input;
		this.obbligatorio = obbligatorio == 1 ? true : false;
		
		this.tabella.colonne[this.nome] = this;
		
		},
		
	//-------------------------------------------------------------------------
	/**
	 * alla digitazione di un tasto sul controllo di input di tipo intero viene 
	 * richiamato questo evento
	 * @ignore
	 */
	intero_onkeyup: function(idRiga) 
		{
		var ctrl = this.tabella.obj.elements[this.nome + "[" + idRiga + "]"];
		var re = /^[0-9]*$/;
		if (!re.test(ctrl.value)) 
			ctrl.value = ctrl.value.replace(/[^0-9]/g,"");	
		},
		
	//-------------------------------------------------------------------------
	// all'accesso al controllo di input di tipo valuta viene richiamato questo 
	// evento
	valuta_onfocus: function(idRiga) 
		{
		var ctrl = this.tabella.obj.elements[this.nome + "[" + idRiga + "]"];
		ctrl.value = ctrl.value.replace(/\./g,"");
		},
		
	//-------------------------------------------------------------------------
	// alla digitazione di un tasto sul controllo di input di tipo valuta viene 
	// richiamato questo evento
	valuta_onkeyup: function(idRiga) 
		{
		var ctrl = this.tabella.obj.elements[this.nome + "[" + idRiga + "]"];
		var re = /^[0-9-',']*$/;
		if (!re.test(ctrl.value)) 
			ctrl.value = ctrl.value.replace(/[^0-9-',']/g,"");	
		},
		
	//-------------------------------------------------------------------------
	// formatta il valore nel controllo di tipo valuta alla perdita del fuoco
	valuta_onblur: function(idRiga) 
		{
		var ctrl = this.tabella.obj.elements[this.nome + "[" + idRiga + "]"];
		if (ctrl.value == '')
			return;
			
		var elems = new Array();
		if (ctrl.value.indexOf(",") >= 0)
			elems = ctrl.value.split(",");
		else if (ctrl.value.indexOf(".") >= 0)
			elems = ctrl.value.split(".");
		else
			elems[0] = ctrl.value;
		var interi = elems[0] == '' ? 0 : elems[0];
		var decimali = elems[1] ? elems[1] : '';
		for (i = interi.length - 3; i > 0; i -= 3)
			interi = interi.substring (0 , i) + "." + interi.substring (i);
	    if (decimali.length > 2)
	    	decimali = decimali.substr(0, 2);
		for (i = decimali.length; i < 2; i++)
	      decimali += "0";
		ctrl.value = interi + "," + decimali;
		},
		
	//-------------------------------------------------------------------------
	// verifica il contenuto di un controllo di input generico
	verificaInput_testo: function(idRiga) 
		{
			
		var ctrl = this.tabella.obj.elements[this.nome + "[" + idRiga + "]"];
		if (this.obbligatorio && !ctrl.value)
			return "id " + idRiga + " - " + this.etichetta + ": il campo e' obbligatorio e non e' stato valorizzato\n";
		
		// se il campo e' stato modificato accendiamo il checkbox di modifica
		// del record
		if (this.tabella.righe[idRiga].campi[this.nome] != ctrl.value)
			{
			this.tabella.obj.elements["watbl_input_mod_chk[" + idRiga + "]"].checked = true;
			this.tabella.righe[idRiga].campi[this.nome] = ctrl.value;
			}
			
		return '';
		},
		
	//-------------------------------------------------------------------------
	// verifica il contenuto di un controllo di input logico (checkbox)
	verificaInput_logico: function(idRiga) 
		{
		var ctrl = this.tabella.obj.elements[this.nome + "[" + idRiga + "]"];
		if (this.obbligatorio && !ctrl.checked)
			return "id " + idRiga + " - " + this.etichetta + ": il campo e' obbligatorio e non e' stato valorizzato\n";
		
		// se il campo e' stato modificato accendiamo il checkbox di modifica
		// del record
		if (this.tabella.righe[idRiga].campi[this.nome] != (ctrl.checked ? "1" : "0"))
			{
			this.tabella.obj.elements["watbl_input_mod_chk[" + idRiga + "]"].checked = true;
			this.tabella.righe[idRiga].campi[this.nome] = ctrl.checked  ? "1" : "0";
			}
			
		return '';
		},
		
	//-------------------------------------------------------------------------
	// verifica il contenuto di un controllo di input di tipo valuta
	verificaInput_valuta: function(idRiga) 
		{
		var ctrl = this.tabella.obj.elements[this.nome + "[" + idRiga + "]"];
		if (this.obbligatorio && !ctrl.value)
			return "id " + idRiga + " - " + this.etichetta + ": il campo e' obbligatorio e non e' stato valorizzato\n";
		
		// se il campo e' stato modificato accendiamo il checkbox di modifica
		// del record
		var valore_cmp = ctrl.value.replace(/\./g,"");
		valore_cmp = valore_cmp.replace(/\,/g,".");
		if (this.tabella.righe[idRiga].campi[this.nome] != valore_cmp)
			{
			this.tabella.obj.elements["watbl_input_mod_chk[" + idRiga + "]"].checked = true;
			this.tabella.righe[idRiga].campi[this.nome] = valore_cmp;
			}
			
		return '';
		},
		
	//-------------------------------------------------------------------------
	// verifica il contenuto di un controllo di input data
	verificaInput_data: function(idRiga) 
		{
		var ctrl = this.tabella.obj.elements[this.nome + "[" + idRiga + "]"];
		if (this.obbligatorio && !ctrl.value)
			return "id " + idRiga + " - " + this.etichetta + ": il campo e' obbligatorio e non e' stato valorizzato\n";

		if (ctrl.value)
			{
			var giorno = ctrl.value.substr(0, 2);
			var mese = ctrl.value.substr(3, 2);
			var anno = ctrl.value.substr(6, 4);
			var data = new Date(anno, mese - 1, giorno);
			giorno = data.getDate() < 10 ? "0" + data.getDate() : data.getDate();
			mese = data.getMonth() + 1 < 10 ? "0" + (data.getMonth() + 1) : data.getMonth() + 1;
			anno = data.getFullYear();
			if (giorno + "/" + mese + "/" + anno != ctrl.value)
				return "id " + idRiga + " - " + this.etichetta + ": formato data non valido (gg/mm/aaaa)\n";
			// se il campo e' stato modificato accendiamo il checkbox di modifica
			// del record; la data nei "campi" e' in formato epoch php
			if (data.getTime() != this.tabella.righe[idRiga].campi[this.nome] * 1000)
				{
				this.tabella.obj.elements["watbl_input_mod_chk[" + idRiga + "]"].checked = true;
				this.tabella.righe[idRiga].campi[this.nome] = data.getTime() / 1000;
				}
			}
		else if (this.tabella.righe[idRiga].campi[this.nome])
			{
			this.tabella.obj.elements["watbl_input_mod_chk[" + idRiga + "]"].checked = true;
			this.tabella.righe[idRiga].campi[this.nome] = '';
			}
			
		return '';
		},
		
	//-------------------------------------------------------------------------
	// verifica il contenuto di un controllo di input dataora
	verificaInput_dataora: function(idRiga) 
		{
		var ctrl = this.tabella.obj.elements[this.nome + "[" + idRiga + "]"];
		if (this.obbligatorio && !ctrl.value)
			return "id " + idRiga + " - " + this.etichetta + ": il campo e' obbligatorio e non e' stato valorizzato\n";

		if (ctrl.value)
			{
			var giorno = ctrl.value.substr(0, 2);
			var mese = ctrl.value.substr(3, 2);
			var anno = ctrl.value.substr(6, 4);
			var ora = ctrl.value.substr(11, 2);
			var min = ctrl.value.substr(14, 2);
			var data = new Date(anno, mese - 1, giorno, ora, min);
			giorno = data.getDate() < 10 ? "0" + data.getDate() : data.getDate();
			mese = data.getMonth() + 1 < 10 ? "0" + (data.getMonth() + 1) : data.getMonth() + 1;
			anno = data.getFullYear();
			ora = data.getHours() < 10 ? "0" + data.getHours() : data.getHours();
			min = data.getMinutes() < 10 ? "0" + data.getMinutes() : data.getMinutes();
			if (giorno + "/" + mese + "/" + anno + " " + ora + ":" + min != ctrl.value)
				return "id " + idRiga + " - " + this.etichetta + ": formato data non valido (gg/mm/aaaa hh:mm)\n";

			// se il campo e' stato modificato accendiamo il checkbox di modifica
			// del record; la data nei "campi" e' in formato epoch php
			if (data.getTime() != this.tabella.righe[idRiga].campi[this.nome] * 1000)
				{
				this.tabella.obj.elements["watbl_input_mod_chk[" + idRiga + "]"].checked = true;
				this.tabella.righe[idRiga].campi[this.nome] = data.getTime() / 1000;
				}				
			}
		else if (this.tabella.righe[idRiga].campi[this.nome])
			{
			this.tabella.obj.elements["watbl_input_mod_chk[" + idRiga + "]"].checked = true;
			this.tabella.righe[idRiga].campi[this.nome] = '';
			}
			
		return '';
		},
		
	//-------------------------------------------------------------------------
	// verifica il contenuto di un controllo di input ora
	verificaInput_ora: function(idRiga) 
		{
		var ctrl = this.tabella.obj.elements[this.nome + "[" + idRiga + "]"];
		if (this.obbligatorio && !ctrl.value)
			return "id " + idRiga + " - " + this.etichetta + ": il campo e' obbligatorio e non e' stato valorizzato\n";

		if (ctrl.value)
			{
			var ora = ctrl.value.substr(0, 2);
			var min = ctrl.value.substr(3, 2);
			var data = new Date(1980, 0, 1, ora, min);
			ora = data.getHours() < 10 ? "0" + data.getHours() : data.getHours();
			min = data.getMinutes() < 10 ? "0" + data.getMinutes() : data.getMinutes();
			if (ora + ":" + min != ctrl.value)
				return "id " + idRiga + " - " + this.etichetta + ": formato ora non valido (hh:mm)\n";

			// se il campo e' stato modificato accendiamo il checkbox di modifica
			// del record; la data nei "campi" e' in formato epoch php
			if (data.getTime() != this.tabella.righe[idRiga].campi[this.nome] * 1000)
				{
				this.tabella.obj.elements["watbl_input_mod_chk[" + idRiga + "]"].checked = true;
				this.tabella.righe[idRiga].campi[this.nome] = data.getTime() / 1000;
				}				
			}
		else if (this.tabella.righe[idRiga].campi[this.nome])
			{
			this.tabella.obj.elements["watbl_input_mod_chk[" + idRiga + "]"].checked = true;
			this.tabella.righe[idRiga].campi[this.nome] = '';
			}
			
		return '';
		},
		
	//-------------------------------------------------------------------------
	// verifica il contenuto di un controllo di input 
	verificaInput: function(idRiga) 
		{
		// se la colonna non ha input, non facciamo ovviamente niente
		if (!this.tipo_input)
			return '';

		// se e' gia' stata richiesta la cancellazione della riga, inutile 
		// controllare la validita' dell'input'
		if (this.tabella.obj.elements["watbl_input_del_chk[" + idRiga + "]"] &&
			this.tabella.obj.elements["watbl_input_del_chk[" + idRiga + "]"].checked)
			return '';
		
		if (this["verificaInput_" + this.tipo_input])
			return this["verificaInput_" + this.tipo_input](idRiga);
		else
			return this.verificaInput_testo(idRiga);
		}
		
	}
);

//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
 * classe che contiene proprietà e metodi di una riga appartenente a un oggetto
 * di classe {@link watabella}
 * 
 * @class wariga
 */
var wariga = new Class
(
	{
	//-------------------------------------------------------------------------
	// proprieta'
	
	/**
	 * oggetto di classe {@link watabella} a cui la riga appartiene
	 * @type watabella
	 */
	tabella		: null,
	
	/**
	 * identificativo della riga; corrisponde alla chiave univoca che deve
	 * sempre essere contenuta nella prima colonna della tabella
	 * @type string
	 */
	id			: '',
	
	/**
	 * oggetto HTML corrispondente al tag TR che contiene la riga
	 * @type HTML_TR_object
	 */
	obj			: null,
	
	/**
	 * oggetto in formato JSON contenente i valori non formattati di tutte le
	 * celle della riga 
	 * @type JSON_object
	 */
	campi		: null,
	
	/**
	 * valore boolean che indica se la riga è attualmente selezionata (click)
	 * o meno
	 * @type boolean
	 */
	selezionata	: false,

	//-------------------------------------------------------------------------
	/**
	 * inizializzazione (costruttore)
	 * 
	 * @param {watabella} tabella valorizza la proprietà {@link tabella}
	 * @param {string} id valorizza la proprietà {@link id}
	 * @param {JSON_object} campi valorizza la proprietà {@link campi}
	 */
	initialize: function(tabella, id, campi) 
		{
		// definizione iniziale delle proprieta'
		this.tabella = tabella;
		this.id = id;
		this.obj = document.getElementById("row_" + this.tabella.nome + "_" + id);
		this.campi = campi;
		this.tabella.righe[this.id] = this;
		},
		
	//-------------------------------------------------------------------------
	/**
	 * seleziona/deseleziona la riga
	 * 
	 * @param {boolean} siNo flag di selezione
	 */
	seleziona: function(siNo) 
		{
		if (siNo && this.tabella.selezioneEsclusiva)
			this.tabella.selezionaTutte(false);
		this.selezionata = siNo;
		this.renderizza();
		},
		
	//-------------------------------------------------------------------------
	/**
	 * inverte lo stato di selezione della riga
	 */
	cambiaStato: function() 
		{
		this.seleziona(!this.selezionata);
		},
		
	//-------------------------------------------------------------------------
	renderizza: function() 
		{
		this.obj.className = this.selezionata ? "selezionata" : '';
		},
		
	//-------------------------------------------------------------------------
	/**
	 * restituisce l'oggetto HTML TD della colonna data
	 * 
	 * @param {string} nomeColonna nome della colonna
	 */
	dammiCella: function(nomeColonna) 
		{
		// cerchiamo l'ordinale della colonna
		var rigaIntestazioni = document.getElementById(this.tabella.nome + "_intestazioni");
		var i = 0;
		for (; i < rigaIntestazioni.cells.length; i++)
			{
			if (rigaIntestazioni.cells[i].id == this.tabella.nome + "_" + nomeColonna)
				break;
			}
			
		return this.obj.cells[i];
		},
		
	//-------------------------------------------------------------------------
	/**
	 * restituisce il contenuto HTML (innerHTML) del TD della colonna data
	 * 
	 * @param {string} nomeColonna nome della colonna
	 */
	dammiContenutoCella: function(nomeColonna) 
		{
		var cella = this.dammiCella(nomeColonna);
		if (!cella)
			return false;
			
		return cella.innerHTML;
		},
		
	//-------------------------------------------------------------------------
	/**
	 * modifica il contenuto HTML (innerHTML) del TD della colonna data con il
	 * valore dato
	 * 
	 * @param {string} nomeColonna nome della colonna
	 * @param {string} nuovoContenuto valore da impostare
	 */
	modificaContenutoCella: function(nomeColonna, nuovoContenuto) 
		{
		var cella = this.dammiCella(nomeColonna);
		if (!cella)
			return false;
			
		cella.innerHTML = nuovoContenuto;
		return true;
		},
		
  	//---------------------------------------------------------------------------
	/**
	 * metodo invocato a fronte di richiesta di azione
  	 * "Vedi" (ossia la visualizzazione del dettaglio della riga).
	 * 
	 * Per default, l'azione apre la pagina del modulo (proprietà paginaModulo
	 * di waTabella) passandole in GET l'identificativo della riga e il codice 
	 * operazione di vsualizzazione (una delle defines di waModulo)
	 */
	vediDettaglio: function ()
		{
		this.tabella.azione_Vedi(this.id);
		},
			
  	//---------------------------------------------------------------------------
	/**
	 * metodo invocato a fronte di richiesta di azione
  	 * "Modifica" (ossia la modifica della riga).
	 * 
	 * Per default, l'azione apre la pagina del modulo (proprietà paginaModulo
	 * di waTabella) passandole in GET l'identificativo della riga e il codice 
	 * operazione di modifica (una delle defines di waModulo)
	 */
	modifica: function ()
		{
		this.tabella.azione_Modifica(this.id);
		},
			
  	//---------------------------------------------------------------------------
	/**
	 * metodo invocato a fronte di richiesta di azione
  	 * "Elimina" (ossia la cancellazione della riga).
	 * 
	 * Per default, l'azione apre la pagina del modulo (proprietà paginaModulo
	 * di waTabella) passandole in GET l'identificativo della riga e il codice 
	 * operazione di eliminazione (una delle defines di waModulo)
	 */
	elimina: function ()
		{
		this.tabella.azione_Elimina(this.id);
		}
			

	}
);

//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
/**
* classe watabella: contiene attributi e metodi relativi a una tabella waLibs.
* 
* La struttura di questa classe prevede che:
*
* <ul>
* <li>
*	per ogni tabella venga creato un oggetto di classe watabella con nome 
* 	uguale a quello della tabella (proprietà waTabella::nome in PHP) e aggiunto 
* 	come nuova proprietà dell'oggetto document. Questa operazione è svolta
* 	automaticamente dall'XSLT
*	<li>ogni azione, su riga o meno, invoca un metodo dell'oggetto del punto 
* 	precedente, metodo il cui nome è così composto:
* 	<p>
* 	azione_[nome_tabella]_[nome_azione]
*	</p>
* 	alle azioni su riga, verrà passato come parametro del metodo 
* 	l'identificativo univoco della riga stessa
*	<li> data la struttura esposta, affinchè il metodo relativo all'azione possa  
* 	essere eseguito è necessario che il metodo sia implementato da parte della
* 	applicazione che usa la tabella, e che la sua
* 	implementazione venga associata (ossia: fatta appartenere) all'oggetto di 
* 	classe watabella creato nel	primo punto
* 	<li>per le azioni standard (Nuovo/Vedi/Modifica/Elimina) l'associazione del
* 	metodo come descritto nel punto precedente è già svolta dalla classe base 
* 	che associa al nome del metodo comprensivo di [nome_tabella] la propria
* 	implementazione di default dei metodi/azioni; è naturalmente possibile fare 
* 	overload di questa implementazione di default implementando i metodi con i
* 	criteri descritti al punto precedente
* 	<li>analogamente a quanto descritto per le azioni, anche eventuali link
* 	presenti nelle celle verranno risolti dall'XSLT con la chiamata ad un metodo
* 	il cui nome è così composto:
* 	<p>
* 	link_[nome_tabella]_[nome_colonna]
*	</p>
* 	a cui verrà passato come parametro del metodo l'identificativo univoco della 
* 	riga stessa. Sarà sempre compito dell'applicazione che usa la tabella 
* 	implementare il metodo e associarlo (ossia: farlo appartenere) all'oggetto 
* 	di classe watabella creato nel	primo punto.
* </ul>
* Per quanto tutto ciò possa apparire complesso, la UI di default di 
* waApplicazione contiene già al suo interno un meccanismo automatico per fare
* tutto questo e renderlo disponibile in modo semplice al programmatore.
* 
* @class watabella
*/
var watabella = new Class
(
	{
	//-------------------------------------------------------------------------
	// proprieta'
	
	/**
	 * nome della tabella
	 * @type string
	 */
	nome			: '',
	
	/**
	 * nome della colonna/campo che contiene l'identificativo univoco della riga
	 * @type string
	 */
	nomeIdRiga			: '',
	
	/**
	 * flag che indica se la tabella deve gestire una selezione mutuamente esclusiva
	 * delle righe, oppure se possono essere selezionate più righe contemporaneamente
	 * @type boolean
	 */
	selezioneEsclusiva	: true,
	
	/**
	 * ogetto HTML contenente il <b>form</b>, che a propria volta contiene la
	 * tabella (HTML table), che devono essere gestiti mediante l'istanza
	 * della classe.
	 * 
	 * All'oggetto sarà aggiunta una proprietà di nome watabella che conterrà
	 * l'istanza corrente della classe watabella (this).
	 * @type HTML_form_object
	 */
	obj					: null,
	
	
	/**
	 * dizionario (array associativo) contenente gli oggetti di classe {@link wariga}
	 * appartenenti alla tabella; la chiave di ogni elemento è l'identificativo della
	 * riga (proprietà {@link wariga#id})
	 * @type object
	 */
	righe				: {},

	/**
	 * dizionario (array associativo) contenente gli oggetti di classe {@link wacolonna}
	 * appartenenti alla tabella; la chiave di ogni elemento è il nome della colonna
	 *  (proprietà {@link wacolonna#nome})
	 * @type object
	 */
	colonne				: {},
	
	// proprieta' per il colloquio con la pagina contenete il modulo deputato
	// alla manipolazione del record; a parte i primi due, che sono evidentemente
	// molto dipendenti dall'applicazione, i restanti valori hanno un default
	// corrispondente a quanto definito di default nella classe php waModulo,
	// e quindi, se non ci sono motivi particolari e ben chiari, non ha senso che
	// siano modificati a runtime
	
	/**
	 * nome della pagina PHP contenente il modulo per 
	 * inserire/vedere/modificare/eliminare il record contenuto in una riga.
	 * 
	 * Corrisponde alla proprietà paginaModulo della classe PHP waTabella
	 * @type string
	 */
	paginaModulo		: '', 		
	
	/**
	 * nome del parametro tramite cui comunicare alla pagina individuata da 
	 * {@link paginaModulo} quale operazione e' stata richiesta dall'utente.
	 * 
	 * Corrisponde alla define WAMODULO_CHIAVE_OPERAZIONE del package waModulo.
	 * @type string
	 */
	chiaveOperazione	: 'wamodulo_operazione',	
	
	/**
	 * valore del parametro tramite cui comunicare alla pagina individuata da 
	 * {@link paginaModulo} che l'utente ha richiesto la visualizzazione in 
	 * dettaglio di un record.
	 * 
	 * Corrisponde alla define WAMODULO_OPE_VIS_DETTAGLIO del package waModulo.
	 * @type int
	 */
	opeDettaglio		: 1, 
	
	/**
	 * valore del parametro tramite cui comunicare alla pagina individuata da 
	 * {@link paginaModulo} che l'utente ha richiesto la creazione di un nuovo 
	 * record.
	 * 
	 * Corrisponde alla define WAMODULO_OPE_INSERIMENTO del package waModulo.
	 * @type int
	 */
	opeInserimento		: 2, 
	
	/**
	 * valore del parametro tramite cui comunicare alla pagina individuata da 
	 * {@link paginaModulo} che l'utente ha richiesto la modifica di un record.
	 * 
	 * Corrisponde alla define WAMODULO_OPE_MODIFICA del package waModulo.
	 * @type int
	 */
	opeModifica			: 3, 
	
	/**
	 * valore del parametro tramite cui comunicare alla pagina individuata da 
	 * {@link paginaModulo} che l'utente ha richiesto la cancellazione di un record.
	 * 
	 * Corrisponde alla define WAMODULO_OPE_ELIMINA del package waModulo.
	 * @type int
	 */
	opeElimina			: 4, 

	//-------------------------------------------------------------------------
	// implements

	//-------------------------------------------------------------------------
	/**
	 * inizializzazione (costruttore)
	 * 
	 * @param {string} nome valorizza la proprietà {@link nome}
	 * @param {string} nomeIdRiga valorizza la proprietà {@link nomeIdRiga}
	 * @param {int} selezioneEsclusiva valorizza la proprietà {@link selezioneEsclusiva} (1 = true; 0 = false)
	 * @param {string} paginaModulo valorizza la proprietà {@link paginaModulo}
	 */
	initialize: function(nome, nomeIdRiga, selezioneEsclusiva, paginaModulo) 
		{
		this.nome = nome;
		this.nomeIdRiga = nomeIdRiga;
		this.selezioneEsclusiva = selezioneEsclusiva == 1;
		this.paginaModulo = paginaModulo;
		this.obj = document.getElementById(this.nome);
		this.obj.watabella = this;
		
		this.collegaAzioni();
		},
		
	//-------------------------------------------------------------------------
	// crea n funzioni che hanno il nome della funzione preceduto dal nome
	// dell'istanza della tabella; in questo modo e' possibile avere piu'
	// tabelle nella stessa pagina senza che qualcuna richiami le azioni
	// dell'altra; queste funzioni, che sono quelle standard (nuovo, vedi,
	// modifica, elimina) lavorano nello scope dell'oggetto watabella
	/**
	 * @ignore
	 */
	collegaAzioni: function ()
		{
		var nomeAzione;
		var li;
		for (li in this)
			{
			if (typeof(this[li]) == 'function' && li.substr(0, ("azione_").length) == "azione_")
				{
				if (li.substr(0, ("azione_" + this.nome).length) == "azione_" + this.nome)
					// stiamo andando in loop
					break;
				nomeAzione = li.substr(("azione_").length);
				this["azione_" + this.nome + "_" + nomeAzione] = this[li];
				}
			}
		},

	//-------------------------------------------------------------------------
	/**
	 * apre una pagina in una finestra concettualmente child.
	 * 
	 * In realtà, per default, il metodo apre la pagina nella medesima finestra
	 * del parent. Sarà poi l'applicazione, con il proprio particolare sistema 
	 * di navigazione, a definire la modalità effettiva di apertura di una 
	 * pagina child
	 * @param {string} target indirizzo della pagina da aprire
	 */
	apriPagina : function (target)
		{
		location.href = target;
		},
	
	//-------------------------------------------------------------------------
	/**
	 * cambia la pagina corrente.
	 * 
	 * @param {string} target indirizzo della pagina di destinazione
	 */
	cambiaPagina: function (target)
		{
		location.href = target;
		},
	
	//-------------------------------------------------------------------------
	/**
	 * mostra un messaggio di errore all'utente.
	 * 
	 * @param {string} msg testo del messaggio
	 */
	msgErrore: function (msg)
		{
		alert(msg);
		return false;
		},
	
  	//---------------------------------------------------------------------------
	/**
	 * restituisce un array contenente tutti gli identificativi univoci delle
	 * righe della tabella
	 */
	dammiTuttiGliId: function ()
		{
		var toret = new Array();
		var cntr = 0;
		for (li in this.righe)
			{
			toret[cntr] = li;
			cntr++;
			}
		return toret;
		},
		
  	//---------------------------------------------------------------------------
	/**
	 * restituisce un array contenente gli identificativi univoci delle righe
	 * correntemente selezionate della tabella
	 * 
	 * @param {int} minElemNrToRet nr. minimo di righe che devono essere 
	 *	selezionate dall'utente affinchè l'azione richiesta possa essere eseguita
	 *	(ad esempio: un'eventuale azione di confronto deve avere almeno 2 righe
	 *	selezionate)
	 * @param {int} maxElemNrToRet nr. massimo di righe che devono essere 
	 *	selezionate dall'utente affinchè l'azione richiesta possa essere eseguita
	 *	(ad esempio: un'eventuale azione di confronto deve avere al massimo 2 righe
	 *	selezionate)
	 */
	dammiIdSelezionati: function (minElemNrToRet, maxElemNrToRet)
		{
		var toret = new Array();
		var cntr = 0;
		for (li in this.righe)
			{
			if (this.righe[li].selezionata)
				{
				toret[cntr] = li;
				cntr++;
				}
			}
		if (toret.length < minElemNrToRet)
			return this.msgErrore("Selezionare almeno " + minElemNrToRet + " " +  (minElemNrToRet == 1 ? "riga" : "righe"));

		if (maxElemNrToRet > 0 && toret.length > maxElemNrToRet)
			return this.msgErrore("Selezionare non piu' di " + maxElemNrToRet + " " + (maxElemNrToRet == 1 ? "riga" : "righe"));
					
		return toret;
		},

  	//---------------------------------------------------------------------------
	/**
	 * seleziona/deseleziona tutte le righe della tabella
	 * 
	 * @param {boolean} siNo flag di selezione/deselezione
	 */
	selezionaTutte: function (siNo)
		{
		for (li in this.righe)
			this.righe[li].seleziona(siNo);
		},
		
	//---------------------------------------------------------------------------
	// dato un url e dovendo aggiungere parametri a questo, restituisce
	// il carattere (?/&) con cui continuare il completamento dell'url
	/*
	 * @ignore
	 */
	dammiInizioQS: function (url)
		{
		return url.indexOf("?") == -1 ? "?" : "&";
		},
		
  	//---------------------------------------------------------------------------
  	// restituisce l'URI della pagina del modulo che serve a manipolare un record
	/*
	 * @ignore
	 */
	dammiUriModulo: function (tipoOperazione, idRiga)
		{
		var uri = this.paginaModulo + 
				this.dammiInizioQS(this.paginaModulo) +
				this.chiaveOperazione + "=" + tipoOperazione;
		if (idRiga)
			uri += "&" + this.nomeIdRiga + "=" + idRiga
		return uri;
		},
		
  	//---------------------------------------------------------------------------
	/**
	 * metodo invocato a fronte di richiesta di azione
  	 * "Nuovo" (ossia di creazione di una nuova riga).
	 * 
	 * Per default, l'azione apre la pagina del modulo (proprietà paginaModulo
	 * di waTabella) passandole in GET il codice 
	 * operazione di inserimento (una delle defines di waModulo)
	 */
	azione_Nuovo: function ()
		{
		this.apriPagina (this.dammiUriModulo(this.opeInserimento));
		},
			
  	//---------------------------------------------------------------------------
	/**
	 * metodo invocato a fronte di richiesta di azione
  	 * "Vedi" (ossia la visualizzazione del dettaglio della riga).
	 * 
	 * Per default, l'azione apre la pagina del modulo (proprietà paginaModulo
	 * di waTabella) passandole in GET l'identificativo della riga e il codice 
	 * operazione di visualizzazione (una delle defines di waModulo)
	 * @param {string} idRiga identificativo della riga di cui visualizzare il 
	 * dettaglio
	 */
	azione_Vedi: function (idRiga)
		{
		this.apriPagina (this.dammiUriModulo(this.opeDettaglio, idRiga));
		},
			

  	//---------------------------------------------------------------------------
	/**
	 * metodo invocato a fronte di richiesta di azione
  	 * "Modifica" (ossia la modifica della riga).
	 * 
	 * Per default, l'azione apre la pagina del modulo (proprietà paginaModulo
	 * di waTabella) passandole in GET l'identificativo della riga e il codice 
	 * operazione di modifica (una delle defines di waModulo)
	 * @param {string} idRiga identificativo della riga da modificare
	 */
	azione_Modifica: function (idRiga)
		{
		this.apriPagina (this.dammiUriModulo(this.opeModifica, idRiga));
		},
			

  	//---------------------------------------------------------------------------
	/**
	 * metodo invocato a fronte di richiesta di azione
  	 * "Elimina" (ossia la cancellazione della riga).
	 * 
	 * Per default, l'azione apre la pagina del modulo (proprietà paginaModulo
	 * di waTabella) passandole in GET l'identificativo della riga e il codice 
	 * operazione di eliminazione (una delle defines di waModulo)
	 * @param {string} idRiga identificativo della riga da eliminare
	 */
	azione_Elimina: function (idRiga)
		{
		if (confirm("Confermi eliminazione"))
			this.apriPagina (this.dammiUriModulo(this.opeElimina, idRiga));
		},
			
  	//---------------------------------------------------------------------------
	/**
	 * metodo invocato a fronte di richiesta di azione
  	 * "Filtro": apre il modulo per l'inserimento parametri di ordinamento e filtro
	 */
	azione_Filtro: function ()
		{
		divOrdFiltro = document.getElementById(this.nome + "_finestra_ordinamento_filtro");
		divOrdFiltro.style.visibility = '';
		document.getElementById(this.nome + "_modulo_ordinamento_filtro")["watbl_oc[" + this.nome + "][0]"].focus();
		},
			
  	//---------------------------------------------------------------------------
  	// funzione interna per creare una query string senza i parametri del filtro
  	// della tabella
	dammiQsResetFiltro: function ()
		{
		// creiamo una query string coi nuovi parametri di filtro azzerati
		var formOf = document.getElementById(this.nome + "_modulo_ordinamento_filtro");
		var dest = "";
		var coppie = (document.location.search.substr(1)).split('&');
		var coppia;
		
		for (var li = 0; li < coppie.length; li++)
			{
			coppia = coppie[li].split("=");
			if (!formOf.elements[unescape(coppia[0])] && 
				coppia[0] != "watbl_pg[" + this.nome + "]" && 
				coppia[0] != "watbl_or[" + this.nome + "]"  && 
				coppia[0] != "watbl_orm[" + this.nome + "]" )
				dest += (dest ? "&" : "?") + coppie[li];
			}
		dest += (dest ? "&" : "?") + "watbl_pg[" + this.nome + "]=0" + 
									"&watbl_or[" + this.nome + "]=" + 
									"&watbl_orm[" + this.nome + "]=";
		return dest;
		},
			
  	//---------------------------------------------------------------------------
	/**
	 * metodo invocato a fronte di richiesta di azione
  	 * "No Filtro": elimina tutti i criteri di filtro applicati in precedenza 
	 * sulla tabella
	 */
	azione_noFiltro: function ()
		{
		location.href = this.dammiQsResetFiltro();
		},
			
  	//---------------------------------------------------------------------------
	/**
	 * invia al server la richiesta di applicazione dei criteri di ordinamento e 
	 * filtro selezionati dall'utente
	 * @ignore
	 */
	filtra: function (formOf)
		{
		// creiamo una query string coi nuovi parametri di filtro
		var dest = 	this.dammiQsResetFiltro();
		var qoe = dest ? "&" : "?";

		// parametri query string per ordinamento
		for (var li = 0; li < 3; li++)
			{
			if (formOf.elements["watbl_oc[" + this.nome + "][" + li + "]"].value != '')
				{
				dest += qoe + "watbl_oc[" + this.nome + "][" + li + "]=" + 
							formOf.elements["watbl_oc[" + this.nome + "][" + li + "]"].value + 
							"&watbl_om[" + this.nome + "][" + li + "]=" + 
							formOf.elements["watbl_om[" + this.nome + "][" + li + "]"].value;
				qoe = "&";
				}
			}
		
		// parametri query string per filtro
		var valore_filtro = '';
		var tipo_campo = '';
		for (var li = 0; li < 6; li++)
			{
			if (formOf.elements["watbl_fc[" + this.nome + "][" + li + "]"].value != '' &&
					formOf.elements["watbl_fm[" + this.nome + "][" + li + "]"].value != '' &&
					formOf.elements["watbl_fv[" + this.nome + "][" + li + "]"].value != '')
				{
				dest += qoe + "watbl_fc[" + this.nome + "][" + li + "]=" + 
							formOf.elements["watbl_fc[" + this.nome + "][" + li + "]"].value + 
							"&watbl_fm[" + this.nome + "][" + li + "]=" + 
							formOf.elements["watbl_fm[" + this.nome + "][" + li + "]"].value + 
							"&watbl_fv[" + this.nome + "][" + li + "]=";
				valore_filtro = formOf.elements["watbl_fv[" + this.nome + "][" + li + "]"].value;
				tipo_campo = this.colonne[formOf.elements["watbl_fc[" + this.nome + "][" + li + "]"].value].tipo_campo;
				if (tipo_campo == 'DATA')
					valore_filtro = valore_filtro.substr(6, 4) + "-" + valore_filtro.substr(3, 2) + "-" + valore_filtro.substr(0, 2);
				else if (tipo_campo == 'DATAORA')
					valore_filtro =  valore_filtro.substr(6, 4) + "-" + valore_filtro.substr(3, 2) + "-" + valore_filtro.substr(0, 2) +
									" " + valore_filtro.substr(11, 2) + ":" + valore_filtro.substr(14, 2) + ":00";
				else if (tipo_campo == 'ORA')
					valore_filtro = valore_filtro.substr(0, 2) + ":" + valore_filtro.substr(3, 2) + ":00";
				else if (tipo_campo == 'DECIMALE')
					valore_filtro = (valore_filtro.replace(/\./g,"")).replace(/\,/g,".");

				dest += valore_filtro;
				qoe = "&";
				}
			}
			
		location.href = dest;
		return false;
		},
			
  	//---------------------------------------------------------------------------
	/**
	 * chiude (nasconde) il modulo di rodinamento/filtro
	 */
	chiudiOrdinamentoFiltro: function ()
		{
		divOrdFiltro = document.getElementById(this.nome + "_finestra_ordinamento_filtro");
		divOrdFiltro.style.visibility = 'hidden';
		},
			
  	//--------------------------------------------------------------------------
  	//---------  funzioni di edit  ---------------------------------------------
  	//--------------------------------------------------------------------------
  	//--------------------------------------------------------------------------
	/**
	 * metodo invocato quando l'utente richiede il submit del form (intera 
	 * tabella)
	 */
	azione_Invia: function ()
		{
		var msg = '';
		
		for (var idRiga in this.righe)
			{
			for (var nomeCol in this.colonne)
				msg += this.colonne[nomeCol].verificaInput(idRiga);
			}
		if (msg != '')
			return alert(msg);
			
		if (confirm("Confermi registrazione modifiche?"))
			this.obj.submit();
		},
			
  	//--------------------------------------------------------------------------
	// evento scatenato quando l'utente seleziona il checkbox della cancellazione
	// della riga
	evento_CheckBoxElimina_onclick: function (idRiga)
		{
		var del_chk = this.obj.elements["watbl_input_del_chk[" + idRiga + "]"];
		var ctrl = null;
		
		for (var nomeCol in this.colonne)
			{
			if (this.colonne[nomeCol].tipo_input)
				{
				// la colonna ha un controllo di input associato
				ctrl = this.obj.elements[this.colonne[nomeCol].nome + "[" + idRiga + "]"];
				ctrl.disabled = del_chk.checked;
				}
			}
			
		},

	//-------------------------------------------------------------------------
	//-----  funzionalità quick-edit  -----------------------------------------
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	getHttpRequestObj: function()
		{
		var hr = false;
		if (window.XMLHttpRequest) 
			{ 
			// Mozilla, Safari,...
			hr = new XMLHttpRequest();
			if (hr.overrideMimeType) 
				hr.overrideMimeType('text/xml');
			}
		else if (window.ActiveXObject) 
			{ 
			// IE
			try 
				{hr = new ActiveXObject("Msxml2.XMLHTTP");}
			catch (e) 
				{
				try 
					{hr = new ActiveXObject("Microsoft.XMLHTTP");}
				catch (e) 
					{}
				}			
			}

		return hr;
		},

	//-------------------------------------------------------------------------
	// mostra un errore in fase di rpc e mantiene il focus sul controllo
	mostraErroreRPC: function(msg, controllo) 
		{
		alert(msg);
		if (controllo)
			setTimeout(function(){controllo.focus();}, 5); 
		return false;
			
		},
		
	//-------------------------------------------------------------------------
	// cerca di leggere dalla struttura xml-rpc un valore
	leggiValoreXML: function(xmlDoc, chiave) 
		{
			
		var retval = '';
		
		if (xmlDoc.getElementsByTagName(chiave) && 
			xmlDoc.getElementsByTagName(chiave)[0] && 
			xmlDoc.getElementsByTagName(chiave)[0].firstChild)
			return xmlDoc.getElementsByTagName(chiave)[0].firstChild.nodeValue;

		return "";

		},
	
	//-------------------------------------------------------------------------
	/**
	 * effettua una chiamata RPC verso il server.
	 * 
	* gli argomenti da passare al metodo sono posizionali 
	* <ol>
	* <li>nome funzione/metodo PHP da richiamare lato server
	* <li>parametro 1 da passare alla funzione/metodo
	* <li>parametro 2 da passare alla funzione/metodo
	* <li>parametro n da passare alla funzione/metodo...
	* </ol>
	* 
	* caso speciale: se il nome funzione/metodo e' watabella_rpc_aggiornamento_immediato, 
	* allora la chiamata e' gestita internamente dalla classe PHP waTabella
	* (non chiama in causa l'applicazione); 
	* in questo caso il secondo parametro e' una stringa di coppie chiave/valore
	* che la classe PHP ricevera' in POST e dara' in pasto all'XSLT di input, 
	* e si comportera' esattamente come avesse ricevuto il post in fase di editing.
	* Questa funzionalita' e' utilizzata quando si vuole consolidare immediatamente 
	* su DB un dato all'uscita da un controllo, o la cancellazione di un record 
	* senza ricaricare la pagina, o la creazione di un nuovo record senza ricaricare
	* la pagina
	 */
	RPC: function() 
		{
		try
			{
			var http_request = this.getHttpRequestObj();

			// creiamo il messaggio da inviare (parametri della chiamata)
			// 6 e' il codice operazione rpc (vedi defines in wamodulo.inc.php)
			var post = 'watabella_rpc=1';
			// passiamo il nome del modulo che richiede RPC, in modo da
			// riconoscere, lato server, quale modulo ha richiesto l'operazione
			post += '&watabella_nome_tabella=' + escape(this.nome);
			// passiamo in wamodulo_funzionerpc il nome della funzione/metodo 
			// PHP da richiamare, che deve essere il primo argomento con cui e'
			// stato chiamato il presente metodo
			post += '&watabella_funzione_rpc=' + escape(arguments[0]);
			
			if (arguments[0] == "watabella_rpc_aggiornamento_immediato")
				// caso speciale: leggi note sopra il metodo
				post += "&" + arguments[1];
			else
				{
				// passiamo i parametri coi quali il presente metodo e' stato 
				// richiamato nel medesimo ordine; li ricevera' nel medesimo ordine
				// posizionale anche la funzione/metodo PHP lato server
				for (var i = 1; i < arguments.length; i++)
					post += '&watabella_dati_rpc[' + (i - 1) + ']=' + encodeURIComponent(arguments[i]);
				}
		
			// chiamiamo il server
			http_request.open('POST', document.location.href, false);
			http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			http_request.send(post);
			
			// verifica eventuali errori di sistema
			if (http_request.status != 200) 
				return this.mostraErroreRPC("Errore HTTP durante chiamata RPC: " + http_request.status);
			
			// gestione della risposta
			var xmlDoc = http_request.responseXML;				
			var esito = this.leggiValoreXML(xmlDoc, 'watabella_esito_rpc');
			var messaggio = this.leggiValoreXML(xmlDoc, 'watabella_messaggio_rpc');
			if (esito != '__rpc_ok__')
				return this.mostraErroreRPC("Errore applicativo server durante chiamata RPC:\n" + messaggio);
			
			if (xmlDoc.getElementsByTagName('item')[0])
				{
				// l'esito e' un array; torniamo UN OGGETTO (dizionario!) perche' le 
				// chiavi non e' detto che siano necessariamente numeriche
				var item = null;
				var retval = {};
				if (xmlDoc.getElementsByTagName('item')[0].getAttribute("id"))
					{
					// l'array non e' vuoto	
					for (var i = 0;  i < xmlDoc.getElementsByTagName('item').length; i++)
						{
						item = xmlDoc.getElementsByTagName('item')[i];
						retval[item.getAttribute("id")] = item.childNodes[0] ? item.childNodes[0].nodeValue : '';
						}
					}
				}
			else 
				var retval = this.leggiValoreXML(xmlDoc, 'watabella_dati_rpc');
						
			return retval;
			}		
		catch(e)
			{
			return this.mostraErroreRPC("Errore durante chiamata RPC: " + e);
			}
			
		},

	//-------------------------------------------------------------------------
	/**
	 * all'evento onblur su un controllo di edit invia tramite RPC i dati 
	 * modificati al server
	 * 
	 * @param {string} nome_colonna nome della colonna/campo a cui appartiene la cella
	 * @param {string} idRiga identificativo della riga a cui appartiene la cella
	 */
	azione_ModificaSubito: function(nome_colonna, idRiga) 
		{
		var colonna = this.colonne[nome_colonna];
		var controllo = this.obj.elements[colonna.nome + "[" + idRiga + "]"];
		var msg = colonna.verificaInput(idRiga) ;
		if (msg != '')
			return this.mostraErroreRPC(msg, controllo);

		// se il controllo non e' stato modificato e' inutile andarlo ad aggiornare
		if (!this.obj.elements["watbl_input_mod_chk[" + idRiga + "]"].checked)
			return true;

		var valore_immesso = colonna.tipo_input == 'logico' ? (controllo.checked ? 'on' : 'off') : controllo.value;
		this.RPC("watabella_rpc_aggiornamento_immediato", 
					"watbl_input_mod_chk[" + idRiga + "]=on" +
						"&" + colonna.nome + "[" + idRiga + "]=" + encodeURIComponent(valore_immesso));

		this.obj.elements["watbl_input_mod_chk[" + idRiga + "]"].checked = false;
		return true;
		},
		
	//-------------------------------------------------------------------------
	/**
	 * richiede al server l'eliminazione di un record tramite RPC
	 * 
	 * @param {string} idRiga identificativo della riga a cui appartiene la cella
	 */
	azione_EliminaSubito: function(idRiga) 
		{
		if (!confirm("Confermi eliminazione record?"))
			return false;
		
		this.RPC("watabella_rpc_aggiornamento_immediato", 
					"watbl_input_del_chk[" + idRiga + "]=on");

		var tr_id = document.getElementById("row_" + this.nome + "_" + idRiga);
		tr_id.style.display = "none";
		return true;
		},
		
	//-------------------------------------------------------------------------
	/**
	 * richiede al server la creazione di un record tramite RPC
	 * 
	 */
	azione_NuovoSubito: function() 
		{
			
		var idRiga;	// identificativo della riga che viene creata
		if (!(idRiga = this.RPC("watabella_rpc_aggiornamento_immediato", "watbl_input_ins_chk=on")))
			return this.mostraErroreRPC("Errore durante chiamata RPC: non ricevuto identificativo riga");

		var tbl = document.getElementById("watbl_" + this.nome + "_tabella_principale");
		// tbl.rows[0] ci sono i th
		// in tbl.rows[1] ci sono i td per quasi tutti i browser, tranne Opera
		// che li numera in ordine di creazione (per essere strict il tfoot deve
		// essere dichiarato prima del tbody); quindi la riga da clonare, che e'
		// invisibile ed e' la prima del tbody, potrebbe avere indice = 1 
		var idxRigaDaClonare = tbl.rows[1].innerHTML.indexOf("<th") != -1 || tbl.rows[1].innerHTML.indexOf("<TH") != -1 ? 2 : 1;
		var nuova_riga = tbl.rows[idxRigaDaClonare].cloneNode(true);
		// per duplicare la riga sarebbe bello lavorare solo sull'innerHTML 
		// della riga, ma IE fa casino, allora dobbiamo duplicare tutte le 
		// colonne
		nuova_riga.innerHTML = "";
		var passo;
		for (var i = 0; i < tbl.rows[idxRigaDaClonare].cells.length; i++)
			{
			passo = tbl.rows[idxRigaDaClonare].cells[i].cloneNode(true);
			passo.innerHTML = passo.innerHTML.replace(/___xxx___/g, idRiga);
			nuova_riga.appendChild(passo);
			}
		nuova_riga.id = "row_" + this.nome + "_" + idRiga;
		new wariga(this, idRiga, '{}');
		try
			{
			nuova_riga.style.display = "table-row";
			}
		catch(e)
			{
			// ie 6/7 non supportano table-row....
			nuova_riga.style.display = "";
			}
		
		tbl.tBodies[0].insertBefore(nuova_riga, tbl.rows[idxRigaDaClonare + 1]);
		
		return idRiga;
		}
		

	}
);
