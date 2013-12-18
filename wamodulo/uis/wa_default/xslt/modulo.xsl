<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!--  template del modulo                                                   -->
<!-- ********************************************************************** -->
<xsl:template match="wamodulo">

	<link href='{wamodulo_path}/uis/wa_default/css/wamodulo.css' rel='stylesheet'/>
	
	<!-- inclusione delle classi che gestiscono i controlli	-->
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/overlib.js'></script>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/calpop.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/moo1.2.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/modulo.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/controllo.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/areatesto.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/bottone.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/captcha.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/caricafile.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/cfpi.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/cornice.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/data.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/dataora.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/email.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/etichetta.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/intero.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/logico.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/noncontrollo.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/opzione.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/ora.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/password.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/selezione.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/multiselezione.js'></script><xsl:text>&#10;</xsl:text>
	<!--<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/multiselezione_checkbox.js'></script><xsl:text>&#10;</xsl:text>-->
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/testo.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/Javascript' src='{wamodulo_path}/uis/wa_default/js/valuta.js'></script><xsl:text>&#10;</xsl:text>
	
 	<xsl:variable name="mysinistra">
		<xsl:choose>
			<xsl:when test="sinistra = 'center'">left: 50%; margin-left: -<xsl:value-of select="larghezza div 2"/>px</xsl:when>
			<xsl:otherwise>left: <xsl:value-of select="sinistra"/>px</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
	<xsl:text>&#10;</xsl:text>
	<form id='{nome}' action='{pagina_destinazione}' method='post' enctype='multipart/form-data' class='wamodulo' style='{$mysinistra}; top: {alto}px; width: {larghezza}px; height: {altezza}px;'>
		<div>
			<!-- controllo hidden che viene utilizzato dalla classe per stabilire 
			se la form che ha effettuato submit sia relativa alla propria 
			istanza o meno -->
			<input type='hidden' name='wamodulo_nome_modulo' value='{nome}' />

			<!-- controllo hidden da valorizzare via js a seconda del tipo di bottone 
			che e' stato usato per submit; se l'applicazione deve gestire anche il nojs,
			allora per verificare che tipo di operazione l'utente ha scelto occorre
			basarsi sul nome del bottone premuto
			default=aggiornamento -->
			<input type='hidden' name='wamodulo_operazione' value='3' />

			<!-- evntuale recid se qualcuno lo vuole utilizzare -->
			<xsl:if test="rec_id/valore != ''">
				<input type='hidden' name='{rec_id/nome}' value='{rec_id/valore}' />
			</xsl:if>
			<!--		mod id per check violation-->
			<xsl:if test="mod_id/valore != ''">
				<input type='hidden' name='{mod_id/nome}' value='{mod_id/valore}' />
			</xsl:if>

			<!-- 		loop dei controlli -->
			<xsl:apply-templates select="wamodulo_controlli"/>

			<xsl:text>&#10;</xsl:text>
		</div>
	</form>	
	
	<xsl:text>&#10;</xsl:text>
	<script type='text/javascript'>
		document.<xsl:value-of select="nome"/> = new wamodulo('<xsl:value-of select="nome"/>');

		<!-- lista dei controlli-->
		<xsl:for-each select="wamodulo_controlli/*">
			<xsl:variable name="valore">
				<xsl:if test="valore/*">
					<xsl:for-each select="valore/*">
						<xsl:value-of select="text()"/>
					</xsl:for-each>
				</xsl:if>
				<xsl:if test="not(valore/*)">
					<xsl:call-template name="escape-javascript-value">
						<xsl:with-param name="string" select="valore"/>
					</xsl:call-template>
				</xsl:if>
			</xsl:variable>
			new wa<xsl:value-of select="name()"/> (document.<xsl:value-of select="/wamodulo/nome"/>, '<xsl:value-of select="@id"/>', "<xsl:value-of select='$valore'/>", '<xsl:value-of select="visibile"/>', '<xsl:value-of select="sola_lettura"/>', '<xsl:value-of select="obbligatorio"/>');
		</xsl:for-each>
	</script>	
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!--  template del modulo in input                                          -->
<!-- ********************************************************************** -->
<xsl:template match="wamodulo.input">

	<wamodulo.input>
		<wamodulo_operazione>
			<xsl:value-of select="/wamodulo.input/post/item[@id = 'wamodulo_operazione']"/>
		</wamodulo_operazione>
		<mod_id>
			<xsl:value-of select="/wamodulo.input/post/item[@id = /wamodulo.input/mod_id/nome]"/>
		</mod_id>
		<xsl:apply-templates select="wamodulo_controlli.input"/>
	</wamodulo.input>
</xsl:template>


<!-- ********************************************************************** -->
</xsl:stylesheet>