<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!--  subroutine template creazione option per le date e le ore             -->
<!-- ********************************************************************** -->
<xsl:template name="option_loop">
	<xsl:param name="start"/>
	<xsl:param name="end"/>
	<xsl:param name="toselect"/>
	<xsl:param name="descending"/>

	<xsl:if test="$descending != '1'">
		<xsl:if test="$start &lt;= $end">
			<xsl:variable name="start_formattato"><xsl:value-of select="format-number($start, '00')"/></xsl:variable>
		 	<option value='{$start_formattato}'>
				<xsl:if test="$toselect = $start_formattato"><xsl:attribute name='selected'>selected</xsl:attribute></xsl:if>
				<xsl:value-of select="$start_formattato"/>
			</option>
			<xsl:call-template name="option_loop">
				<xsl:with-param name="start"><xsl:value-of select="$start + 1"/></xsl:with-param>
				<xsl:with-param name="end"><xsl:value-of select="$end"/></xsl:with-param>
				<xsl:with-param name="toselect"><xsl:value-of select="$toselect"/></xsl:with-param>
			</xsl:call-template>	
		</xsl:if>
		<xsl:if test="$start &gt; $end">
			<xsl:text>&#10;</xsl:text>
		</xsl:if>
	</xsl:if>

	<xsl:if test="$descending = '1'">
		<xsl:if test="$end &gt;= $start">
			<xsl:variable name="end_formattato"><xsl:value-of select="format-number($end, '00')"/></xsl:variable>
		 	<option value='{$end_formattato}'>
				<xsl:if test="$toselect = $end_formattato"><xsl:attribute name='selected'>selected</xsl:attribute></xsl:if>
				<xsl:value-of select="$end_formattato"/>
			</option>
			<xsl:call-template name="option_loop">
				<xsl:with-param name="start"><xsl:value-of select="$start"/></xsl:with-param>
				<xsl:with-param name="end"><xsl:value-of select="$end - 1"/></xsl:with-param>
				<xsl:with-param name="toselect"><xsl:value-of select="$toselect"/></xsl:with-param>
				<xsl:with-param name="descending"><xsl:value-of select="$descending"/></xsl:with-param>
			</xsl:call-template>	
		</xsl:if>
		<xsl:if test="$end &lt; $start">
			<xsl:text>&#10;</xsl:text>
		</xsl:if>
	</xsl:if>

</xsl:template>

<!-- ********************************************************************** -->
<!--  subroutine template creazione tendine per le date                     -->
<!-- ********************************************************************** -->
<xsl:template name="tendine_data">
	
	<select name='wamodulo_giorno_{@id}'>
		<xsl:call-template name="dammiattributicontrollo"/>
		<option value=''></option>
		<xsl:call-template name="option_loop">
			<xsl:with-param name="start">1</xsl:with-param>
			<xsl:with-param name="end">31</xsl:with-param>
			<xsl:with-param name="toselect"><xsl:value-of select="substring(valore, 9, 2)"/></xsl:with-param>
		</xsl:call-template>
	</select>
	<xsl:text>&#10;</xsl:text>

	<select name='wamodulo_mese_{@id}'>
		<xsl:call-template name="dammiattributicontrollo">
			<xsl:with-param name="offset_sinistra">50</xsl:with-param>
		</xsl:call-template>
		<option value=''></option>
		<xsl:call-template name="option_loop">
			<xsl:with-param name="start">1</xsl:with-param>
			<xsl:with-param name="end">12</xsl:with-param>
			<xsl:with-param name="toselect"><xsl:value-of select="substring(valore, 6, 2)"/></xsl:with-param>
		</xsl:call-template>
	</select>
	<xsl:text>&#10;</xsl:text>

	<select name='wamodulo_anno_{@id}'>
		<xsl:call-template name="dammiattributicontrollo">
			<xsl:with-param name="offset_sinistra">100</xsl:with-param>
		</xsl:call-template>
		<option value=''></option>
		<xsl:call-template name="option_loop">
			<xsl:with-param name="start"><xsl:value-of select="anno_partenza"/></xsl:with-param>
			<xsl:with-param name="end"><xsl:value-of select="anno_termine"/></xsl:with-param>
			<xsl:with-param name="toselect"><xsl:value-of select="substring(valore, 1, 4)"/></xsl:with-param>
			<xsl:with-param name="descending"><xsl:value-of select="anno_decrescente"/></xsl:with-param>
		</xsl:call-template>
	</select>
	
</xsl:template>
	
