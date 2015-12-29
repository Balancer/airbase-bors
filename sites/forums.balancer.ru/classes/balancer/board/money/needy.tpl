<table>
<tr>
	<td>
		<table class="btab">
		<caption>Временно забаненные</caption>
		<tr>
			<th>Пользователь</th>
			<th>Число штрафов</th>
			<th>Репутация</th>
		</tr>
		{foreach $banned as $u}
		<tr>
			<td>{$u->titled_link()}</td>
			<td>{$u->warnings()}</td>
			<td>{$u->reputation_html()}</td>
		</tr>
		{/foreach}
		</table>
	</td>
	<td>
		<table class="btab">
		<caption>Имеющие недостаточно средств для написания в произвольные темы</caption>
		<tr>
			<th>Пользователь</th>
			<th>Объём средств</th>
			<th>Репутация</th>
		</tr>
		{foreach $ban_money as $u}
		<tr>
			<td>{$u->titled_link()}</td>
			<td>{$u->money()}</td>
			<td>{$u->reputation_html()}</td>
		</tr>
		{/foreach}
		</table>

	</td>

	<td>
		<table class="btab">
		<caption>Имеющие мало средств, на грани блокировки в обычных темах</caption>
		<tr>
			<th>Пользователь</th>
			<th>Объём средств</th>
			<th>Репутация</th>
		</tr>
		{foreach $low_money as $u}
		<tr>
			<td>{$u->titled_link()}</td>
			<td>{$u->money()}</td>
			<td>{$u->reputation_html()}</td>
		</tr>
		{/foreach}
		</table>

	</td>
</tr>
</table>