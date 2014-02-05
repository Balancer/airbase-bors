$(function() {
	$('.title_actions').each(function(){
		t=$(this)
		t.append('&nbsp;<img src="/_bors-ext/i16/actions/cancel.png" class="btn16 unwatch half-transparent" title="Перестать следить за темой" />')
	})

	$('.unwatch').on('click', function(){
		t=$(this)
		td = t.parent().parent()
		topic_id = td.attr('id').replace('vtt_', '')
		ans = confirm('Вы точно хотите перестать следить за обновлением этой темы?')
		if(ans)
			$.getScript('/_bors/tools/act/pub/balancer_board_ajax_actions_unwatch?topic_id='+topic_id)
	})
})
