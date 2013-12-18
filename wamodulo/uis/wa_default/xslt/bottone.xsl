<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template bottone                                                       -->
<!-- ********************************************************************** -->
<xsl:template match="bottone">
	
	<xsl:call-template name="intestazione_controllo"/>

	<input name='{@id}' value='{valore}' title='{valore}'>
		<xsl:if test="invia = '1'"><xsl:attribute name='type'>submit</xsl:attribute></xsl:if>
		<xsl:if test="invia != '1'"><xsl:attribute name='type'>button</xsl:attribute></xsl:if>
		
		<xsl:call-template name="dammiattributicontrollo" />

	</input>
	<!--	parcheggiamo le proprieta' particolari nel controllo html, in modo che-->
	<!--	poi la classe applicativa  possa ritrovarli-->
	<script type='text/Javascript'>
		document.getElementById('<xsl:value-of select="/wamodulo/nome" />').<xsl:value-of select="@id" />.annulla  =  "<xsl:value-of select="annulla"/>";
		document.getElementById('<xsl:value-of select="/wamodulo/nome" />').<xsl:value-of select="@id" />.elimina  =  "<xsl:value-of select="elimina"/>";
	</script>
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="bottone.input">
	<xsl:element name="{@id}">
		<xsl:variable name="id" select="@id" />
		<xsl:choose>
			<xsl:when test="/wamodulo.input/post/item[@id=$id]">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose>
	</xsl:element>
</xsl:template>


</xsl:stylesheet>