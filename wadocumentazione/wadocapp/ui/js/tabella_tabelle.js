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
	dammiNomePaginaRitorno: function (id)
		{
		return escape(
					"Tabella: " + 
					this.tabella.righe[id].campi.nomePagina + "/" + 
					(
					 this.tabella.righe[id].campi.titolo ?
					 this.tabella.righe[id].campi.titolo :
					 this.tabella.righe[id].campi.nome
					)
				);
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Colonne: function (id)
		{
		this.apriPagina("tabella_colonne.php?idTabella=" + id + 
						"&nomepaginaritorno=" + this.dammiNomePaginaRitorno(id));
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Azioni: function (id)
		{
		this.apriPagina("tabella_azioni.php?idTabella=" + id + 
						"&nomepaginaritorno=" + this.dammiNomePaginaRitorno(id));
		}
		
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
document.wapagina = new wapagina();




