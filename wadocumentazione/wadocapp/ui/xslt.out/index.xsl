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
				<xsl:with-param name="titolo" select="concat(titolo, ' - Documento di analisi funzionale')" />
			</xsl:call-template>

			<body >

			<xsl:call-template name="titolo">
				<xsl:with-param name="titolo" select="concat(titolo, ' - Documento di analisi funzionale')" />
			</xsl:call-template>
		
				<table>
					<tr>
						<th>Versione</th>
						<td>
							<xsl:value-of select="versione" />
						</td>
					</tr>
					<tr>
						<th>Data</th>
						<td>								
							<xsl:value-of select="substring(data_versione, 9, 2)"/>/
							<xsl:value-of select="substring(data_versione, 6, 2)"/>/
							<xsl:value-of select="substring(data_versione, 1, 4)"/>
						</td>
					</tr>
					<tr>
						<th>Autore/i</th>
						<td>
							<xsl:value-of select="autore" />
						</td>
					</tr>
					<xsl:if test="descrizione != ''">
						<tr>
							<th>Descrizione</th>
							<td>
								<xsl:value-of select="descrizione" disable-output-escaping="yes"/>
							</td>
						</tr>
					</xsl:if>
				</table>

				<div>
					<h3>Indice dei contenuti</h3>
				</div>
		
				<div class='wadoc_indice'>
					<a href='db.html'>Base dati</a>
				</div>

				<h2>Sezioni applicative</h2>
				<div class='wadoc_indice'>
					<xsl:for-each select="sezioni/item">
						<a href='sezione.{@id}.html'>
							<xsl:value-of select="." />
						</a>
						<br />
					</xsl:for-each>
				</div>

				<h2>Allegati</h2>
				<div class='wadoc_indice'>
					<xsl:for-each select="allegati/item">
						<a href='allegati/{nome}' target='_blank'>
							<xsl:value-of select="titolo" />
						</a>
						<br />
					</xsl:for-each>
				</div>

			
			</body>
		</html>
	</xsl:template>


	<!-- ********************************************************************** -->
	<!-- ********************************************************************** -->
	<!-- ********************************************************************** -->
</xsl:stylesheet>