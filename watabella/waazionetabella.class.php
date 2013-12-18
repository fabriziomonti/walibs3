<?php
/**
* @package waTabella
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_AZIONETABELLA'))
{
/**
* @ignore
*/
define('_WA_AZIONETABELLA',1);

//***************************************************************************
//****  classe waAzioneTabella **********************************************
//***************************************************************************
/**
* waAzioneTabella
*
* contiene le informazioni di una azione che la tabella puo' compiere.
 * 
 * Lato server non c'e' alcun riferimento all'azione che effettivamente verra'
 * compiuta lato client: sara' il foglio xslt che implementera' come crede 
 * la funzionalita' che viene qui prevista esclusivamente dal punto di vista
 * logico
*
* @package waTabella
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waAzioneTabella
	{
	/**
	* nome dell'azione (e quindi del bottone associato). 
	*
	* E' buona norma che 
	 * segua le regole di naming della programmazione (no spazi, punteggiatura, 
	 * ecc.), perche' e' verosimile che lato client questa proprieta' venga 
	 * utilizzata per accedere ad un oggetto di programmazione.
	 * 
	* @var string
	*/	
	var $nome;
	
	/**
	* caption del bottone. 
	*
	* Se non valorizzato si assumera' come etichetta il 
	 * valore della proprietà {link nome}.
	 * 
	* @var string
	*/	
	var $etichetta;
	
	/**
	* flag azione su record
	*
	* indica se l'azione viene eseguita su un singolo record della tabella 
	* oppure su un entita' diversa dal singolo record, entita' che puo' essere 
	* o l'intero insieme dei record della pagina
	* o l'insieme dei record selezionati qualora sia prevista una multiselezione
	* o qualsiasi cosa arbitraria che non sia un singolo record
	 * 
	* @var boolean
	*/	
	var $suRecord = false;
	
	/**
	* callback function di abilitazione dell'azione
	*
	* nome di una funzione/metodo PHP che deve
	* essere chiamata per la verifica dell'abilitazione dell'azione per la riga corrente. 
	* Se il valore passato 
	* e' una stringa, allora verra' invocata una funzione procedurale; se e' un array
	* il primo elemento sara' l'oggetto a cui il metodo appartiene, il secondo
	* elemento il nome del metodo. Il valore ritornato dalla funzione/metodo potra'
	* essere:
	* - true: l'azione e' abilitata per la riga corrente
	* - false: l'azione non e' abilitata per la riga corrente
	* 
	* Alla funzione/metodo verra' passato come parametro l'oggetto 
	* waTabella a cui l'azione appartiene.
	* @var string
	*/	
	var $funzioneAbilitazione = '';
	
//***************************************************************************
	}	// fine classe waAzioneTabella
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_AZIONETABELLA'))
?>