{literal}<style>
ul.operations {
	margin: 0;
	padding: 0;
	text-align: left;
}

ul.operations li {
	margin: 10px 0 10px 0;
}
</style>{/literal}

{if $me && $me->is_coordinator()}
{include file="xfile:topic-coordinators.tpl"}
{/if}

<ul>
<li><a href="http://www.balancer.ru/forum/topics/{$this->id()}/reports/users-graph.svg">Граф взаимных ответов участников темы</a></li>
<li><a href="http://www.balancer.ru/forum/topics/{$this->id()}/reports/users-ograph.svg">Граф взаимных ответов участников темы (ориентированный)</a></li>
<li><a href="http://forums.balancer.ru/topics/{$this->id()}/votes-map.svg">Граф последних оценок сообщений темы</a> <small>[<a href="http://www.balancer.ru/g/p3491506">обсуждение</a>]</small></li>
{if $is_subscribed}
<li><a href="http://www.balancer.ru/forum/punbb/misc.php?unsubscribe={$this->id()}">Отписаться от получения e-mail извещений при ответах в эту тему</a></li>
{else}
<li><a href="http://www.balancer.ru/forum/punbb/misc.php?subscribe={$this->id()}">Подписаться на получение e-mail извещений при ответах в эту тему</a></li>
{/if}
</ul>

<h2>Участники темы</h2>
<ul>
{foreach from=$authors item="x"}
<li>{$x->titled_link()} [<a href="http://www.balancer.ru/forum/user-{$x->id()}-posts-in-topic-{$this->id()}/">сообщения в этой теме</a> ]</li>
{/foreach}
</ul>
