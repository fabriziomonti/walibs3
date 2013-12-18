<?php
include "applicazionetest.inc.php";

//*****************************************************************************
/**
* tabellacorsi 
* 
* questa classe si preoccupera' di mostrare all'utente una tabella di classe 
* {@link waTabella} contenente tutte le amministyrazioni gestite dalla nostra 
* applicazione
* 
* Deriva da {@link applicazioneTest}, dalla quale quindi
* eredita l'interfaccia programmatica (proprieta' e metodi); a questa noi potremo
* aggiungere i nostri metodi applicativi e se necessario modificare il 
* comportamento della classe di default mediante l'override dei metodi.
*/
class tabellaallievi extends applicazioneTest
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
		// costruiamo la tabella
		$tabella = $this->creaTabella();
			
		// prepara la pagina, ossia il contenitore della tabella
		$this->aggiungiElemento($this->dammiMenu());
		$this->aggiungiElemento("Tabella allievi", "titolo");
		$this->aggiungiElemento($tabella);
		
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
		$sql = "SELECT allievi.*, corsi.rifpa, corsi.nome as nomecorso," .
				"organismi.id_organismo, organismi.nome as nomeorgan," .
				"amministrazioni.sigla, amministrazioni.nome as nomeamm" .
				" FROM allievi" .
				" LEFT JOIN corsi ON allievi.id_corso=corsi.id_corso".
				" LEFT JOIN amministrazioni ON corsi.id_amministrazione=amministrazioni.id_amministrazione".
				" LEFT JOIN organismi ON corsi.id_organismo=organismi.id_organismo";
		$tabella = $this->dammiTabella($sql);
		
		// definizione delle proprieta' di base della tabella
		$tabella->titolo = "allievi";
		$tabella->paginaModulo = "moduloallievi.php";
		
		// definizione delle colonne della tabella e delle relative proprieta'
		$tabella->aggiungiColonna("id_allievo", "ID", true, false, true, WATBL_ALLINEA_DX, WATBL_FMT_INTERO);
		$col = $tabella->aggiungiColonna("nome", "Nome");
			$col->aliasDi = 'allievi.nome';
		$tabella->aggiungiColonna("codice_fiscale", "Codice fiscale");
		$tabella->aggiungiColonna("rifpa", "Rif. P.A.");
		$col = $tabella->aggiungiColonna("nomecorso", "Corso");
			$col->aliasDi = 'corsi.nome';
		$col = $tabella->aggiungiColonna("id_organismo", "Cod. org.", true, true, true, WATBL_ALLINEA_DX, WATBL_FMT_INTERO);
			$col->aliasDi = 'organismi.id_organismo';
		$col = $tabella->aggiungiColonna("nomeorgan", "Organismo");
			$col->aliasDi = 'organismi.nome';
		$col = $tabella->aggiungiColonna("sigla", "Amministrazione");
		$col = $tabella->aggiungiColonna("flag_ammissione", "Ammesso", true, false, false, WATBL_ALLINEA_CENTRO);
			$col->funzioneCalcolo = array($this, "mostraAmmissione");
		$col = $tabella->aggiungiColonna("flag_promozione", "Promosso", true, false, false, WATBL_ALLINEA_CENTRO);
			$col->funzioneCalcolo = array($this, "mostraPromozione");
		$col = $tabella->aggiungiColonna("nome_file_curriculum", "Curriculum");
			$col->link = true;
		
		// se la tabella fosse destinata anche all'input (post o rpc), questo 
		// sarebbe il punto dove chiamare $tabella->leggiValoriIngresso()
			
		// lettura dal database delle righe che andranno a popolare la tabella
		if (!$tabella->caricaRighe())
			$this->mostraErroreDB($tabella->righeDB->connessioneDB);
		
		return $tabella;
		}
		
	//*************************************************************************
	function mostraAmmissione(waTabella $table)
		{		
		return $table->record->valore("FLAG_AMMISSIONE") == 1 ? 'si' : 'no';
		}
	
	//*************************************************************************
	function mostraPromozione(waTabella $table)
		{		
		return $table->record->valore("FLAG_PROMOZIONE") == 1 ? 'si' : 'no';
		}
	
	
	}	// fine classe pagina
	
//*****************************************************************************
// istanzia la pagina
$page = new tabellaallievi();
$page->mostraPagina();

