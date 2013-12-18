<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 
<xsl:decimal-format decimal-separator=","  grouping-separator="." /> 

<!-- ********************************************************************** -->
<!-- template creazione degli oggetti javascript                            -->
<!-- ********************************************************************** -->
<xsl:template name="crea_oggetti_javascript">

	<script type='text/javascript'>
		// inizializzazione parametri tabella <xsl:value-of select="nome"/>
		document.<xsl:value-of select="nome"/> = new watabella('<xsl:value-of select="nome"/>', '<xsl:value-of select="watabella_intestazioni/intestazione[1]/nome"/>', '<xsl:value-of select="selezione_esclusiva"/>', '<xsl:value-of select="pagina_modulo"/>');
		<xsl:for-each select="watabella_righe/riga">
			new wariga(document.<xsl:value-of select="/watabella/nome"/>, '<xsl:value-of select="@id"/>', <xsl:call-template name="riga2json" />);
		</xsl:for-each>

		// inizializzazione delle proprieta' delle colonne per gestione input
		<xsl:for-each select="watabella_intestazioni/intestazione[mostra=1]">
			new wacolonna(document.<xsl:value-of select="/watabella/nome"/>, "<xsl:value-of select="nome"/>", "<xsl:value-of select="etichetta"/>", "<xsl:value-of select="tipo_campo"/>", "<xsl:value-of select="input/tipo"/>", "<xsl:value-of select="input/obbligatorio"/>");
		</xsl:for-each>

	</script>		
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- template finestra ordinamento e filtro  -->
<!-- ********************************************************************** -->
<xsl:template name="watabella_finestra_ordinamento_filtro">

	<div id='{/watabella/nome}_finestra_ordinamento_filtro' class='watabella_finestra_ordinamento_filtro' style='visibility: hidden;'>
		<p>Ordinamento/Filtro <xsl:value-of select="titolo" /></p>
		
		<form  action='{/watabella/uri}' id='{/watabella/nome}_modulo_ordinamento_filtro' onsubmit='return document.{nome}.filtra(this)'>
		
			<table border='0'>
				<tr>
					<td style='width: 50%; text-align: center;'>Ordina per</td>	
					<td style='width: 50%; text-align: center;'>In modalità</td>	
				</tr>
				<xsl:call-template name="option_ordinamento">
					<xsl:with-param name="start"><xsl:value-of select="0"/></xsl:with-param>
					<xsl:with-param name="end"><xsl:value-of select="2"/></xsl:with-param>
				</xsl:call-template>	
			</table>
			<table border='0'>
				<tr>
					<td style='width: 33%; text-align: center;'>Filtro</td>	
					<td style='width: 33%; text-align: center;'>Modo</td>	
					<td style='width: 33%; text-align: center;'>Valore</td>	
				</tr>
				<xsl:call-template name="option_filtro">
					<xsl:with-param name="start"><xsl:value-of select="0"/></xsl:with-param>
					<xsl:with-param name="end"><xsl:value-of select="5"/></xsl:with-param>
				</xsl:call-template>	
			</table>
			<table border='0'>
				<tr>
					<td  style='width: 50%; text-align: right;'>
						<input type='submit' value='Ordina/Filtra' />
					</td>
					<td  style='width: 50%; text-align: left;'>
						<input type='button' value='Annulla' onclick='document.{/watabella/nome}.chiudiOrdinamentoFiltro()' />
					</td>
				</tr>
			</table>
		</form>
	</div>

		
</xsl:template>

<!-- ********************************************************************** -->
<!--  subroutine template creazione option per ordinamento                  -->
<!-- ********************************************************************** -->
<xsl:template name="option_ordinamento">
	<xsl:param name="start"/>
	<xsl:param name="end"/>

	<xsl:if test="$start &lt;= $end">
		<tr>
			<td style='width: 50%; text-align: center;'>
				<select name='watbl_oc[{/watabella/nome}][{$start}]'>
					<option value=''></option>
					<xsl:for-each select="watabella_intestazioni/intestazione[ordina=1]">
						<option value='{nome}'>
							<xsl:if test="/watabella/ordinamento/item[indice = $start]/campo = nome">
								<xsl:attribute name='selected'>yes</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="etichetta" />
						</option>
					</xsl:for-each>
				</select>
			</td>			
			<td style='width: 50%; text-align: center;'>
				<select name='watbl_om[{/watabella/nome}][{$start}]'>
					<xsl:for-each select="/watabella/modi_ordinamento/item">
						<option value='{valore}'>
							<xsl:if test="/watabella/ordinamento/item[indice = $start]/modo = valore">
								<xsl:attribute name='selected'>yes</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="nome"/>
						</option>
					</xsl:for-each>
				</select>
			</td>
		</tr>
		<xsl:call-template name="option_ordinamento">
			<xsl:with-param name="start"><xsl:value-of select="$start + 1"/></xsl:with-param>
			<xsl:with-param name="end"><xsl:value-of select="$end"/></xsl:with-param>
		</xsl:call-template>	
	</xsl:if>
	<xsl:if test="$start &gt; $end">
		<xsl:text>&#10;</xsl:text>
	</xsl:if>

</xsl:template>

