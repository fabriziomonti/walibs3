<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template selezione                                                     -->
<!-- ********************************************************************** -->
<xsl:template match="selezione">

	<xsl:call-template name="intestazione_controllo"/>
	<select name='{@id}'>
		<xsl:call-template name="dammiattributicontrollo"/>
		<xsl:if test="riga_vuota = '1' ">
			<option value=''></option>
		</xsl:if>

		<xsl:for-each select="lista/elemento">
			<option value='{@id}'>
			 	<xsl:variable name="myoption">
					<xsl:choose>
						<xsl:when test="substring-before(@id, '|') != ''">
							<xsl:value-of select="substring-before(./@id, '|')"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="@id"/>
						</xsl:otherwise>
					</xsl:choose>	
			 	</xsl:variable>
				<xsl:if test="$myoption = ../../valore">
					<xsl:attribute name='selected'>selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="."/>
			</option>
		</xsl:for-each>
		<xsl:text>&#10;</xsl:text>
	</select>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="selezione.input">
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