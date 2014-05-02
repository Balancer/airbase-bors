{$this->pages_links_nul()}

<table class="btab w100p">
<caption>Ответы на Ваши сообщения</caption>
<tr><th>Время</th><th>тема</th><th>форум</th></tr>
{foreach from=$posts item="p"}
<tr{if not $p->has_readed_by_user($me)} class="b"{/if}><td>{$p->create_time()|smart_time}</td>
	<td><a href="{$p->url_for_igo()}">{airbase_fun::replace_2013($p->title())}</a>
	{if !$p->has_readed_by_user($me)} [<a href="{$p->topic()->url_ex('new')}">к непрочитанному</a>]{/if}
	<div class="transgray small">"{$p->snip()}</div>
	</td>
	<td>{$p->topic()->forum()->nav_named_url()}</td>
</tr>
{/foreach}
</table>

{$this->pages_links_nul()}

<dl class="box">
<dd>
<a href="http://www.wrk.ru/support/2014/05/t69496--otvety-na-vashi-soobscheniya.1289.html">Обсуждение страницы</a>
</dd>
</dl>
