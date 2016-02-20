$(function() {
	$('.title_actions').each(function(){
		t=$(this)
		t.append('&nbsp;<img src="/_bors-ext/i16/actions/cancel.png" class="btn16 unwatch half-transparent" title="Перестать следить за темой" />')
	})

	$('.forum_actions').each(function(){
		t=$(this)
		t.append('&nbsp;<img src="/_bors-ext/i16/actions/cancel.png" class="btn16 forum_unwatch half-transparent" title="Перестать следить за форумом" />')
	})

	$('.unwatch').on('click', function(){
		t=$(this)
		td = t.parent().parent()
		topic_id = td.attr('id').replace('vtt_', '')
		ans = confirm('Вы точно хотите перестать следить за обновлением этой темы?')
		if(ans)
			$.getScript('/_bors/tools/act/pub/balancer_board_ajax_actions_unwatch?topic_id='+topic_id)
	})

	$('.forum_unwatch').on('click', function(){
		t=$(this)
		href = t.prev().attr('href')
		forum_id = href.replace(/^.+id=/, '')
		ans = confirm('Вы точно хотите перестать следить за обновлением этого форума?')
		if(ans)
			window.location.href = 'http://forums.balancer.ru/actions/unwatchforum/'+forum_id;
	})
})
