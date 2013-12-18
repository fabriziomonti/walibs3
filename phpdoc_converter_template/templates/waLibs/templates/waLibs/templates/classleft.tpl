{foreach key=subpackage item=files from=$classleftindex}
	{if $subpackage != ""}{$subpackage}<br />{/if}
	{section name=files loop=$files}
    {if $subpackage != ""}&nbsp;&nbsp;{/if}
		{if $files[files].link != ''}<a href="{$files[files].link}">{/if}{$files[files].title}{if $files[files].link != ''}</a>{/if}<br />
	{/section}
{/foreach}
