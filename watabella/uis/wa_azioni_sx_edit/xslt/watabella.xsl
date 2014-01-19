<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="../../wa_file_comuni/xslt/watabella_template_comuni.xsl"/>

<xsl:output method="xml" omit-xml-declaration="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" indent="yes" /> 
<xsl:decimal-format decimal-separator=","  grouping-separator="." /> 

<!-- ********************************************************************** -->
<!--  template della tabella -->
<!-- ********************************************************************** -->
<xsl:template match="watabella">

	<xsl:text>&#10;</xsl:text>
	<link href='{watabella_path}/uis/wa_file_comuni/css/watabella.css' rel='stylesheet'/><xsl:text>&#10;</xsl:text>
	
	<!-- roba menu contestuale	(una parte...) -->
	<script type='text/javascript' src='{watabella_path}/uis/wa_file_comuni/js/strmanage.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/javascript' src='{watabella_path}/uis/wa_file_comuni/js/moo1.2.js'></script><xsl:text>&#10;</xsl:text>
	<script type='text/javascript' src='{watabella_path}/uis/wa_file_comuni/js/watabella.js'></script><xsl:text>&#10;</xsl:text>

 	<xsl:variable name="qoe">
		<xsl:choose>
			<xsl:when test="contains(uri, '?')">&amp;</xsl:when>
			<xsl:otherwise>?</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>
	<xsl:call-template name="watabella_finestra_ordinamento_filtro" />
	
	<xsl:apply-templates select="watabella_azioni_pagina"/>
	<xsl:apply-templates select="watabella_ricerca_rapida"/>
	<xsl:apply-templates select="watabella_barra_navigazione"/>
	<form id='{nome}' action='' method='post' class='watabella'>
		<!--nome della tabella da inviare insieme al submit, per sapere quale tabella ha fatto submit -->
		<input type="hidden" name="watabella_nome_tabella" value="{nome}" />

	    <table style='width: 100%'>
			<xsl:apply-templates select="watabella_intestazioni"/>
			<xsl:apply-templates select="watabella_riga_totali"/>
			<xsl:apply-templates select="watabella_righe"/>
	    </table>
	</form>
	
	<!-- creazione degli oggetti javascript -->
	<xsl:call-template name="crea_oggetti_javascript"/>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- template delle azioni su pagina -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_azioni_pagina">

	<form action='{uri}' method='post' id='{nome}_bottoniera' class='watabella'>
		<xsl:text>&#10;</xsl:text>
		<div>
			<!--bottone di submit del modulo-->
			<button type='button' name='{../nome}_submit' id='{../nome}_submit' title='Invia' value='Invia'  onclick='document.{/watabella/nome}.azione_Invia()'>
				Invia
			</button>

			<xsl:for-each select="azione">
				<button type='button' name='{nome}' id='{nome}' title='{etichetta}' value='{etichetta}' onclick='document.{/watabella/nome}.azione_{/watabella/nome}_{nome}()'>
					<xsl:value-of disable-output-escaping="yes" select="etichetta"/>
				</button>
		</xsl:for-each>
		
		<!--bottoni di esportazione-->
		<xsl:variable name="qoe">
			<xsl:choose>
				<xsl:when test="contains(/watabella/uri, '?')">&amp;</xsl:when>
				<xsl:otherwise>?</xsl:otherwise>
			</xsl:choose>	
		</xsl:variable>
		<button type='button' title='CSV' value='CSV' onclick='document.location.href="{/watabella/uri}{$qoe}watbl_esporta_csv[{/watabella/nome}]=1"'>
			CSV
		</button>
		<button type='button' title='XLS' value='XLS' onclick='document.location.href="{/watabella/uri}{$qoe}watbl_esporta_xls[{/watabella/nome}]=1"'>
			XLS
		</button>
		<button type='button' title='PDF' value='PDF' onclick='document.location.href="{/watabella/uri}{$qoe}watbl_esporta_pdf[{/watabella/nome}]=1"'>
			PDF
		</button>
		<xsl:text>&#10;</xsl:text>
		</div>
	</form>
	<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- ********************************************************************** -->
