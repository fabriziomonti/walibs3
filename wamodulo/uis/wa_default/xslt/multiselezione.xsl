<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template multiselezione                                                -->
<!-- ********************************************************************** -->
<xsl:template match="multiselezione">

	<xsl:call-template name="intestazione_controllo"/>
	<select multiple='multiple' name='{@id}[]'>
		<xsl:call-template name="dammiattributicontrollo"/>
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

				<xsl:if test="../../valore/*[@id = $myoption] != ''">
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
<xsl:template match="multiselezione.input">
	
	<xsl:variable name="id" select="@id" />
	<xsl:choose>
		<xsl:when test="/wamodulo.input/post/item[@id=$id]">
			<xsl:for-each select="/wamodulo.input/post/item[@id=$id]/*">
				<xsl:element name="{$id}"><xsl:value-of select="."/></xsl:element>
			</xsl:for-each>
		</xsl:when>
		<xsl:otherwise>
			<xsl:element name="{$id}">__wamodulo_valore_non_ritornato__</xsl:element>
		</xsl:otherwise>
	</xsl:choose>
	

</xsl:template>

</xsl:stylesheet>