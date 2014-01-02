<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template valuta                                                        -->
<!-- ********************************************************************** -->
<xsl:template match="valuta">

	<xsl:call-template name="intestazione_controllo"/>
	<xsl:variable name="caratteri_max" select="nr_interi + floor(nr_interi div 3) + number(nr_interi mod 3 &gt; 0) + nr_decimali" />
		
	<input type='text' name='{@id}' maxlength='{$caratteri_max}' size='{$caratteri_max}'>
		<xsl:attribute name='value'>
			<!--	manca la definizione del nr decimali... -->
			<xsl:choose>
				<xsl:when test="valore &gt; 0">
					<xsl:value-of select="format-number(valore,  '#.##0,00')"/>
				</xsl:when>
				<xsl:when test="vuotoSeZero != '1'">0,00</xsl:when>
			</xsl:choose>
		</xsl:attribute>
	
		<xsl:call-template name="dammiattributicontrollo">
			<xsl:with-param name="allineamento_testo">right</xsl:with-param>
		</xsl:call-template>
	</input>
	
	<!--	parcheggiamo le proprieta' particolari nel controllo html, in modo che-->
	<!--	poi la classe applicativa  possa ritrovarli-->
	<script type='text/Javascript'>
		document.getElementById('<xsl:value-of select="/wamodulo/nome" />').<xsl:value-of select="@id" />.nrDecimali  =  "<xsl:value-of select="nr_decimali"/>";
	</script>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="valuta.input">
	<xsl:element name="{@id}">
		<xsl:variable name="id" select="@id" />
		<xsl:choose>
			<xsl:when test="/wamodulo.input/post/item[@id=$id]">
				<!--toglie la formattazione--> 
				<xsl:call-template name="replace-string">
					<xsl:with-param name="search" select="','"/>
					<xsl:with-param name="replace" select="'.'"/>
					<xsl:with-param name="string" >
						<xsl:call-template name="replace-string">
							<xsl:with-param name="search" select="'.'"/>
							<xsl:with-param name="replace" select="''"/>
							<xsl:with-param name="string" select="/wamodulo.input/post/item[@id=$id]"/>
						</xsl:call-template>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>__wamodulo_valore_non_ritornato__</xsl:otherwise>
		</xsl:choose>
	</xsl:element>

</xsl:template>


</xsl:stylesheet>