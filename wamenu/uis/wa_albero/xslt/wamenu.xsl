<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 

<!--  template del menu -->
<xsl:template match="wamenu">

	<link href='{wamenu_path}/uis/wa_albero/css/wamenu.css' rel='stylesheet'/>
	
	<script type='text/javascript' src='{wamenu_path}/uis/wa_albero/js/treemenu.js'></script>
	<script type='text/javascript'>
		window.onload = function() {new TreeMenu('<xsl:value-of select="nome"/>')};
	</script>
	<ul id='{nome}' class='tree-menu'>	
		<xsl:apply-templates select="wamenu_sezione"/>
	</ul>
</xsl:template>

<!-- template della sezione di menu -->
<xsl:template match="wamenu_sezione">

 	<xsl:variable name="myurl">
		<xsl:choose>
			<xsl:when test="url = ''">javascript:void(0)</xsl:when>
			<xsl:otherwise><xsl:value-of select="url"/></xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>

 	<li>
		<a href='{$myurl}'>
			<xsl:if test="finestra != ''">
				<xsl:attribute name='target'><xsl:value-of select="finestra" /></xsl:attribute>
			</xsl:if>
			<xsl:value-of select="etichetta"/>
		</a>
		<xsl:if test="wamenu_sezione | wamenu_voce">
			<ul>
				<xsl:apply-templates select="wamenu_sezione | wamenu_voce"/>
			</ul>
		</xsl:if>
	</li>
</xsl:template>
 
<!-- template della singola voce di menu -->
<xsl:template match="wamenu_voce">
 	
 	<xsl:variable name="myurl">
		<xsl:choose>
			<xsl:when test="url = ''">javascript:void(0)</xsl:when>
			<xsl:otherwise><xsl:value-of select="url"/></xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>

 	<li>
		<a href='{$myurl}'>
			<xsl:if test="finestra != ''">
				<xsl:attribute name='target'><xsl:value-of select="finestra" /></xsl:attribute>
			</xsl:if>
			<xsl:value-of select="etichetta"/>
		</a>
	</li>
</xsl:template>


</xsl:stylesheet>