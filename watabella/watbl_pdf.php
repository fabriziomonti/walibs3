<?php
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
	
	var $larghezze = array();
	var $allineamenti = array();

	//**************************************************************************
	/**
	 * 
	 */
	function __construct(waTabella $tabella)
		{
		$this->tabella = $tabella;
		parent::__construct($this->tabella->pdf_orientazione);
		$this->AliasNbPages();
		
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
		$celle = array();
		foreach ($this->tabella->colonne as $col)
			{
			if ($col->mostra)
				$celle[] = $col->etichetta;
			}
		$this->MultiLineRow($this->larghezze, 5, $celle, 1, $this->allineamenti, 1);
		
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
	 * determina l'allineamento di una colonna secondo quanto definito dal
	 * programmatore o il default del tipo campo
	 *
	 */
	function dammiAllineamentoColonna(waColonna $col)
		{
		static $codici_allineamento = array(WATBL_ALLINEA_CENTRO => 'C', WATBL_ALLINEA_DX => 'R', WATBL_ALLINEA_SX => 'L');
		if ($col->allineamento)
			// se è stato definito un allineamento, non c'e' bisogno di 
			// desumerlo dal tipo campo
			return $codici_allineamento[$col->allineamento];

		
		switch ($this->tabella->righeDB->tipoCampo($col->nome))
			{
			case WADB_DECIMALE:
			case WADB_INTERO:
				return $codici_allineamento[WATBL_ALLINEA_DX];
				break;
			case WADB_DATA:
			case WADB_DATAORA:
			case WADB_ORA:
				return $codici_allineamento[WATBL_ALLINEA_CENTRO];
				break;
			}
		
		return $codici_allineamento[WATBL_ALLINEA_SX];
		}
		
	//**************************************************************************
	/**
	 * determina la larghezza di una colonna secondo quanto definito dal
	 * programmatore o il default del tipo campo
	 *
	 */
	function dammiLarghezzaColonna(waColonna $col, $larghezzaPagina)
		{
		$rs = $this->tabella->righeDB;

		if ($col->pdf_perc)
			return $col->pdf_perc * $larghezzaPagina / 100;
		elseif ($rs->tipoCampo($col->nome) == WADB_DECIMALE)
			return 40;
		elseif ($rs->tipoCampo($col->nome) == WADB_DATA)
			return 40;
		elseif ($rs->tipoCampo($col->nome) == WADB_DATAORA)
			return 40;
		elseif ($rs->lunghezzaMaxCampo($col->nome) > 50)
			return 50;
		elseif ($rs->lunghezzaMaxCampo($col->nome) < 20)
			return 20;

		return $rs->lunghezzaMaxCampo($col->nome);
		}
		
	//**************************************************************************
	/**
	 * determina euristicamente una larghezza delle colonne per 
	 * cui la percentuale di larghezza non è stata definita e apre il pdf
	 *
	 */
	function apri()
		{
		
		$this->Open($this->tabella->pdf_orientazione);
		$this->SetTitle($this->tabella->titolo, true);
		$rs = $this->tabella->righeDB;
		$larghezzaPagina = $this->w - ($this->lMargin + $this->rMargin);
		
		// determinazione degli attributi delle colonne (allineamento e larghezza
		foreach ($this->tabella->colonne as $col)
			{
			if ($col->mostra)
				{
				$this->allineamenti[] = $this->dammiAllineamentoColonna($col);
				$totlen += $this->larghezze[] = $this->dammiLarghezzaColonna ($col, $larghezzaPagina);
				}
			}
			
		for ($i = 0; $i < count($this->larghezze); $i++)	
			$totLarghezze += $this->larghezze[$i] = intval(floor($this->larghezze[$i] * $larghezzaPagina / $totlen));
		
		// aggiungiamo lo sfrido all'ultima colonna 
		$this->larghezze[count($this->larghezze) - 1] += intval(floor($larghezzaPagina - $totLarghezze));
			
		$this->AddPage();
		
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
						}
					}
				if (!$this->page)
					$this->apri();
				
				$this->MultiLineRow($this->larghezze, 4, $celle, 1, $this->allineamenti, 0);
				}
			}
		
		$this->Output($this->tabella->soloLettereNumeri($this->tabella->titolo) . date("_YmdHis") . ".pdf", 'I');
		exit();
		}
	
		
	}
//*****************************************************************************
