var size = clientSize();
with(document)
{
	if(size[0] < 640)
		write('<link rel="stylesheet" type="text/css" href="http://forums.balancer.ru/tpl/default/css/lowres.css" />')
	else
		write('<link rel="stylesheet" type="text/css" href="http://forums.balancer.ru/tpl/default/css/main3.css" />')

	write('<style>')
	if(ff=readCookie('body.fontFamily'))
		write('body {font-family: '+ff+'}')
	if(fs=readCookie('body.fontSize'))
		write('body {font-size: '+fs+'}')
	if((cw=readCookie('incenter.width')) && size[0] >= 640)
		write('.incenter {width: '+cw+'}')
	write('</style>');
}

function onLoadPage()
{
	if(me_is_coordinator)
	{
		posts = readCookie('selected_posts', '').split(/,/)
		for(i in posts)
		{
			pid = posts[i]
			if(is_numeric(pid))
			{
				el = document.getElementById('_chkbx_p'+pid)
				if(el)
					el.checked = true
			}
		}
	}
}
