{if $model->logo()}<a href="{$model->www()}"><img src="{$model->logo()}" /></a>{/if}

<h2>Информация о ресурсе</h2>
<ul>
<li>Адрес сайта: <b><a href="{$model->www()}">{$model->www()}</a></b></li>
<li>Первое зарегистрированное обращение в статистике: <b>{$model->started()|full_hdate}</b></li>
<li>В среднем обращений к сайту в неделю: <b>{if $model->per_week()}{$model->per_week()}{else}отсутствует{/if}</b></li>
<li>Владелец ресурса: {if $model->owner()}<b>{$model->owner()->titled_link()}</b>{else}не зарегистирован. Если Вы владелец ресурса и Вам требуется подтверждение ресурса, обратитесь в <a href="http://balancer.ru/support/2005/12/t7226--Obsuzhdenie-AVIA-Top.1539.html">соответствующую тему форума</a>.{/if}</li>
</ul>

<h2>Описание</h2>
{if $model->source()}
{$model->source()|lcml_bb}
{else}
<p>Описание сайта отсутствует.</p>
{/if}