<!-- ********************************************************************** -->
<!--  subroutine template creazione tendine per le ore                      -->
<!-- ********************************************************************** -->
<xsl:template name="tendine_ora">
	<xsl:param name="offset_sinistra" select="0"/>
	<xsl:param name="offset_valore" select="0"/>

	<xsl:text>&#10;</xsl:text>
	<select name='wamodulo_ora_{@id}'>
		<xsl:call-template name="dammiattributicontrollo">
			<xsl:with-param name="offset_sinistra">
				<xsl:value-of select="$offset_sinistra"/>			
			</xsl:with-param>
		</xsl:call-template>
		<option value=''></option>
		<xsl:call-template name="option_loop">
			<xsl:with-param name="start">0</xsl:with-param>
			<xsl:with-param name="end">24</xsl:with-param>
			<xsl:with-param name="toselect"><xsl:value-of select="substring(valore, 1 + $offset_valore, 2)"/></xsl:with-param>
		</xsl:call-template>
	</select>
	<xsl:text>&#10;</xsl:text>

	<select name='wamodulo_min_{@id}'>
		<xsl:call-template name="dammiattributicontrollo">
			<xsl:with-param name="offset_sinistra">
				<xsl:value-of select="$offset_sinistra + 50"/>			
			</xsl:with-param>
		</xsl:call-template>
		<option value=''></option>
		<xsl:call-template name="option_loop">
			<xsl:with-param name="start">0</xsl:with-param>
			<xsl:with-param name="end">59</xsl:with-param>
			<xsl:with-param name="toselect"><xsl:value-of select="substring(valore, 4 + $offset_valore, 2)"/></xsl:with-param>
		</xsl:call-template>
	</select>

	<xsl:if test="mostra_secondi = '1'">
		<xsl:text>&#10;</xsl:text>
		<select name='wamodulo_sec_{@id}'>
			<xsl:call-template name="dammiattributicontrollo">
				<xsl:with-param name="offset_sinistra">
					<xsl:value-of select="$offset_sinistra + 100"/>			
				</xsl:with-param>
			</xsl:call-template>
			<option value=''></option>
			<xsl:call-template name="option_loop">
				<xsl:with-param name="start">0</xsl:with-param>
				<xsl:with-param name="end">59</xsl:with-param>
				<xsl:with-param name="toselect"><xsl:value-of select="substring(valore, 7 + $offset_valore, 2)"/></xsl:with-param>
			</xsl:call-template>
		</select>
	</xsl:if>
	
</xsl:template>
	
<!-- ********************************************************************** -->
<!--  subroutine template creazione bottoni calendario per le date          -->
<!-- ********************************************************************** -->
<xsl:template name="bottone_calendario">
	<xsl:param name="tipo" />
	<xsl:param name="offset_sinistra"/>

	<xsl:text>&#10;</xsl:text>
	<input name='wamodulo_{$tipo}cal_{@id}' title='Calendario {$tipo}' type='button'>
		<xsl:if test="indice_tab != '' and indice_tab != '0' "><xsl:attribute name='tabindex'><xsl:value-of select="indice_tab"/></xsl:attribute></xsl:if>
		<xsl:if test="sola_lettura = '1'"><xsl:attribute name='disabled'>disabled</xsl:attribute></xsl:if>
		<xsl:attribute name='style'>
			<xsl:text>position:absolute;</xsl:text>
			<xsl:if test="alto != '' and alto != '0' ">top:<xsl:value-of select="alto"/>px;</xsl:if>
			<xsl:if test="visibile = '0'">visibility:hidden;</xsl:if>
			<xsl:text>width:30px;</xsl:text>
			<xsl:text>left:</xsl:text><xsl:value-of select="sinistra + $offset_sinistra"/><xsl:text>px;</xsl:text>
		</xsl:attribute>
		<xsl:attribute name='value'>
			<xsl:text>...</xsl:text><xsl:if test="$tipo = 'anno'">.</xsl:if>
		</xsl:attribute>
		
		<xsl:attribute name='onclick'>
			<xsl:text>myShow</xsl:text>
			<xsl:if test="$tipo = 'mese'">Month</xsl:if>
			<xsl:if test="$tipo = 'anno'">Year</xsl:if>
			<xsl:text>Cal(this.form.wamodulo.nome + ".obj.pass_</xsl:text>
			<xsl:value-of select="@id"/>
			<xsl:text>")</xsl:text>
		</xsl:attribute>
		
	</input>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->

</xsl:stylesheet>