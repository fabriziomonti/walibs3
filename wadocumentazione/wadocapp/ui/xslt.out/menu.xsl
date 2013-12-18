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
				<xsl:with-param name="titolo" select="concat('Menu - ', menu/titolo)" />
			</xsl:call-template>

			<body >

				<xsl:call-template name="barra_navigazione" />

				<xsl:call-template name="titolo">
					<xsl:with-param name="titolo" select="'Menu'" />
					<xsl:with-param name="titolo2" select="menu/titolo" />
				</xsl:call-template>

				<h2>Appartiene a</h2>
				<table>
				<tr><th>Sezione</th></tr>
				<tr><td>
						<a href='sezione.{sezione/idSezione}.html'><xsl:value-of select="sezione/nome" /></a>
					</td>
				</tr>
				</table>

				<xsl:if test="menu/descrizione != ''">
					<p />
					<table>
						<tr>
							<th>Descrizione</th>
							<td>
								<xsl:value-of select="menu/descrizione" disable-output-escaping="yes"/>
							</td>
						</tr>
					</table>
						
				</xsl:if>
				

				<h2>Voci</h2>

				<table>
					<tr>
						<th>Voce</th>
						<th style="text-align: right">Livello</th>
						<th>Note</th>
					</tr>


					<xsl:for-each select="voci/item">

						<tr>
							<td>
								<xsl:if test="livello = '1'">-- </xsl:if>
								<xsl:if test="livello = '2'">---- </xsl:if>
								<xsl:if test="livello = '3'">------ </xsl:if>
								<xsl:if test="livello = '4'">-------- </xsl:if>
								<xsl:if test="livello = '5'">---------- </xsl:if>

								<xsl:choose>
									<xsl:when test="idPagina > 0">
										<a href="pagina.{idPagina}.html">
											<xsl:value-of select="etichetta" />
										</a>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="etichetta" />
									</xsl:otherwise>
								</xsl:choose>
							</td>
							<td style="text-align: right">
								<xsl:value-of select="livello" />
							</td>
							<td>
								<xsl:value-of select="descrizione" disable-output-escaping="yes"/>
							</td>
						</tr>
					</xsl:for-each>
				</table>
				
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