<!-- ********************************************************************** -->
<!--  subroutine template creazione option per filtro                       -->
<!-- ********************************************************************** -->
<xsl:template name="option_filtro">
	<xsl:param name="start"/>
	<xsl:param name="end"/>

	<xsl:if test="$start &lt;= $end">
		<tr>
			<td style='width: 33%; text-align: center;'>
				<select name='watbl_fc[{/watabella/nome}][{$start}]'>
					<option value=''></option>
					<xsl:for-each select="watabella_intestazioni/intestazione[filtra=1]">
						<option value='{nome}'>
							<xsl:if test="/watabella/filtro/item[indice = $start]/campo = nome">
								<xsl:attribute name='selected'>yes</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="etichetta" />
						</option>
					</xsl:for-each>
				</select>
			</td>			
			<td style='width: 34%; text-align: center;'>
				<select name='watbl_fm[{/watabella/nome}][{$start}]'>
					<option value=''></option>
					<xsl:for-each select="/watabella/modi_filtro/item">
						<option value='{valore}'>
							<xsl:if test="/watabella/filtro/item[indice = $start]/modo = valore">
								<xsl:attribute name='selected'>yes</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="nome"/>
						</option>
					</xsl:for-each>
				</select>
			</td>
			<td style='width: 33%; text-align: center;'>
				<input name='watbl_fv[{/watabella/nome}][{$start}]' value='{/watabella/filtro/item[indice = $start]/valore}'>
					<xsl:attribute name='value'>
						<xsl:variable name="my_val">
							<xsl:value-of select="/watabella/filtro/item[indice = $start]/valore"/>
						</xsl:variable>
						<xsl:choose>
							<xsl:when test="/watabella/watabella_intestazioni/intestazione[nome = /watabella/filtro/item[indice = $start]/campo]/tipo_campo = 'DATA'">
								<xsl:value-of select="substring($my_val, 9, 2)"/>/<xsl:value-of select="substring($my_val, 6, 2)"/>/<xsl:value-of select="substring($my_val, 1, 4)"/>
							</xsl:when>
							<xsl:when test="/watabella/watabella_intestazioni/intestazione[nome = /watabella/filtro/item[indice = $start]/campo]/tipo_campo = 'DATAORA'">
								<xsl:value-of select="substring($my_val, 9, 2)"/>/<xsl:value-of select="substring($my_val, 6, 2)"/>/<xsl:value-of select="substring($my_val, 1, 4)"/>
									<xsl:text>&#x20;</xsl:text>
								<xsl:value-of select="substring($my_val, 12, 2)"/>:<xsl:value-of select="substring($my_val, 15, 2)"/>
							</xsl:when>
							<xsl:when test="/watabella/watabella_intestazioni/intestazione[nome = /watabella/filtro/item[indice = $start]/campo]/tipo_campo = 'ORA'">
								<xsl:value-of select="substring($my_val, 1, 2)"/>:<xsl:value-of select="substring($my_val, 4, 2)"/>
							</xsl:when>
							<xsl:when test="/watabella/watabella_intestazioni/intestazione[nome = /watabella/filtro/item[indice = $start]/campo]/tipo_campo = 'DECIMALE'">
								<xsl:value-of select="format-number($my_val,  '#.##0,00')"/>
							</xsl:when>
							<xsl:otherwise>	
								<xsl:value-of select="$my_val"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
				</input>
			</td>
		</tr>
		<xsl:call-template name="option_filtro">
			<xsl:with-param name="start"><xsl:value-of select="$start + 1"/></xsl:with-param>
			<xsl:with-param name="end"><xsl:value-of select="$end"/></xsl:with-param>
		</xsl:call-template>	
	</xsl:if>
	<xsl:if test="$start &gt; $end">
		<xsl:text>&#10;</xsl:text>
	</xsl:if>

</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template name="linebreak">
	<xsl:param name="text"/>
	
	<xsl:choose>
		<xsl:when test="contains($text, '&#xa;')">
			<xsl:value-of select="substring-before($text, '&#xa;')" />
			<br/>
			<xsl:call-template name="linebreak">
				<xsl:with-param name="text" select="substring-after($text, '&#xa;')"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$text" />
		</xsl:otherwise>
	</xsl:choose>
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
<!--  maschera un valore per farlo "masticare" a javascript                 -->
<!-- ********************************************************************** -->
<xsl:template name="valore2json">
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
						<xsl:with-param name="search" select="'&#x09;'" />
						<xsl:with-param name="replace" select="'\t'" />
						<xsl:with-param name="string">
							<xsl:call-template name="replace-string">
								<xsl:with-param name="search" select="'&#x0a;'" />
								<xsl:with-param name="replace" select="'\n'" />
								<xsl:with-param name="string">
									<xsl:call-template name="replace-string">
										<xsl:with-param name="search" select="'&#x0d;'" />
										<xsl:with-param name="replace" select="'\r'" />
										<xsl:with-param name="string">
											<xsl:call-template name="replace-string">
												<!-- remember to do backslash first -->
												<xsl:with-param name="search">\</xsl:with-param>
												<xsl:with-param name="replace">\\</xsl:with-param>
												<xsl:with-param name="string" select="$string"></xsl:with-param>
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
<!--  data una riga, restituisce i dati della stessa in una stringa         -->
<!--  contenente un oggetto in formato json                                 -->
<!-- ********************************************************************** -->
<xsl:template name="riga2json">
	<xsl:text>{</xsl:text>
	<xsl:for-each select="cella">
		<xsl:variable name="cellpos" select="position()" />
		<xsl:variable name="col_info" select="/watabella/watabella_intestazioni/intestazione[position()=$cellpos]" />
		<xsl:text>"</xsl:text><xsl:value-of select="$col_info/nome" /><xsl:text>":"</xsl:text>
		<xsl:call-template name="valore2json">
			<xsl:with-param name="string" select="valore" />
		</xsl:call-template>
		<xsl:text>"</xsl:text>
		<xsl:if test="following-sibling::*"><xsl:text>,</xsl:text></xsl:if>
	</xsl:for-each>
	<xsl:text>}</xsl:text>
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
</xsl:stylesheet>
