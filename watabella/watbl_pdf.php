<?php
/**
* @package waTabella
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

include_once(dirname(__FILE__) . '/ya_wam_pdf.php');

//******************************************************************************
/**
 * classettina che dato un oggetto waTabella ne produce l'output
 * pdf
 * 
 * @ignore
 * 
 */
//******************************************************************************
class watbl_pdf extends ya_wam_pdf
	{
	var $tabella;
	
	var $allineamenti;
	var $larghezzaPagina;
	var $larghezzaColonnaDefault;

	//**************************************************************************
	/**
	 * 
	 */
	function __construct(waTabella $tabella)
		{
		$this->tabella = $tabella;
		parent::__construct($this->tabella->pdf_orientazione);
		$this->AliasNbPages();
		
		// calcolo della larghezza percentuale media di una colonna; se non 
		// viene fornita la larghezza della colonna verra' usato questo valore 
		// di default
		$nrCol = 0;
		foreach ($this->tabella->colonne as $col)
			{
			if ($col->mostra)
				$nrCol++;
			}
		
		$this->larghezzaPagina = $this->w - ($this->lMargin + $this->rMargin);
		$this->larghezzaColonnaDefault = intval(floor($this->larghezzaPagina / $nrCol));
		$this->allineamenti = array(WATBL_ALLINEA_CENTRO => 'C', WATBL_ALLINEA_DX => 'R', WATBL_ALLINEA_SX => 'L');
		
		$this->Open();
		$this->SetTitle($table->titolo, true);
		$this->AddPage();
		
		}

	//**************************************************************************
	/**
	 *
	 */
	function Header()
		{
		if ($this->page == 1)
			{
			$this->SetFont("Arial",'B', 12);
			$this->Cell(0, 8, $this->tabella->titolo, 0, 1, 'C');
			}
			
		$this->SetFillColor(0xcc);
		$this->SetFont("Arial",'B', 9);
		$celle = $larghezze = $allineamenti = array();
		foreach ($this->tabella->colonne as $col)
			{
			if ($col->mostra)
				{
				$celle[] = $col->etichetta;
				$allineamenti[] = $this->allineamenti[$col->allineamento];
				$larghezze[] = $col->pdf_perc ? intval(floor($this->larghezzaPagina * $col->pdf_perc / 100)) : $this->larghezzaColonnaDefault;
				}
			}
		$this->MultiLineRow($larghezze, 5, $celle, 1, $allineamenti, 1);
		
		}
	
	//**************************************************************************
	/**
	 * 
	 */
	function Footer()
		{
		$this->SetY(-12);
		$this->Cell(0, 4, 'pagina ' . $this->PageNo() . " di {nb}", "T", 0, 'C');
		}

	//**************************************************************************
	/**
	 * esporta la tabella
	 *
	 */
	function esporta()
		{
		
		$this->SetFillColor(0xff);
		$this->SetFont("Arial",'', 9);
		
		// si legge da db a blocchi di 100 righe, in modo da non sforare il 
		// memory limit
		while ($this->tabella->leggiBloccoSuccessivoEsportazione())
			{
			foreach ($this->tabella->righeDB->righe as $this->tabella->record)
				{
				$celle = $larghezze = $allineamenti = array();
				foreach ($this->tabella->colonne as $col)
					{
					if ($col->mostra)
						{
						if (!empty($col->funzioneCalcolo))
							$celle[] = call_user_func($col->funzioneCalcolo, $this->tabella);
						else
							
							
							{
							switch($col->formattazione)
								{
								case WATBL_FMT_DATA:
									$celle[] =  $this->tabella->record->valore($col->nome) ?
												date("d/m/Y", $this->tabella->record->valore($col->nome)) :
												'';
									break;
								case WATBL_FMT_DATAORA:
									$celle[] =  $this->tabella->record->valore($col->nome) ?
												date("d/m/Y H:i", $this->tabella->record->valore($col->nome)) :
												'';
									break;
								case WATBL_FMT_ORA:
									$celle[] =  $this->tabella->record->valore($col->nome) ?
												date("H:i", $this->tabella->record->valore($col->nome)) :
												'';
									break;
								case WATBL_FMT_DECIMALE:
									$celle[] =  $this->tabella->record->valore($col->nome) !== null && $this->tabella->record->valore($col->nome) !== '' ?
												number_format($this->tabella->record->valore($col->nome), $col->nrDecimali, ",", ".") :
												'';
									break;
								case WATBL_FMT_STRINGA:
								case WATBL_FMT_INTERO:
								case WATBL_FMT_CRUDO:
									$celle[] =  $this->tabella->record->valore($col->nome);
									break;
								default:
									$Index = $this->tabella->righeDB->indiceCampo($col->nome);
									switch ($this->tabella->righeDB->tipoCampo($col->nome))
										{
										case WADB_DECIMALE:
										$celle[] =  $this->tabella->record->valore($col->nome) !== null && $this->tabella->record->valore($col->nome) !== '' ?
													number_format($this->tabella->record->valore($col->nome), $col->nrDecimali, ",", ".") :
													'';
											break;
										case WADB_DATA:
											$celle[] =  $this->tabella->record->valore($col->nome) ?
														date("d/m/Y", $this->tabella->record->valore($col->nome)) :
														'';
											break;
										case WADB_DATAORA:
											$celle[] =  $this->tabella->record->valore($col->nome) ?
														date("d/m/Y H:i", $this->tabella->record->valore($col->nome)) :
														'';
											break;
										case WADB_ORA:
											$celle[] =  $this->tabella->record->valore($col->nome) ?
														date("H:i", $this->tabella->record->valore($col->nome)) :
														'';
											break;
										default:
											$celle[] =  $this->tabella->record->valore($col->nome);
										}
								}
							}
							
						$allineamenti[] = $this->allineamenti[$col->allineamento];
						$larghezze[] = $col->pdf_perc ? intval(floor($this->larghezzaPagina * $col->pdf_perc / 100)) : $this->larghezzaColonnaDefault;
						}
					}
				$this->MultiLineRow($larghezze, 4, $celle, 1, $allineamenti, 0);
				}
			}
		
		$this->Output($this->tabella->soloLettereNumeri($this->tabella->titolo) . date("_YmdHis") . ".pdf", 'D');
		exit();
		}
	
		
	}
//*****************************************************************************
?>