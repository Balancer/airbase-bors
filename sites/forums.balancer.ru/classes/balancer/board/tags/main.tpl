{if $tags_top}
<table class="btab w100p">
<caption>Вложенные теги</caption>
<tr>
	<th>Тэг</th>
	<th>Тем</th>
	<th>Сообщений</th>
	<th>Последнее сообщение</th>
</tr>
{foreach from=$tags_top item="x"}
<tr>
	<td>{$x->titled_link()}</td>
	<td>{$x->target_containers_count()}</td>
	<td>?</td>
	<td>?</td>
</tr>
{/foreach}
<tr>
	<td colspan="4"><b><big><a href="http://forums.balancer.ru/tags/*/">Все теги форумов</a></big></b></td>
</tr>
</table>
{/if}

{if $items}

{include file="xfile:airbase/forum/forum.html" topics=$items}

{else}
<div class="box red_box">Сообщения с этим тегом не найдено</div>
{/if}

<dl class="box">
<dt>Смотри также</dt>
<dd>
<ul>
<li><a href="http://www.balancer.ru/support/2011/03/t69616--tegi-klyuchevye-slova-metki-tem-forumov.1943.html">Обсуждение этой страницы на форуме</a></li>
</ul>
</dd>
</dl>
