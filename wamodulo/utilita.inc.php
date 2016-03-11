<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

//***************************************************************************
//***   utilities   *********************************************************
//***************************************************************************
/**
 * 
 * @ignore
 */
function wamodulo_miaPath()
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

?>