<dl class="forumposting">
<dd>
<div class="avatar_block">{include file="xfile:forum/post-avatar.html"}</div>
<h2>Информация</h2>
{if $user->is_deleted() && (!$me || !$me->is_admin())}
{$user->delete_notice()}
<ul>
{if $me && $me->is_coordinator()}
<li><a href="http://www.balancer.ru/users/{$this->id()}/use-topics.html">Все темы с участием пользователя</a></li>
<li><a href="http://www.balancer.ru/forum/punbb/profile.php?id={$this->id()}">Профиль на старом форуме</a></li>
{/if}
</ul>
<div class="clear">&nbsp;</div>
{else}
<ul>
{if $ban && is_object($ban)}
	<li class="red">Пользователь забанен перманентно по причине: {$ban->message()}</li>
{/if}
	<li>RPG-уровень: <span class="b big red">{$user->rpg_level()}</span> (вес голоса: <b>{pow(3,$user->rpg_level())})</b></li>
	<li>Зарегистрирован: {$user->create_time()|full_time}</li>
	<li>Последнее посещение: {$user->last_visit_time()|full_time}</li>
{if $user->username()}
	<li>Имя пользователя: {$user->username()}</li>
{/if}
{if $user->user_nick()}
	<li>Ник: {$user->user_nick()}</li>
{/if}
{if $user->realname()}
	<li>Настоящее имя: {$user->realname()}</li>
{/if}
	<li>Всего сообщений: {$user->num_posts()}</li>
	<li>Последнее сообщение: <a href="http://www.balancer.ru/user/{$user->id()}/posts/last/">{$user->last_post_time()|full_time}</a></li>
	<li><a href="http://www.balancer.ru/users/{$this->id()}/posts/chart/">График форумной активности</a></li>
{if $user->messages_daily_limit() >= 0}
	<li>Ограничение числа сообщений в день на один форум: <span class="red">{$user->messages_daily_limit()}</span></li>
	<li>Баланс: <span style="color: {if $user->money()>0}green{elseif $user->money()<0}red{/if}">☼{$user->money()}</span> [<a href="http://forums.balancer.ru/money/?target_user_id={$user->id()}">перевести пользователю ☼☼</a>]</li>
{/if}
{* <li>Карма: <b>{$user->karma()|sprintf:'%.2f'}</b></li> *}
{* <li>Суточный прирост кармы: <b style="color: {if $user->karma_rate() > 0}green{else}red{/if}">{$user->karma_rate()|sprintf:'%.2f'}</b></li> *}
{if $me}
{if 0 && $votes_from}
	<li>Оценки от этого пользователя Вам: {$votes_from}</li>
{/if}
{if $votes_to}
	<li>Оценки сообщений этого пользователя Вами: {$votes_to}</li>
{/if}
{if $me_id && $this->id() != $me_id }
	{if $rel_to && !$rel_to->get('is_empty') && $rel_to->ignore()}
	<li><a href="http://forums.balancer.ru/personal/?act=undelete&uid={$this->id()}">Перестать игнорировать пользователя</a></li>
	{else}
	<li><a href="http://forums.balancer.ru/personal/?act=delete&uid={$this->id()}">Игнорировать сообщения пользователя</a></li>
	{/if}
{/if}
{/if}
</ul>


<h2>Поиск в собщениях пользователя</h2>
{form class="this" method="get" action="http://www.balancer.ru/tools/search/result/" uri="NULL" ref="NULL" class_name='NULL' form_class_name='NULL' no_auto_checkboxes=true go='NULL' label="-"}
{input name="q" value="" label="Запрос" css_class="w100p"}
{hidden name="u" value="{$user->login()|htmlspecialchars}"}
{hidden name="w" value="a"}
{submit label="Искать"}
{/form}

