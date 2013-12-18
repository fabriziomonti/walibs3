<?xml version="1.0" encoding="UTF-8"?>
<!--
questo template è un possibile rimpiazzo per multiselezione.xsl; 

mostra le opzioni della multiselezione sotto forma di checkbox; il che è 
decisamente più user-friendly; ci sono però delle limitazioni:

- non è possibile distinguere quando viene inviato un insieme di opzioni vuoto o
	quando non viene inviato nulla perchè il controllo è disabilitato (problema
	analogo a waLogico); è l'applicazione che deve sapere come
	gestire eventuali simili casi

per usare il template occorre:
- modificare wamodulo.xsl sostituendo multiselezione.xsl con multiselezione_checkbox.xsl
- modificare modulo.xsl sostitundo multiselezione.js con multiselezione_checkbox.js
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 
<xsl:decimal-format decimal-separator=","  grouping-separator="." /> 

<!-- ********************************************************************** -->
<!-- template multiselezione                                                -->
<!-- ********************************************************************** -->
<xsl:template match="multiselezione">

	<xsl:call-template name="intestazione_controllo"/>
	<div id="{@id}">
		<xsl:call-template name="dammiattributicontrollo"/>
		<xsl:attribute name='class'>wamodulo_multiselezione_checkbox</xsl:attribute>

		<xsl:text>&#10;</xsl:text>
		<xsl:variable name="nome_controllo">
			<xsl:value-of select="@id" />
		</xsl:variable>
		<xsl:for-each select="lista/elemento">
			<div>
				<xsl:variable name="myoption">
					<xsl:value-of select="@id"/>
				</xsl:variable>
				<input type="checkbox" name="{$nome_controllo}[{@id}]" id="{$nome_controllo}{@id}" >

					<xsl:if test="../../valore/*[@id = $myoption] != ''">
						<xsl:attribute name='checked'>checked</xsl:attribute>
					</xsl:if>
					<xsl:if test="../../sola_lettura = '1' or ../../non_selezionabili/*[@id = $myoption] != ''">
						<xsl:attribute name='disabled'>disabled</xsl:attribute>
					</xsl:if>
				</input>
				<div><label for='{$nome_controllo}{@id}'>
					<xsl:if test="../../non_selezionabili/*[@id = $myoption] != ''">
						<xsl:attribute name='class'>disabilitato</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="."/>
                                        </label>
				</div>

			</div>
			<xsl:text>&#10;</xsl:text>

		</xsl:for-each>

		<xsl:text>&#10;</xsl:text>
	</div>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template match="multiselezione.input">
	<xsl:variable name="id" select="@id" />
	<xsl:for-each select="/wamodulo.input/post/item[@id=$id]/*">
		<xsl:element name="{$id}"><xsl:value-of select="@id"/></xsl:element>
	</xsl:for-each>

</xsl:template>

</xsl:stylesheet>