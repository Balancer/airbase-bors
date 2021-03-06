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
{/if}

{literal}
<script>

if(top.class_name == 'forum_topic' || top.class_name == 'balancer_board_topic' || top.class_name == 'balancer_board_topics_view' )
{
	tid = top.object_id
	page = top.page
}
else
{
	page = 1
	tid = 0
}

if(tid > 0)
{
	document.write('<b>Тема:</b>')
	document.write('<ul>')
	document.write('<li><a href="http://www.balancer.ru/2000/01/t'+tid+'--.pdf">PDF-версия темы</a></li>')
	document.write('<li><a href="http://www.balancer.ru/2000/01/01/printable-'+tid+'--.html">Версия для печати</a></li>')
	document.write('<li><a href="http://www.balancer.ru/1973/10/tpc'+tid+','+page+'--.html">Печать текущей страницы</a></li>')
	document.write('<li><a href="http://www.balancer.ru/1973/10/t'+tid+'/blog">Блог</a></li>')
	document.write('<li><a href="http://forums.balancer.ru/topics/'+tid+'/best/" class="red">Лучшее в теме</a></li>')
	document.write('<li><a href="http://www.balancer.ru/1973/10/t'+tid+'/images">Все картинки</a></li>')
	document.write('<li><a href="http://www.balancer.ru/1973/10/t'+tid+'/video">Все видео</a></li>')
	document.write('<li><a href="http://www.balancer.ru/2000/01/t'+tid+'/attaches/">Все аттачи</a></li>')
	document.write('<li><a href="http://www.balancer.ru/forum/tools/topic/'+tid+'/">Инструменты темы</a></li>')
	document.write('<li><a href="http://www.balancer.ru/forum/tools/topic/'+tid+'/reload/">Сбросить кеш темы</a></li>')
	document.write('<li><form action="http://www.balancer.ru/tools/search/result/"><input name="q" size="20" class="text" type="text" /><input type="hidden" name="w" value="1" /><br/><input type="submit" value="Найти в теме" /><input type="hidden" name="t" value="'+tid+'" /><input type="hidden" name="w" value="a" /></form></li>')
	document.write('</ul>')
}
</script>
{/literal}

<b>Форумы:</b>
<ul>
<li><a href="http://forums.airbase.ru/guidelines/">Правила</a></li>
<li><a href="http://forums.balancer.ru/help/">Помощь</a></li>
<li><a href="http://www.balancer.ru/forum/punbb/search.php?action=show_24h">За сутки</a></li>
<li><a href="http://forums.balancer.ru/news/" class="red">Новости</a></li>
<li><a href="http://www.balancer.ru/chat/">Чат</a> <small><a href="http://www.balancer.ru/support/2011/03/t19123--na-aviabaze-zapuschen-novyj-chat.5520.html">[?]</a></small></li>
<li><a href="http://forums.balancer.ru/tags/">Теги</a></li>
<li><a href="http://www.balancer.ru/tools/search/">Поиск</a></li>
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
<li><a href="http://www.balancer.ru/g/p3563189" class="red">Обсуждение</a></li>
<li><a href="http://forums.balancer.ru/rpg/requests/" class="red">Очередь запросов</a></li>
</ul>
