if(top.me_is_coordinator)
{
	$('ul.postfooter').each(
		function() {
			pid = $(this).parent().parent().prev().attr('name').replace(/p/, '')
			selected = jQuery.cookie('selected_posts').split(',')
			checked = jQuery.inArray(pid, selected) < 0 ? '' : ' checked="checked"'
			$(this).prepend(
				'<li><label><input type="checkbox"'+checked+'onclick="return apfgp(this, '+pid+')" />&nbsp;отметить</label></li>'
			)
		}
	)
}
