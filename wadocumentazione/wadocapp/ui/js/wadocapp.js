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
						selector : "textarea:not(.invisibile)",
						plugins : "fullscreen, link, image, textcolor, emoticons, table, code, media, template, hr",
						menu :	
							{ 
							edit   : {title : 'Edit'  , items : 'undo redo | cut copy paste pastetext | selectall'},
							insert : {title : 'Insert', items : 'link image media | hr'},
							format : {title : 'Format', items : 'bold italic underline strikethrough superscript subscript | formats removeformat'},
							table  : {title : 'Table' , items : 'inserttable tableprops deletetable | cell row column'},
							tools  : {title : 'Tools' , items : 'fullscreen code'}
							},
			
						toolbar: "fontselect fontsizeselect bullist numlist outdent indent forecolor backcolor emoticons fullscreen",
			
						readonly : false,
						statusbar : false,
						setup: function(editor) 
							{
							editor.on('BeforeRenderUI', function(e) 
								{
								this.settings.width = this.getElement().style.width;
								});

							editor.on('change', function(e) 
								{
								document.wapagina.tinyMCE_change(this);
								}
								);
							}
					
						}
					);		
			
			
		},

	//-------------------------------------------------------------------------
	tinyMCE_change: function(editorInstance) 
		{
		// l'aggiornamento immediato avviene solo sulle tabelle, non sui moduli
		if (document.watabella)
			{
			var textarea = editorInstance.getElement();
			textarea.value = editorInstance.getContent();
			textarea.onblur();
			}
		},
		
	//-------------------------------------------------------------------------
	// creazione dinamica di una nuova riga in una watabella ; a causa della 
	// presenza di tinyMCE
	// va gestita in un modo un po' particolare: tinymce va applicato al
	// textarea dopo che è stato creato
	azione_myNuovoSubito: function(nomeTbl) 
		{
		var idRiga = this.tabelle[nomeTbl].azione_NuovoSubito();

		for (var nomeCol in this.tabelle[nomeTbl].colonne)
			{
			if (this.tabelle[nomeTbl].colonne[nomeCol].tipo_input == 'areatesto')
				{
				tinyMCE.execCommand("mceAddEditor", true, nomeCol + '[' + idRiga + ']');
				}
			}
			
		return idRiga;
		}
		

	}
);

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------




