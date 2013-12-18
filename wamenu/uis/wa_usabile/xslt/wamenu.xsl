<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

 <xsl:output method="html"/>

 <xsl:template match="wamenu">
	
 
	<link href='{wamenu_path}/uis/wa_usabile/css/wamenu.css' rel='stylesheet'/>

	<table  cellpadding='0' cellspacing='0' id='{nome}' class='ddmx'>
     	<xsl:apply-templates select="wamenu_sezione"/>
    </table>
 </xsl:template>

 <xsl:template match="wamenu_sezione">
 	<xsl:variable name="classebase">
		<xsl:choose>
			<xsl:when test="livello = 0">item1</xsl:when>
			<xsl:otherwise>item2</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
	
 	<xsl:variable name="classecss">
		<xsl:choose>
			<xsl:when test="selezionato = 0"><xsl:value-of select="$classebase"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="$classebase"/> selected</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
 	<tr>
		<td>
			<a href='{url}' class='{$classecss}' id='a_{@id}'>
				<xsl:if test="finestra != ''">
					<xsl:attribute name='target'><xsl:value-of select="finestra" /></xsl:attribute>
				</xsl:if>
				<xsl:value-of select="etichetta"/>
			</a>
		</td>
	</tr>
		<xsl:apply-templates select="wamenu_sezione | wamenu_voce"/>
 </xsl:template>
 
 <xsl:template match="wamenu_voce">
 	<xsl:variable name="classecss">
		<xsl:choose>
			<xsl:when test="selezionato = 0">item2</xsl:when>
			<xsl:otherwise>item2 selected</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
 	<tr>
		<td>
			<a href='{url}' class='{$classecss}'>
				<xsl:if test="finestra != ''">
					<xsl:attribute name='target'><xsl:value-of select="finestra" /></xsl:attribute>
				</xsl:if>
				<xsl:value-of select="etichetta"/>
			</a>
		</td>
	</tr>
 </xsl:template>


</xsl:stylesheet>