<table class="btab" width="100%">
<tr><th colSpan="7">Последние 25 штрафов:</th></tr>
<tr>
	<th colspan="2">Дата</th>
	<th rowspan="2">Пользователь</th>
	<th rowspan="2">Сообщение</th>
	<th rowspan="2">Штраф, причина</th>
	<th rowspan="2">Комментарий</th>
	<th rowspan="2">Модератор</th>
</tr>
<tr>
	<th>выставления</th>
	<th>истечения</th>
</tr>
{foreach from=$warnings_last item="w"}
<tr class="small" style="color: {if $w->score()>0}red{else}green{/if};">
	<td>{$w->create_time()|date:'d.m.y H:i'}</td>
	<td>{$w->expire_time()|date:'d.m.y'}</td>
	<td>{$w->user()|get:"titled_link"}</td>
	<td>{$w->target()|get:"titled_link"}</td>
	<td>{$w->score()}, {$w->type_id()|bors_list_item_name:"airbase_user_warning_type"}</td>
	<td>{$w->source()|lcmlbb}</td>
	<td>{$w->moderator()|get:"titled_link"}</td>
</tr>
{/foreach}
</table>

<div class="box">
<a href="http://forums.balancer.ru/users/warnings/">Полный список штрафов</a>
</div>

<table class="btab" width="100%">
<tr><th colSpan="5">50 главных нарушителей месяца:</th></tr>
<tr>
	<th>№</th>
	<th>Пользователь</th>
	<th>Штрафов в среднем в месяц</th>
	<th>Штрафов всего</th>
	<th>Сообщений всего</th>
</tr>
{foreach from=$top_warn_users item="u" name="loop"}
<tr>
	<td>{$smarty.foreach.loop.iteration}</td>
	<td>{$u->titled_link()}</td>
	<td>{$u->warnings_rate(31)|sprintf:"%.1f"}</td>
	<td>{$u->warnings_total()}</td>
	<td>{$u->num_posts()}</td>
</tr>
{/foreach}
</table>
<p><i>Примечание: учитываются только пользователи, активно просуществовавшие более недели</i></p>


{*
<table class="btab" width="100%">
<tr><th colSpan="4">50 самых часто штрафуемых за месяц:</th></tr>
<tr>
	<th>№</th>
	<th>Пользователь</th>
	<th>Число штрафов на одно сообщение</th>
	<th>Штрафов всего</th>
	<th>Сообщений всего</th>
</tr>
{foreach from=$top_warn_relative_users item="x" key=uid name="loop"}
{assign var="u" value=$x.u}
<tr>
	<td>{$smarty.foreach.loop.iteration}</td>
	<td>{$u->titled_link()}</td>
	<td>{$x.w}</td>
	<td>{$u->warnings_total()}</td>
	<td>{$u->num_posts()}</td>
</tr>
{/foreach}
</table>
*}
