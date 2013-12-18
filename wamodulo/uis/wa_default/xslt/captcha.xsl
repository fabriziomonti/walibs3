<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template captcha                                                         -->
<!-- ********************************************************************** -->
<xsl:template match="captcha">
	<xsl:call-template name="intestazione_controllo"/>

	<img id='{/wamodulo/nome}_captcha_img_{@id}' src='{/wamodulo/wamodulo_path}/uis/wa_default/img/captcha.php?k={valore}' >
		<xsl:call-template name="dammilayout"/>
	</img>
	<input type='hidden' name='k_{@id}' value='{valore}'/>
	<input type='text' name='{@id}' maxlength='{caratteri_max}' size='{caratteri_max}'>
		<xsl:call-template name="dammiattributicontrollo">
			<xsl:with-param name="offset_sinistra"><xsl:value-of select="80"/></xsl:with-param>
		</xsl:call-template>
	</input>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="captcha.input">

	<!-- deve tornare un array con 2 elementi: la chiave e il valore -->
	<xsl:element name="{@id}">
		<xsl:variable name="id" select="@id" />
		<xsl:choose>
			<xsl:when test="/wamodulo.input/post/item[@id=$id]">
				<k><xsl:value-of select="/wamodulo.input/post/item[@id=concat('k_', $id)]" /></k>
				<v><xsl:value-of select="/wamodulo.input/post/item[@id=$id]" /></v>
			</xsl:when>
			<xsl:otherwise>__wamodulo_valore_non_ritornato__</xsl:otherwise>
		</xsl:choose>
	</xsl:element>
	

</xsl:template>


</xsl:stylesheet>