<!-- template intestazioni colonne -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_intestazioni">
 	
	<xsl:variable name="qoe">
		<xsl:choose>
			<xsl:when test="contains(/watabella/uri, '?')">&amp;</xsl:when>
			<xsl:otherwise>?</xsl:otherwise>
		</xsl:choose>	
 	</xsl:variable>

 	<thead>
		<tr id='{/watabella/nome}_intestazioni'>
			<th style='text-align: center'>Elimina</th>
		 	<th></th>
			<xsl:for-each select="intestazione[mostra=1]">
			 	<xsl:variable name="alignment">
					<xsl:choose>
						<xsl:when test="allineamento = 1">center</xsl:when>
						<xsl:when test="allineamento = 2">right</xsl:when>
						<xsl:otherwise>left</xsl:otherwise>
					</xsl:choose>	
			 	</xsl:variable>
				<th style='text-align: {$alignment}' id='{/watabella/nome}_{nome}'>
					<xsl:choose>
						<xsl:when test="ordina = '1'">
						 	<xsl:variable name="modo_ordinamento">
								<xsl:if test="ordinamento_rapido = 'asc'">desc</xsl:if>
								<xsl:if test="ordinamento_rapido = 'desc' or ordinamento_rapido = 'no'">asc</xsl:if>
						 	</xsl:variable>
							<a href='{/watabella/uri}{$qoe}watbl_or[{/watabella/nome}]={nome}&amp;watbl_orm[{/watabella/nome}]={$modo_ordinamento}'>
								<xsl:value-of select="etichetta"/>
								<xsl:if test="ordinamento_rapido != 'no'">
									<center>
										<img src='{/watabella/watabella_path}/uis/wa_file_comuni/img/{ordinamento_rapido}_order.gif' border='0'/>
									</center>
								</xsl:if>
							</a>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="etichetta"/>
						</xsl:otherwise>
					</xsl:choose>	
				</th>
			</xsl:for-each>
		</tr>
	</thead>
</xsl:template>

<!-- ********************************************************************** -->
<!-- template delle righe -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_righe">
	
	<tbody>
		<xsl:for-each select="riga">
			<xsl:variable name="objRiga">document.<xsl:value-of select="/watabella/nome"/>.righe['<xsl:value-of select="@id"/>']</xsl:variable>
			<tr id='row_{/watabella/nome}_{@id}' onclick='{$objRiga}.cambiaStato()'>
			
				<!-- valorizzazione azioni in linea -->
				<xsl:call-template name="azioni_record" />
			
				<xsl:text>&#10;</xsl:text>
				<xsl:for-each select="cella">
					<xsl:variable name="cellpos" select="position()" />
					<xsl:variable name="col_info" select="/watabella/watabella_intestazioni/intestazione[position()=$cellpos]" />
				 	<xsl:if test="$col_info/mostra = 1">
					 	<xsl:variable name="alignment">
							<xsl:choose>
								<xsl:when test="$col_info/allineamento = 1">center</xsl:when>
								<xsl:when test="$col_info/allineamento = 2">right</xsl:when>
								<xsl:otherwise>left</xsl:otherwise>
							</xsl:choose>	
					 	</xsl:variable>
						<td style='text-align: {$alignment}'>
							<xsl:choose>
								<xsl:when test="$col_info/input">
									<xsl:call-template name="input">
										<xsl:with-param name="cella" select="."/>
										<xsl:with-param name="colonna" select="$col_info"/>
									</xsl:call-template>
								</xsl:when>
								<xsl:when test="$col_info/link = 1">
									<a href='javascript:document.{/watabella/nome}.link_{/watabella/nome}_{$col_info/nome}("{../@id}")'>
										<xsl:value-of select="valore"/>
									</a>
								</xsl:when>
								<xsl:when test="$col_info/converti_html = '0'">
									<xsl:copy-of select="valore"/>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DATA'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="substring(valore, 9, 2)"/>/<xsl:value-of select="substring(valore, 6, 2)"/>/<xsl:value-of select="substring(valore, 1, 4)"/>
									</xsl:if>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DATAORA'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="substring(valore, 9, 2)"/>/<xsl:value-of select="substring(valore, 6, 2)"/>/<xsl:value-of select="substring(valore, 1, 4)"/>
											<xsl:text>&#x20;</xsl:text>
										<xsl:value-of select="substring(valore, 12, 2)"/>:<xsl:value-of select="substring(valore, 15, 2)"/>
									</xsl:if>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'ORA'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="substring(valore, 1, 2)"/>:<xsl:value-of select="substring(valore, 4, 2)"/>
									</xsl:if>
								</xsl:when>
								<xsl:when test="$col_info/tipo_campo = 'DECIMALE'">
									<xsl:if test="string-length(valore) &gt; 0">
										<xsl:value-of select="format-number(valore,  '#.##0,00')"/>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="linebreak">
										<xsl:with-param name="text" select="valore"/>
									</xsl:call-template>
								</xsl:otherwise>
							</xsl:choose>
						</td>
					</xsl:if>
				</xsl:for-each>
				
				
			</tr>
		</xsl:for-each>
	</tbody>
	
