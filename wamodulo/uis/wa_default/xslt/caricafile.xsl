<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- template caricafile                                                    -->
<!-- ********************************************************************** -->
<xsl:template match="caricafile">

	<xsl:call-template name="intestazione_controllo"/>
	<input type='file' name='{@id}'>
		<xsl:call-template name="dammiattributicontrollo"/>
	</input>

	<xsl:call-template name="bottone_mostrafile"/>

	<label id='wamodulo_etichettaeliminafile_{/wamodulo/nome}_{@id}'>
		<xsl:attribute name='style'>
			<xsl:text>position:absolute;</xsl:text>
			<xsl:if test="alto != '' and alto != '0' ">top:<xsl:value-of select="alto"/>px;</xsl:if>
			<xsl:if test="valore = '' or visibile = '0' or sola_lettura = '1' or obbligatorio = '1'">visibility:hidden;</xsl:if>
			<xsl:text>width: 40px;</xsl:text>
			<xsl:text>left:</xsl:text><xsl:value-of select="sinistra + larghezza + 65"/><xsl:text>px;</xsl:text>
		</xsl:attribute>
		<xsl:text>elimina</xsl:text>
	</label>
	
	<input type='checkbox' name='wamodulo_logicoeliminafile_{/wamodulo/nome}[{@id}]'>
		<xsl:attribute name='style'>
			<xsl:text>position:absolute;</xsl:text>
			<xsl:if test="alto != '' and alto != '0' ">top:<xsl:value-of select="alto"/>px;</xsl:if>
			<xsl:if test="valore = '' or visibile = '0' or sola_lettura = '1' or obbligatorio = '1'">visibility:hidden;</xsl:if>
			<xsl:text>left:</xsl:text><xsl:value-of select="sinistra + larghezza + 115"/><xsl:text>px;</xsl:text>
		</xsl:attribute>
	</input>
	
</xsl:template>


<!-- ********************************************************************** -->
<!--  subroutine template creazione bottone visualizzazione file per caricafile -->
<!-- ********************************************************************** -->
<xsl:template name="bottone_mostrafile">

	<xsl:variable name='tooltip'>
		<xsl:text>vedi il documento esistente </xsl:text>
		<xsl:value-of select="basename"/>
		<xsl:if test="dimensioni_file != ''">
			<xsl:text> (</xsl:text>
			<xsl:value-of select="dimensioni_file"/>
			<xsl:text> Kb)</xsl:text>
		</xsl:if>
	</xsl:variable>
	
	<input name='wamodulo_mostrafile_{@id}' value='vedi' type='button' onclick='window.open("{pagina_visualizzazione}")'>
		<xsl:if test="indice_tab != '' and indice_tab != '0' ">
			<xsl:attribute name='tabindex'><xsl:value-of select="indice_tab"/></xsl:attribute>
		</xsl:if>
		<xsl:attribute name='title'>
			<xsl:text>vedi il documento esistente </xsl:text>
			<xsl:value-of select="basename"/>
			<xsl:if test="dimensioni_file != ''">
				<xsl:text> (</xsl:text>
				<xsl:value-of select="dimensioni_file"/>
				<xsl:text> Kb)</xsl:text>
			</xsl:if>
		</xsl:attribute>
		<xsl:attribute name='style'>
			<xsl:text>position:absolute;</xsl:text>
			<xsl:if test="alto != '' and alto != '0' ">top:<xsl:value-of select="alto"/>px;</xsl:if>
			<xsl:if test="visibile = '0' or valore = ''">visibility:hidden;</xsl:if>
			<xsl:text>width:45px;</xsl:text>
			<xsl:text>left:</xsl:text><xsl:value-of select="sinistra + larghezza + 10"/><xsl:text>px;</xsl:text>
		</xsl:attribute>
		
	</input>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="caricafile.input">
	<xsl:element name="{@id}">
		<xsl:variable name="id" select="@id" />
		<xsl:choose>
			<xsl:when test="/wamodulo.input/files/item[@id=$id] or /wamodulo.input/post/item[@id=concat('wamodulo_logicoeliminafile_', /wamodulo.input/nome)]">
				<xsl:for-each select="/wamodulo.input/files/item[@id=$id]/*">
					<xsl:element name="{@id}">
						<xsl:value-of select="."/>
					</xsl:element>
				</xsl:for-each>
				<elimina>
					<xsl:choose>
						<xsl:when test="/wamodulo.input/post/item[@id=concat('wamodulo_logicoeliminafile_', /wamodulo.input/nome)]/item[@id=$id] = 'on'">1</xsl:when>
						<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose>
				</elimina>
			</xsl:when>
			<xsl:otherwise>"__wamodulo_valore_non_ritornato__"</xsl:otherwise>
		</xsl:choose>
	</xsl:element>

</xsl:template>

</xsl:stylesheet>