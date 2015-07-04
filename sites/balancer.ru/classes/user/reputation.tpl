<b>Общая репутация:</b> {$reputation_abs_value}<br />
<b>Общая репутация на основе равных весов голосующих:</b> {$pure_reputation}<br />
<b>Положительных голосов:</b> {$plus}<br />
<b>Негативных голосов:</b> {$minus}<br />

<p><i><b>Внимание!</b>
Репутацию может выставить только участник, имеющий более 50 сообщений на форуме и имеющий не более трёх активных штрафов.
 <small>Страница обновляется один раз в сутки, либо при изменении репутации. Длина сообщения ограничена 500-ми символами.</small>
</i>
</p>

<table width="100%"><tr>
<td width="90%">

<script type="text/javascript">
{* for(i in document.location)
	document.write(i + '=' + document.location[i] + '<br />')
*}
document.write('<s'+'cript type="text/javascript" src="http://www.balancer.ru/js/users/reputation,{$user_id}.js?ref='+(document.location.search.substring(1) ? document.location.search.substring(1) : document.referrer)+'"></s'+'cript>')
</script>
<td>
{bors_class_load var=owner class="balancer_board_user" id=$user_id}
<div class="avatar_block">{include file="xfile:forum/post-avatar.html"}</div>
</td>
</tr></table>

{* <script>document.forms['rep'].elements['uri'].value = document.location.search.substring(1) ? document.location.search.substring(1) : document.referrer </script> *}

<script><!--
function add_warn(o,uid)
{ldelim}
if(top.me_is_coordinator)
	document.write('<div class="float right">'
		+'<a href="http://www.balancer.ru/_bors/admin/edit-smart/?object='+o+'"><img src="http://www.balancer.ru/_bors/i16/edit.png"></a>'
		+'<a href="http://www.balancer.ru/admin/users/'+uid+'/warnings.html?object='+o+'"><img src="http://www.balancer.ru/img/web/skull.gif"></a>'
		+'<a href="/_bors/admin/mark/delete/?object='+o+'"><img src="/_bors/images/drop-16.png"></a>'
		+'</div>')
{rdelim}
--></script>

{if $list}
{$this->pages_links_nul()}

<table cellSpacing="0" class="btab">
<tr><th>Дата</th><th>Вид</th><th>От кого</th><th>Комментарий</th></tr>
{foreach from=$list item=r}
{bors_object_load var=u class="balancer_board_user" id=$r->voter_id()}

{if $r->score()>0}
{assign var="backcolor" value="pos_reputation"}
{else}
{assign var="backcolor" value="neg_reputation"}
{/if}
<tr>
	<td class="{$backcolor}">{$r->create_time()|full_time}</td>
	<td class="{$backcolor}">{if $r->score() > 0}+{/if}{$r->score()}</td>
	<td class="{$backcolor}">
		<a href="http://www.balancer.ru/user/{$r->voter_id()}/">{object_property($u, 'title')}</a><br />
		<a href="http://www.balancer.ru/user/{$r->voter_id()}/reputation/"><img src="http://www.balancer.ru/user/{$r->voter_id()}/rep.gif" width="100" height="16" border="0" /></a></td> 
	<td class="{$backcolor}">{$r->comment_html()}
		<script>add_warn('{$r->internal_uri()}', {$r->voter_id()})</script>
	</td>
</tr>
{/foreach}
</table>

{$this->pages_links_nul()}
{/if}
