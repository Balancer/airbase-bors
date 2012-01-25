// Общий JS

$(document).ready(function(){
	$(".ab_rec").click(function() { alert($(this).html()); } );
	$(".post_info_ajax").click(function() {
		url = $(this).parent().children(':first-child').attr('href')
		matches = url.match(/post=(\d+)$/)
		post_id = matches[1]
		div = $("#pfo"+post_id)
		if(div.html())
			div.toggle(100)
		else
		{
			div.html('<img src="/_bors/i/wait-16.gif" width="16" height="16" style="vertical-align: middle;" /> Загружаю...')
			div.load(url)
		}

		return false
	} );
})
