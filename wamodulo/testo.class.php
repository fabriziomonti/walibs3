<?php
/**
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WA_TESTO'))
{
/**
* @ignore
*/
define('_WA_TESTO',1);

/**
* @ignore
*/
include_once(dirname(__FILE__) . "/controllo.class.php");

//***************************************************************************
//****  classe waTesto ******************************************************
//***************************************************************************
/**
* waTesto
*
* classe per la gestione di un controllo text generico.
* 
* @package waModulo
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2016 {@link http://www.webappls.com WebAppls} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waTesto extends waControllo
	{
	
	/**
	* larghezza in caratteri del controllo
	*
	* questa informazione viene utilizzata a piacere nella UI
	* @var integer
	*/
	var $caratteriVideo			= 50;
	
	/**
	* nr. massimo di caratteri accettabili dal controllo
	*
	* laddove possibile, la classe provvedera' a desumere dal database questa informazione
	* @var integer
	*/
	var $caratteriMax		= 255;

	/**
	* @ignore
	* @access protected
	*/
	var $tipo			= 'testo';
	
	//***************************************************************************
	/**
	* @ignore
	*/
	function mostra()
		{
		if ($this->corrispondenzaDB && $this->modulo->righeDB)
			$this->caratteriMax = $this->modulo->righeDB->lunghezzaMaxCampo($this->nome);
		
		$this->xmlOpen();
		$this->xmlAdd("valore", $this->valore);
		$this->xmlAdd("caratteri_max", $this->caratteriMax);
		$this->xmlAdd("caratteri_video", $this->caratteriVideo);

		$this->xmlClose();
		}


	}	// fine classe waTesto
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
} //  if (!defined('_WA_TESTO'))
?>