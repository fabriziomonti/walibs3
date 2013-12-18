{if $sdesc != ''}<div><p style="margin-top: -4px"><b>{$sdesc|default:''}</b></p></div>{/if}
{if $desc != ''}<div>{$desc|default:''}</div>{/if}
{if count($tags) > 0}
	<div class="tags">
		<table border="0" cellspacing="0" cellpadding="0">
			{section name=tag loop=$tags}
				<tr>
					<td><b>
							{if $tags[tag].keyword == 'author'}{/if}
							{if $tags[tag].keyword == 'version'}{/if}
							{if $tags[tag].keyword == 'copyright'}{/if}
							{if $tags[tag].keyword == 'license'}{/if}
							{if $tags[tag].keyword == 'return'}ritorna:</b>&nbsp;&nbsp;</td><td>{$tags[tag].data}{/if}
							{if $tags[tag].keyword == 'access'}accesso:</b>&nbsp;&nbsp;</td><td>{$tags[tag].data}{/if}
							</td>
				</tr>
			{/section}
		</table>
	</div>
{/if}