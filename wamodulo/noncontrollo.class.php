<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_NONCONTROLLO'))
{
/**
* @ignore
*/
define('_WA_NONCONTROLLO',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waNonControllo ***********************************************
//***************************************************************************
/**
* waNonControllo
*
* questa classe permette l'inserimento di un elemento arbitrario, che non sia
 * un'etichetta ne' un controllo di input, all'interno della collezione dei 
 * controlli di un modulo. Il valore di questo oggetto, così come viene passato
 * dall'applicazione, viene passato all'XSLT, il quale saprà applicativamente
 * come gestirlo (l'XSLT di default ne fa un semplice output, senza entrare
 * minimamente nel merito di cosa il valore contiene).
 * 
 * E' in questo modo possibile, per l'applicazione, inserire all'interno della
 * lista dei controlli visualizzati, un elemento a piacere: una waTabella,
 * un paragrafo di testo, qualsiasi cosa. Volendo anche un testo già formattato
 * HTML, anche se non è concettualmente corretto utilizzare questa modalità: 
 * dovrebbe sempre essere l'XSLT che formatta l'output, non l'applicazione
 * PHP. Questa ultima modalità è da usare solo in casi estremi e laddove
 * non esista possibilità di fare altrimenti.
 * 
 * Dal punto di vista dell'input/output questo controllo si comporta
 * esattamente come un {@link waTesto}: se trova match nel record il valore
 * viene preso dal db e se viene trovato match in POST il valore viene
 * tornato all'applicazione. Ovviamente, laddove tutto ciò non abbia 
 * applicativamente senso è sufficiente non utilizzare nomi corrispondenti a 
 * nomi di campo sul db e ignorare qualsiasi riferimento a valori di input.
* 
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waNonControllo extends waControllo
	{
	
	/**
	* informazione di genere applicativo che viene utilizzata a piacere
	 * dall'XSLT
	 * 
	* @var integer
	*/
	var $tipoElemento		= '';

	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'noncontrollo';
	
	//***************************************************************************
	/**
	* @ignore
	*/
	function mostra()
		{
		$this->xmlOpen();
		$this->xmlAdd("tipo_elemento", $this->tipoElemento);
		$this->xmlAdd("valore", "<![CDATA[$this->valore]]>");

		$this->xmlClose();
		}


	}	// fine classe waNonControllo
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_NONCONTROLLO'))
?>