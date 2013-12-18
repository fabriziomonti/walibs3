<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template etichetta                                                     -->
<!-- ********************************************************************** -->
<xsl:template match="etichetta">

	<xsl:call-template name="intestazione_controllo"/>
 	<xsl:variable name="classecss">
 		<xsl:if test="sola_lettura = '1'">
 			<xsl:text>wamodulo_disabilitato</xsl:text>
 		</xsl:if>
 		<xsl:if test="obbligatorio = '1'">
	 		<xsl:if test="sola_lettura = '1'">
	 			<xsl:text> </xsl:text>
	 		</xsl:if>
 			<xsl:text>wamodulo_obbligatorio</xsl:text>
 		</xsl:if>
 	</xsl:variable>
	
	<label id='{/wamodulo/nome}_{@id}' class='{$classecss}'>
		<xsl:if test="indice_tab != '' and indice_tab != '0' "><xsl:attribute name='tabindex'><xsl:value-of select="indice_tab"/></xsl:attribute></xsl:if>
		<xsl:if test="sola_lettura = '1'"><xsl:attribute name='disabled'>disabled</xsl:attribute></xsl:if>
		<xsl:call-template name="dammilayout"/>
		<xsl:value-of select="valore"/>
	</label>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->

</xsl:stylesheet>