<ul>
<li><a href="requests/">Очередь запросов</a></li>
</ul>

<table cellSpacing="0" class="btab">
<tr>
	<th>№</th>
	<th>Пользователь</th>
	<th>RPG-уровень</th>
	<th>Репутация</th>
	<th>Вес голоса</th>
</tr>
{foreach $top as $u}
<tr>
	<td>{$u@iteration}</td>
	<td>{$u->titled_link()}</td>
	<td>{$u->rpg_level()}</td>
	<td>
		<a href="/user/{$u->id()}/reputation/">{$u->reputation_html()}</a>&nbsp;({$u->reputation()|sprintf:"%.1f"})
	</td>
	<td>{pow(3,$u->rpg_level())}</td>
</tr>
{/foreach}
</table>
