<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template opzione                                                       -->
<!-- ********************************************************************** -->
<xsl:template match="opzione">

	<xsl:call-template name="intestazione_controllo"/>
	<xsl:variable name="nome" select="@id" />
	<xsl:variable name="valore" select="valore" />

	<xsl:for-each select="lista/elemento">
		<xsl:variable name="idx" select="position() - 1" />
		<input type='radio' name='{$nome}' value='{@id}'>
			<xsl:if test="@id = $valore">
				<xsl:attribute name='checked'>checked</xsl:attribute>
			</xsl:if>
			<xsl:call-template name="dammiattributicontrollo">
				<xsl:with-param name="offset_sinistra"><xsl:value-of select="$idx * 110"/></xsl:with-param>
				<xsl:with-param name="src_parametri" select="../.." />
			</xsl:call-template>
		</input>
		<label id='wamodulo_lblradio_{$nome}[{$idx}]'>
			<xsl:call-template name="dammiattributicontrollo">
				<xsl:with-param name="offset_sinistra"><xsl:value-of select="($idx * 110) + 26"/></xsl:with-param>
				<xsl:with-param name="src_parametri" select="../.." />
			</xsl:call-template>
			<xsl:value-of select="." />
		</label>
	
	</xsl:for-each>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="opzione.input">
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