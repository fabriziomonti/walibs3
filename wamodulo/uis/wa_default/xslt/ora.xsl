<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="dateore_template_comuni.xsl"/>

<!-- ********************************************************************** -->
<!-- template ora                                                           -->
<!-- ********************************************************************** -->
<xsl:template match="ora">
	
	<xsl:call-template name="intestazione_controllo"/>
	<xsl:call-template name="tendine_ora"/>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="ora.input">
	<xsl:element name="{@id}">
		<xsl:variable name="id" select="@id" />
		<xsl:choose>
			<xsl:when test="/wamodulo.input/post/item[@id=concat('wamodulo_ora_', $id)]">
				<xsl:variable name="ora" select="/wamodulo.input/post/item[@id=concat('wamodulo_ora_', $id)]" />
				<xsl:variable name="min" select="/wamodulo.input/post/item[@id=concat('wamodulo_min_', $id)]" />
				<xsl:variable name="sec">
					<xsl:choose>
						<xsl:when test="/wamodulo.input/post/item[@id=concat('wamodulo_sec_', $id)]">
							<xsl:value-of select="/wamodulo.input/post/item[@id=concat('wamodulo_sec_', $id)]" />
						</xsl:when>
						<xsl:otherwise>00</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				<xsl:value-of select="concat($ora, ':', $min, ':', $sec)" />
			</xsl:when>
			<xsl:otherwise>__wamodulo_valore_non_ritornato__</xsl:otherwise>
		</xsl:choose>
	</xsl:element>

</xsl:template>

</xsl:stylesheet>