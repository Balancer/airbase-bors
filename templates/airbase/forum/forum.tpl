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
		<th>Ответов</th>
		<th>Создано</th>
		<th>Обновлено</th>
	</tr>
	{foreach from=$topics item=t}
		{assign var=f value=$t->forum()}
		{assign var=o value=$t->owner()}
		{if $all_new}
			{assign var="updated" value=true}
		{else}
			{if $t->get('was_updated') || ($me && method_exists($t, 'was_updated_for_user'))}
				{assign var="updated" value=$t->was_updated_for_user($me,true)}
			{/if}
		{/if}
	<tr class="tr_forum_{$f->id()}{if !$f->is_public()} bb-background-private{/if}">
		{if $with_images}
		<td style="padding: 4px; line-height: 0;">
			{if $img=$t->image()}
			<a href="{$t->url()}">{$img->thumbnail_96x96()->html_code()}</a>
			{/if}
		</td>
		{/if}
		<td id="vtt_{$t->id()}">
			<a href="{$t->url_ex('new')}"{if $updated} class="b"{/if}>{$t->title()}</a>
		{if $t->get('updated_count')}
			<small class="title_actions {if $t->get('updated_count')<5}transgray {else}b {if $t->get('updated_count')>=10}red {/if}{/if}small">&nbsp;(+{$t->updated_count()})</small>
		{/if}
		{if $t->total_pages() > 1}
			<small><span class="topic_pages_links">{$t->title_pages_links()}</span></small>
		{/if}
		{if $t->description()}
			<br />
			<small><i>{$t->description()}</i></small>
		{/if}
		{if $updated && $t->get('first_post')}
			<br />
			<small class="transgray xsmall nb">&nbsp;—&nbsp;{$t->first_post()->author_name()}: {$t->first_post()->snip()}</i></small>
		{/if}
		{if not $skip_forums}
			<div class="forum-link-small">{$f->titled_link()}</div>
		{/if}
	</td>
	<td class="small aligncenter">{$t->num_replies()}</td>
	<td class="small">
		<a href="http://www.balancer.ru/g/p{$t->first_post_id()}">{$t->create_time()|airbase_time}</a><br />
		<small>{$t->author_name()}</small>
	</td>
	<td class="small">
		<a href="http://www.balancer.ru/g/p{$t->last_post_id()}">{$t->last_post_create_time()|airbase_time}</a><br />
		<small>{$t->last_poster_name()}</small>
	</td>
</tr>
	{/foreach}
</table>
{else}
<dl class="box"><dd>Нет ни одной темы.</dd></dl>
{/if}

{$this->pages_links_nul()}
