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
		return escape("Sezione: " + this.tabella.righe[id].campi.sigla);
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Menu: function (id)
		{
		this.apriPagina("tabella_menu.php?idSezione=" + id + 
						"&nomepaginaritorno=" + this.dammiNomePaginaRitorno(id));
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Pagine: function (id)
		{
		this.apriPagina("tabella_pagine.php?idSezione=" + id + 
						"&nomepaginaritorno=" + this.dammiNomePaginaRitorno(id));
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Tabelle: function (id)
		{
		this.apriPagina("tabella_tabelle.php?idSezione=" + id + 
						"&nomepaginaritorno=" + this.dammiNomePaginaRitorno(id));
		},
		
	//-------------------------------------------------------------------------
	azione_watabella_Moduli: function (id)
		{
		this.apriPagina("tabella_moduli.php?idSezione=" + id + 
						"&nomepaginaritorno=" + this.dammiNomePaginaRitorno(id));
		}
		
	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
document.wapagina = new wapagina();




