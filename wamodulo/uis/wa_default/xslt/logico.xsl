<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template logico                                                        -->
<!-- ********************************************************************** -->
<xsl:template match="logico">
	<xsl:call-template name="intestazione_controllo"/>
	<input type='checkbox' name='{@id}'>
		<xsl:if test="valore != '' and valore != '0'">
			<xsl:attribute name='checked'>checked</xsl:attribute>
		</xsl:if>
		<xsl:call-template name="dammiattributicontrollo"/>
	</input>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="logico.input">
	<xsl:element name="{@id}">
		<xsl:variable name="id" select="@id" />
		<xsl:choose>
			<xsl:when test="/wamodulo.input/post/item[@id=$id] = 'on'">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose>
	</xsl:element>

</xsl:template>

</xsl:stylesheet>