{module class="module_date_calend_month" year=$year month=$month list=$calend table="btab" now=$today}

<div class="pages_select">
{if $previous_day_link}<a href="{$previous_day_link}" class="select_page">Предыдущий день</a>{/if}
{if $next_day_link}<a href="{$next_day_link}" class="select_page">Следующий день</a>{/if}
</div>

{assign var="skip_author_name" value=1}
{foreach from=$posts item=p}
<!-- post $post_id -->
{if $p}
	{assign var="t" value=$p->topic()}
	{if $t}
		{assign var="f" value=$t->forum()}
		{if $p->is_hidden()}
			<div class="box">** Это <a href="{$p->url_in_container()}">сообщение</a> было скрыто координатором **</div>
		{else}
			{if $f->can_read()}
				{* if $f->is_public_access() *}
				{assign var="show_title" value=$t->titled_link()}
				{include file="xfile:forum/post.html" show_title=$t->titled_link() strip="8192" topic=$t}
			{else}
				<div class="box">** Сообщение с ограниченным доступом **</div>
			{/if}
		{/if}
	{else}
		{include file="xfile:forum/post.html" show_title="Потерянный топик" strip="8192"}
{hidden_log type='topic-lost' message="Lost topic for post "|cat:$p->url_for_igo()}
	{/if}
{/if}
{/foreach}

<div class="pages_select">
{if $previous_day_link}<a href="{$previous_day_link}" class="select_page">Предыдущий день</a>{/if}
{if $next_day_link}<a href="{$next_day_link}" class="select_page">Следующий день</a>{/if}
</div>

{js_ready}
if(top.me_can_move)
{
	$('ul.postfooter').each(
		function() {
			pid = $(this).parent().parent().prev().attr('name').replace(/p/, '')
			x = jQuery.cookie('selected_posts')
			selected = x ? x.split(',') : []
			checked = jQuery.inArray(pid, selected) < 0 ? '' : ' checked="checked"'
			$(this).prepend(
				'<li><label><input type="checkbox"'+checked+'onclick="return apfgp(this, '+pid+')" />&nbsp;отметить</label></li>'
			)
		}
	)
}
{/js_ready}