</xsl:template>

<!-- ********************************************************************** -->
<!-- template riga dei totali -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_riga_totali">
	<tfoot>
		<tr>
			<th></th>
			<th></th>
			<xsl:for-each select="cella">
				<xsl:variable name="cellpos" select="position()" />
				<xsl:variable name="col_info" select="/watabella/watabella_intestazioni/intestazione[position()=$cellpos]" />
			 	<xsl:if test="$col_info/mostra = 1">
				 	<xsl:variable name="alignment">
						<xsl:choose>
							<xsl:when test="$col_info/allineamento = 1">center</xsl:when>
							<xsl:when test="$col_info/allineamento = 2">right</xsl:when>
							<xsl:otherwise>left</xsl:otherwise>
						</xsl:choose>	
				 	</xsl:variable>
					<th style='text-align: {$alignment}'>
						<xsl:choose>
							<xsl:when test="$col_info/tipo_campo = 'DECIMALE'">
								<xsl:if test="string-length(valore) &gt; 0">
									<xsl:value-of select="format-number(valore,  '#.##0,00')"/>
								</xsl:if>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="valore"/>
							</xsl:otherwise>
						</xsl:choose>
					</th>
				</xsl:if>
			</xsl:for-each>
		</tr>
	</tfoot>
</xsl:template>


<!-- ********************************************************************** -->
<!-- template del controllo per la ricerca rapida -->
<!-- ********************************************************************** -->
<xsl:template match="watabella_ricerca_rapida">
	<form action='{uri}' class='watabella'><xsl:text>&#10;</xsl:text>
		<div>
			<input name='watbl_rr[{/watabella/nome}]' value='{valore}'/>
			<span>
				<button type='submit' title='Cerca' value='Cerca'>Cerca</button>
			</span>
		</div>
	</form><xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<xsl:template name="azioni_record">
	<xsl:variable name="record" select="."/>

	<xsl:text>&#10;</xsl:text>
	<td style='text-align: center'>
		<xsl:if test="not($record/azioni_abilitabili/azione[@id='Elimina']) or $record/azioni_abilitabili/azione[@id='Elimina']/abilitazione = '1'">
			<!-- checkbox per cancellazione -->
			<input type='checkbox' name='watbl_input_del_chk[{@id}]' onclick='document.{/watabella/nome}.evento_CheckBoxElimina_onclick({@id})' />
		</xsl:if>
		<!-- checkbox di modifica: viene valorizzato al momento del -->
		<!-- submit se il record ha subito modifiche -->
		<input type='checkbox' name='watbl_input_mod_chk[{@id}]' style='visibility: hidden; position: absolute;'/>
	</td>

	<td style='width: 1%'>
 		<xsl:variable name="id_riga" select="@id"/>
		<xsl:for-each select="/watabella/watabella_azioni_record/azione">
			<!--non mostriamo le azioni di default, perche' in una tabella edit non--> 
			<!--hanno molto senso; se nell'applicazione di qualcuno ha senso avere--> 
			<!--le azioni di default si fara' un xsl su misura-->
			<xsl:if test="nome != 'Vedi' and nome != 'Modifica' and nome != 'Elimina'">
				<!-- verifichiamo se il bottone e' abilitato -->
				<xsl:variable name="id_azione" select="@id"/>
				<xsl:if test="not($record/azioni_abilitabili/azione[@id=$id_azione]) or $record/azioni_abilitabili/azione[@id=$id_azione]/abilitazione = '1'">
					<button type='button' onclick='document.{/watabella/nome}.azione_{/watabella/nome}_{nome}("{$id_riga}")'>
						<xsl:value-of select="etichetta"/>
					</button>
				</xsl:if>
			</xsl:if>
		</xsl:for-each>
	</td>
	
