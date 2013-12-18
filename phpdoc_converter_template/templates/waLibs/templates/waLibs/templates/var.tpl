{section name=vars loop=$vars}
	{if $vars[vars].static}
		{if $show == 'summary'}
			static var {$vars[vars].var_name}, {$vars[vars].sdesc}<br>
		{else}
			<a name="{$vars[vars].var_dest}"></a>
			<p></p>
			<h4>static {$vars[vars].var_name} = <span class="value">{$vars[vars].var_default|replace:"\n":"<br>\n"|replace:" ":"&nbsp;"|replace:"\t":"&nbsp;&nbsp;&nbsp;"}</span></h4>
			<p>[line {if $vars[vars].slink}{$vars[vars].slink}{else}{$vars[vars].line_number}{/if}]</p>
			{include file="docblock.tpl" sdesc=$vars[vars].sdesc desc=$vars[vars].desc tags=$vars[vars].tags}

			<br />
			<div class="tags">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><b>Tipo:</b>&nbsp;&nbsp;</td>
						<td>{$vars[vars].var_type}</td>
					</tr>
					{if $vars[vars].var_overrides != ""}
						<tr>
							<td><b>Overrides:</b>&nbsp;&nbsp;</td>
							<td>{$vars[vars].var_overrides}</td>
						</tr>
					{/if}
				</table>
			</div><br /><br />
			<div class="top">[ <a href="#top">Top</a> ]</div><br />
		{/if}
	{/if}
{/section}
{section name=vars loop=$vars}
	{if !$vars[vars].static}
		{if $show == 'summary'}
			var {$vars[vars].var_name}, {$vars[vars].sdesc}<br>
		{else}
			<p></p>
			<a name="{$vars[vars].var_dest}"></a>
				<table width="100%" border="0" cellspacing="0" cellpadding="1"><tr><td class="code_border">
					<table width="100%" border="0" cellspacing="0" cellpadding="2">
						<tr>
							<td class="code">
								<code>
									{$vars[vars].var_name} = <span class="value">{$vars[vars].var_default|replace:"\n":"<br>\n"|replace:" ":"&nbsp;"|replace:"\t":"&nbsp;&nbsp;&nbsp;"}</span>										
								</code>
							</td>
						</tr>
					</table>
			</td></tr></table><br />

			<!--<p>[line {if $vars[vars].slink}{$vars[vars].slink}{else}{$vars[vars].line_number}{/if}]</p>-->
			{include file="docblock.tpl" sdesc=$vars[vars].sdesc desc=$vars[vars].desc tags=$vars[vars].tags}
			<div class="tags">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><b>Tipo:</b>&nbsp;&nbsp;</td>
						<td>{$vars[vars].var_type}</td>
					</tr>
					{if $vars[vars].var_overrides != ""}
						<tr>
							<td><b>Overrides:</b>&nbsp;&nbsp;</td>
							<td>{$vars[vars].var_overrides}</td>
						</tr>
					{/if}
				</table>
			</div><br />
			<div class="top" style="margin-left: 4px;">[ <a href="#top">Top</a> ]</div>
		{/if}
	{/if}
{/section}
