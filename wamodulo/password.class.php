<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_PASSWORD'))
{
/**
* @ignore
*/
define('_WA_PASSWORD',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/testo.class.php");

//***************************************************************************
//****  classe waPassword *******************************************************
//***************************************************************************
/**
* waPassword
*
* classe per la gestione dei controlli di tipo password. 
 * 
* E' un normale {@link waTesto} dal quale si differenzia solo per il tipo, in 
* modo da permetterne il riconoscimento lato client e di conseguenza le le
* relative procedure di controllo
*
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waPassword extends waTesto
	{
	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'password';

	}	// fine classe waPassword
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_PASSWORD'))
?>