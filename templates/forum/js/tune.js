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
	if(fontFamily=readCookie('html.fontFamily'))
		write('html {font-family: '+fontFamily+'}')
	if(fontSize=readCookie('html.fontSize'))
		write('html {font-size: '+fontSize+'}')

	if((textWidth=readCookie('incenter.width')) && size[0] >= 640)
		write('.incenter {width: '+textWidth+'}')
	write('</style>');

	if(fontFamily == 'Play')
		write("<link href='http://fonts.googleapis.com/css?family=Play&subset=cyrillic' rel='stylesheet' type='text/css'>")
	if(fontFamily == 'Ubuntu')
		write("<link href='http://fonts.googleapis.com/css?family=Ubuntu&subset=cyrillic' rel='stylesheet' type='text/css'>")
	if(fontFamily == 'PT Sans')
		write("<link href='http://fonts.googleapis.com/css?family=PT+Sans&subset=cyrillic' rel='stylesheet' type='text/css'>")
}
