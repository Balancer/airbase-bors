<div class="footer-info">
<ul>
{if $post->original_topic_id() && $post->original_topic_id() != $post->topic_id()}
	<li>Оригинальный топик, откуда было перемещено сообщение: {$post->original_topic()->titled_link()}</li>
{/if}
{if $owner->is_subscribed($topic->id())}
	<li>Пользователь <span style="green">подписан</span> на эту тему</li>
{else}
	<li>Пользователь <span style="red">не подписан</span> на эту тему</li>
{/if}

{if $post->answers_count()}
	<li>
		<h5>Ответы на сообщение (+число вторичных ответов)</h5>
		<h6>Всего ответов: {$post->answers_count()}</h6>
		<ul>
	{foreach from=$post->direct_answers() item="p"}
			<li>{$p->titled_link_in_container($post->topic())}&nbsp;(+{$p->answers_count()})</li>
	{/foreach}
		</ul>
{else}
		<h5>На это сообщение нет ни одного ответа</h5>
{/if}
	</li>
</ul>

{if $me->is_coordinator() or $me->is_watcher()}
	<h6>Информация для координаторов</h6>
	<ul>
		<li>IP адрес: {$post->poster_ip()}</li>
		<li>GeoIP: {$post->poster_ip()|geoip_place}</li>
		<li>User-Agent: {$post->poster_ua()}</li>
		<li>Точное время сообщения: {$post->create_time()|date:'d.m.Y H:i:s'}</li>
	</ul>
{/if}


</div>