</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* gestione dei template dei controlli di input ********* -->
<!--   ******************************************************************** -->
<xsl:template name="input">
	<xsl:param name="cella"/>
	<xsl:param name="colonna"/>
	

	<xsl:choose>
		<xsl:when test="$colonna/input/tipo = 'areatesto'">
			<xsl:call-template name="input_areatesto"><xsl:with-param name="cella" select="$cella"/><xsl:with-param name="colonna" select="$colonna"/></xsl:call-template>
		</xsl:when>
		<xsl:when test="$colonna/input/tipo = 'data'">
			<xsl:call-template name="input_data"><xsl:with-param name="cella" select="$cella"/><xsl:with-param name="colonna" select="$colonna"/></xsl:call-template>
		</xsl:when>
		<xsl:when test="$colonna/input/tipo = 'dataora'">
			<xsl:call-template name="input_dataora"><xsl:with-param name="cella" select="$cella"/><xsl:with-param name="colonna" select="$colonna"/></xsl:call-template>
		</xsl:when>
		<xsl:when test="$colonna/input/tipo = 'ora'">
			<xsl:call-template name="input_ora"><xsl:with-param name="cella" select="$cella"/><xsl:with-param name="colonna" select="$colonna"/></xsl:call-template>
		</xsl:when>
		<xsl:when test="$colonna/input/tipo = 'intero'">
			<xsl:call-template name="input_intero"><xsl:with-param name="cella" select="$cella"/><xsl:with-param name="colonna" select="$colonna"/></xsl:call-template>
		</xsl:when>
		<xsl:when test="$colonna/input/tipo = 'logico'">
			<xsl:call-template name="input_logico"><xsl:with-param name="cella" select="$cella"/><xsl:with-param name="colonna" select="$colonna"/></xsl:call-template>
		</xsl:when>
		<xsl:when test="$colonna/input/tipo = 'selezione'">
			<xsl:call-template name="input_selezione"><xsl:with-param name="cella" select="$cella"/><xsl:with-param name="colonna" select="$colonna"/></xsl:call-template>
		</xsl:when>
		<xsl:when test="$colonna/input/tipo = 'valuta'">
			<xsl:call-template name="input_valuta"><xsl:with-param name="cella" select="$cella"/><xsl:with-param name="colonna" select="$colonna"/></xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="input_testo"><xsl:with-param name="cella" select="$cella"/><xsl:with-param name="colonna" select="$colonna"/></xsl:call-template>
		</xsl:otherwise>
	</xsl:choose>	

</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* controllo input di tipo areatesto ******************** -->
<!--   ******************************************************************** -->
<xsl:template name="input_areatesto">
	<xsl:param name="cella"/>
	<xsl:param name="colonna"/>
	
	<textarea name='{$colonna/nome}[{$cella/../@id}]'>
		<xsl:value-of select="$cella/valore" />
	</textarea>
</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* controllo input di tipo data ************************* -->
<!--   ******************************************************************** -->
<xsl:template name="input_data">
	<xsl:param name="cella"/>
	<xsl:param name="colonna"/>
	
	<xsl:variable name="my_val">
		<xsl:if test="string-length($cella/valore) &gt; 0">
			<xsl:value-of select="substring($cella/valore, 9, 2)"/>/<xsl:value-of select="substring($cella/valore, 6, 2)"/>/<xsl:value-of select="substring($cella/valore, 1, 4)"/>
		</xsl:if>
	</xsl:variable>

	<input type='text' name='{$colonna/nome}[{$cella/../@id}]' value='{$my_val}' size='10' maxlength='10' style='text-align: center'>
	</input>
