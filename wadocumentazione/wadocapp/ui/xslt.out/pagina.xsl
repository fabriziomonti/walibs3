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
				<xsl:with-param name="titolo" select="concat('Pagina - ', pagina/titolo)" />
			</xsl:call-template>

			<body >

				<xsl:call-template name="barra_navigazione" />

				<xsl:call-template name="titolo">
					<xsl:with-param name="titolo" select="'Pagina'" />
					<xsl:with-param name="titolo2" select="pagina/titolo" />
				</xsl:call-template>

				<h2>Appartiene a</h2>
				<table>
				<tr><th>Sezione</th></tr>
				<tr><td>
						<a href='sezione.{sezione/idSezione}.html'><xsl:value-of select="sezione/nome" /></a>
					</td>
				</tr>
				</table>

				<xsl:if test="pagina/descrizione != ''">
					<p />
					<table>
						<tr>
							<th>Descrizione</th>
							<td>
								<xsl:value-of select="pagina/descrizione" disable-output-escaping="yes"/>
							</td>
						</tr>
					</table>
						
				</xsl:if>
				
				<h2>Tabelle</h2>
				<div class='wadoc_indice'>

					<xsl:for-each select="tabelle/item">
						<a href='tabella.{@id}.html'>
							<xsl:value-of select="titolo" />
						</a>
						<br />
					</xsl:for-each>
				</div>

				<h2>Moduli</h2>
				<div class='wadoc_indice'>
					<xsl:for-each select="moduli/item">
						<a href='modulo.{@id}.html'>
							<xsl:value-of select="titolo" />
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