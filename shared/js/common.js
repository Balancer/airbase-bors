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

	$(".post_reload").each(function() { post_ajax_reload(this) })
})

function post_ajax_reload(el)
{
	el = $(el);
	while(el && !el.is('.post_body'))
		el = el.parent()

	if(!el)
		return;

	matches = el.attr('id').match(/pb_(\d+)$/)
	post_id = matches[1]


	setInterval(function() {
//		alert(post_id)
		el.load('/_bal/ajax/body?object=balancer_board_post__'+post_id) 
	}, 2000);
}
