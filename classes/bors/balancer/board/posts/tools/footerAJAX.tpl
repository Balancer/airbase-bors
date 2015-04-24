<div class="footer-info">

{if $me->can_move()}
<h6>Инструменты сообщения</h6>
<ul>
<li><a href="http://www.balancer.ru/admin/forum/post/{$id}/as-new-topic">Вынести сообщение со всеми ответами в <b>новую</b> тему</a></li>
<li><a href="http://www.balancer.ru/admin/forum/post/{$id}/move-tree">Перенести сообщение со всеми ответами в <b>другую</b>, уже имеющуюся тему</a></li>
<li><a href="/admin/forum/posts/move-tree">Перенести все <b>отмеченные</b> сообщения в другую тему</a></li>
	{if not $p->sort_order()}
<li><a href="http://www.balancer.ru/admin/forum/post/{$id}/do-pinned.bas">Закрепить сообщение в начале темы</a></li>
	{else}
<li><a href="http://www.balancer.ru/admin/forum/post/{$id}/do-unpinned.bas">Открепить сообщение от начала темы</a></li>
	{/if}
</ul>
{/if}

{if $me->is_coordinator()}
{* <script type="text/javascript" src="/_bors/js/funcs3.js"></script> *}
{* <script type="text/javascript" src="/_bors/js/coordinators.js"></script> *}

<h6>Инструменты координатора</h6>
<ul>
<li><a href="http://www.balancer.ru/admin/users/{$owner_id}/warnings.html?object={$p->internal_uri()}"><img src="http://www.balancer.ru/img/web/skull.gif" />&nbsp;Выставить штраф пользователю за это сообщение</a></li>
<li><a href="http://www.balancer.ru/forum/tools/post/{$id}/">Старая страница инструментов</a></li>
{if $me->is_admin()}<li><a href="http://www.balancer.ru/forum/punbb/delete.php?id={$id}">Удалить это сообщение</a></li>{/if}

{if $p->is_hidden()}
<li><a href="http://www.balancer.ru/admin/forum/post/{$id}/do-show.bas">Показать сообщение</a></li>
{else}
<li><a href="http://www.balancer.ru/admin/forum/post/{$id}/do-hide.bas">Скрыть сообщение</a></li>
{/if}
<li><a href="http://forums.balancer.ru/admin/do/?pid={$id}&act=logo_assign_by_post">Назначить логотип темы по картинке из сообщения</a></li>
</ul>

{/if}{*/is_coordinator*}

{if $me->is_coordinator() or $me->is_watcher()}
<h6>Информация для координаторов</h6>
<ul>
<li>IP адрес: {$p->poster_ip()}</li>
<li>GeoIP: {$p->poster_ip()|geoip_place}</li>
<li>User-Agent: {$p->poster_ua()}</li>
<li>Точное время сообщения: {$p->create_time()|date:'d.m.Y H:i:s'}</li>
<li{if $overquote_crit} style="color:red!important"{/if}>Уровень цитирования: {$overquote}%</li>
<li><a href="http://forums.balancer.ru/admin/posts/spam">Spam (авто)</a>: {if $spam}<span class="red">Да</span>{else}<span class="green">Нет</span>{/if}</li>
</ul>
{/if}

{if $me->id() == $owner_id || $me->is_admin()}
<h6>Инструменты автора темы</h6>
<ul>
<li>{$p->imaged_texted_edit_link('Редактировать')}</li>
</ul>
{/if}

<h6>Инструменты посетителя</h6>
<ul>
<li><a href="#" onclick="ptrch({$id},'up')"><img src="/_bors/i/thumb_up.gif" /> Одобрить это сообщение</a></li>
<li><a href="#" onclick="ptrch({$id},'down')"><img src="/_bors/i/thumb_down.gif" /> Неодобрить это сообщение</a></li>
<li><a href="http://www.balancer.ru/user/{$owner_id}/reputation/?{$p->internal_uri_ascii()}">Изменить репутацию автора за это сообщение</a></li>
{if $can_take_warning}
<li><a href="http://forums.balancer.ru/users/do/?pid={$id}&act=get_warn"><img src="http://www.balancer.ru/img/web/skull.gif" />&nbsp;Забрать у пользователя его штраф (его {$warning->score()} в Ваш {$take_warning_score|sign|sklonn:'балл,балла,баллов'})</a></li>
{/if}
<li><a href="http://www.balancer.ru/forum/punbb/misc.php?report={$id}">Сообщить о нарушении модераторам</a></li>
<li><a href="http://www.balancer.ru/forum/user-{$owner_id}-posts-in-topic-{$p->topic()->id()}/">Показать все сообщения данного пользователя в данной теме</a></li>
<li><a href="http://www.balancer.ru/admin/forum/post/{$id}/do-drop-cache.bas">Сбросить кеш этого сообщения</a></li>
</ul>
</div>