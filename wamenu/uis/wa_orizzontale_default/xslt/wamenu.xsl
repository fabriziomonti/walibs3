<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="../../wa_file_comuni/xslt/orizzontale_comune.xsl"/>

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 


<!--  template del menu -->
<xsl:template match="wamenu">

	<link href='{wamenu_path}/uis/wa_file_comuni/css/wamenu.css' rel='stylesheet'/>

	<script type='text/javascript' src='{wamenu_path}/uis/wa_file_comuni/js/ddmenu.js'></script>
	<table  cellpadding='0' cellspacing='0' id='{nome}' class='ddmx'>
		<tr>
     		<xsl:apply-templates select="wamenu_sezione"/>
     	</tr>
    </table>
    <script type='text/javascript'>
    var a= '';
	<xsl:for-each select="wamenu_sezioni_selezionate">
		a = document.getElementById('a_<xsl:value-of select="id_sezione"/>');
		if (a)
			a.className = a.className + ' selected';
	</xsl:for-each>
    var ddmx = new DropDownMenuX('<xsl:value-of select="nome"/>');
    ddmx.type = 'horizontal';
    ddmx.delay.show = 0;
    ddmx.delay.hide = 600;
	ddmx.position.level1.left = 0;
	ddmx.position.level1.top = 0;
	ddmx.position.levelX.left = 0;
    ddmx.position.levelX.top = 0;
	ddmx.init();
	
	</script>
</xsl:template>


</xsl:stylesheet>