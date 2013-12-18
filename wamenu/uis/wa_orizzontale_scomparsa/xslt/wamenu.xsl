<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="../../wa_file_comuni/xslt/orizzontale_comune.xsl"/>

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 

<!--  template del menu -->
<xsl:template match="wamenu">

	<link href='{wamenu_path}/uis/wa_file_comuni/css/wamenu.css' rel='stylesheet'/>

	<script type='text/javascript' src='{wamenu_path}/uis/wa_file_comuni/js/ddmenu.js'></script>
	
	<table  cellpadding='0' cellspacing='0' onclick='toggleDDMM("{nome}", false, "{wamenu_path}/uis/wa_file_comuni/img")' id='{nome}_toggler_container' class='ddmx_toggler'>
		<tr>
			<td>
				<img src='{wamenu_path}/uis/wa_file_comuni/img/arrowdxblack.gif' style='border: none' alt='' id='{nome}_imgOnOff' />
			</td>
		</tr>
	</table>
	
	<table  cellpadding='0' cellspacing='0' id='{nome}' class='ddmx'>
		<tr>
     		<xsl:apply-templates select="wamenu_sezione"/>
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
    ddmx.type = 'horizontal';
    ddmx.delay.show = 0;
    ddmx.delay.hide = 600;
	ddmx.position.level1.left = 0;
	ddmx.position.level1.top = 0;
	ddmx.position.levelX.left = 0;
    ddmx.position.levelX.top = 0;
	ddmx.init();
	
	
	var menu = document.getElementById('<xsl:value-of select="nome"/>');
	menu.style.position = 'absolute';
	menu.style.left = '30px';
	menu.style.top = '0px';
	menu.style.visibility = 'hidden';
	var toggler = document.getElementById('<xsl:value-of select="nome"/>_toggler_container');
	toggler.style.top = '0px';
	
	</script>
</xsl:template>


</xsl:stylesheet>