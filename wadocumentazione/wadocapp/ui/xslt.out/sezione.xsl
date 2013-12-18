<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="template_comuni.xsl"/>

	<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 

	<!-- ********************************************************************** -->
	<!--  ************* template della pagina ********************************* -->
	<!-- ********************************************************************** -->
	<xsl:template match="wadocumentazione">
		<html xmlns="http://www.w3.org/1999/xhtml">

			<xsl:call-template name="head">
				<xsl:with-param name="titolo" select="concat('Sezione - ', nome)" />
			</xsl:call-template>

			<body >

				<xsl:call-template name="barra_navigazione" />

				<xsl:call-template name="titolo">
					<xsl:with-param name="titolo" select="'Sezione'" />
					<xsl:with-param name="titolo2" select="nome" />
				</xsl:call-template>

				<xsl:if test="descrizione != ''">
					<p />
					<table>
						<tr>
							<th>Descrizione</th>
							<td>
								<xsl:value-of select="descrizione" disable-output-escaping="yes"/>
							</td>
						</tr>
					</table>
						
				</xsl:if>
				
				<h2>Menu</h2>
				<div class='wadoc_indice'>

					<xsl:for-each select="menu/item">
						<a href='menu.{@id}.html'>
							<xsl:value-of select="." />
						</a>
						<br />
					</xsl:for-each>
				</div>

				<h2>Pagine</h2>
				<div class='wadoc_indice'>
					<xsl:for-each select="pagine/item">
						<a href='pagina.{@id}.html'>
							<xsl:value-of select="." />
						</a>
						<br />
					</xsl:for-each>
				</div>

				<xsl:call-template name="barra_navigazione" >
					<xsl:with-param name="fine_pagina" select="'1'" />
				</xsl:call-template>
			
			</body>
		</html>
	</xsl:template>


	<!-- ********************************************************************** -->
	<!-- ********************************************************************** -->
	<!-- ********************************************************************** -->
</xsl:stylesheet>