<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="../../wa_file_comuni/xslt/watabella_template_comuni.xsl"/>

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 
<xsl:decimal-format decimal-separator=","  grouping-separator="." /> 

<!-- ********************************************************************** -->
<!--  template della tabella -->
<!-- ********************************************************************** -->
<xsl:template match="watabella">

	<xsl:text>&#10;</xsl:text>
	<link href='{watabella_path}/uis/wa_file_comuni/css/watabella.css' rel='stylesheet'/><xsl:text>&#10;</xsl:text>
	<link href='{watabella_path}/uis/wa_azioni_context/css/context.css' rel='stylesheet'/><xsl:text>&#10;</xsl:text>
	
	<!-- roba menu contestuale	(una parte...) -->
	<script type='text/javascript' src='{watabella_path}/uis/wa_file_comuni/js/strmanage.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/javascript' src='{watabella_path}/uis/wa_file_comuni/js/moo1.2.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/javascript' src='{watabella_path}/uis/wa_file_comuni/js/watabella.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/javascript' src='{watabella_path}/uis/wa_azioni_context/js/domready.js'></script><xsl:text>&#10;</xsl:text>

 	<xsl:variable name="qoe">
		<xsl:choose>
			<xsl:when test="contains(uri, '?')">&amp;</xsl:when>
			<xsl:otherwise>?</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
	<xsl:call-template name="watabella_finestra_ordinamento_filtro" />
	
	<form action='{uri}' id='{nome}_bottoniera' class='watabella'><xsl:text>&#10;</xsl:text>
		<div>
			<xsl:apply-templates select="watabella_azioni_pagina"/>
			<xsl:apply-templates select="watabella_ricerca_rapida"/>
		</div>
	</form><xsl:text>&#10;</xsl:text>
	<xsl:apply-templates select="watabella_barra_navigazione"/>
	<form id='{nome}' action='' method='post' class='watabella'>
	    <table style='width: 100%'>
			<xsl:apply-templates select="watabella_intestazioni"/>
			<xsl:apply-templates select="watabella_riga_totali"/>
			<xsl:apply-templates select="watabella_righe"/>
	    </table>
	</form>
	
	<script type='text/javascript'>
		if (!myDomReady)
			{
			// dobbiamo caricare il js una volta sola, altrimenti in caso di piu' tabelle fa casino
			var myDomReady = -1;
			var head= document.getElementsByTagName('head')[0];
			var script= document.createElement('script');
			script.type= 'text/javascript';
			script.src= '<xsl:value-of select="watabella_path"/>/uis/wa_azioni_context/js/context.js';
			head.appendChild(script);
			}
			
		// chiama la funzione di aggiunta menu contestuale solo quando il dom e' completo e lo script esterno e' stato caricato
		Event.domReady.add(function() {myDomReady = 1;});
		function addRowContextMenuWhenLoaded(tblName, rowId)
			{
			<xsl:variable name='ampamp'>&amp;&amp;</xsl:variable>
			if (myDomReady == 1 <xsl:value-of select="$ampamp" disable-output-escaping="yes" /> typeof addRowContextMenu == 'function')
				addRowContextMenu(tblName, rowId);
			else
				setTimeout("addRowContextMenuWhenLoaded('" + tblName + "', '" + rowId + "')", 3);
			}
	
		<!--agganciamento menu contestuale a ogni riga-->
		<xsl:for-each select="watabella_righe/riga">
			addRowContextMenuWhenLoaded('<xsl:value-of select="/watabella/nome"/>', '<xsl:value-of select="@id"/>');
		</xsl:for-each>

	</script>		

	<!-- creazione degli oggetti javascript -->
	<xsl:call-template name="crea_oggetti_javascript"/>


</xsl:template>

<!-- ********************************************************************** -->
<!-- template delle azioni su pagina -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_azioni_pagina">
		<xsl:for-each select="azione">
			<button type='button' name='{nome}' id='{nome}' title='{etichetta}' value='{etichetta}' onclick='document.{/watabella/nome}.azione_{/watabella/nome}_{nome}()'>
				<xsl:value-of disable-output-escaping="yes" select="etichetta"/>
			</button>
		</xsl:for-each>
		
		<!--bottoni di esportazione-->
		<xsl:variable name="qoe">
			<xsl:choose>
				<xsl:when test="contains(/watabella/uri, '?')">&amp;</xsl:when>
				<xsl:otherwise>?</xsl:otherwise>
			</xsl:choose>	
		</xsl:variable>
		<button type='button' title='CSV' value='CSV' onclick='document.location.href="{/watabella/uri}{$qoe}watbl_esporta_csv[{/watabella/nome}]=1"'>
			CSV
		</button>
		<button type='button' title='XLS' value='XLS' onclick='document.location.href="{/watabella/uri}{$qoe}watbl_esporta_xls[{/watabella/nome}]=1"'>
			XLS
		</button>
		<button type='button' title='PDF' value='PDF' onclick='document.location.href="{/watabella/uri}{$qoe}watbl_esporta_pdf[{/watabella/nome}]=1"'>
			PDF
		</button>
		<xsl:text>&#10;</xsl:text>
		
