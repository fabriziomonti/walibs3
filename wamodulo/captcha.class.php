<?php

/**
 * @package waModulo
 * @version 3.0
 * @author G. Di Bona, F.Monti
 * @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 */
if (!defined('_WA_CAPTCHA'))
	{
	/**
	 * @ignore
	 */
	define('_WA_CAPTCHA', 1);

	/**
	 * @ignore
	 */
	include_once(dirname(__FILE__) . "/testo.class.php");

//***************************************************************************
//****  classe waCaptcha *******************************************************
//***************************************************************************
	/**
	 * waCaptcha
	 *
	 * classe per la gestione del submit di un codice di controllo, al fine di
	 * verificare la presenza fisica di un operatore ed evitare le azioni dei 
	 * "bot". 
	 * 
	 * Il controllo può funzionare solo se la sessione è attiva!
	 * 
	 * @package waModulo
	 * @version 3.0
	 * @author G.Di Bona, F.Monti
	 * @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
	 * @license http://www.gnu.org/licenses/gpl.html GPLv3
	 */
	class waCaptcha extends waTesto
		{

		/**
		 * @ignore
		 * @access protected
		 */
		var $tipo = 'captcha';

		/**
		 * per sua natura il controllo è obbligatorio
		 */
		var $obbligatorio = true;

		/**
		 * per sua natura il controllo non ha corrispondenza sul db
		 */
		var $corrispondenzaDB = false;

		/**
		* larghezza in caratteri del controllo
		*
		* questa informazione viene utilizzata a piacere nella UI
		* @var integer
		*/
		var $caratteriVideo			= 5;

		/**
		* nr. massimo di caratteri accettabili dal controllo
		*
		* @var integer
		*/
		var $caratteriMax		= 5;

		//***************************************************************************
		/**
		 * @access protected
		 * @ignore
		 */
		function mostra()

			{
			// creiamo la stringa che andrà mostrata nell'immagine e la
			// salviamo in sessione
			$mt = microtime();
			$elems = explode(" ", microtime());
			$chiave = chr((substr($elems[1], -1) + ord('a'))) . substr($elems[0], 2, 4) . substr($elems[1], -3);
			$chiave = substr($chiave, 0, -1) . chr(substr($chiave, -1) + ord('l'));
			// sostituisco un'eventuale 0 con 1 in modo da non potersi 
			// confondere con la lettera "o" maiuscola
			$chiave = str_replace("0", "1", substr($chiave, 0, $this->caratteriMax));
			
			// il valore in questo caso è la chiave del parametro di sessione 
			// che nasconde il vero valore
			$this->valore = microtime(true) * rand(2, 5);
			$_SESSION["WAMODULO_CODICE_CAPTCHA_$this->valore"] = $chiave;
		
			parent::mostra();
			}

	//***************************************************************************
	/**
	 * converte il valore proveniente dal post nel valore logico del controllo
	* @ignore
	*/	
	function input2valoreInput($valoreIn)
		{
		$this->valoreInput = false;
		
		if ($valoreIn === "__wamodulo_valore_non_ritornato__")
			$this->inputNonRitornato = true;
		elseif (is_array($valoreIn))
			$this->valoreInput = $_SESSION["WAMODULO_CODICE_CAPTCHA_$valoreIn[k]"] == $valoreIn['v'];
		
		return $this->valoreInput;
		}
	
		//****************************************************************************************
		}

	// fine classe waCaptcha
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
	} //  if (!defined('_WA_CAPTCHA'))
?>