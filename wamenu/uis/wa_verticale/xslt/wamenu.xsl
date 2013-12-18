<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 

<!--  template del menu -->
<xsl:template match="wamenu">

	<link href='{wamenu_path}/uis/wa_file_comuni/css/wamenu.css' rel='stylesheet'/>

	<script type='text/javascript' src='{wamenu_path}/uis/wa_file_comuni/js/ddmenu.js'></script>
	<table  cellpadding='0' cellspacing='0' id='{nome}' class='ddmx'>
		<tr>
			<td>
     			<xsl:apply-templates select="wamenu_sezione"/>
			</td>
     	</tr>
    </table>
    <script type='text/javascript'>
    var a= '';
	<xsl:for-each select="wamenu_sezioni_selezionate">
		if (a)
			a = document.getElementById('a_<xsl:value-of select="id_sezione"/>');
		a.className = a.className + ' selected';
	</xsl:for-each>
    var ddmx = new DropDownMenuX('<xsl:value-of select="nome"/>');
    ddmx.type = 'vertical';
    ddmx.delay.show = 0;
    ddmx.delay.hide = 600;
	ddmx.position.level1.left = 0;
	ddmx.position.level1.top = 0;
	ddmx.position.levelX.left = 0;
    ddmx.position.levelX.top = 0;
	ddmx.init();
	
	</script>
</xsl:template>


<!-- template della sezione di menu -->
<xsl:template match="wamenu_sezione">
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