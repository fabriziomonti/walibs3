<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<!-- template della sezione di menu -->
<xsl:template match="wamenu_sezione">
	<xsl:if test="livello = 0">
		<xsl:text disable-output-escaping="yes">&lt;td></xsl:text>
	</xsl:if>
 	
 	<xsl:variable name="classebase">
		<xsl:choose>
			<xsl:when test="livello = 0">item1</xsl:when>
			<xsl:otherwise>item2 arrow</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
	
 	<xsl:variable name="classecss">
		<xsl:choose>
			<xsl:when test="selezionato = 0"><xsl:value-of select="$classebase"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="$classebase"/> selected</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
 	
 	<xsl:variable name="myurl">
		<xsl:choose>
			<xsl:when test="url = ''">javascript:void(0)</xsl:when>
			<xsl:otherwise><xsl:value-of select="url"/></xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
 	
 	<a href='{$myurl}' class='{$classecss}' id='a_{@id}'>
		<xsl:if test="finestra != ''">
			<xsl:attribute name='target'><xsl:value-of select="finestra" /></xsl:attribute>
		</xsl:if>
		<xsl:value-of select="etichetta"/>
	</a>
 	<div class='section' id='{@id}'>
		<xsl:apply-templates select="wamenu_sezione | wamenu_voce"/>
	</div>
	<xsl:if test="livello = 0">
		<xsl:text disable-output-escaping="yes">&lt;/td></xsl:text>
	</xsl:if>
</xsl:template>
 
<!-- template della singola voce di menu -->
<xsl:template match="wamenu_voce">
 	<xsl:variable name="classecss">
		<xsl:choose>
			<xsl:when test="selezionato = 0">item2</xsl:when>
			<xsl:otherwise>item2 selected</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
 	
 	<xsl:variable name="myurl">
		<xsl:choose>
			<xsl:when test="url = ''">javascript:void(0)</xsl:when>
			<xsl:otherwise><xsl:value-of select="url"/></xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
 	
 	<a href='{$myurl}' class='{$classecss}'>
		<xsl:if test="finestra != ''">
			<xsl:attribute name='target'><xsl:value-of select="finestra" /></xsl:attribute>
		</xsl:if>
		<xsl:value-of select="etichetta"/>
	</a>
</xsl:template>


</xsl:stylesheet>