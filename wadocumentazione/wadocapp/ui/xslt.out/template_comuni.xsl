<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template name="head">
	<xsl:param name="titolo"/>

			<head>
				<link href='wadocumentazione.css' rel='stylesheet' type='text/css' />
				<title>
					<xsl:value-of select="$titolo" />
				</title>
			</head>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template name="titolo">
	<xsl:param name="titolo"/>
	<xsl:param name="titolo2"/>

	<h3>
		<xsl:value-of select="$titolo" /><br /><xsl:value-of select="$titolo2" />
	</h3>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template name="barra_navigazione">
	<xsl:param name="fine_pagina"/>
	
	<div class='wadoc_torna'>
		<xsl:if test="$fine_pagina"><hr /></xsl:if>
		<a href='javascript:history.back()'>Torna alla pagina precedente</a> 
		|
		<a href='index.html'>Indice</a>
		<xsl:if test="not($fine_pagina)"><hr /></xsl:if>
	</div>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->

</xsl:stylesheet>