</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* controllo input di tipo dataora ********************** -->
<!--   ******************************************************************** -->
<xsl:template name="input_dataora">
	<xsl:param name="cella"/>
	<xsl:param name="colonna"/>
	
	<xsl:variable name="my_val">
		<xsl:if test="string-length($cella/valore) &gt; 0">
			<xsl:value-of select="substring($cella/valore, 9, 2)"/>/<xsl:value-of select="substring($cella/valore, 6, 2)"/>/<xsl:value-of select="substring($cella/valore, 1, 4)"/>
				<xsl:text>&#x20;</xsl:text>
			<xsl:value-of select="substring($cella/valore, 12, 2)"/>:<xsl:value-of select="substring($cella/valore, 15, 2)"/>
		</xsl:if>
	</xsl:variable>

	<input type='text' name='{$colonna/nome}[{$cella/../@id}]' value='{$my_val}' size='16' maxlength='16' style='text-align: center'>
	</input>
</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* controllo input di tipo ora ************************** -->
<!--   ******************************************************************** -->
<xsl:template name="input_ora">
	<xsl:param name="cella"/>
	<xsl:param name="colonna"/>
	
	<xsl:variable name="my_val">
		<xsl:if test="string-length($cella/valore) &gt; 0">
			<xsl:value-of select="substring($cella/valore, 1, 2)"/>:<xsl:value-of select="substring($cella/valore, 4, 2)"/>
		</xsl:if>
	</xsl:variable>

	<input type='text' name='{$colonna/nome}[{$cella/../@id}]' value='{$my_val}' size='5' maxlength='5' style='text-align: center'>
	</input>
</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* controllo input di tipo logico *********************** -->
<!--   ******************************************************************** -->
<xsl:template name="input_logico">
	<xsl:param name="cella"/>
	<xsl:param name="colonna"/>
	
	<input type='checkbox' name='{$colonna/nome}[{$cella/../@id}]'>
		<xsl:if test="$cella/valore = '1'">
			<xsl:attribute name='checked'>yes</xsl:attribute>
		</xsl:if>
	</input>
</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* controllo input di tipo selezione ******************** -->
<!--   ******************************************************************** -->
<xsl:template name="input_selezione">
	<xsl:param name="cella"/>
	<xsl:param name="colonna"/>
	
	<select name='{$colonna/nome}[{$cella/../@id}]'>
		<xsl:for-each select="$colonna/input/opzioni/opzione">
			<option value="{@val}">
				<xsl:if test="$cella/valore = @val">
					<xsl:attribute name='selected'>yes</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="."/>
			</option>
		</xsl:for-each>
	</select>
		
</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* controllo input di tipo testo ************************ -->
<!--   ******************************************************************** -->
<xsl:template name="input_testo">
	<xsl:param name="cella"/>
	<xsl:param name="colonna"/>
	
	<input type='text' name='{$colonna/nome}[{$cella/../@id}]' value='{$cella/valore}'>
		<xsl:attribute name='maxlength'><xsl:value-of select="$colonna/input/lunghezza_max_campo" /></xsl:attribute>
		<xsl:if test="$colonna/input/lunghezza_max_campo &gt; 20">
			<xsl:attribute name='size'>20</xsl:attribute>
		</xsl:if>
		<xsl:if test="$colonna/input/lunghezza_max_campo &lt;= 20">
			<xsl:attribute name='size'><xsl:value-of select="$colonna/input/lunghezza_max_campo" /></xsl:attribute>
		</xsl:if>
	</input>
