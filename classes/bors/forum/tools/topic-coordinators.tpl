<ul class="operations">

{* <li><a href="http://www.balancer.ru/forum/punbb/delete.php?id="></a></li> *}
{* <li><a href="http://www.balancer.ru/forum/punbb/moderate.php?fid={$this->topic()|get:forum_id}&tid={$this->id()}&p=$p">".$lang_common['Delete posts'].'</a></li> *}
<li><a href="http://www.balancer.ru/forum/punbb/moderate.php?fid={$this->topic()|get:forum_id}&move_topics={$this->id()}" class="select_page">Перенести тему</a></li>
{* <li><a href="http://www.balancer.ru/forum/punbb/moderate.php?fid={$this->topic()|get:forum_id}&topics={$this->id()}&move_to_forum=191&move_topics_to=Переместить" class="select_page">Перенести тему в спам-отстойник</a> (без запросов, использовать для сноса спама. В остальных случаях <a href="http://www.balancer.ru/2008/05/27/post-1544716.html">читать тут</a>)</li> *}

{if $this->topic()|get:is_closed}
<li><a href="http://www.balancer.ru/forum/punbb/moderate.php?fid={$this->topic()|get:forum_id}&open={$this->id()}" class="select_page">Открыть тему</a> (сейчас тема закрыта)</li>
{else}
<li><a href="http://www.balancer.ru/forum/punbb/moderate.php?fid={$this->topic()|get:forum_id}&close={$this->id()}" class="select_page">Закрыть тему</a> (сейчас тема открыта)</li>
{/if}

{if $this->topic()|get:is_sticky}
<li><a href="http://www.balancer.ru/forum/punbb/moderate.php?fid={$this->topic()|get:forum_id}&unstick={$this->id()}" class="select_page">Открепить тему</a> (сейчас тема закреплена)</li>
{else}
<li><a href="http://www.balancer.ru/forum/punbb/moderate.php?fid={$this->topic()|get:forum_id}&stick={$this->id()}" class="select_page">Закрепить тему</a> (сейчас тема не закреплена)</li>
{/if}

</ul>

{form class='NULL' act="topic_edit"}
<table class="btab w100p">
<caption>Параметры темы</caption>
<tr><th width="150">Заголовок:</th><td>{input name="title" value=$this->topic()|get:title class="w100p"}</td></tr>
<tr><th>Описание:</th><td>{input name="description" value=$this->topic()|get:description class="w100p"}</td></tr>
<tr><th>Ключевые слова:<br/><small>(разделяются запятой)</small></th><td>{input name="keywords_string" value=$this->topic()|get:keywords_string class="w100p"}</td></tr>
<tr><th>Вводная при ответе в тему:<br/><small>Продолжение фразы «В эту тему пишут [что?]...»</small></th><td>{textarea name="answer_notice" value=$this->topic()->get('answer_notice') rows="10" class="w100p"}</td></tr>
<tr><th>Примечание для старожилов/координаторов (обычным пользователям не видно):</th><td>{textarea name="admin_notice" value=$this->topic()->get('admin_notice') rows="10" class="w100p"}</td></tr>
<tr><th colSpan="2">{submit value="Сохранить"}</th></tr>
</table>
{/form}