</xsl:template>

<!-- ********************************************************************** -->
<!-- template barra di navigazione -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_barra_navigazione">

	<xsl:variable name="qoe">
		<xsl:choose>
			<xsl:when test="contains(/watabella/uri, '?')">&amp;</xsl:when>
			<xsl:otherwise>?</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>

 	<table style='width: 100%' class='watabella'>
		<tr>
			<td style='width: 20%; text-align: left'>
				<xsl:if test="nr_pagina_corrente &gt; 0">
					<xsl:variable name="pag_prec" select="concat(/watabella/uri, $qoe, 'watbl_pg[', /watabella/nome, ']=', nr_pagina_corrente -1)" />
					<button type='button' name='paginaprecedente' id='paginaprecedente' title='Pagina precedente' value='&lt;&lt; Pagina precedente' onclick='document.{/watabella/nome}.cambiaPagina("{$pag_prec}")'>
						&lt;&lt; Pagina precedente
					</button><xsl:text>&#10;</xsl:text>
				</xsl:if>
			</td>
			<td style='width: 60%; text-align: center'>
				pagina <xsl:value-of select="nr_pagina_corrente + 1"/> di <xsl:value-of select="totale_pagine"/>
				-
				registrazioni dalla <xsl:value-of select="primo_record"/> alla <xsl:value-of select="ultimo_record"/> di <xsl:value-of select="totale_record"/>
			</td>
			<td style='width: 20%; text-align: right'>
				<xsl:if test="nr_pagina_corrente &lt; totale_pagine - 1">
					<xsl:variable name="pag_succ" select="concat(/watabella/uri, $qoe, 'watbl_pg[', /watabella/nome, ']=', nr_pagina_corrente + 1)" />
					<button type='button' name='paginasuccessiva' id='paginasuccessiva' title='Pagina successiva' value='Pagina successiva &gt;&gt;' onclick='document.{/watabella/nome}.cambiaPagina("{$pag_succ}")'>
						Pagina successiva &gt;&gt;
					</button><xsl:text>&#10;</xsl:text>
				</xsl:if>
			</td>
		</tr>
	</table>
</xsl:template>

<!-- ********************************************************************** -->
<!-- template intestazioni colonne -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_intestazioni">
 	
	<xsl:variable name="qoe">
		<xsl:choose>
			<xsl:when test="contains(/watabella/uri, '?')">&amp;</xsl:when>
			<xsl:otherwise>?</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>

 	<thead>
		<tr id='{/watabella/nome}_intestazioni'>
			<xsl:for-each select="intestazione[mostra=1]">
			 	<xsl:variable name="alignment">
					<xsl:choose>
						<xsl:when test="allineamento = 1">center</xsl:when>
						<xsl:when test="allineamento = 2">right</xsl:when>
						<xsl:otherwise>left</xsl:otherwise>
					</xsl:choose>	
			 	</xsl:variable>
				<th style='text-align: {$alignment}' id='{/watabella/nome}_{nome}'>
					<xsl:choose>
						<xsl:when test="ordina = '1'">
						 	<xsl:variable name="modo_ordinamento">
								<xsl:if test="ordinamento_rapido = 'asc'">desc</xsl:if>
								<xsl:if test="ordinamento_rapido = 'desc' or ordinamento_rapido = 'no'">asc</xsl:if>
						 	</xsl:variable>
							<a href='{/watabella/uri}{$qoe}watbl_or[{/watabella/nome}]={nome}&amp;watbl_orm[{/watabella/nome}]={$modo_ordinamento}'>
								<xsl:value-of select="etichetta"/>
								<xsl:if test="ordinamento_rapido != 'no'">
									<center>
										<img src='{/watabella/watabella_path}/uis/wa_file_comuni/img/{ordinamento_rapido}_order.gif' border='0'/>
									</center>
								</xsl:if>
							</a>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="etichetta"/>
						</xsl:otherwise>
					</xsl:choose>	
				</th>
			</xsl:for-each>
		</tr>
	</thead>
</xsl:template>

