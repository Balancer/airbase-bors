<?php

template_jquery();
template_jquery_plugin('tageditor/jquery.tag.editor.js');
//template_jquery_plugin('jquery.lazyload-ad-1.4.min.js'); // Это загрузка с задержкой рекламных блоков!
//template_jquery_plugin('jquery.lazyload.js'); — тормозит скроллинг

template_js_include('/_bors/js/funcs3.js?');
template_js_include('/_bors/js/tune.js');
template_js_include('/_bors/js/cfuncs.js');
template_js_include('/_bal/js/common.js?');
template_js_include('/_bors/js/bors-jquery.js');

jquery_lazyLoadAd::on("'.bors_lazy_ad'");
// jquery_cloudZoom::load(); Перенесено в _topic

// template_js_include('/_bors3rdp/js/'.config('js.flowplayer.path').'/'.config('js.flowplayer.include'));

jquery::chrome_alt_fix();

if(config('is_developer'))
{
//	css_plusstrap::load();
}

// twitter_bootstrap::load();

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

// JS-плагины для топиков теперь в balancer_board_topic::pre_show()
