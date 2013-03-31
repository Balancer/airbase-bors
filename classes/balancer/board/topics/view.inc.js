function bb_popover_show(el, content)
{
	top.bb_popover_visible = true

	return el.popover({
		content: content,
		html: true,
		placement: 'top',
		trigger: 'click',
//		title : '<span class="text-info"><strong>'+el.text()+'</strong></span>'
//			+ '<button type="button" id="close" class="close" onclick="$(&quot;a.bb-ajax-popover&quot;).popover(&quot;hide&quot;);">&times;</button>',
	});
}

$(function() {
	$('a.bb-ajax-popover').click(function() {
		var e=$(this)
//		e.preventDefault()
/*
		if(top.bb_popover)
		{
			top.bb_popover.popover('hide')
			top.bb_popover = false
			return false;
		}
*/
/*
		if(d = e.data('loaded-content'))
		{
			p = bb_popover_show(e, d)
			top.bb_popover = p
			p.popover('show')
			return false
		}
*/
		$.get(e.attr('href'), function(d) {
//			alert('loaded '+d)
//			e.data('loaded-content', d)
			p = bb_popover_show(e, d)
//			top.bb_popover = p
			p.popover('show')
//			p.popover('toggle')
		})

		return false;
	})
/*
	$(document).click(function(e) {
		if(top.bb_popover_visible)
		{
			$('a.bb-ajax-popover').popover('destroy')
			top.bb_popover_visible = false
			return false;
		}
	})
*/

	$('a.brand-nav-ajax-dropdown').click(function() {
		var el = $(this)
		$.get(el.attr("rel"), function(content) {
			$('a.brand-nav-ajax-dropdown').parent().append(content)
		})
	})
})
