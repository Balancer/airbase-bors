<script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>

<style>
.post-footer-tile a:hover { text-decoration: none; }
.post-footer-tile a { color: inherit !important; }
.post-footer-tile {
	width: 36px;
	height: 46px;
	float: left;
	font-size: 10px;
	text-align: center;
	margin: 0 8px 4px 0;
}
.pft-icon {
	width: 36px;
	height: 36px;
	padding: 2px;
	margin-bottom: 2px;
	font-size: 32px;
	text-align: center;
	vertical-align: middle;
	font-weight: bold;
	background: #fff;
	color: #000;
	box-shadow: 2px 2px 4px rgba(0,0,0,.5);
	border-radius: 4px;
}
.pfi-red {
	background: red;
	color: white;
}
.pfi-yellow {
	background: yellow;
	color: red;
}
</style>

<script>
$(function() {
	$('.post-footer-tools').on('mouseenter', '.post-footer-tile', function () {
			el = $(this).children().first()
			$('.post-footer-info-note').html('<b>'+el.attr('title')+'</b><br/>')
	}).on('mouseleave', '.post-footer-tile', function () {
			$('.post-footer-info-note').html('')
	})
});
</script>

<div class="footer-info">
	<div style="margin: 4px">

{if $me->is_coordinator()}
		<div class="post-footer-tile">
			<a href="http://www.balancer.ru/admin/users/{$owner_id}/warnings.html?object={$p->internal_uri()}" title="Выставить штраф пользователю за это сообщение">
				<div class="pft-icon pfi-red">☠</div>
				Штраф
			</a>
		</div>
{/if}

		<div class="post-footer-tile">
			<a href="http://www.balancer.ru/user/{$owner_id}/reputation/?{$p->internal_uri_ascii()}" title="Изменить репутацию автора за это сообщение">
				<div class="pft-icon"><span style="color:yellow; font-size: 32px">★</span></div>
				Реп.
			</a>
		</div>

		<div class="post-footer-tile">
			<a href="http://www.balancer.ru/forum/punbb/misc.php?report={$id}" title="Сообщить о нарушении модераторам">
				<div class="pft-icon pfi-yellow"><i class="fa fa-warning"></i></div>
				Жалоба
			</a>
		</div>

		<div class="clear">&nbsp;</div>

		<div class="post-footer-info-note" style="font-size: 8pt; margin-top: 4px;">&nbsp;</div>
	</div>

	<hr/>

	<h6>Поделиться ссылкой точно на это сообщение:</h6>
	<div class="yashare-auto-init"
		data-yashareL10n="ru"
		data-yashareType="small"
		data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,moimir,gplus"
		data-yashareTheme="counter"
		data-yashareLink="{$p->url_for_igo()}"
	></div>
	<br/>

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
		<li><a href="http://www.balancer.ru/forum/tools/post/{$id}/">Старая страница инструментов</a></li>
	{if $me->is_admin()}
		<li><a href="http://www.balancer.ru/forum/punbb/delete.php?id={$id}">Удалить это сообщение</a></li>
	{/if}

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
{if $can_take_warning}
		<li><a href="http://forums.balancer.ru/users/do/?pid={$id}&act=get_warn"><img src="http://www.balancer.ru/img/web/skull.gif" />&nbsp;Забрать у пользователя его штраф (его {$warning->score()} в Ваш {$take_warning_score|sign|sklonn:'балл,балла,баллов'})</a></li>
{/if}
		<li><a href="http://www.balancer.ru/forum/user-{$owner_id}-posts-in-topic-{$p->topic()->id()}/">Показать все сообщения данного пользователя в данной теме</a></li>
		<li><a href="http://www.balancer.ru/admin/forum/post/{$id}/do-drop-cache.bas">Сбросить кеш этого сообщения</a></li>
	</ul>
</div>
