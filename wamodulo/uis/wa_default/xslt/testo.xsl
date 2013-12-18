<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template testo                                                         -->
<!-- ********************************************************************** -->
<xsl:template match="testo" name="testo">
	<xsl:param name="tipo" />

	<xsl:call-template name="intestazione_controllo"/>
	<input name='{@id}' value='{valore}' maxlength='{caratteri_max}'>
		<xsl:call-template name="dammiattributicontrollo"/>
	
		<xsl:attribute name='type'>
			<xsl:choose>
				<xsl:when test="$tipo = ''">
					<xsl:text>text</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$tipo"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<xsl:attribute name='size'>
			<xsl:choose>
				<xsl:when test="caratteri_max &gt; '50'">
					<xsl:text>50</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="caratteri_max"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
	</input>
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="testo.input">
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