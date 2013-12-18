<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="orizzontale_comune.xsl"/>

<xsl:output method="html"/>


<!--  template del menu -->
<xsl:template match="wamenu">

	<link href='{wamenu_path}/uis/orizzontale/css/wamenu.css' rel='stylesheet'/>

	<script type='text/javascript' src='{wamenu_path}/uis/orizzontale/js/ddmenu.js'></script>
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