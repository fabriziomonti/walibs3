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
				<xsl:with-param name="titolo" select="concat('Tabella DB - ', nome)" />
			</xsl:call-template>

			<body >

				<xsl:call-template name="barra_navigazione" />

				<xsl:call-template name="titolo">
					<xsl:with-param name="titolo" select="'Tabella DB'" />
					<xsl:with-param name="titolo2" select="nome" />
				</xsl:call-template>

				<h2>Dati tecnici</h2>
				<table>
					<tr>
						<th>Tipo DB</th>
						<td>
							<xsl:value-of select="tipo" />
						</td>
					</tr>
					<tr>
						<th>Nome DB</th>
						<td>
							<xsl:value-of select="nome_db" />
						</td>
					</tr>
					<tr>
						<th>Descrizione</th>
						<td>
							<xsl:value-of select="descrizione" disable-output-escaping="yes"/>
						</td>
					</tr>
				</table>
				

				<h2>Campi</h2>

				<table>
					<tr>
						<th>Nome campo</th>
						<th>Tipo</th>
						<th>Tipo orig.</th>
						<th>Lunghezza</th>
						<th>Chiave prim.</th>
						<th>Note</th>
					</tr>


					<xsl:for-each select="campi/*">

						<tr>
							<td>
								<a name="{idCampo}">
									<xsl:value-of select="nome" />
								</a>
							</td>
							<td>
								<xsl:value-of select="tipo" />
							</td>
							<td>
								<xsl:value-of select="tipoDB" />
							</td>
							<td style="text-align: right">
								<xsl:value-of select="lunghezza" />
							</td>
							<td style="text-align: center">
								<xsl:if test="chiavePrimaria = '1'">si</xsl:if>
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