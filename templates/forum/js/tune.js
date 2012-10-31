var size = clientSize();
with(document)
{
/*
	if(size[0] < 640)
		write('<link rel="stylesheet" type="text/css" href="http://forums.balancer.ru/tpl/default/css/lowres.css" />')
	else
		write('<link rel="stylesheet" type="text/css" href="http://forums.balancer.ru/tpl/default/css/main3.css" />')
*/
	write('<style>')
	if(fontFamily=readCookie('body.fontFamily'))
		write('body {font-family: '+fontFamily+'}')
	if(fontSize=readCookie('body.fontSize'))
		write('body {font-size: '+fontSize+'}')

	if((textWidth=readCookie('incenter.width')) && size[0] >= 640)
		write('.incenter {width: '+textWidth+'}')
	write('</style>');

	if(fontFamily == 'Play')
		write("<link href='http://fonts.googleapis.com/css?family=Play&subset=cyrillic' rel='stylesheet' type='text/css'>")
	if(fontFamily == 'Ubuntu')
		write("<link href='http://fonts.googleapis.com/css?family=Ubuntu&subset=cyrillic' rel='stylesheet' type='text/css'>")
	if(fontFamily == 'Scada')
		write("<link href='http://fonts.googleapis.com/css?family=Scada:400italic,700italic,400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>")
	if(fontFamily == 'PT Sans')
		write("<link href='http://fonts.googleapis.com/css?family=PT+Sans&subset=cyrillic' rel='stylesheet' type='text/css'>")
}

function onLoadPage()
{
	if(top.me_is_coordinator)
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
