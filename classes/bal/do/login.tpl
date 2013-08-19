{if $error}
<div class="alert">{$error}</div>
{else}

<noscript>
<div class="alert">Для автоматического входа на все сайты требуется JavaScript!</div>
</noscript>
<table class="nul">
{foreach $ids as $domain => $id}
<tr>
	<td style="padding-right: 5ex;">Вход в домен <b>{$domain}</b></td>
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
		$('#{$ids[$domain]}').css('color', 'red').html('Ошибка: ' + status + ' ' + '{$haction->url_ex($domain)}')
	})

	{/foreach}
{/js_ready}

{/if}
