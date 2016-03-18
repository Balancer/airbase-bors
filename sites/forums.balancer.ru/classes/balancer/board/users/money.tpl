<table class="btab">
<caption>Пользователи с малым числом «солнышек»</caption>
<tr>
	<th>Пользователь</th>
	<th>Репутация</th>
	<th>Баланс</th>
</tr>
{foreach $low_money as $u}
<tr>
	<td>{$u->titled_link()}</td>
	<td>{$u->reputation_html()}</td>
	<td>{$u->money()}</td>
</tr>
{/foreach}
</table>