<h2>Отношения с пользователями</h2>
<table class="nul w100p small"><tr><td width="50%">
	<table class="btab w100p">
	<caption>Лучше всех к его сообщениям относятся</caption>
	<tr>
		<th colspan="2">За 10 лет его активности</th>
		<th colspan="2">За квартал его активности</th>
	</tr>
	<tr>
		<th>Пользователь</th>
		<th>Отношение</th>
		<th>Пользователь</th>
		<th>Отношение</th>
	</tr>
	{foreach $friends_from as $u}
	<tr>
		<td>{object_property($u->from_user(), 'titled_link')}</td>
		<td class="{if $u->score()>0}green{/if}">{$u->score()}</td>
{$u2=$friends_from_quartal[$u@index]}
		<td>{if $u2}{object_property($u2->from_user(), 'titled_link')}{/if}</td>
		<td class="{if $u2 && $u2->score()>0}green{/if}">{if $u2}{$u2->score()}{/if}</td>
	</tr>
	{/foreach}
	</table>
</td><td>
	<table class="btab w100p">
	<caption>Лучше он относится к сообщениям</caption>
	<tr>
		<th colspan="2">За 10 лет его активности</th>
		<th colspan="2">За квартал его активности</th>
	</tr>
	<tr>
		<th>Пользователь</th>
		<th>Отношение</th>
		<th>Пользователь</th>
		<th>Отношение</th>
	</tr>
	{foreach $friends_to as $u}
	<tr>
		<td>{object_property($u->to_user(), 'titled_link')}</td>
		<td class="{if $u->score()>0}green{/if}">{$u->score()}</td>
{$u2=$friends_to_quartal[$u@index]}
		<td>{if $u2}{object_property($u2->to_user(), 'titled_link')}{/if}</td>
		<td class="{if $u2 && $u2->score()>0}green{/if}">{if $u2}{$u2->score()}{/if}</td>
	</tr>
	{/foreach}
	</table>
</td></table>

<table class="nul w100p small"><tr><td width="50%">
	<table class="btab w100p">
	<caption>Хуже всех к его сообщениям относятся</caption>
	<tr>
		<th colspan="2">За 10 лет его активности</th>
		<th colspan="2">За квартал его активности</th>
	</tr>
	<tr>
		<th>Пользователь</th>
		<th>Отношение</th>
		<th>Пользователь</th>
		<th>Отношение</th>
	</tr>
	{foreach $enemies_from as $u}
	<tr>
		<td>{object_property($u->from_user(), 'titled_link', $u->from_user_id())}</td>
		<td class="{if $u->score()<0}red{/if}">{$u->score()}</td>
{$u2=$enemies_from_quartal[$u@index]}
		<td>{if $u2}{object_property($u2->from_user(), 'titled_link', $u2->from_user_id())}{/if}</td>
		<td class="{if $u2 && $u2->score()<0}red{/if}">{if $u2}{$u2->score()}{/if}</td>
	</tr>
	{/foreach}
	</table>
</td><td>
	<table class="btab w100p">
	<caption>Хуже он относится к сообщениям</caption>
	<tr>
		<th colspan="2">За 10 лет его активности</th>
		<th colspan="2">За квартал его активности</th>
	</tr>
	<tr>
		<th>Пользователь</th>
		<th>Отношение</th>
		<th>Пользователь</th>
		<th>Отношение</th>
	</tr>
	{foreach $enemies_to as $u}
	<tr>
		<td>{object_property($u->to_user(), 'titled_link')}</td>
		<td class="{if $u->score()<0}red{/if}">{$u->score()}</td>
{$u2=$enemies_to_quartal[$u@index]}
		<td>{if $u2}{object_property($u2->to_user(), 'titled_link')}{/if}</td>
		<td class="{if $u2 && $u2->score()<0}red{/if}">{if $u2}{$u2->score()}{/if}</td>
	</tr>
	{/foreach}
	</table>
</td></table>

