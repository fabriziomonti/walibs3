<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template cfpi                                                         -->
<!-- ********************************************************************** -->
<xsl:template match="cfpi">

	<xsl:call-template name="testo"/>

	<!--	parcheggiamo le proprieta' particolari nel controllo html, in modo che-->
	<!--	poi la classe applicativa  possa ritrovarli-->
	<script type='text/Javascript'>
		document.getElementById('<xsl:value-of select="/wamodulo/nome" />').<xsl:value-of select="@id" />.gestioneCF  =  "<xsl:value-of select="gestione_cf"/>";
		document.getElementById('<xsl:value-of select="/wamodulo/nome" />').<xsl:value-of select="@id" />.gestionePI  =  "<xsl:value-of select="gestione_pi"/>";
	</script>


</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="cfpi.input">

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