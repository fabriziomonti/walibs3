<?php
/**
* @package waTabella
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_COLONNA'))
{
/**
* @ignore
*/
define('_WA_COLONNA',1);

//***************************************************************************
//****  classe waColonna ****************************************************
//***************************************************************************
/**
* waColonna
*
* contiene le informazioni di una colonna contenuta in un oggetto di classe
* {@link waTabella}
*
* @package waTabella
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waColonna
	{
	/**
	* nome della colonna
	*
	* nome univoco della colonna all'interno della tabella; deve corrispondere 
	 * al nome della colonna all'interno del cursore restituito dalla query SQL 
	 * (senza prefisso della tabella, quindi), oppure alla chiave dell'array 
	 * associativo che alimentano la classe  {@link waTabella}.
	 * 
	 * Qualora la colonna dovesse causare situazioni di ambiguità
	 * tra nomi uguali di tabelle diverse all'interno della query SQL, 
	 * (evidentemente solo in caso di filtro o ordinamento), allora è necessario 
	 * valorizzare con il nome completo di prefisso tabellare  anche la
	 * proprietà {@link $aliasDi}.
	 * 
	* E' buona norma che il nome
	 * segua le regole di naming della programmazione (no spazi, punteggiatura, 
	 * ecc.), perche' e' verosimile che lato client questa proprieta' venga 
	 * utilizzata per accedere ad un oggetto di programmazione.
	 * 
	* @var string
	*/	
	var $nome;
	
	/**
	* intestazione della colonna
	*
	* @var string
	*/	
	var $etichetta;
	
	/**
	* indica se la colonna puo' essere sottoposta a ordinamento
	*
	* @var boolean
	*/	
	var $ordina = true;
	
	/**
	* indica se la colonna puo' essere sottoposta a filtro
	*
	* @var boolean
	*/	
	var $filtra = true;
	
	/**
	* indica se la colonna deve essere mostrata a video
	*
	* La proprietà non ha alcun effetto visivo di per se': è l'XSLT che si 
	 * comporterà come ritiene opportuno a fronte di questa informazione.
	 * 
	* @var boolean
	*/	
	var $mostra = true;
	
	/**
	* allineamento della colonna
	*
	* Puo' valere una delle seguenti define:
	* - {@link WATBL_ALLINEA_SX} ->	allineamento a sinistra
	* - {@link WATBL_ALLINEA_CENTRO} ->	allineamento al centro
	* - {@link WATBL_ALLINEA_DX} ->	allineamento a destra
	 * 
	 * si presti attenzione al fatto che non e' necessario
	 * fornire questa informazione; in alternativa e' possibile utilizzare la
	 * proprieta' {@link tipoCampo}: il foglio XSLT, sulla base del valore di 
	 * questa proprietà, puo' prendere decisioni relative alla formattazione
	 * * @var integer
	*/	
	var $allineamento = WATBL_ALLINEA_SX;
	
	/**
	* lunghezza massima di una cella espressa in caratteri;
	*
	* se 0, allora la cella non ha limite di caratteri
	* @var integer
	*/	
	var $maxCaratteri = WATBL_CELLA_MAX_CARATTERI;
	
	/**
	* formattazione colonna
	*
	* formattazione da applicare al valore da visualizzare. 
	* Puo' valere una delle seguenti define:
	* - {@link WATBL_FMT_DATA}		->	dd/mm/YYYY
	* - {@link WATBL_FMT_DATAORA}	->	dd/mm/YYYY HH:ii:ss
	* - {@link WATBL_FMT_ORA}		->	HH:ii:ss
	* - {@link WATBL_FMT_DECIMALE}	->	#.###,##
	* - {@link WATBL_FMT_INTERO}	->	####
	* - {@link WATBL_FMT_STRINGA}	->	stringa html-encoded
	* - {@link WATBL_FMT_CRUDO}		->	stringa non html-encoded
	* - qualsiasi altra cosa: -> viene applicata la formattazione standard per il tipo rilevato dal database
	 * 
	 * si presti attenzione al fatto che non e' necessario
	 * fornire questa informazione; in alternativa e' possibile utilizzare la
	 * proprieta' {@link tipoCampo}: il foglio xslt, sulla base del valore di 
	 * questa proprieta', puo' prendere decisioni relative alla formattazione
	* @var integer
	*/	
	var $formattazione;
	
	/**
	* nr. decimali formattazione
	*
	* nr. di decimali da applicare alla eventuale formattazione di un nr. float
	* @var integer
	*/	
	var $nrDecimali = 2;
	
	/**
	* link
	*
	* se true, l'informazione viene semplicemente passata all'xslt, che dovra'
	 * implementare i meccanismi affinche' il link diventi effettivo. 
	 * 
	* @var boolean
	*/	
	var $link;
	
	/**
	* callback function di formattazione/calcolo del valore da mandare in output
	*
	* nome di una funzione/metodo PHP che deve
	* essere chiamata al posto della formattazione standard. Se il valore passato 
	* e' una stringa, allora verra' invocata una funzione procedurale; se e' un array
	* il primo elemento sara' l'oggetto a cui il metodo appartiene, il secondo
	* elemento il nome del metodo. Il valore ritornato dalla funzione/metodo sara' 
	* cio' che viene effettivamente mostrato come contenuto della cella. 
	* 
	* In questo modo
	* e' possibile avere una o piu' colonne totalmente gestite dall'applicazione,
	* la quale puo' effettuare le opportune elaborazioni non standard (verifica 
	* di presenza di file sul filesystem, ecc.). Alla funzione/metodo verra' passato
	* come parametro l'oggetto di classe {@link waTabella} istanziato ($this).
	* @var mixed (string | array)
	*/	
	var $funzioneCalcolo;
	
	/**
	* flag totalizzatore della colonna
	*
	* indica che la colonna deve produrre un totale 
	* che verra' mostrato in una riga aggiuntiva in fondo alla tabella
	* @var boolean
	*/	
	var $totalizza = false;
	
	/**
	* -
	*
	* indica che il nome della colonna e' un alias di un altro campo/calcolo, di cui qui
	* va specificato il nome/calcolo, oppure che il nome della colonna potrebbe
	 * entrare in conflitto con quello di un altro  campo del DB appartenente
	 * a una tabella diversa ma coinvolta nella stessa query SQL: in questo
	 * caso la proprietà dovrà contenere il nome del campo prefissato dalla
	 * tabella, ad esempio: <b>Fornitori.Nome</b> 
	 * 
	 * La valorizzazione della proprietà ha senso solo quando la colonna può
	 * essere sottoposta a filtro o ordinamento.
	 * 
	* @var string
	*/	
	var $aliasDi = '';
	
	/**
	* noACapo
	*
	* indica che la cella deve usare il nowrap; l'XSLT può utilizzare a piacere
	 * questa informazione
	 * 
	* @var boolean
	*/	
	var $noACapo = false;
	
	/**
	* flag di escaping del codice HTML
	*
	* indica se convertire il valore del campo prima di mostrarlo a video affinche' 
	* i caratteri non possano generare sequenze di tag HTML.
	* 
	* Il default e' true, ossia cio' che viene mostrato in corrispondenza della colonna
	* sara' l'eventuale <b>codice</b> HTML, non il suo rendering.
	* 
	* Qualora per particolari motivi fosse necessario mostrare il rendering HTML
	* del valore del campo all'interno della cella, e' necessario porre questa
	* proprieta' a false. Attenzione ovviamente ai casi in cui cio' possa generare 
	 * cross-site-scripting.
	* @var boolean
	*/	
	var $convertiHTML = true;
	
	/**
	* -
	*
	* indica che tipo di input type html utilizzare lato client nel caso in cui la tabella venga utilizzata 
	* per effettuare editing direttamente, senza passare da un waModulo. Ovviamente questo tipo 
	* di tabella deve essere renderizzata da un XSLT in grado di:
	* - gestire il submit dei dati
	* - effettuare al momento del submit una validazione dei dati inseriti
	* Altrettanto ovviamente, lato server occorre scrivere il codice per recepire le modifiche effettuate
	* tramite i controlli di input dall'operatore.
	* 
	* Esiste una codifica predefinita per i tipi che è possibile trovare all'interno delle define
	 * del package, e che si consiglia di utilizzare per ovvi motivi di mutua
	 * leggibilità del codice. Se poi volete usare una codifica
	* personalizzata, ad esempio "giuseppe" che mostra un checkbox, siete liberissimi di farlo,
	* anche se non e' molto carino nei confronti dei colleghi.
	* 
	* Non esiste il default per questa proprieta' (si suppone che non tutte le colonne debbano essere editabili,
	* quindi il programmatore valorizzera' la proprieta' solo laddove necessita di editing)
	* 
	* @var string
	*/	
	var $inputTipo = "";
	
	/**
	* inputObbligatorio
	*
	* nel caso in cui {@link $inputTipo} sia valorizzato, indica se il controllo di input deve essere 
	* obbligatoriamente valorizzato o meno
	* 
	* @var boolean
	*/	
	var $inputObbligatorio = false;
	
	/**
	* inputOpzioni
	*
	* nel caso in cui {@link $inputTipo} sia valorizzato, indica le opzioni che l'input puo' assumere
	* (tipicamente in caso di tipo = selezione o opzione). La proprieta' e' un array associativo in cui la chiave di
	* ogni elemento e' il valore dell'opzione, e il valore dell'elemento e' il testo da selezionare
	* 
	* @var array()
	*/	
	var $inputOpzioni = "";

	/**
	* tipoCampo
	*
	* indica il tipo di campo contenuto nella colonna. Questa informazione puo' essere utile 
	 * ai fini della formattazione oppure qualora si intenda fare
	* editing direttamente tramite la tabella, e quindi in congiunzione 
	 * con l'utilizzo esplicito di {@link $tipoInput}.
	* 
	* In caso di recordset letto da base dati, per default questa proprieta' riporta il risultato di waRigheDB::tipoCampo
	* (e' ovviamente possibile sovrascrivere questo valore e forzarlo). In caso di recordset generato da array sara'
	* compito del programmatore valorizzare opportunamente questa proprieta', qualora servisse lato UI.
	* 
	* Non esiste una codifica predefinita per il tipo, ma al fine di mantenere convenzionalmente una medesima
	* modalita' di lavoro all'interno dell'azienda, si consiglia di utilizzare la stessa codifica utilizzata all'interno
	* di waDB; ad esempio: STRINGA, INTERO, DATA, DATAORA, CONTENITORE, ecc.. Se poi volete usare una codifica
	* personalizzata, ad esempio "giuseppe" che mostra un decimale, siete liberissimi di farlo,
	* anche se non e' molto carino nei confronti dei colleghi.
	* 
	* Non esiste il default per questa proprieta'
	* 
	* @var string
	*/	
	var $tipoCampo = "";
	
	/**
	* lunghezzaMaxCampo
	*
	* indica la lunghezza massima del campo contenuto nella colonna (non e' il numero massimo di caratteri che la colonna deve presentare, 
	* corrispondente alla proprieta' {@link $maxCaratteri}; e' la lunghezza massima del campo su db, concetto ben diverso). 
	* Questa informazione puo' essere utile qualora si intenda fare
	* editing direttamente tramite la tabella, e quindi in congiunzione con l'utilizzo esplicito di {@link $tipoInput}.
	* 
	* In caso di recordset letto da base dati, per default questa proprieta' riporta il risultato di waRigheDB::lunghezzaMaxCampo
	* (e' ovviamente possibile sovrascrivere questo valore e forzarlo). In caso di recordset generato da array sara'
	* compito del programmatore valorizzare opportunamente questa proprieta', qualora servisse lato UI.
	* 
	* Non esiste il default per questa proprieta'
	* 
	* @var string
	*/	
	var $lunghezzaMaxCampo = "";
	
	/**
	* totalizzatore
	*
	* variabile d'appoggio che mantiene i parziali per la totalizzazione
	* @ignore
	* @var string
	*/	
	var $totalizzatore = 0;
	
	/**
	* pdf_perc
	*
	* percentuale della larghezza della pagina che la colonna deve occupare
	 * in una eventuale esportazione in PDF
	 * 
	* @var integer
	*/	
	var $pdf_perc = 0;
	
//***************************************************************************
	}	// fine classe waColonna
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_COLONNA'))
?>