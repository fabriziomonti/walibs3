<?php
/**
* @package waTabella
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

include_once(dirname(__FILE__) . '/fpdf/fpdf.php');

//******************************************************************************
/**
 * estensione della classe fpdf che:
 * - gestisce dati in input in codifica UTF8
 * - implementa la cella multilinea
 * 
 * ya sta per "yet another"...
 * 
 * @ignore
 */
// 
//******************************************************************************
class ya_wam_pdf extends FPDF
	{

	//**************************************************************************
	function isoText($txt)
		{
		$txt = str_replace("€", "E.", $txt);
		$txt = str_replace("’", "'", $txt);
		$txt = str_replace("‘", "'", $txt);
		$txt = mb_convert_encoding($txt, 'ISO-8859-1', 'UTF-8');
		return $txt;
		}

	//**************************************************************************
	function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '')
		{
		return parent::Cell($w, $h, $this->isoText($txt), $border, $ln, $align, $fill, $link);
		}

	//**************************************************************************
	/**
	 * Dato un array crea una riga di una tabella con acapi preinseriti ed 
	 * automatici
	 *
	 * (eredità di Elisa Andrini)
	 * 
	 * @param   mixed    la larghezza delle celle in ordine di composizione; 
	 *                   possibile inserire un unico valore se si vuole che tutte le celle siano della stessa dimensione
	 * @param   double   l'altezza della cella (per ogni riga...)
	 * @param   array    i testi da inserire nelle varie celle
	 * @param   mixed    Dove mettere i bordi o meno (vedere il metodo Cell() )
	 * @param   mixed    Allineamento (un unico valore o un array.... vedere il metodo Cell())
	 * @param   integer  se riempire o meno le celle.
	 * @param   array	 font delle varie celle; ogni font e' un array composto dagli elementi family, style, size (es. array("Arial", 'B', 12) )
	 *
	 * @access  public
	 */
	function MultiLineRow($w, $h, $row, $border = 1, $align = 'L', $fill = 0, $font = array())
		{

		$x = $this->GetX();
		$tot = count($row);  // numero di colonne in una riga
		$max = 0;			// numero di righe di testo massimo in una riga
		// potrebbero aver messo un'unico valore per w e quindi faccio tutto uguale
		if (!is_array($w)) $w = array_pad(array(), $tot, $w);
		// o aver messo meno valori di quelli necessari e quindi completo con dimensione 0 e quindi automatico
		else $w = array_pad($w, $tot, 0);

		// devo assegnare delle dimensione automatiche alle celle per cui ho valore di w = 0;
		// trovo il numero delle celle vuote, 
		// calcolo quanto spazio mi rimane tra i margini 
		// e lo divido tra le celle che ne hanno bisogno
		$nvuoti = array_count_values($w);
		$nvuoti = $nvuoti[0];
		if ($nvuoti != 0)
			{
			$wtot = array_sum($w);							 // usato
			$spazio = $this->w - $this->lMargin - $this->x;	// spazio totale
			$filler = ($spazio - $wtot) / $nvuoti;			 // per ogni cella non dimensionata
			}

		// preparo il contenuto celle dividendone il testo in righe. 
		// Gia' che ci sono guardo quante righe avranno le celle
		for ($i = 0; $i < $tot; $i++)
			{
			if ($w[$i] == 0) $w[$i] = $filler;
			$this->_MultiLineSetFont($font[$i]);
			$newRow [$i] = $this->_MultiLineCell($row[$i], $w[$i]);
			if ($max < $now = count($newRow [$i])) $max = $now;
			}//end for($i=0; $i<$totale; $i ++) 
		// per le celle che non hanno dimensione massima creo nuove righe riempite di niente
		for ($i = 0; $i < $tot; $i++) $newRow [$i] = array_pad($newRow[$i], $max, "");

		//Se border non � un array lo rendo tale
		if (!is_array($border)) $border = array_pad(array(), $tot, $border);
		//se sono stati messi meno valori di quelli necessari li aggiungo duplicando l'ultimo valore immesso
		else $border = array_pad($border, $tot, $border[count($border)]);

		// copiato da MultiCell ... per i bordi delle celle
		//e riadattato per array
		for ($i = 0; $i < $tot; $i++)
			{
			$b[$i] = 0;
			if ($border[$i] == 1)
				{
				$border[$i] = 'LTRB';
				$b[$i] = 'LRT';
				$b2[$i] = 'LR';
				}
			else
				{
				$b2[$i] = '';
				if (strpos(' ' . $border[$i], 'L')) $b2[$i] .= 'L';
				if (strpos(' ' . $border[$i], 'R')) $b2[$i] .= 'R';
				$b[$i] = (strpos(' ' . $border[$i], 'T')) ? $b2[$i] . 'T' : $b2[$i];
				} // end if ($border ==1 ) ... else...
			} // end for
		// adesso creo il codice pdf per la riga della tabella
		for ($i = 0; $i < $max; $i++)
			{
			for ($j = 0; $j < $tot; $j++)
				{
			$this->_MultiLineSetFont($font[$j]);
				// dalla seconda riga ... non devo piu' fare il bordo in alto
				if ($i == 1) $b[$j] = $b2[$j];
				// ultima riga... eventualmente devo fare il bordo in basso
				if (($i == $max - 1) && strpos(' ' . $border[$j], 'B')) $b[$j] .= 'B';
				// creo la cella
				if (is_array($align)) $this->Cell($w[$j], $h, $newRow[$j][$i], $b[$j], 0, $align[$j], $fill);
				else $this->Cell($w[$j], $h, $newRow[$j][$i], $b[$j], 0, $align, $fill);
				}
			$this->Ln();
			$this->SetX($x);
			}
		}

	//**************************************************************************
	/**
	 * Dato una stringa la divide in un'array considerando gli acapi, preinseriti o automatici, 
	 * e stando attento alle dimensioni dei caratteri
	 *
	 * (eredità di Elisa Andrini)
	 * 
	 * @param   string   contenuto dela cella da spezzare a seconda delle dimensioni della cella 
	 *                   stando attenti al carattere usato
	 * @param   double   la larghezza della cella 
	 *
	 * @access  private
	 */
	function _MultiLineCell($cell, $w)
		{
		$newCell = array();

		// dimensione della cella tenendo conto dei margini... calcolata nell'unita' di misura in cui sto lavorando
		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;

		// elimino tutti gli acapi non unix e l'ultimo acapo
		$s = str_replace("\r", '', $cell);
		$nb = strlen($s);
		if ($nb > 0 && $s[$nb - 1] == "\n") $nb--;

		// un po' di inizializzazioni
		$index = 0;  // indice dell'array di risposta
		$i = 0;	 // dove sto leggendo
		$j = 0;	 // fino dove sono arrivata a copiare
		$sep = -1;	// nessuno spazio trovato per la divisione
		$line = 0;
		$l = 0;	 // dimensione della stringa trovata fino ad ora... somma dell'ingombro dei caratteri
		//$ns  = 0; 
		//$nl  = 1; 
		// e comincio .... 
		while ($i < $nb)
			{
			// carattere successivo
			$c = $s[$i];

			// se il carattere e' un acapo '\n'
			if ($c == "\n")
				{
				// inserisco la sottostringa trovata fino ad ora inserisco nell'array di risposta
				$newCell[$index++] = substr($s, $j, $i - $j);
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				//$ns    = 0; 
				//$nl++; 
				continue;
				} // end if ($c == "\n")
			// il carattere e' uno spazio e quindi un candidato per la divisione
			if ($c == ' ')
				{
				$sep = $i;
				//$ls  = $l; 
				//$ns++; 
				} // end if ($c == ' ')

			$l += $this->CurrentFont['cw'][$c];

			// se l'ingombro supera la dimensione massima disponibile devo dividere la riga altrimenti passo oltre
			if ($l > $wmax)
				{
				// acapo automatico senza spazi
				if ($sep == -1)
					{
					// scrivo almeno un carattere.... 
					if ($i == $j) $i++;
					$newCell[$index++] = substr($s, $j, $i - $j);
					}
				// acapo automatico con spazio...poi devo ricominciare l'elaborazione da dopo lo spazio
				else
					{
					$newCell[$index++] = substr($s, $j, $sep - $j);
					$i = $sep + 1;
					} // end if... else...
				// ho finito una riga e mi preparo per la nuova
				$sep = -1;
				$j = $i;
				$l = 0;
				//$ns    = 0;
				//$nl++;
				}
			else
				{
				$i++;
				} // end if ($l > $wmax) else 
			} //end while ($i < $nb)
		// scrivo tutto cio' che rimane...
		$newCell[$index++] = substr($s, $j, $i);
		return $newCell;
		}

	//**************************************************************************
	/**
	 * definisce il font di una cella di una multiline
	 * @access  private
	 */
	function _MultiLineSetFont($font)
		{
		if ($font)
			{
			list($family, $style, $size) = $font;
			$this->SetFont($family, $style, $size);
			}
		
		}
		
	}
//*****************************************************************************
?>