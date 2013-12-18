<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
if (!defined('_WA_EMAIL'))
{
/**
* @ignore
*/
define('_WA_EMAIL',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/testo.class.php");

//***************************************************************************
//****  classe waEmail ************************************************
//***************************************************************************
/**
* waEmail
*
* classe per la gestione dei controlli destinati a contenere un indirizzo email. 
* E' un normale {@link waTesto} dal quale si differenzia solo per il tipo, in 
* modo da permetterne il riconoscimento lato client e di conseguenza le
* relative procedure di controllo
* 
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waEmail extends waTesto
	{
	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'email';

	//****************************************************************************************
	/**
	* Restituisce il valore se valido, altrimenti null
	*
	* Si usa in fase di ricezione dei dati, non
	* durante la costruzione della form.
	*
	* @ignore
	* @return string
	*/
	function input2valoreInput($valoreIn)
		{
		if ($valoreIn === "__wamodulo_valore_non_ritornato__")
			{
			$this->inputNonRitornato = true;
			return null;
			}
			
		if ($valoreIn === '')
			return null;
			
	    if (strlen($valoreIn) < 7) return(null);
	    $arr = explode("@", $valoreIn);
	    if (count($arr) != 2) return(null);
	    $User = $arr[0];
	    $Domain = $arr[1];

	    // check dei caratteri dello user
	    if (strlen($User) < 2) return(null);
	    for ($CharCntr = 0; $CharCntr < strlen($User); $CharCntr++)
	        {
	        $ToCheck = substr($User, $CharCntr, 1);
	        if (! preg_match("/([0-9]|[A-Z]|-|_|\.)/i", $ToCheck)) return(null);
	        }

	    // check dei caratteri del dominio
	    $DomArray = explode(".", $Domain);
	    if (count($DomArray) < 2) return(null);
	    for ($cntr = 0; $cntr < count($DomArray); $cntr++)
	        {
	        if (strlen($DomArray[$cntr]) < 2) return(null);
	        for ($CharCntr = 0; $CharCntr < strlen($DomArray[$cntr]); $CharCntr++)
	            {
	            $ToCheck = substr($DomArray[$cntr], $CharCntr, 1);
	            if (! preg_match("/([0-9]|[A-Z]|-)/i", $ToCheck)) return(null);
	            }
	        }

		
		return $this->valoreInput = $valoreIn;
			
		}

	//****************************************************************************************
	}	// fine classe waEmail
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_EMAIL'))
?>