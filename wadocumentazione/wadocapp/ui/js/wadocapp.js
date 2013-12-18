//*****************************************************************************
//*****************************************************************************
//*****************************************************************************
// classe wapagina
var wadocapp = new Class
(
	{
	//-------------------------------------------------------------------------
	// extends
	Extends: waapplicazione,

	//-------------------------------------------------------------------------
	// proprieta'

	//-------------------------------------------------------------------------
	//initialization
	initialize: function() 
		{
		this.parent();	

		tinyMCE.init(
						{
						relative_urls : true,
						document_base_url : "./",
						forced_root_block : false,
						force_br_newlines : true,
						force_p_newlines : false,
						theme : "advanced",
						mode : "textareas",
						plugins : "fullscreen",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "center",
						theme_advanced_buttons3_add : "fullscreen",
						theme_advanced_statusbar_location : "none",
						readonly : false,

						onchange_callback : "document.wapagina.tinyMCE_change"
						}
					);
		},

	//-------------------------------------------------------------------------
	tinyMCE_change: function(instance) 
		{
		// l'aggiornamento immediato avviene solo sulle tabelle, non sui moduli
		if (document.watabella)
			{
			document.watabella.obj.elements[instance.editorId].value = instance.getBody().innerHTML;
			document.watabella.obj.elements[instance.editorId].onblur();
			}
		},
		
	//-------------------------------------------------------------------------
	// creazione dinamica di una nuova riga in una watabella ; a causa della 
	// presenza di tinyMCE
	// va gestita in un modo un po' particolare: occorre creare la riga,
	// rimuovere gli attributi tinyMCE dalle textarea e poi aggiungere le 
	// textarea a tinyMCE; sorry, non ho trovato un sistema meno incasinato...
	azione_myNuovoSubito: function(textarea_array) 
		{
			
		var idRiga = this.tabella.azione_NuovoSubito();

		for (var nomeTbl in this.tabelle)
			{
			for (var nomeCol in this.tabelle[nomeTbl].colonne)
				{
				if (this.tabelle[nomeTbl].colonne[nomeCol].tipo_input == 'areatesto')
					{
					var spanna = document.getElementById(nomeCol + '[' + idRiga + ']_parent');
					var divva = spanna.parentNode;
					divva.removeChild(spanna);
					var ta = document.getElementById(nomeCol + '[' + idRiga + ']');
					ta.style.display = "inline";
					tinyMCE.execCommand("mceAddControl", true, nomeCol + '[' + idRiga + ']');		
					}
				}
				
				
			}
			
		return idRiga;
		}
		

	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------




