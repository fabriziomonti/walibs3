<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ************************   SUBROUTINES   ***************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->

<!-- ********************************************************************** -->
<!-- subroutine template dammiattributicontrollo -->
<!-- ********************************************************************** -->
<xsl:template name="dammiattributicontrollo">
	<xsl:param name="offset_sinistra" />
	<xsl:param name="allineamento_testo" />
	<!-- normalmente la sorgente dei parametri e' l'elemento corrente (.); nel caso
	delle opzioni (radio) la sorgente dei parametri viene passata perche'
	e' il parent del current -->
	<xsl:param name="src_parametri" select="."/>

	<xsl:if test="$src_parametri/indice_tab != '' and $src_parametri/indice_tab != '0' ">
		<xsl:attribute name='tabindex'>
			<xsl:value-of select="$src_parametri/indice_tab"/>
		</xsl:attribute>
	</xsl:if>
	<xsl:if test="$src_parametri/sola_lettura = '1'">
		<xsl:attribute name='disabled'>disabled</xsl:attribute>
	</xsl:if>
	<xsl:call-template name="dammilayout">
		<xsl:with-param name="offset_sinistra" select="$offset_sinistra"/>
		<xsl:with-param name="allineamento_testo" select="$allineamento_testo"/>
		<xsl:with-param name="src_parametri" select="$src_parametri"/>
	</xsl:call-template>
	<xsl:call-template name="dammiclassecss" >
		<xsl:with-param name="src_parametri" select="$src_parametri"/>
	</xsl:call-template>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- subroutine template dammilayout -->
<!-- ********************************************************************** -->
<xsl:template name="dammilayout">
	<xsl:param name="offset_sinistra" />
	<xsl:param name="allineamento_testo" />
	<xsl:param name="src_parametri" select="."/>
	
	<xsl:attribute name='style'>
		<xsl:text>position:absolute;</xsl:text>
		<xsl:if test="$src_parametri/alto != '' and $src_parametri/alto != '0' ">top:<xsl:value-of select="$src_parametri/alto"/>px;</xsl:if>
		<xsl:if test="$src_parametri/visibile = '0'">visibility:hidden;</xsl:if>
		<xsl:if test="$src_parametri/larghezza != '' and $src_parametri/larghezza != '0' ">width:<xsl:value-of select="$src_parametri/larghezza"/>px;</xsl:if>
		<xsl:if test="$src_parametri/altezza != '' and $src_parametri/altezza != '0' ">height:<xsl:value-of select="$src_parametri/altezza"/>px;</xsl:if>
	 	<xsl:variable name="mysinistra">
			<xsl:choose>
				<xsl:when test="$offset_sinistra = ''"><xsl:value-of select="$src_parametri/sinistra"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="$src_parametri/sinistra + $offset_sinistra"/></xsl:otherwise>
			</xsl:choose>	
	 	</xsl:variable>
		<xsl:text>left:</xsl:text><xsl:value-of select="$mysinistra"/><xsl:text>px;</xsl:text>
		<xsl:if test="$allineamento_testo != ''">text-align:<xsl:value-of select="$allineamento_testo"/>;</xsl:if>
	</xsl:attribute>
		
</xsl:template>

<!-- ********************************************************************** -->
<!--  subroutine template dammiclassecss -->
<!-- ********************************************************************** -->
<xsl:template name="dammiclassecss">
	<xsl:param name="src_parametri" select="."/>
	
	<xsl:attribute name='class'>
		<xsl:if test="$src_parametri/obbligatorio = '1'">
			<xsl:text>wamodulo_obbligatorio</xsl:text>
		</xsl:if>
	</xsl:attribute>
		
</xsl:template>

<!-- ********************************************************************** -->
<!--  subroutine template intestazione controllo (per dare un po' d'ordine) -->
<!-- ********************************************************************** -->
<xsl:template name="intestazione_controllo">

	<xsl:text disable-output-escaping="yes">&#10;&lt;!-- </xsl:text>
	<xsl:value-of select="name()"/>
	<xsl:text> - </xsl:text>
	<xsl:value-of select="@id"/>
	<xsl:text disable-output-escaping="yes"> -->&#10;</xsl:text>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template name="replace-string">
	<!-- search for this: -->
	<xsl:param name="search" select="string(.)"/>

	<!-- and replace it with this: -->
	<xsl:param name="replace" select="string(.)"/>

	<!-- here is the original string: -->
	<xsl:param name="string" select="string(.)"/>
  
	<xsl:choose>
		<xsl:when test="not(contains($string, $search))">
			<!-- if there are no more appearances of $search in the
			$string, output the rest of the string and stop. -->
			<xsl:value-of select="$string"/>
		</xsl:when>
		<xsl:otherwise>
			<!-- output the part of the $string that is before the
			 first appearance of $search. -->
			<xsl:value-of select="substring-before($string, $search)"/>
      
			<!-- output the replacement $replace.  -->
			<xsl:value-of select="$replace"/>

			<!-- repeat the process, using the part of $string that
			comes after the first appearance of $search. -->
			<xsl:call-template name="replace-string">
				<xsl:with-param name="search" select="$search"/>
				<xsl:with-param name="replace" select="$replace"/>
				<xsl:with-param name="string" select="substring-after($string, $search)"/>
			</xsl:call-template>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>


<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->

<xsl:template name="escape-javascript-value">
	<xsl:param name="string" select="string(.)"/>

	<!-- replace all characters not matching SingleStringCharacter
	or DoubleStringCharacter according to ECMA262.  Note: not all
	characters that should be escaped are legal XML characters:
	"\a", "\b", "\v", and "\f" are not escaped. -->
	<xsl:call-template name="replace-string">
		<xsl:with-param name="search">'</xsl:with-param>
		<xsl:with-param name="replace">\'</xsl:with-param>
		<xsl:with-param name="string">
			<xsl:call-template name="replace-string">
				<xsl:with-param name="search">"</xsl:with-param>
				<xsl:with-param name="replace">\"</xsl:with-param>
				<xsl:with-param name="string">
					<xsl:call-template name="replace-string">
						<xsl:with-param name="search">
							<xsl:text>#9;</xsl:text>
						</xsl:with-param>
						<xsl:with-param name="replace">\t</xsl:with-param>
						<xsl:with-param name="string">
							<xsl:call-template name="replace-string">
								<xsl:with-param name="search">
									<xsl:text>&#10;</xsl:text>
								</xsl:with-param>
								<xsl:with-param name="replace">\n</xsl:with-param>
								<xsl:with-param name="string">
									<xsl:call-template name="replace-string">
										<xsl:with-param name="search">
											<xsl:text>&#13;</xsl:text>
										</xsl:with-param>
										<xsl:with-param name="replace">\r</xsl:with-param>
										<xsl:with-param name="string">
											<xsl:call-template name="replace-string">
												<!-- remember to do backslash first -->
												<xsl:with-param name="search">\</xsl:with-param>
												<xsl:with-param name="replace">\\</xsl:with-param>
												<xsl:with-param name="string" select="$string" />
											</xsl:call-template>
										</xsl:with-param>
									</xsl:call-template>
								</xsl:with-param>
							</xsl:call-template>
						</xsl:with-param>
					</xsl:call-template>
				</xsl:with-param>
			</xsl:call-template>
		</xsl:with-param>
	</xsl:call-template>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->

</xsl:stylesheet>