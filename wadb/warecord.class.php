<?php
/**
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/

if (!defined('_WADB_RECORD'))
{
/**
* @ignore
*/
define('_WADB_RECORD',1);

//***************************************************************************
//***************************************************************************
//***************************************************************************
/**
* waRecord
* 
* Classe per la gestione di una riga di database
*
* @package waDB
* @version 3.0
* @author G.Gaiba, F.Monti
* @copyright (c) 2007-2013 {@link http://www.webappls.com WebAppls S.p.A.} Bologna, Italy
* @license http://www.gnu.org/licenses/gpl.html GPLv3
*/
class waRecord
	{
	/**
	*
	* Oggetto {@link waRigheDB} che contiene la riga in oggetto
	* @var waRigheDB 
	*/ 
	var 	$righeDB;

	/**
	*
	* Ordinale della riga all'interno del recordset
	* @var int 
	*/ 
	var	$indiceRiga;
	
	//***********************************************************************
	/**
	* -
	* 
	* costruttore.
	* @param waRigheDB $righeDB istanza della classe per la gestione del 
	* recordset che contiene la riga in oggetto
	* @param int $indiceRiga ordinale della riga all'interno del recordset
	*  connessione al database
	* @ignore
	*/ 
	function __construct ($righeDB, $indiceRiga)
		{
		$this->righeDB = $righeDB;
		$this->indiceRiga = $indiceRiga;
		}
		
	//***********************************************************************
	/**
	* -
	* 
	* Dato, indifferentemente, il nome o l'indice di un campo tra quelli ritornati 
	* dalla query, restituisce il valore del campo corrispondente nella riga.
	* @param mixed (string | int) $nomeOIndiceCampo nome del campo o indice della colonna
	* @param boolean $comeSuDB : se vero torna il valore esattamente come restituito
	* dal motore del database, ossia non formattato per il normale utilizzo PHP
	* @return mixed valore del campo
	*/ 
	function valore($nomeOIndiceCampo, $comeSuDB = false)
		{
		$indiceCampo = $this->righeDB->dammiOrdinaleColonnaDaNomeOIndice($nomeOIndiceCampo);
		return ($comeSuDB ? 
				$this->righeDB->valoriCrudi[$this->indiceRiga][$indiceCampo] :
				$this->righeDB->valori[$this->indiceRiga][$indiceCampo]);
		}
		
	//***********************************************************************
	/**
	* -
	* 
	* Dato, indifferentemente, il nome o l'indice di un campo tra quelli ritornati 
	* dalla query, sostituisce nella riga il valore esistente con quello passato.
	*
	* Il valore sara' consolidato sul db a fronte della successiva chiamata al
	* metodo {@link waRigheDB::salva()} dell'oggetto che contiene il record
	* @param mixed (string | int) $nomeOIndiceCampo nome del campo o indice della colonna
	* @param mixed $valore : il nuovo valore da inserire
	* @return void
	*/ 
	function inserisciValore($nomeOIndiceCampo, $valore)
		{
		$indiceCampo = $this->righeDB->dammiOrdinaleColonnaDaNomeOIndice($nomeOIndiceCampo);
		$this->righeDB->valori[$this->indiceRiga][$indiceCampo] = $valore;
		if ($this->righeDB->statiRecord[$this->indiceRiga] == WADB_RECORD_INALTERATO)
			$this->righeDB->statiRecord[$this->indiceRiga] = WADB_RECORD_MODIFICATO;
		}
		
	//***********************************************************************
	/**
	* -
	*
	* Marca il record per la successiva eliminazione dal db. Il record sara'
	* cancellato effettivamente dal db solo a fronte di successiva chiamata al
	* metodo {@link waRigheDB::salva()} dell'oggetto che contiene il record
	* @return void
	*/ 
	function elimina()
		{
		$this->righeDB->statiRecord[$this->indiceRiga] = WADB_RECORD_DA_CANCELLARE;
		}
		
	}

//*****************************************************************************
} //  if (!defined('_WADB_RECORD'))
?>