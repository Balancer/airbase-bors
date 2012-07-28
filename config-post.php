<?php

template_jquery();
template_jquery_plugin('tageditor/jquery.tag.editor.js');
template_jquery_plugin('jquery.lazyload-ad-1.4.min.js'); // Это загрузка с задержкой рекламных блоков!
//template_jquery_plugin('jquery.lazyload.js'); — тормозит скроллинг

template_js_include('/_bors/js/funcs3.js?');
template_js_include('/_bors/js/tune.js');
template_js_include('/_bors/js/cfuncs.js');
template_js_include('/_bal/js/common.js?');
template_js_include('/_bors/js/bors-jquery.js');

config_set('locked_db', @file_get_contents('/tmp/mysqldump.lock'));
if($fm = @filemtime('/tmp/mysqldump.lock'))
{
	config_set('locked_db_time', bors_lib_time::smart_interval_vp(time() - $fm));
	config_set('locked_db_message', @file_get_contents('/tmp/mysqldump-message.lock'));
}

template_jquery_cloud_zoom();
template_jquery_hoverZoom();
// template_js_include('/_bors3rdp/js/'.config('js.flowplayer.path').'/'.config('js.flowplayer.include'));

function balancer_anniversary_html()
{
	$file = '/var/www/bors/bors-airbase/data/anniversary/'.date('md').'.txt';
	if(!file_exists($file))
		return '';

	$data = explode("\n", trim(file_get_contents($file)));
	list($year, $title, $image, $url) = explode(' | ', $data[rand(0, count($data)-1)]);
	if(!$image)
		return '';

	$info = getimagesize($image);

	$html = "
		<dl class=\"box w200\">
		<dd><a href=\"$url\"><img src=\"$image\" title=\"$title\" {$info[3]} /></a></dd>
		</dl>
";

	return $html;
}
