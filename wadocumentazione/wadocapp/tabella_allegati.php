<?php
include "wadocapp.inc.php";

//*****************************************************************************
/**
*/
class pagina extends waDocApp
	{
	
	//*****************************************************************************
	/**
	* mostraPagina
	* 
	* costruisce la pagina contenente la tabella e la manda in output
	* @return void
	*/
	function mostraPagina()
		{
		if ($_GET['vedifile'])
			//e' stata richiesta la visualizzazione di uno degli allegati
			$this->vediAllegato();
		
		// prepara la pagina, ossia il contenitore della tabella
		$this->aggiungiElemento($this->dammiMenu());
		$this->aggiungiElemento("Allegati", "titolo");
		$this->aggiungiElemento($this->creaTabella());
		
		// manda in output l'intera pagina
		$this->mostra();
		}
		
	//*****************************************************************************
	/**
	* creaTabella
	* 
	* costruisce la tabella 
	* 
	* @return waTabella
	*/
	function creaTabella()
		{
		// creazione della tabella sulla base della query sql
		$sql = "SELECT *" . 
				" FROM wadoc_allegati" .
				" ORDER BY posizione";
		$tabella = $this->dammiTabella($sql);

		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "allegati";
	
		// azioni

		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("idAllegato", "ID", false, false, false);
		
		$col = $tabella->aggiungiColonna("nome", "Nome file");
			$col->link = true;
			
		$col = $tabella->aggiungiColonna("titolo", "Titolo");
			$col->inputTipo = WATBL_INPUT_TESTO;
			
		$col = $tabella->aggiungiColonna("posizione", "Posizione");
			$col->inputTipo = WATBL_INPUT_INTERO;
			
		$col = $tabella->aggiungiColonna("descrizione", "Descrizione");
			$col->inputTipo = WATBL_INPUT_AREATESTO;
			
		// colonne non visibili

		$tabella->leggiValoriIngresso();

		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe())
			$this->mostraErroreDB($tabella->righeDB->connessioneDB);
		
		return $tabella;
		}
		
	//*****************************************************************************
	/**
	*/
	function vediAllegato()
		{
		$dbconn = $this->dammiConnessioneDB();
		$sql = "SELECT *" . 
				" FROM wadoc_allegati" .
				" WHERE idAllegato=" . $dbconn->interoSql($_GET['vedifile']);
		$rs = $this->dammiRigheDB($sql, $dbconn);
		if (!($riga = $rs->righe[0]))
			$this->mostraMessaggio ("Riga non trovata", "Riga non trovata", false, true);
		
		$file = "$this->dirOutput/allegati/" . $riga->valore("nome");
		if (!is_readable($file))
			$this->mostraMessaggio ("File non trovato", "File non trovato", false, true);
		
		
	    header("Pragma: ");
		header("Expires: Fri, 15 Aug 1980 18:15:00 GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0, false");
		header('Content-Length: '. filesize($file));
		
		$nomeOriginale = basename($file);
		$extension = strtolower(pathinfo($nomeOriginale, PATHINFO_EXTENSION));
		if ($extension == "pdf")
			{
			header("Content-Disposition: inline; filename=\"$nomeOriginale\";" );
			header("Content-Type: application/pdf");
			}
		elseif ($extension == "doc")
			{
			header("Content-Disposition: inline; filename=\"$nomeOriginale\";" );
			header("Content-Type: application/msword");
			}
		elseif ($extension == "xls")
			{
			header("Content-Disposition: inline; filename=\"$nomeOriginale\";" );
			header("Content-Type: application/vnd.ms-excel");
			}
		elseif ($extension == "rtf")
			{
			header("Content-Disposition: inline; filename=\"$nomeOriginale\";" );
			header("Content-Type: application/rtf");
			}
		elseif ($extension == "jpg")
			{
			header("Content-Disposition: inline; filename=\"$nomeOriginale\";" );
			header("Content-Type: image/jpeg");
			}
		elseif ($extension == "png")
			{
			header("Content-Disposition: inline; filename=\"$nomeOriginale\";" );
			header("Content-Type: image/png");
			}
		else 
			{
			header("Content-Disposition: attachment; filename=\"$nomeOriginale\";" );
			header("Content-Type: application/force-download");
			header("Content-Transfer-Encoding: binary");
			}
			
		readfile($file);
		exit();
		
		}		
	
	}	// fine classe pagina
	
//*****************************************************************************
// istanzia la pagina
$page = new pagina();
$page->mostraPagina();

