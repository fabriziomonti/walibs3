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
					"Pagina: " +
					this.tabella.righe[id].campi.siglaSezione + "/" + 
					(
					 this.tabella.righe[id].campi.titolo ?
					 this.tabella.righe[id].campi.titolo :
					 this.tabella.righe[id].campi.nome
					)
				);
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Tabelle: function (id)
		{
		this.apriPagina("tabella_tabelle.php?idPagina=" + id + 
						"&nomepaginaritorno=" + this.dammiNomePaginaRitorno(id));
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Moduli: function (id)
		{
		this.apriPagina("tabella_moduli.php?idPagina=" + id + 
						"&nomepaginaritorno=" + this.dammiNomePaginaRitorno(id));
		}
		
		
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
document.wapagina = new wapagina();




