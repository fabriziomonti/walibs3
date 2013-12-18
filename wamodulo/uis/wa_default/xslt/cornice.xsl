<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template cornice                                                     -->
<!-- ********************************************************************** -->
<xsl:template match="cornice">

	<xsl:call-template name="intestazione_controllo"/>
	<div id='{/wamodulo/nome}_{@id}'>
		<xsl:call-template name="dammiattributicontrollo"/>
		
		<xsl:if test="valore != ''">
			<span style='position: relative; top: {altoEtichetta}px; left: {sinistraEtichetta}px'>
			<xsl:text> </xsl:text>
			<xsl:value-of select="valore"/>
			<xsl:text> </xsl:text>
			</span>		
		</xsl:if>
		
	</div>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->

</xsl:stylesheet>