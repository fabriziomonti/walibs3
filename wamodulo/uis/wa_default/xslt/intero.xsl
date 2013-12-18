<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template intero                                                        -->
<!-- ********************************************************************** -->
<xsl:template match="intero">
	<xsl:call-template name="intestazione_controllo"/>
	<input type='text' name='{@id}' value='{valore}' maxlength='{caratteri_max}'  size='{caratteri_max}'>
		<xsl:call-template name="dammiattributicontrollo">
			<xsl:with-param name="allineamento_testo">right</xsl:with-param>
		</xsl:call-template>
	</input>

</xsl:template>


<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="intero.input">
	<xsl:element name="{@id}">
		<xsl:variable name="id" select="@id" />
		<xsl:choose>
			<xsl:when test="/wamodulo.input/post/item[@id=$id]">
				<xsl:value-of select="/wamodulo.input/post/item[@id=$id]" />
			</xsl:when>
			<xsl:otherwise>__wamodulo_valore_non_ritornato__</xsl:otherwise>
		</xsl:choose>
	</xsl:element>

</xsl:template>


</xsl:stylesheet>