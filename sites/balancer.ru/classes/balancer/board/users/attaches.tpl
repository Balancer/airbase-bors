{$this->pages_links_nul()}

<table class="btab">
{foreach $items as $attach}
	{if $atach@iteration % 2 == 1}
<tr>
	{/if}
	<td>
	{$attach->thumbnail_link('300x300')}<br/>
		<a href="{$attach->url()}">{$attach->title()}</a><br/>
		[{$attach->size()|smart_size}, {$attach->downloads()} {$attach->downloads()|sklon:'загрузка':'загрузки':'загрузок'}]<br/>
	{if $attach->post()}
		Исходное сообщение: <a href="{$attach->post()->url_in_container()}">{object_property($attach->post()->topic(), 'title', 'Потерянный топик')}</a>
	{/if}<br/>
	</td>
	{if $attach@iteration % 2 == 0}
</tr>
	{/if}
{/foreach}
</table>

<div class="clear">&nbsp;</div>

{$this->pages_links_nul()}
