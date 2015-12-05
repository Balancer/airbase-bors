<small><i><b>АвиаТОП в процессе модернизации</b></i></small>

<ul>
<li>Всего активных сайтов в рейтинге: <b>{$this->total_items()}</b></li>
</ul>

{$this->pages_links_nul()}

<table class="btab w100p">
<tr>
	<th>Сайт</th>
	<th>В рейтинге</th>
	<th>Всего просмотров</th>
	<th>Просмотров в неделю</th>
</tr>
{foreach from=$items item="v" name="loop"}
{bors_object_load class="aviatop_member" id=$v->top_id() var="x"}
{if $x}
{math assign="pos" equation="(page-1)*per_page+cur" page=$this->page() per_page=$this->items_per_page() cur=$smarty.foreach.loop.iteration}
<tr>
	<td><b>{$pos}. <a href="{$x->url()}">{$x->title()}</a></b> <small>[<a href="{$x->www()}" target="_blank">www</a>]</small><br />
	{if $x->logo()}<a href="{$x->url()}"><img src="{$x->logo()}" align="right"></a>{/if}
	<small>{$x->description()}</small>
	</td>
	<td>{$x->age()|smart_interval}</td>
	<td>{$x->visits()}</td>
	<td>{if $v->per_week()}{$v->per_week()}{else}&nbsp;{/if}</td>
</tr>
{/if}
{/foreach}
</table>

{$this->pages_links_nul()}

<ul>
<li><a href="http://www.balancer.ru/support/2005/12/t7226--Obsuzhdenie-AVIA-Top.1539.html">Обсуждение на форуме</a></li>
<li>Статистика обновляется один раз в час</li>
</ul>
