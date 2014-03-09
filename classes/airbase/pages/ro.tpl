<p>На сервере проходят технические работы. Форумы временно
находятся в состоянии «только чтение».
{* недоступны. *}
Продолжительность работ точно неизвестна,
скорее всего — около часа (<b>~до 05:30 утра</b>). Пока можно общаться на других, связанных
ресурсах:
<ul>
<li><a href="http://vault.balancer.ru/chat/">Чат Убежища</a> — простой
	чат на запасном сервере для общения, регистрация не требуется</li>
<li><a href="http://vault.balancer.ru/forum/">Форумы Убежища</a> —
	Запасные форумы Авиабазы. Регистрация требуется, с регистрацией на
	основных форумых никак не связана</li>
{*
<li><a href="http://ls.balancer.ru/">LS.Balancer.Ru</a> —
	Перспективная блоговая система. Авторизация используется форумная.
	<b>Внимание!</b> Временно </li>
*}
</ul>
</p>

<div class="row">
	<div class="span6">
		<h3>Последние записи Чата Убежища</h3>
		<table class="table border">
{foreach $chat_messages as $m}
			<tr>
				<td>{$m.user_title}</td>
				<td>{$m.create_time|date:'H:i'}</td>
				<td>{$m.text}</td>
			</tr>
{/foreach}
		</table>
		<a href="http://vault.balancer.ru/chat/" class="btn btn-primary">Войти в чат</a>
	</div>
	<div class="span6">
		<h3>Последние записи Форумов Убежища</h3>
		<table class="table border">
{foreach $forum_messages as $m}
			<tr>
				<td>{$m.user_title}</td>
				<td>{$m.create_time|date:'H:i'}</td>
				<td>{$m.source|lcml_bb|strip_text:128:'...':true}</td>
			</tr>
{/foreach}
		</table>
		<a href="http://vault.balancer.ru/forum/" class="btn">Войти на форумы</a>
	</div>
</div>
