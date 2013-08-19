<noscript>
<div class="alert">Для автоматического выхода из всех сайтов требуется JavaScript!</div>
</noscript>
<table class="nul">
{foreach $ids as $domain => $id}
<tr>
	<td style="padding-right: 5ex;">Выход из домена <b>{$domain}</b></td>
	<td><div id="{$id}">{icon image="wait"}</div></td>
</tr>
{/foreach}
</table>
{js_ready}
	{foreach $hactions as $domain => $haction}
	$.ajax('{$haction->url_ex($domain)}', {
		dataType: 'jsonp',
		timeout: 5000
	})
	.done(function(data) {
		$('#{$ids[$domain]}').css('color', 'green').html('Ok')
	})
	.fail(function(xhr, status) {
		$('#{$ids[$domain]}').css('color', 'red').html('Ошибка: ' + status)
	})

	{/foreach}
{/js_ready}