<!-- ********************************************************************** -->
<!-- template delle righe -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_righe">
	
	<tbody>
		<xsl:for-each select="riga">
			<xsl:variable name="objRiga">document.<xsl:value-of select="/watabella/nome"/>.righe['<xsl:value-of select="@id"/>']</xsl:variable>
			<tr id='row_{/watabella/nome}_{@id}' onclick='{$objRiga}.cambiaStato()'>
	
				<xsl:text>&#10;</xsl:text>
				<xsl:for-each select="cella">
					<xsl:variable name="cellpos" select="position()" />
					<xsl:variable name="col_info" select="/watabella/watabella_intestazioni/intestazione[position()=$cellpos]" />
				 	<xsl:if test="$col_info/mostra = 1">
					 	<xsl:variable name="alignment">
							<xsl:choose>
								<xsl:when test="$col_info/allineamento = 1">center</xsl:when>
								<xsl:when test="$col_info/allineamento = 2">right</xsl:when>
								<xsl:otherwise>left</xsl:otherwise>
							</xsl:choose>	
					 	</xsl:variable>
						<td style='text-align: {$alignment}'>
							<xsl:if test="position()=1">
								<xsl:attribute name="style">width: 30px; text-align: <xsl:value-of select="$alignment"/>;</xsl:attribute>
								<!-- valorizzazione azioni in linea -->
								<xsl:call-template name="azioni_record">
									<xsl:with-param name="record" select=".."/>
								</xsl:call-template>
							</xsl:if>
							<xsl:if test="position()!=1">
								<xsl:attribute name="style">text-align: <xsl:value-of select="$alignment"/>;</xsl:attribute>
							</xsl:if>
							
							<xsl:choose>
								<xsl:when test="$col_info/link = 1">
									<a href='javascript:document.{/watabella/nome}.link_{/watabella/nome}_{$col_info/nome}("{../@id}")'>
										<xsl:value-of select="valore"/>
									</a>
								</xsl:when>
								<xsl:when test="$col_info/converti_html = '0'">
									<xsl:copy-of select="valore"/>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DATA'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="substring(valore, 9, 2)"/>/<xsl:value-of select="substring(valore, 6, 2)"/>/<xsl:value-of select="substring(valore, 1, 4)"/>
									</xsl:if>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DATAORA'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="substring(valore, 9, 2)"/>/<xsl:value-of select="substring(valore, 6, 2)"/>/<xsl:value-of select="substring(valore, 1, 4)"/>
											<xsl:text>&#x20;</xsl:text>
										<xsl:value-of select="substring(valore, 12, 2)"/>:<xsl:value-of select="substring(valore, 15, 2)"/>
									</xsl:if>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'ORA'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="substring(valore, 1, 2)"/>:<xsl:value-of select="substring(valore, 4, 2)"/>
									</xsl:if>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DECIMALE'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="format-number(valore,  '#.##0,00')"/>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="linebreak">
										<xsl:with-param name="text" select="valore"/>
									</xsl:call-template>
								</xsl:otherwise>
							</xsl:choose>
						</td>
					</xsl:if>
				</xsl:for-each>
				
				
			</tr>
		</xsl:for-each>
	</tbody>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- template riga dei totali -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_riga_totali">
	<tfoot>
		<tr>
			<xsl:for-each select="cella">
				<xsl:variable name="cellpos" select="position()" />
				<xsl:variable name="col_info" select="/watabella/watabella_intestazioni/intestazione[position()=$cellpos]" />
			 	<xsl:if test="$col_info/mostra = 1">
				 	<xsl:variable name="alignment">
						<xsl:choose>
							<xsl:when test="$col_info/allineamento = 1">center</xsl:when>
							<xsl:when test="$col_info/allineamento = 2">right</xsl:when>
							<xsl:otherwise>left</xsl:otherwise>
						</xsl:choose>	
				 	</xsl:variable>
					<th style='text-align: {$alignment}'>
						<xsl:choose>
							<xsl:when test="$col_info/tipo_campo = 'DECIMALE'">
								<xsl:if test="string-length(valore) &gt; 0">
									<xsl:value-of select="format-number(valore,  '#.##0,00')"/>
								</xsl:if>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="valore"/>
							</xsl:otherwise>
						</xsl:choose>
					</th>
				</xsl:if>
			</xsl:for-each>
		</tr>
	</tfoot>
</xsl:template>


<!-- ********************************************************************** -->
<!-- template del controllo per la ricerca rapida -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_ricerca_rapida">
	<br/>
	<input name='watbl_rr[{/watabella/nome}]' value='{valore}'/><span><button type='submit' title='Cerca' value='Cerca'>Cerca</button></span>
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template name="azioni_record">
	<xsl:param name="record" select="."/>

	<xsl:text>&#10;</xsl:text>

	<xsl:text>&#10;</xsl:text>
	<ul id='contextmenu_{/watabella/nome}_{$record/@id}' class='contextmenu' style='visibility: hidden'>
 		<xsl:variable name="id_riga" select="$record/@id"/>
		<xsl:for-each select="/watabella/watabella_azioni_record/azione">
			<!-- verifichiamo se il bottone e' abilitato -->
			<xsl:variable name="id_azione" select="@id"/>
			<xsl:if test="not($record/azioni_abilitabili/azione[@id=$id_azione]) or $record/azioni_abilitabili/azione[@id=$id_azione]/abilitazione = '1'">
				<li><a href='javascript:document.{/watabella/nome}.azione_{/watabella/nome}_{nome}("{$id_riga}")' class='contextmenu'><xsl:value-of select="etichetta"/></a></li>
			</xsl:if>
		</xsl:for-each>
	</ul>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
</xsl:stylesheet>
