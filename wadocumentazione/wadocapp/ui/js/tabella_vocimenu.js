//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
// classe wapagina
var wapagina = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: wadocapp,

	//-------------------------------------------------------------------------
	// proprieta'

	//-------------------------------------------------------------------------
	//initialization
			
	//-------------------------------------------------------------------------
	azione_watabella_Pagina: function (id)
		{
		// l'id del menu è il valore corrente della select
		var idPagina = this.tabella.RPC("rpc_dammiIdPagina", id);
		this.apriPagina("tabella_pagine.php?idPagina=" + idPagina + 
						"&nomepaginaritorno=" + escape("Voce menu: " + 
													this.tabella.righe[id].campi.nomeMenu + 
													"/" + 
													this.tabella.righe[id].campi.etichetta));
		}
		
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
document.wapagina = new wapagina();




