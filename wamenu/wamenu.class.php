<?php
/**
* @package waMenu
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_MENU'))
{
/**
* @ignore
*/
define('_WA_MENU',1);

//***************************************************************************
//****  classe waMenu *******************************************************
//***************************************************************************
/**
* waMenu
*
* @package waMenu
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waMenu 
	{
	/**
	* foglio XSLT
	*
	* e' il file XSLT che viene applicato all'XML generato dalla classe.
	* 
	* Se non valorizzato viene utilizzato il foglio XSLT di default della
	* classe (menu orizzontale a tendina e uso di javascript)
	* @var string
	*/	
	var $xslt 			= '';
	
	/**
	* nome del menu
	*
	* importante solo quando ci sono piu' menu all'interno della stassa pagina
	* @var string
	*/	
	var $nome	= 'wamenu';
	
	/**
	 * titolo del menu che puo' essere utilizzato come si vuole nell'xslt
	 *
	 * @var string
	 */
	var $titolo;
		
	/**
	* @ignore
	* @access protected
	*/	
	protected $selectSections	= false;
	
	/**
	* @ignore
	* @access protected
	*/	
	protected	$sectionCounter	= 0;
	
	/**
	* @ignore
	* @access protected
	*/	
	protected	$currLevel		= 0;
	
	/**
	* @ignore
	* @access protected
	*/	
	protected $openedSections = array();
	
	/**
	* @ignore
	* @access protected
	*/	
	protected $sectionsToSelect = array();
	
	/**
	* buffer utilizzato per contenere l'output del menu
	*
	* @ignore
	* @access protected
	*/	
	protected	$buffer; 
	
	/**
	* array che contiene le voci cn cui costruire il menu;
	* non serve a niente, se non a renderlo documentabile in modo automatico 
	* (waDocumentazione)
	*
	* @ignore
	* @access protected
	*/	
	var	$voci; 
	
	

	//***********************************************************************
	/**
	* costruttore
	*
	* inizializza il menu.
	* @param string $xslt vedi proprieta' {@link xslt}
	* @return void 
	*/
	function __construct($xslt =  '')
		{
			
		$this->xslt = empty($xslt) ? dirname(__FILE__) . "/uis/wa_orizzontale_default/xslt/wamenu.xsl" : $xslt;
		
		}
		
	//***********************************************************************
	/**
	* apre il menu
	*
	* @return void 
	*/
	function apri()
		{
		//header("Content-Type: text/xml; charset=utf-8");			

	 	$this->buffer .= "<?xml version='1.0' encoding='UTF-8'?>\n" .
								"<wamenu>\n" .
 								"\t<versione_librerie_xsl>" . LIBXSLT_VERSION . "</versione_librerie_xsl>\n" .
 								"\t<nome>$this->nome</nome>\n" .
 								"\t<titolo>$this->titolo</titolo>\n" .
 								"\t<wamenu_path>" . $this->GetMyDir() . "</wamenu_path>\n";
 								
		}
	
	//***********************************************************************
	/**
	* apre una sezione del menu
	*
	* @param string $etichetta etichetta (label/caption) della sezione
	* @param string $url href eventuale link
	* @param string $finestra e' il target HTML (_blank, _current, ecc.)
	* in cui aprire l'eventuale link; in realtà e' possibile scrivere qualsiasi 
	 * cosa, perche' e' il foglio di stile xslt che si occupa di interpretare
	 * il parametro
	* @return boolean indica che la voce di menu e' selezionata, ossia che 
	 * la pagina corrente corrisponde alla voce di menu passata
	*/
	function apriSezione($etichetta, $url = '', $finestra = '')
	    {
	    $voce = array();
	    $voce['etichetta'] = $etichetta;
	    $voce['url'] = $url;
	    $voce['livello'] = $this->currLevel;
    	$this->voci[] = $voce;
    	
	    if ($this->_isCurrent($url))
	    	$isSelected = true;
	    	
    	$id = $this->nome .  "_" . $this->sectionCounter;
    	$this->openedSections[] = $id;
    	
    	$url = str_replace("&", "&amp;", $url);
    	$tabs = str_repeat("\t", $this->currLevel + 1);
    	$this->buffer .= "$tabs<wamenu_sezione id='$id'>\n".
    						"$tabs\t<url>$url</url>\n" .
    						"$tabs\t<finestra>$finestra</finestra>\n" .
    						"$tabs\t<etichetta>" . htmlspecialchars($etichetta) . "</etichetta>\n" .
    						"$tabs\t<livello>$this->currLevel</livello>\n" .
    						"$tabs\t<selezionato>" . ($isSelected ? 1 : 0) . "</selezionato>\n";
    						
    	$this->sectionCounter++;
	    $this->currLevel++;
	    
	    return $isSelected;
	    }
	    
	//*****************************************************************************
	/**
	*
	* chiude una sezione del menu precedentemente aperta con {@link apriSezione}
	* @return void
	*/
	function chiudiSezione()
	    {
	    
    	$tabs = str_repeat("\t", $this->currLevel);
    	$this->buffer .= "$tabs</wamenu_sezione>\n";
	    $this->currLevel--;

	    array_pop($this->openedSections);
	    }

	//*****************************************************************************
	/**
	* aggiunge una voce di menu alla sezione correntemente aperta
	*
	* @param string $etichetta etichetta (label/caption) della sezione
	* @param string $url href eventuale link
	* @param string $finestra e' il target HTML (_blank, _current, ecc.)
	* in cui aprire l'eventuale link; in realtà e' possibile scrivere qualsiasi 
	 * cosa, perche' e' il foglio di stile xslt che si occupa di interpretare
	 * il parametro
	* @return boolean indica che la voce di menu e' selezionata, ossia che la pagina
	*			corrente corrisponde alla voce di menu
	*/
	function aggiungiVoce($etichetta, $url = '', $finestra='')
	    {
	    $voce = array();
	    $voce['etichetta'] = $etichetta;
	    $voce['url'] = $url;
	    $voce['livello'] = $this->currLevel;
    	$this->voci[] = $voce;
    	
	    if ($this->_isCurrent($url))
	    	{
			$this->sectionsToSelect = $this->openedSections;
	    	$isSelected = true;
	    	}
	    else 
	    	$class = "item2";

    	$url = str_replace("&", "&amp;", $url);
    	$tabs = str_repeat("\t", $this->currLevel + 1);
		$this->buffer .= "$tabs<wamenu_voce>\n".
    						"$tabs\t<url>$url</url>\n" .
    						"$tabs\t<finestra>$finestra</finestra>\n" .
    						"$tabs\t<etichetta>" . htmlspecialchars($etichetta) . "</etichetta>\n" .
    						"$tabs\t<selezionato>" . ($isSelected ? 1 : 0) . "</selezionato>\n" .
    					"$tabs</wamenu_voce>\n";
    						
	    return $isSelected;
	    
	    }
	    
    //***********************************************************************
	/**
	* chiude il menu
	*
	* @return void
	*/
	function chiudi()
		{
	 	$this->buffer .= "\t<wamenu_sezioni_selezionate>\n";
		foreach ($this->sectionsToSelect as $id)
		 	$this->buffer .= "\t\t<id_sezione>$id</id_sezione>\n";
	 	$this->buffer .= "\t</wamenu_sezioni_selezionate>\n";
				
	 	$this->buffer .= "</wamenu>\n\n";
	 	
		}
		 
	//***************************************************************************
	/**
	* mostra
	*
	* deve essere invocato invocato al termine della costruzione
	* del menu per produrne l'output. Dopo l'invocazione di questo metodo non
	* ha piu' alcun senso operare sul menu lato server: il controllo passa
	* alla parte client che provvedera' con il comportamento previsto dal foglio 
	 * di stilexslt.
	 * 
	* @param boolean $bufferizza se false, allora viene immediatamente effettuato
	* l'output del menu; altrimenti la funzione ritorna il buffer di output 
	* del menu stesso
	* @return void|string
	*/
	function mostra($bufferizza = false)
		{
		// Create an XSLT processor
		$xp = new XsltProcessor();
		
		// create a DOM document and load the XSL stylesheet
		$xsl = new DomDocument;
		$xsl->load($this->xslt);
		
		// import the XSL styelsheet into the XSLT process
		$xp->importStylesheet($xsl);
		
		// create a DOM document and load the XML datat
		$xml_doc = new DomDocument;
		//  $xml_doc->load("test.xml");
		$xml_doc->loadXML($this->buffer);
		
		// transform the XML into HTML using the XSL file
		if (!$html = $xp->transformToXML($xml_doc))
			trigger_error('XSL transformation failed.', E_USER_ERROR);
			
		// in alcuni casi e' comodo usare il metodo di output xhtml; questo pero'
		// crea una riga !DOCTYPE che non ha senso per la sola tabella (che e' solo una parte
		// della pagina e potrebbe anche essecene piu' d'una); nel caso rimuoviamo
		// il tag
		while (true)
			{
			list ($prima, $dopo) = explode("<!DOCTYPE ", $html, 2);
			if (!$dopo)
				break;
			list ($dt, $dopo) = explode(">", $dopo, 2);
			$html = "$prima$dopo";
			}
		
		if ($bufferizza)
			return $html;
			
		echo $html;
		}
		
	//***************************************************************************
	/**
	* mostraXML
	*
	* da usare in fase di debug per mostrare l'output XML anziche' l'HTML generato
	* dall'XSLT.
	* @param boolean $bufferizza se false, allora viene immediatamente effettuato
	* l'output del menu; altrimenti la funzione ritorna il buffer di output 
	* del menu stesso
	* @return void|string
	*/
	function mostraXML($bufferizza = false)
		{
		if ($bufferizza)
			return $this->buffer;
			
		header("Content-Type: text/xml; charset=utf-8");			
		echo $this->buffer;
		}
		
    //***********************************************************************
	/**
	* verifica se la voce di menu corrente corrisponde alla pagina corrente
	*
	* @ignore
	* @access protected
	*/
	function _isCurrent($href)
		{
		list($mn_path, $mn_qs) = explode('?', $href);
		//list($srv_path, $srv_qs) = explode('?', $_SERVER['REQUEST_URI']);
		// if ($mn_path != $srv_path)
		if ($mn_path != $_SERVER['SCRIPT_NAME'])
			return false;
		
		$prms = explode("&", $mn_qs);
		foreach($prms as $prm)
			{
			list($n, $v) = explode("=", $prm);
			$mn_prms[$n] = $v;
			}

		//$prms = explode("&", $srv_qs);
		$prms = explode("&", $_SERVER['QUERY_STRING']);
		foreach($prms as $prm)
			{
			list($n, $v) = explode("=", $prm);
			$srv_prms[$n] = $v;
			}
		
		// eliminiamo gli elementi vuoti e eventuali parametri waTabella
		foreach($mn_prms as $n => $v)
			{
			if ($n == "watbl_params" || $n == "")
				unset($mn_prms[$n]);
			}
		foreach($srv_prms as $n => $v)
			{
			if ($n == "watbl_params" || $n == "")
				unset($srv_prms[$n]);
			}
		if (count($srv_prms) != count($mn_prms))
			return false;
		foreach($srv_prms as $n => $v)
			{
			if ($mn_prms[$n] != $v)
			return false;
			}
			
	    return true;
		}
	
	//***********************************************************************
	/**
	* @ignore
	* @access protected
	*/	
	function GetMyDir()
		{
		if (strpos(__FILE__, "\\") !== false)
			{
			// siamo sotto windows
			$thisFile = strtolower(str_replace("\\", "/", __FILE__));
			$dr = strtolower(str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']));
			}
		else
			{
			$thisFile = __FILE__;
			$dr = $_SERVER['DOCUMENT_ROOT'];
			}
		
		if (substr($dr,-1) == "/")
			$dr = substr($dr, 0, -1);
		if ($dr != substr($thisFile, 0, strlen($dr)))
			// quando la document root non e' in comune con la path del file corrente, 
			// allora significa che siamo in ambiente di sviluppo, e si includono
			// i file da un link simbolico; in questo caso la libreria deve essere 
			// posta immediatamente al di sotto della document root; se non si puo'
			// fare, occorre copiare la lib dove si ritiene opportuno
			$toret = "/" . basename(dirname(dirname($thisFile))) . "/" . basename(dirname($thisFile));
		else
			$toret = substr(dirname($thisFile), strlen($dr));
			
		return $toret;		
		}
	
	}
	
//***************************************************************************
} //  if (!defined('_WA_MENU'))
    
?>