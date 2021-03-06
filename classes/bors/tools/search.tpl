{form class="this" method="get" action="/tools/search/result/" uri="NULL" ref="NULL" class_name='NULL' form_class_name='NULL' no_auto_checkboxes=true go='NULL'}
<table class="w100p">
<tr><td align="right"><small>Запрос:</small></td><td>{input name="q"  size="40"}</td></tr>
<tr><td align="right"><small>Автор:</small></td><td>{input name="u"  size="7"}</td></tr>
<tr><td align="right"><small>Тема&nbsp;№:</small></td><td>{input name="t"  size="7"}</td></tr>
<tr><td align="right"><small>Форум:</small></td>
	<td colSpan="5">{dropdown multiple="multiple" name="f" list="airbase_forum_listfulld" size="10" class="w100p"}</td>
</tr>
<tr><td align="right"><small>Искать:</small></td><td>
	{dropdown name="w" list="array(
		'' => 'В заголовках, описания и ключевых словах',
		'a'=>'Всюду',
		't'=>'В заголовках (названиях тем)',
		'b' => 'В блогах',
		'p' => 'В теле сообщений',
	);" is_int=false}
	{dropdown name="x" list="array(''=>'По умолчанию','e'=>'По фразе','a'=>'Любое слово','b'=>'Бинарный запрос', 'x'=>'Расширенный запрос');"}
	<br/>
	За&nbsp;какие&nbsp;даты:&nbsp;{input name="d1" placeholder="ДД" size="2"}.{input name="m1" placeholder="ММ" size="2"}.{input name="y1" placeholder="ГГГГ" size="4"}&nbsp;—&nbsp;{input name="d2" placeholder="ДД" size="2"}.{input name="m2" placeholder="ММ" size="2"}.{input name="y2" placeholder="ГГГГ" size="4"}
</td></tr>
<tr><td align="right">&nbsp;</td><td>{checkbox name="origins" label="Не искать в ответах, только в стартах веток"}</td></tr>
<tr><td align="right"><small>Сортировать:</small></td><td>{dropdown name="s" list="bors_tools_search_orders" is_int=false}</td></tr>
<tr><td>&nbsp;</td><td>{submit value="Найти"}</td></tr>
</table>
{/form}

{if $error}
<div class="box error">
Внутренняя ошибка: {$error}
</div>
{/if}

{if $warning}
<div class="box warning">
Внутренний сбой: {$warning}
</div>
{/if}

{if $res && $res.matches}

{$this->pages_links_nul()}

{if $topics}
<table class="btab w100p">
<tr>
	<th>Тема</th>
	<th>Автор</th>
	<th>Форум</th>
	<th>Создано</th>
	<th>Обновлено</th>
	<th>Ответов</th>
</tr>
{foreach from=$topics item="t"}
<tr><td><b>
{if $me}
<a href="{$t->url_ex('new')}">
{else}
<a href="{$t->url()}">
{/if}
{$t->title()}</a></b>{if $t->get('description') or $t->get('keywords_string')}<small>
	{if $t->get('description')}<br/>{$t->get('description')}{/if}
	{if $t->get('keywords_string')}<br/><i>теги: {$t->get('keywords_string')}</i>{/if}
</small>{/if}</td>
	<td>{$t->author_name()}</td>
	<td>{$t->forum()|get:titled_link}</td>
	<td>{$t->create_time()|airbase_time}</td>
	<td>{$t->modify_time()|airbase_time}</td>
	<td>{$t->num_replies()}</td>
</tr>
{/foreach}
</table>
{/if}

{assign var="no_show_answers" value=1}
{foreach from=$posts item="p"}
	{assign var="topic" value=$p->topic()}
	{assign var="forum" value=object_property($topic,'forum')}
	{assign var="show_title" value=$p->titled_link_for_igo()}
	{assign var="skip_author_name" value=1}
	{assign var="skip_date" value=1}
	{include file="xfile:forum/post.html"}
{/foreach}

{$this->pages_links_nul()}

{else}
{if $this->id()}Ничего не найдено<br />{/if}
{/if}

<ul>
{* <li><a href="http://balancer.ru/search/topic_titles/">Старый поиск по заголовкам</a></li> *}
<li>Время поиска ограничено для зарегистрированных пользователей 10-ю секундами, для гостей - тремя.</li>
</ul>

<small>
<ul>
{if $res}<li>{$res.total} за {$res.time}с.</li>{/if}
</ul>
</small>

<dl class="box">
<dt>Поиск через Google</dt>
<dd>
<style type="text/css">
@import url(http://www.google.ru/cse/api/branding.css);
</style>
<div class="cse-branding-right" style="background-color:#FFFFFF;color:#000000">
  <div class="cse-branding-form">
    <form action="http://www.google.com/cse" id="cse-search-box">
      <div>
        <input type="hidden" name="cx" value="007791417584937929882:oluuy6b7wqk" />
        <input type="hidden" name="ie" value="UTF-8" />
        <input type="text" name="q" size="31" />
        <input type="submit" name="sa" value="Поиск" />
      </div>
    </form>
  </div>
  <div class="cse-branding-logo">
    <img src="http://www.google.com/images/poweredby_transparent/poweredby_FFFFFF.gif" alt="Google" />
  </div>
<div class="clear">&nbsp;</div>
<div><small>Для поиска в заголовках добавьте префикс allintitle. Пример: <b>allintitle: Су-27</b></small></div>
</div>
</dd>
</dl>


<dl class="box">
<dt>Поиск через Яndex</dt>
<dd>
<form method="get" action="http://yandex.ru/sitesearch"><table 
style="border-collapse:collapse;font-size:12px;"><tr><td rowspan="2" style="width:47px;padding:0 
.5em;background:#fff;vertical-align:middle;border:none;"><a href="http://yandex.ru/"><img 
src="http://site.yandex.ru/i/yandex_search.png" alt="Яндекс" style="border:none;" /></a></td><td 
style="width:16em;padding:.5em 0 0 .5em;background:#99bbdd;vertical-align:middle;border:none;"><input type="text" 
name="text" value="" maxlength="160" style="width:100%;font-size:12px;" /></td><td style="width:3em;padding:.5em .5em 
0em;background:#99bbdd;vertical-align:middle;border:none;"><input type="hidden" name="searchid" value="111101" 
/><input type="submit" value="Найти" style="font-size:12px;" /></td><td 
style="width:1.6em;height:2.2em;background:url(http://site.yandex.ru/arrow.xml?color=%2399bbdd) left top 
no-repeat;"><div style="width:1.6em;height:2.2em;"></div></td></tr><tr><td colspan="2" 
style="background:#99bbdd;color:#335588;vertical-align:middle;padding:.2em .5em .2em;white-space:nowrap;"><label 
for="y_web0"><input type="radio" name="web" value="0" id="y_web0" style="vertical-align:middle;" checked="checked" /> 
на сайте</label>&nbsp; <label for="y_web1"><input type="radio" name="web" value="1" id="y_web1" 
style="vertical-align:middle;" /> в интернете</label></td><td 
style="background:url(http://site.yandex.ru/arrow.xml?color=%2399bbdd) left bottom no-repeat;"><div 
style="width:1.6em;height:2.2em;"></div></td></tr></table></form>
<div><small>Для поиска в заголовках используйте title[...]. Пример: <b>title[Су-27]</b></small></div>
</dd>
</dl>
