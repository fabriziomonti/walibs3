<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 

<!-- ********************************************************************** -->
<!--  ************* template della pagina ********************************* -->
<!-- ********************************************************************** -->
<xsl:template match="waapplicazione">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<link href='{waapplicazione_path}/uis/wa_default/css/waapplicazione.css' rel='stylesheet'/><xsl:text>&#10;</xsl:text>
			<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/mootools/1.2.5/mootools-yui-compressed.js'></script><xsl:text>&#10;</xsl:text>
			<script type='text/javascript' src='{waapplicazione_path}/uis/wa_default/js/strmanage.js'></script><xsl:text>&#10;</xsl:text>
			<script type='text/javascript' src='{waapplicazione_path}/uis/wa_default/js/waapplicazione.js'></script><xsl:text>&#10;</xsl:text>
			
		    <title>
		    	<xsl:value-of select="titolo" />
		    </title>
		</head>
		<body >
			<noscript>
				<hr />
				<div style='text-align: center'>
					<b>
						Questa applicazione usa Javascript, ma il tuo browser ha questa funzione
						disabilitata. Sei pregato di abilitare Javascript per il dominio <xsl:value-of select="dominio" />
						e ricaricare la pagina.
					</b>
				</div>
				<hr />
			</noscript>
			
			<!-- se lavoriamo con navigazione interna creiamo anche l'iframe destinato a contenere la finestra figlia-->
			<xsl:text>&#10;</xsl:text>
			<xsl:if test="modalita_navigazione = '3'">
				<iframe id='waapplicazione_iframe_figlia' class='waapplicazione_iframe_figlia' style='visibility:hidden'>
				</iframe>
			</xsl:if>
	
			<!-- creazione degli elementi costitutivi della pagina (titolo, tabelle, moduli, testo libero, ecc.-->
			<xsl:for-each select="pagina/elementi/elemento">
				<xsl:text>&#10;</xsl:text>
				<div class="waapplicazione_{nome}">
					<xsl:value-of disable-output-escaping="yes" select="valore" />
				</div>
				<xsl:text>&#10;</xsl:text>
			</xsl:for-each>
			
			<!-- tentativi euristici: qui l'xsl tenta sempre di caricare:-->
			<!-- - un css dell'applicazione (directory_di_lavoro/ui/css/nome_applicazione.css)-->
			<!-- - un css della pagina  (directory_di_lavoro/ui/css/nome_pagina.css)-->
			<!-- - un js dell'applicazione (directory_di_lavoro/ui/js/nome_applicazione.js)-->
			<!-- - un js della pagina  (directory_di_lavoro/ui/js/nome_pagina.js)-->
			<!-- i js della pagina sono sempre gli ultimi a dover essere caricati, altrimenti non vedono le strutture altrui... -->
			<link href='{directory_lavoro}/ui/css/{nome}.css' rel='stylesheet'/><xsl:text>&#10;</xsl:text>
			<link href='{directory_lavoro}/ui/css/{pagina/nome}.css' rel='stylesheet'/><xsl:text>&#10;</xsl:text>
			<script type='text/javascript' src='{directory_lavoro}/ui/js/{nome}.js'></script><xsl:text>&#10;</xsl:text>
			<script type='text/javascript' src='{directory_lavoro}/ui/js/{pagina/nome}.js'></script><xsl:text>&#10;</xsl:text>
			
			<!-- se non esiste il file js relativo alla pagina, creiamo un oggetto pagina che ha le proprieta' -->
			<!-- e i metodi di default dell'applicazione. -->
			<!-- In ogni caso diciamo all'applicazione/pagina in che modalita' si dovra' navigare -->
			<!-- e se la pagina deve allineare la mamma e/o eventualmente chiudersi -->
			<script type='text/javascript'>
				if (!document.wapagina)
					document.wapagina = new waapplicazione();
				document.wapagina.modalitaNavigazione = '<xsl:value-of select="modalita_navigazione" />';
				<xsl:if test="pagina/ritorno/valori">
					document.wapagina.allineaGenitore('<xsl:value-of select="pagina/ritorno/valori" />');
					<xsl:if test="pagina/ritorno/chiudi">
						document.wapagina.chiudiPagina();
					</xsl:if>
				</xsl:if>
			</script><xsl:text>&#10;</xsl:text>
			
		</body>
	</html>
</xsl:template>


<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
</xsl:stylesheet>