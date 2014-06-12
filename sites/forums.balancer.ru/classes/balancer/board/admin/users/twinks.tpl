{foreach $users as $utmx => $xx}
<table class="btab small w100p">
<thead>
<caption>{$xx.first_name}</caption>
<tr>
	<th width="15%">Пользователь</th>
	<th width="13%">Дата регистрации</th>
	<th width="13%">Последний визит</th>
	<th width="5%">Число сообщений</th>
	<th>IP при регистрации</th>
	<th>Штрафных баллов</th>
	<th>Админ бан</th>
	<th>Репутация</th>
</tr>
</thead>
<tbody>
	{foreach $xx.list as $u}
<tr{if $u->is_admin_banned()} class="s"{/if}>
	<td>{$u->titled_link()}</td>
	<td>{$u->create_time()|date:'d.m.Y H:i'}</td>
	<td>{$u->last_visit_time()|date:'d.m.Y H:i'}</td>
	<td>{$u->num_posts()}</td>
	<td>{$u->registration_ip()|geoip_flag} {$u->registration_ip()}</td>
	<td>{$u->warnings()}</td>
	<td>{if $u->is_admin_banned()}да{else}&nbsp;{/if}</td>
	<td>{$u->reputation()}</td>
</tr>
	{/foreach}
</tbody>
</table>
{/foreach}
