<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template testo                                                         -->
<!-- ********************************************************************** -->
<xsl:template match="noncontrollo" name="noncontrollo">
	<xsl:param name="tipo" />

	<xsl:call-template name="intestazione_controllo"/>
	<div id='{@id}'>
		<xsl:call-template name="dammiattributicontrollo"/>
		<xsl:attribute name='class'>wamodulo_noncontrollo</xsl:attribute>
		<xsl:value-of disable-output-escaping="yes" select="valore" />
	</div>
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="noncontrollo.input">
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