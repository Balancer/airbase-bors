<table class="btab table table-bordered">
<tr>
	<th>Коды</th>
	<th>Смайлик</th>
</tr>
{foreach $smilies as $file => $codes}
<tr>
	<td>
	{foreach $codes as $code}
		{$code}<br/>
	{/foreach}
	</td>
	<td>
		<img src="{$files[$file]}" />
	</td>
</tr>
{/foreach}
</table>
