{include file="header.tpl" eltype="File" class_name=$name hasel=true contents=$pagecontents}

{if $classes}
<div class="contents">
{if $tutorial}
<span class="maintutorial">Main Tutorial: {$tutorial}</span>
{/if}
<h2>Classi:</h2>
{section name=classes loop=$classes}
<dt>{$classes[classes].link}</dt>
	<dd>{$classes[classes].sdesc}</dd>
{/section}
</div><br /><br />
{/if}

<h3>Contenuto del file:</h3>
{include file="docblock.tpl" type="page"}
{include file="include.tpl"}
{include file="global.tpl"}
{include file="define.tpl"}
{include file="function.tpl"}
{include file="footer.tpl"}