</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* controllo input di tipo intero *********************** -->
<!--   ******************************************************************** -->
<xsl:template name="input_intero">
	<xsl:param name="cella"/>
	<xsl:param name="colonna"/>

	<input type='text' name='{$colonna/nome}[{$cella/../@id}]' value='{$cella/valore}' style='text-align: right'>
		<xsl:attribute name='maxlength'><xsl:value-of select="$colonna/input/lunghezza_max_campo" /></xsl:attribute>
		<xsl:attribute name='size'><xsl:value-of select="$colonna/input/lunghezza_max_campo" /></xsl:attribute>
		<xsl:attribute name='onkeyup'>document.<xsl:value-of select="/watabella/nome" />.colonne["<xsl:value-of select="$colonna/nome" />"].intero_onkeyup("<xsl:value-of select="$cella/../@id" />")</xsl:attribute>
	</input>
</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* controllo input di tipo valuta *********************** -->
<!--   ******************************************************************** -->
<xsl:template name="input_valuta">
	<xsl:param name="cella"/>
	<xsl:param name="colonna"/>

	<xsl:variable name="my_val">
		<xsl:if test="string-length($cella/valore) &gt; 0">
			<xsl:value-of select="format-number($cella/valore,  '#.##0,00')"/>
		</xsl:if>
	</xsl:variable>

	<input type='text' name='{$colonna/nome}[{$cella/../@id}]' value='{$my_val}' style='text-align: right'>
		<xsl:attribute name='maxlength'><xsl:value-of select="$colonna/input/lunghezza_max_campo - 1" /></xsl:attribute>
		<xsl:attribute name='size'><xsl:value-of select="$colonna/input/lunghezza_max_campo + 2" /></xsl:attribute>
		<xsl:attribute name='onfocus'>document.<xsl:value-of select="/watabella/nome" />.colonne["<xsl:value-of select="$colonna/nome" />"].valuta_onfocus("<xsl:value-of select="$cella/../@id" />")</xsl:attribute>
		<xsl:attribute name='onkeyup'>document.<xsl:value-of select="/watabella/nome" />.colonne["<xsl:value-of select="$colonna/nome" />"].valuta_onkeyup("<xsl:value-of select="$cella/../@id" />")</xsl:attribute>
		<xsl:attribute name='onblur'>document.<xsl:value-of select="/watabella/nome" />.colonne["<xsl:value-of select="$colonna/nome" />"].valuta_onblur("<xsl:value-of select="$cella/../@id" />")</xsl:attribute>
	</input>
</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* fine gestione controlli di input ********************* -->
<!--   ******************************************************************** -->

<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!-- ********************************************************************** -->
<!--  template dei risultati in input di eventuale edit  -->
<!-- ********************************************************************** -->
<xsl:template match="watabella.input">

	<watabella.input>
		<watabella_righe>

			<!--ciclo delle righe eliminate-->
			<xsl:for-each select="post/item[@id = 'watbl_input_del_chk']/item" >
				<riga>
					<!-- determinazione identificativo (chiave primaria) della riga -->
					<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
					<watabella_elimina>1</watabella_elimina>
				</riga>
			</xsl:for-each>

			<!--ciclo delle righe modificate-->
			<xsl:for-each select="post/item[@id = 'watbl_input_mod_chk']/item" >
				<riga>
					<!-- determinazione identificativo (chiave primaria) della riga -->
					<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
					<xsl:variable name="id" select="@id" />
					<!--ciclo sulle colonne di input-->
					<xsl:for-each select="/watabella.input/watabella_intestazioni/intestazione[input[child::node()]]">
						<!--determinazione del nome della colonna-->
						<xsl:element name="{nome}">
							<xsl:variable name="nome" select="nome" />
							<!--determinazione del valore pervenuto in post per la riga/colonna-->
							<xsl:variable name="valori_input" select="/watabella.input/post/item[@id = $nome]/item[@id = $id]" />
							<xsl:choose>
								<xsl:when test="input/tipo = 'data'">
									<xsl:call-template name="risultato.input_data"><xsl:with-param name="valore_input" select="$valori_input"/></xsl:call-template>
								</xsl:when>
								<xsl:when test="input/tipo = 'dataora'">
									<xsl:call-template name="risultato.input_dataora"><xsl:with-param name="valore_input" select="$valori_input"/></xsl:call-template>
								</xsl:when>
								<xsl:when test="input/tipo = 'ora'">
									<xsl:call-template name="risultato.input_ora"><xsl:with-param name="valore_input" select="$valori_input"/></xsl:call-template>
								</xsl:when>
								<xsl:when test="input/tipo = 'logico'">
									<xsl:call-template name="risultato.input_logico"><xsl:with-param name="valore_input" select="$valori_input"/></xsl:call-template>
								</xsl:when>
								<xsl:when test="input/tipo = 'valuta'">
									<xsl:call-template name="risultato.input_valuta"><xsl:with-param name="valore_input" select="$valori_input"/></xsl:call-template>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="risultato.input_testo"><xsl:with-param name="valore_input" select="$valori_input"/></xsl:call-template>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:element>
					</xsl:for-each>
				</riga>
			</xsl:for-each>

		</watabella_righe>
	</watabella.input>