<h2>Ссылки</h2>
<ul>
<li><a href="blog/">Блог</a></li>
<li><a href="/users/{$this->id()}/own/">Все темы, созданные пользователем</a></li>
<li><a href="use-topics.html">Все темы с участием пользователя</a></li>
<li><a href="posts/">Все сообщения</a> <small>[ <a href="/user/{$this->id()}/posts/first/">первое</a> | <a href="/user/{$this->id()}/posts/last/">последнее</a> ]</small></li>
<li><a href="/users/{$this->id()}/attaches/">Все файлы («аттачи») пользователя (<span class="red">новое!</span>)</a></li>
<li><a href="/users/{$this->id()}/votes/">Оценки сообщений, сводка</a></li>
<li><a href="http://forums.balancer.ru/users/{$this->id()}/votes/">Оценки сообщений с разбивкой по страницам</a></li>
<li><a href="/users/{$this->id()}/aliases/">Пользователи, писавшие с тех же IP</a></li>
<li><a href="reputation/">Репутация</a></li>
<li><a href="warnings/">Предупреждения</a></li>
<li><a href="http://www.balancer.ru/forum/punbb/misc.php?email={$this->id()}">Отправить сообщение на e-mail пользователя</a></li>
<li><a href="http://www.balancer.ru/forum/punbb/profile.php?id={$this->id()}">Профиль на старом форуме</a></li>
{if $is_watcher}
<li><a href="http://forums.balancer.ru/users/{$this->id()}/interlocutors/">Дополнительная административная информация по собеседникам</a></li>
<li><a href="http://forums.balancer.ru/users/{$this->id()}/ips/">IP, с которых писал пользователь</a></li>
{/if}
</ul>

<div class="clear">&nbsp;</div>
</dd>
</dl>

{if $best_of_month}
<dl class="box">
<dt>Лучшие {$best_of_month|count} сообщени{$best_of_month|count|sklon:'е,я,й'} за последний месяц</dt>
<dd>
{foreach $best_of_month as $x}
&nbsp;&nbsp;&nbsp;<img src="http://www.balancer.ru/_bors/i/thumb_{if $x->score() > 0}up{else}down{/if}.gif" />&nbsp;{$x->target()->titled_link()} {$x->target()->score_colorized()}<br/>
{/foreach}
</dd>
</dl>
{/if}

{if $best}
<dl class="box">
<dt>Лучшие {$best|count} сообщений {$best|count|sklon:'е я й'} за всю историю</dt>
<dd>
{foreach $best as $x}
{if $x->target()}
&nbsp;&nbsp;&nbsp;<img src="http://www.balancer.ru/_bors/i/thumb_{if $x->score() > 0}up{else}down{/if}.gif" />&nbsp;{$x->target()->titled_link()} {$x->target()->score_colorized()}<br/>
{/if}
{/foreach}
</dd>
</dl>
{/if}
{*
<table class="nul w100p"><tr><td width="50%">
{if $scores_positive}
<table class="btab w100p">
<caption>Его выше всего ценят</caption>
<tr><th rowspan="2">Пользователь</th><th colspan="3">оценок за месяц</th></tr>
<tr><th>всего</th><th>+</th><th>-</th></tr>
{foreach from=$scores_positive item="s"}
<tr><td class="green">{$s->owner()->titled_link()}</td><td>{$s->total()}</td><td class="green">{$s->pos()}</td><td class="red">{$s->neg()}</td></tr>
{/foreach}
</table>
{/if}
</td><td width="50%">
{if $scores_negative}
<table class="btab w100p">
<caption>Его ниже всего ценят</caption>
<tr><th rowspan="2">Пользователь</th><th colspan="3">оценок за месяц</th></tr>
<tr><th>всего</th><th>+</th><th>-</th></tr>
{foreach from=$scores_negative item="s"}
<tr><td class="green">{$s->owner()->titled_link()}</td><td>{$s->total()}</td><td class="green">{$s->pos()}</td><td class="red">{$s->neg()}</td></tr>
{/foreach}
</table>
{/if}
</td></tr></table>

<table class="nul w100p"><tr><td width="50%">
{if $votes_positive}
<table class="btab w100p">
<caption>Он выше всего ценит</caption>
<tr><th rowspan="2">Пользователь</th><th colspan="3">оценок за месяц</th></tr>
<tr><th>всего</th><th>+</th><th>-</th></tr>
{foreach from=$votes_positive item="s"}
<tr><td class="green">{$s->target_user()->titled_link()}</td><td>{$s->total()}</td><td class="green">{$s->pos()}</td><td class="red">{$s->neg()}</td></tr>
{/foreach}
</table>
{/if}
</td><td width="50%">
{if $votes_negative}
<table class="btab w100p">
<caption>Он ниже всего ценит</caption>
<tr><th rowspan="2">Пользователь</th><th colspan="3">оценок за месяц</th></tr>
<tr><th>всего</th><th>+</th><th>-</th></tr>
{foreach from=$votes_negative item="s"}
<tr><td class="green">{$s->target_user()->titled_link()}</td><td>{$s->total()}</td><td class="green">{$s->pos()}</td><td class="red">{$s->neg()}</td></tr>
{/foreach}
</table>
{/if}
</td></tr></table>
*}

