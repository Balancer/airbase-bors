<dl class="box w200">
	<dt>{ec('Персональное')}</dt>
	<dd>

{if $owner}
<p>
	<b>Здравствуйте, {$owner->titled_link()}!</b> [&nbsp;<a href="/actions/do-logout/">Выход</a>&nbsp;]
</p>

<div class="alert alert-info text-center" style="padding: 0"><div style="font-size: 1.1rem"><a href="http://forums.balancer.ru/money/" style="color: {if $owner->money()>0}green{elseif $owner->money()<0}red{/if}">☼{$owner->money()}</a></div><div style="font-size: 0,5rem">Ваш баланс</div></div>

<b>Ваше:</b>
<ul>
<li><a href="http://forums.balancer.ru/personal/updated/">Обновившиеся темы</a></li>
<li><a href="http://forums.balancer.ru/personal/unvisited/">Непосещённые темы</a></li>
<li id="pers_answ_cont"><a href="http://forums.balancer.ru/personal/answers/">Ответы Вам<span id="pers_answ_cnt"></span></a></li>
<li><a href="http://www.balancer.ru/forum/punbb/search.php?action=show_new">Новое</a></li>
<li><a href="http://forums.balancer.ru/personal/clients/">Профили браузера</a></li>
<li><a href="http://www.balancer.ru/user/{$owner->id()}/blog/">Блог</a></li>
<li><a href="http://www.balancer.ru/users/favorites/">Избранное</a></li>
<li><a href="http://www.balancer.ru/user/{$owner->id()}/use-topics.html">Темы с участием</a></li>
<li><a href="http://www.balancer.ru/user/{$owner->id()}/posts/">Сообщения</a></li>
<li><a href="http://www.balancer.ru/user/{$owner->id()}/reputation/">Репутация</a></li>
<li><a href="http://www.balancer.ru/users/{$owner->id()}/votes/">Оценки</a></li>
</ul>
{else}
<p><b>Здравствуйте, гость!</b></p>
Гостевой функционал сайта ограничен. Для
полноценной работы
<a href="http://www.balancer.ru/forum/punbb/register.php">зарегистрируйтесь</a>,
пожалуйста.

<a href="http://www.wrk.ru/forums/register.php" style="display: block; font-size: 10pt; padding: 2px 4px; text-align: center; box-shadow: 2px 2px 4px rgba(0,0,0,0.5); color: white; background: rgb(28, 184, 65); margin: 0 4px 4px 0">Зарегистрироваться</a>
<form action="/do-login/" method="post"><table>
<tr><td>Login:</td><td><input name="req_username"></td></tr>
<tr><td>Password:</td><td><input name="req_password" type="password"></td></tr>
<tr><td></td><td><input type="submit" value="Login"></td></tr>
</table></form>

{/if}

{if $object && $object->get('type') == 'topic'}
	{$tid=$object->id()}
	<b>Тема:</b>
	<ul>
	<li><a href="http://www.wrk.ru/2000/01/t{$tid}--.pdf">PDF-версия темы</a></li>
	<li><a href="http://www.wrk.ru/2000/01/01/printable-{$tid}--.html">Версия для печати</a></li>
	<li><a href="http://www.wrk.ru/1973/10/tpc{$tid},{$page}--.html">Печать текущей страницы</a></li>
	<li><a href="http://www.wrk.ru/1973/10/t{$tid}/blog">Блог</a></li>
	<li><a href="http://forums.balancer.ru/topics/{$tid}/best/" class="red">Лучшее в теме</a></li>
	<li><a href="http://www.wrk.ru/1973/10/t{$tid}/images">Все картинки</a></li>
	<li><a href="http://www.wrk.ru/1973/10/t{$tid}/video">Все видео</a></li>
	<li><a href="http://www.wrk.ru/2000/01/t{$tid}/attaches/">Все аттачи</a></li>
	<li><a href="http://www.balancer.ru/forum/tools/topic/{$tid}/">Инструменты темы</a></li>
	<li><a href="http://www.balancer.ru/forum/tools/topic/{$tid}/reload/">Сбросить кеш темы</a></li>
	<li><form action="http://www.wrk.ru/tools/search/result/"><input name="q" size="20" class="text" type="text" /><input type="hidden" name="w" value="1" /><br/><input type="submit" value="Найти в теме" /><input type="hidden" name="t" value="{$tid}" /><input type="hidden" name="w" value="a" /></form></li>
	</ul>
{/if}

<b>Форумы:</b>
<ul>
<li><a href="http://forums.airbase.ru/guidelines/">Правила</a></li>
<li><a href="http://forums.balancer.ru/help/">Помощь</a></li>
<li><a href="http://www.wrk.ru/forums/search.php?action=show_24h">За сутки</a></li>
<li><a href="http://forums.balancer.ru/news/" class="red">Новости</a></li>
<li><a href="http://www.balancer.ru/chat/">Чат</a> <small><a href="http://www.wrk.ru/support/2011/03/t19123--na-aviabaze-zapuschen-novyj-chat.5520.html">[?]</a></small></li>
<li><a href="http://forums.balancer.ru/tags/">Теги</a></li>
<li><a href="http://www.wrk.ru/tools/search/">Поиск</a></li>
<li><a href="http://www.balancer.ru/users/toprep/">Репутации</a></li>
<li><a href="http://www.balancer.ru/tools/votes/">Оценки</a></li>
<li><a href="http://www.balancer.ru/users/warnings/">Штрафы</a></li>
<li><a href="http://forums.balancer.ru/tools/">Инструменты</a></li>
{if $owner && $owner->is_coordinator()}
<li><a href="http://forums.balancer.ru/admin/">Админка</a></li>
{/if}
</ul>

<b>RPG:</b>
<ul>
<li><a href="http://www.wrk.ru/g/p3563189" class="red">Обсуждение</a></li>
<li><a href="http://forums.balancer.ru/rpg/requests/" class="red">Очередь запросов</a></li>
</ul>

	</dd>
</dl>
