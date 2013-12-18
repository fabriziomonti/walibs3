<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="dateore_template_comuni.xsl"/>

<!-- ********************************************************************** -->
<!-- template dataora                                                       -->
<!-- ********************************************************************** -->
<xsl:template match="dataora">

	<xsl:call-template name="data"/>

	<xsl:call-template name="tendine_ora">
		<xsl:with-param name="offset_sinistra">250</xsl:with-param>
		<xsl:with-param name="offset_valore">11</xsl:with-param>
	</xsl:call-template>
	
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="dataora.input">
	<xsl:element name="{@id}">
		<xsl:variable name="id" select="@id" />
		<xsl:choose>
			<xsl:when test="/wamodulo.input/post/item[@id=concat('wamodulo_giorno_', $id)]">
				<xsl:variable name="giorno" select="/wamodulo.input/post/item[@id=concat('wamodulo_giorno_', $id)]" />
				<xsl:variable name="mese" select="/wamodulo.input/post/item[@id=concat('wamodulo_mese_', $id)]" />
				<xsl:variable name="anno" select="/wamodulo.input/post/item[@id=concat('wamodulo_anno_', $id)]" />
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
				<xsl:value-of select="concat($anno, '-', $mese, '-', $giorno, ' ', $ora, ':', $min, ':', $sec)" />
			</xsl:when>
			<xsl:otherwise>__wamodulo_valore_non_ritornato__</xsl:otherwise>
		</xsl:choose>
	</xsl:element>

</xsl:template>

</xsl:stylesheet>