{if $votes_by_categories}
<table class="btab w100p">
<caption>Суммарная оценка сообщений по категориям:</caption>
<tr><th>Категория</th><th>Суммарная оценка</th></tr>
{foreach $votes_by_categories as $p}
<tr><td>{$p->topic()->forum()->category()->titled_link()}</td>
	<td>{bors_votes_thumb::colorize_html($p->scores())} ({bors_votes_thumb::colorize_pm($p->scores_pos(), -$p->scores_neg())})</td>
</tr>
{/foreach}
{/if}


{if $messages_today}
<table class="btab w100p">
<caption>Количество сообщений по форумам за последние сутки:</caption>
<tr><th>Форум</th><th>Число сообщений</th></tr>
{foreach from=$messages_today_by_forums item="x"}
{bors_object_load var="f" class="forum_forum" id=$x.forum_id}
<tr><td>{$f->titled_link()}</td>
	<td>{$x.count}</td>
</tr>
{/foreach}
<tr><td>Итого:</td>
	<td>{$messages_today}</td>
</tr>
</table>
{/if}

{if $messages_month_by_forums}
<table class="btab w100p">
<caption>Количество сообщений по форумам за последний месяц:</caption>
<tr><th>Форум</th><th>Число сообщений</th></tr>
{foreach from=$messages_month_by_forums item="x"}
{bors_object_load var="f" class="forum_forum" id=$x.forum_id}
<tr><td>{$f->titled_link()}</td>
	<td>{$x.count}</td>
</tr>
{/foreach}
</table>
{/if}

{if $messages_month_by_categories}
<table class="btab w100p">
<caption>Количество сообщений по разделам за последний месяц:</caption>
<tr><th>Раздел</th><th>Число сообщений</th></tr>
{foreach $messages_month_by_categories as $x}
	{bors_object_load var="c" class="balancer_board_category" id=$x.cat_id}
<tr><td>{if $c}{$c->titled_link()}{else}-{/if}</td>
	<td>{$x.count}</td>
</tr>
{/foreach}
</table>
{/if}

{if $messages_year_by_forums}
<table class="btab w100p">
<caption>Количество сообщений по форумам за последний год, первая 20-ка:</caption>
<tr><th>Форум</th><th>Число сообщений</th></tr>
{foreach from=$messages_year_by_forums item="x"}
{bors_object_load var="f" class="forum_forum" id=$x.forum_id}
<tr><td>{$f->titled_link()}</td>
	<td>{$x.count}</td>
</tr>
{/foreach}
</table>
{/if}

{/if}
</dd>
</dl>

<dl class="box">
<dt>Ссылки</dt>
<dd>
<ul>
	<li><a href="http://forums.balancer.ru/support/2015/12/t92264,new--obsuzhdenie-stranitsy-profilya-polzovatelya.html">Обсуждение страницы профиля на форуме</a></li>
</ul>
</dd>
</dl>