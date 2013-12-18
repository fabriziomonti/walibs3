{include file="header.tpl" eltype="Defines applicative" class_name="" hasel=true contents=""}


{if $classes}
<div class="contents">
{if $tutorial}
<span class="maintutorial">Main Tutorial: {$tutorial}</span>
{/if}
<h2>Classes:</h2>
{section name=classes loop=$classes}
<dt>{$classes[classes].link}</dt>
	<dd>{$classes[classes].sdesc}</dd>
{/section}
</div><br /><br />
{/if}

<br /><br />
{include file="include.tpl"}
<br /><br />
{include file="global.tpl"}
<br /><br />
{include file="define.tpl"}
<br />
{include file="function.tpl"}

{include file="footer.tpl"}

