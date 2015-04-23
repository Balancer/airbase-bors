<div class="footer-info">
{if $post->original_topic_id() && $post->original_topic_id() != $post->topic_id()}
<ul>
<li>Оригинальный топик, откуда было перемещено сообщение: {$post->original_topic()->titled_link()}</li>
</ul>
{/if}
<h5>Ответы на сообщение (+число вторичных ответов)</h5>
<ul>
{if $post->answers_count()}
<h6>Всего ответов: {$post->answers_count()}</h6>
{foreach from=$post->direct_answers() item="p"}
<li>{$p->titled_link_in_container($post->topic())}&nbsp;(+{$p->answers_count()})</li>
{/foreach}
{else}
<li>Нет ни одного ответа</li>
{/if}
</ul>
</div>
