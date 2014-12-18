if(top.me_can_move)
{
	$('ul.postfooter').each(
		function() {
			pid = $(this).parent().parent().prev().attr('name').replace(/p/, '')
			x = jQuery.cookie('selected_posts')
			selected = x ? x.split(',') : []
			checked = jQuery.inArray(pid, selected) < 0 ? '' : ' checked="checked"'
			$(this).prepend(
				'<li><label><input type="checkbox"'+checked+'onclick="return apfgp(this, '+pid+')" />&nbsp;отметить</label></li>'
			)
		}
	)
}

