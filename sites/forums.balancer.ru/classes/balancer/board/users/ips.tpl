<ul>
<li>IP при регистрации: {$user->registration_ip()} (GeoIP: {$user->reg_geo_ip()})</li>
<li>Последний визит на форум: {$user->last_visit_time()|full_time}</li>
</ul>

<table class="btab">
<tr>
	<th>IP</th>
	<th>Количество сообщений</th>
	<th>Первое сообщение</th>
	<th>Последниее сообщение</th>
</tr>
{foreach $last_ips as $x}
<tr>
	<td>{$x->poster_ip()|geoip_flag}&nbsp;{$x->poster_ip()}, {$x->poster_ip()|geoip_place}</td>
	<td>{$x->count()}</td>
	<td>{$x->first_posted()|date:"d.m.Y H:i:s"}</td>
	<td>{$x->last_posted()|date:"d.m.Y H:i:s"}</td>
</tr>
{/foreach}
</table>
