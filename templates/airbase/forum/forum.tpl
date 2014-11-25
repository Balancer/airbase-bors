{if $subforums}
	{assign var=subforums value=$this->direct_subforums()}
	{include file="xfile:forums-list.html"}
{/if}

{$this->pages_links_nul()}

{if topics}
<table class="btab w100p shadow8" cellSpacing="0">
	<tr>
	{if $with_images}
		<th>&nbsp;</th>
	{/if}
		<th>Тема</th>
	</tr>
	{foreach from=$topics item=t}
		{assign var=f value=$t->forum()}
		{assign var=o value=$t->owner()}
		{$updated_count=$t->get('updated_count')}
		{if $all_new}
			{assign var="updated" value=true}
		{else}
			{if $t->get('was_updated') || ($me && method_exists($t, 'was_updated_for_user'))}
				{assign var="updated" value=$t->was_updated_for_user($me,true)}
			{/if}
		{/if}
	<tr class="tr_forum_{$f->id()}{if !$f->is_public()} bb-background-private{/if}">
		{if $with_images}
		<td style="padding: 4px; line-height: 0;" width="96">
			{if $img=$t->image()}
			<a href="{$t->url()}">{$img->thumbnail_96x96()->html_code()}</a>
			{/if}
		</td>
		{/if}
		<td id="vtt_{$t->id()}">
			<div class="title_actions"><a href="{$t->url_ex('new')}"{if $updated} class="b"{/if}{if $updated_count > 0  and $updated_count < 10} style="opacity: {if $updated_count >= 5}0.8{else}0.5{/if}"{/if}>{$t->title()}</a>
		{if $t->total_pages() > 1}
				<small><span class="topic_pages_links">{$t->title_pages_links()}</span></small>
		{/if}
			</div>
		{if $t->description()}
			<small><i>{$t->description()}</i></small>
		{/if}
		{if not $skip_forums}
			<div class="forum-link-small forum_actions" title="Форум: {$f->title()|escape}">{$f->titled_link()}</div>
		{/if}
		<div class="forum-topic-snippet-replies" title="Сообщений в теме и число новых ответов">
			{$t->num_replies()}
		{if $updated_count}
			<span class="{if $updated_count >=5}b {if $updated_count >=10}red {else}black {/if}{/if}">&nbsp;(+{$updated_count})</span>
		{/if}
		</div>
		{if $t->num_replies() > 0}
			{if $updated_count > 0 && $t->get('first_post')}
			<div class="forum-topic-snippet-post"><span class="time">{$t->first_post()->create_time()|airbase_time}, {$t->first_post()->author_name()}:</span> {$t->first_post()->snip()}</i></div>
			{/if}
			{if $updated_count == 0 or $updated_count > 1}
			<div class="forum-topic-snippet-post"><span class="time">{$t->last_post_create_time()|airbase_time}
			{if $t->get('last_post')}, {$t->last_post()->author_name()}:</span> {$t->last_post()->snip()}{/if}</i></div>
			{/if}
		{/if}
	</td>
</tr>
	{/foreach}
</table>
{else}
<dl class="box"><dd>Нет ни одной темы.</dd></dl>
{/if}

{$this->pages_links_nul()}
