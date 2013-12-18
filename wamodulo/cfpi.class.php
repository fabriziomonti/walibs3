<?php

/**
 * @package waModulo
 * @version 3.0
 * @author G.Gaiba, F.Monti
 * @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 */
if (!defined('_WA_CFPI'))
	{
	/**
	 * @ignore
	 */
	define('_WA_CFPI', 1);

	/**
	 * @ignore
	 */
	include_once(dirname(__FILE__) . "/testo.class.php");

//***************************************************************************
//****  classe waCFPI *******************************************************
//***************************************************************************
	/**
	 * waCFPI
	 *
	 * classe per la gestione dei controlli destinati a contenere un codice fiscale,
	 * una partita IVA o entrambi. 
	 * 
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
	class waCFPI extends waTesto
		{

		/**
		 * @ignore
		 * @access protected
		 */
		var $tipo = 'cfpi';

		/**
		 * flag di gestione CF (default true)
		 * @var boolean
		 */
		var $gestioneCF = true;

		/**
		 * flag di gestione PI (default false)
		 * @var boolean
		 */
		var $gestionePI = false;

		//***************************************************************************
		/**
		 * @access protected
		 * @ignore
		 */
		function mostra()
			{
			if ($this->gestioneCF)
				$this->caratteriMax = $this->caratteriVideo = 16;
			elseif ($this->gestionePI)
				$this->caratteriMax = $this->caratteriVideo = 11;
			
			$this->xmlOpen();
			$this->xmlAdd("valore", $this->valore);
			$this->xmlAdd("caratteri_max", $this->caratteriMax);
			$this->xmlAdd("caratteri_video", $this->caratteriVideo);
			$this->xmlAdd("gestione_cf", $this->gestioneCF);
			$this->xmlAdd("gestione_pi", $this->gestionePI);
			$this->xmlClose();
			}

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

			if ($valoreIn === '') return null;

			$tester = strtoupper(substr($valoreIn, 0, 1));
			if (preg_match('/^[A-Z]$/', $tester) && $this->gestioneCF) return $this->verificaCF($valoreIn);
			elseif (preg_match('/^\d$/', $tester) && $this->gestionePI) return $this->verificaPI($valoreIn);

			return null;
			}

		//****************************************************************************************
		/**
		 * @ignore
		 * @return string
		 */
		protected function verificaCF($valoreIn)
			{
			$pari = array(
				'0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4,
				'5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
				'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4,
				'F' => 5, 'G' => 6, 'H' => 7, 'I' => 8, 'J' => 9,
				'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14,
				'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19,
				'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24,
				'Z' => 25
			);

			$dispari = array(
				'0' => 1, '1' => 0, '2' => 5, '3' => 7, '4' => 9,
				'5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21,
				'A' => 1, 'B' => 0, 'C' => 5, 'D' => 7, 'E' => 9,
				'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21,
				'K' => 2, 'L' => 4, 'M' => 18, 'N' => 20, 'O' => 11,
				'P' => 3, 'Q' => 6, 'R' => 8, 'S' => 12, 'T' => 14,
				'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24,
				'Z' => 23
			);

			$valoreIn = strtoupper($valoreIn);
			$sum = 0;

			for ($i = 1; $i < 16; $i++) $sum += ($i % 2) ? $dispari[$valoreIn[$i - 1]] : $pari[$valoreIn[$i - 1]];

			if (chr($sum % 26 + ord('A')) != substr($valoreIn, 15))
			// check digit sbagliato return null;

			if (preg_match('/^[A-Z]{6}\d{2}[A-EHLMPRST]\d{2}[A-Z]\d{3}[A-Z]$/', $valoreIn))
			// codice standard, non c'e' bisogno di controllare omocodia return $valoreIn;

			// verifichiamo che la parte letterale sia composta effettivamente di
			// lettere e che la parte normalmente numerica contenga solo lettere
			// previste nei casi di omocodia
			if (!preg_match('/^[A-Z]{6}[\dL-NP-V]{2}[A-EHLMPRST][\dL-NP-V]{2}[A-Z][\dL-NP-V]{3}[A-Z]$/', $valoreIn)) return null;

			// una eventuale lettera al posto del numero indicante la decina del mese
			// non può essere una U o una V (corrispondenti a 8 e 9)
			if (!preg_match('/^[\dA-Z]{9}[\dL-NP-T][\dA-Z]{6}$/', $valoreIn)) return null;

			return $valoreIn;
			}

		//****************************************************************************************
		/**
		 * controlla una pèartita iva
		 * @ignore
		 * @return string
		 */
		protected function verificaPI($valoreIn)
			{
			//la p.iva deve avere solo 11 cifre
			if (!preg_match('/^[0-9]{11}$/', $valoreIn)) 
				return null;

			$primo = 0;
			for ($i = 0; $i <= 9; $i+=2) $primo+= ord($valoreIn[$i]) - ord('0');

			for ($i = 1; $i <= 9; $i+=2)
				{
				$secondo = 2 * ( ord($valoreIn[$i]) - ord('0') );
				if ($secondo > 9) 
					$secondo = $secondo - 9;
				$primo+=$secondo;
				}
			if ((10 - $primo % 10) % 10 != ord($valoreIn[10]) - ord('0'))
				return null;

			return $valoreIn;
			}

		//****************************************************************************************
		}

	// fine classe waCFPI
//***************************************************************************
//******* fine della gnola **************************************************
//***************************************************************************
	} //  if (!defined('_WA_CFPI'))
?>