</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* risultato input di tipo data ************************* -->
<!--   ******************************************************************** -->
<xsl:template name="risultato.input_data">
	<xsl:param name="valore_input"/>

	<xsl:choose>
		<xsl:when test="$valore_input = ''"></xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="substring($valore_input, 7, 4)"/>-<xsl:value-of select="substring($valore_input, 4, 2)"/>-<xsl:value-of select="substring($valore_input, 1, 2)"/>
		</xsl:otherwise>
	</xsl:choose>

</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* risultato input di tipo dataora ********************** -->
<!--   ******************************************************************** -->
<xsl:template name="risultato.input_dataora">
	<xsl:param name="valore_input"/>

	<xsl:choose>
		<xsl:when test="$valore_input = ''"></xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="substring($valore_input, 7, 4)"/>-<xsl:value-of select="substring($valore_input, 4, 2)"/>-<xsl:value-of select="substring($valore_input, 1, 2)"/><xsl:text> </xsl:text><xsl:value-of select="substring($valore_input, 12, 2)"/>:<xsl:value-of select="substring($valore_input, 15, 2)"/><xsl:text>:00</xsl:text>
		</xsl:otherwise>
	</xsl:choose>

</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* risultato input di tipo ora ********************** -->
<!--   ******************************************************************** -->
<xsl:template name="risultato.input_ora">
	<xsl:param name="valore_input"/>

	<xsl:choose>
		<xsl:when test="$valore_input = ''"></xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="substring($valore_input, 1, 2)"/>:<xsl:value-of select="substring($valore_input, 4, 2)"/><xsl:text>:00</xsl:text>
		</xsl:otherwise>
	</xsl:choose>

</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* risultato input di tipo logico *********************** -->
<!--   ******************************************************************** -->
<xsl:template name="risultato.input_logico">
	<xsl:param name="valore_input"/>

	<xsl:choose>
		<xsl:when test="$valore_input = 'on'">1</xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose>

</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* risultato input di tipo testo ************************ -->
<!--   ******************************************************************** -->
<xsl:template name="risultato.input_testo">
	<xsl:param name="valore_input"/>
	<xsl:value-of select="$valore_input" />
</xsl:template>

<!--   ******************************************************************** -->
<!--   ************* risultato input di tipo valuta  ********************** -->
<!--   ******************************************************************** -->
<xsl:template name="risultato.input_valuta">
	<xsl:param name="valore_input"/>

	<xsl:choose>
		<xsl:when test="$valore_input = ''"></xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="replace-string">
				<xsl:with-param name="search" select="','"/>
				<xsl:with-param name="replace" select="'.'"/>
				<xsl:with-param name="string" >
					<xsl:call-template name="replace-string">
						<xsl:with-param name="search" select="'.'"/>
						<xsl:with-param name="replace" select="''"/>
						<xsl:with-param name="string" select="$valore_input"/>
					</xsl:call-template>
				</xsl:with-param>
			</xsl:call-template>
		</xsl:otherwise>
	</xsl:choose>

</xsl:template>



<!--   ******************************************************************** -->
<!--   ******************************************************************** -->
<!--   ******************************************************************** -->
</xsl:stylesheet>
