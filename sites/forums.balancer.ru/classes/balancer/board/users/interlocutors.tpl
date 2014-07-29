{if $is_watcher}
<h2>Дополнительная информация</h2>
<small>Собеседники (число ответов):<br/>
{foreach from=$interlocutors item='i'}
{$i->titled_link()}&nbsp;({$i->answers()})<br/>
{/foreach}
</small>
<ul>
<li>IP при регистрации: {$user->registration_ip()} (GeoIP: {$user->reg_geo_ip()})</li>
<li>Последний визит на форум: {$user->last_visit_time()|full_time}</li>
<li>IP за месяц (количество сообщений, geoip):<br/>
<small>
{foreach from=$last_ips item="x"}
{$x.poster_ip}&nbsp;({$x.count}, {$x.poster_ip|geoip_place})<br/>
{/foreach}
</small></li>
</ul>
{/if}
