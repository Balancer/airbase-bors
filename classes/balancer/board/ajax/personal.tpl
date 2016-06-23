<dl class="box w200">
	<dt>{ec('Персональное')}</dt>
	<dd>

{if $owner}
		<p>
			<b>Здравствуйте, {$owner->titled_link()}!</b> [&nbsp;<a href="/actions/do-logout/">Выход</a>&nbsp;]
		</p>

		<div class="alert alert-info text-center" style="padding: 0">
			<div style="font-size: 1.1rem">
				<a href="http://forums.balancer.ru/money/" style="color: {if $owner->money()>0}green{elseif $owner->money()<0}red{/if}">☼{$owner->money()}</a>
			</div>
			<div style="font-size: 0,5rem">Ваш баланс</div>
		</div>

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

		<div class="container">
			<div class="row">
				<a href="http://www.wrk.ru/forums/register.php" class="btn btn-success" style="margin-bottom: 10px;">Зарегистрироваться</a>
			</div>
		</div>
		<hr/>
		<form action="/do-login/" method="post">
			<div class="form-group input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
				<input type="text" name="req_username" class="form-control" placeholder="Логин или Email" />
			</div>
			<div class="form-group input-group">
				<span class="input-group-addon"><i class="fa fa-key"  ></i></span>
				<input type="password" name="req_password" class="form-control" placeholder="Пароль" />
			</div>
			<button type="submit" class="btn btn-primary">Войти</button>
		</form>
{/if}
	</dd>
</dl>

{if $object && $object->get('type') == 'topic'}
	{$tid=$object->id()}
<dl class="box w200">
	<dt>Тема</dt>
	<dd>
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
		</ul>
		<form action="http://www.wrk.ru/tools/search/result/">
			<div class="form-group input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
				<input name="q" size="20" type="text" class="form-control" placeholder="Поиск в этой теме" /><br/>
			</div>

			<button type="submit" class="btn btn-default">Найти</button>
			<input type="hidden" name="w" value="1" />
			<input type="hidden" name="t" value="{$tid}" />
			<input type="hidden" name="w" value="a" />
		</form>
	</dd>
</dl>
{/if}

<dl class="box w200">
	<dt>Форумы</dt>
	<dd>
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
	</dd>
</dl>

<dl class="box w200">
	<dt>RPG (самоуправление)</dt>
	<dd>
		<ul>
			<li><a href="http://www.wrk.ru/g/p3563189" class="red">Обсуждение</a></li>
			<li><a href="http://forums.balancer.ru/rpg/requests/" class="red">Очередь запросов</a></li>
		</ul>
	</dd>
</dl>
