<div class="pages_select">
{if $previous_day_link}<a href="{$previous_day_link}" class="select_page">Предыдущий день</a>{/if}
{if $next_day_link}<a href="{$next_day_link}" class="select_page">Следующий день</a>{/if}
</div>

<table class="btab">
<tr><th>Время создания</th><th>Тема</th><th>Форум</th><th>Автор</th><th>Всего ответов</th></tr>
{foreach from=$items item="x"}
<tr><td>{$x->create_time()|date:'H:i'}</td>
	<td>{$x->titled_link()}</td>
	<td>{$x->forum()|get:titled_link}</td>
	<td>{$x->author_name()}</td>
	<td>{$x->num_replies()}</td>
</tr>
{/foreach}
</table>

<div class="pages_select">
{if $previous_day_link}<a href="{$previous_day_link}" class="select_page">Предыдущий день</a>{/if}
{if $next_day_link}<a href="{$next_day_link}" class="select_page">Следующий день</a>{/if}
</div>
