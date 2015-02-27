<table class="btab">
{foreach $items as $v}
<tr>
	<td>{$v->score()}</td>
	<td>{$v->target()->snip()}</td>
	<td>{$v->target()->titled_link()}</td>
</tr>
{/foreach}
</table>
