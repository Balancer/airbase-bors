<?php

if(@$_SERVER['HTTP_HOST'] == 'www.airbase.ru')
{
//	echo 'x';
	return;
}

/*
config_set('user_class', NULL);
config_set('user_class_skip', true);
$ro_page = bors_load('airbase_pages_ro', NULL);
$ro_page->pre_show();
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 600');
echo $ro_page->content();
exit();
*/

jquery::plugin('tageditor/jquery.tag.editor.js');
//template_jquery_plugin('jquery.lazyload-ad-1.4.min.js'); // Это загрузка с задержкой рекламных блоков!
//template_jquery_plugin('jquery.lazyload.js'); — тормозит скроллинг

template_js_include('/_bors/js/funcs3.js?');
template_js_include('/_bors/js/tune.js');
template_js_include('/_bors/js/cfuncs.js');
template_js_include('/_bal/js/common.js?');
template_js_include('/_bors/js/bors-jquery.js');

if(class_exists('jquery_lazyLoadAd'))
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

	array_walk($data, function(&$s) {
		list($year, $title, $image, $url) = preg_split('/\s*\|\s*/', $s);
		$s = [
			'year' => trim($year),
			'title' => trim($title),
			'image' => trim($image),
			'url' => trim($url),
			'diff' => date('Y') - $year,
		];

		if(empty($image))
			return $s = NULL;

		if($year == '*')
			return $s;

		if(preg_match('/^(\d+)\*$/', $year, $m))
		{
			$s['year'] = $m[1];
			return $s;
		}

		if(preg_match('/^(\d+)\!$/', $year, $m) && $m[1] == date('Y'))
		{
			$s['year'] = $m[1];
			return $s;
		}

		if(!is_numeric($year))
			return $s = NULL;

		if($year <= date('Y'))
			return $s;

		$diff = date('Y') - $year;
		if($diff<=5)
			return $s;

		if($diff>5 and $diff % 5 == 0)
			return $s;

		$s = NULL;
	});

	$data = array_filter($data);

	if(empty($data))
		return false;

	$x = $data[rand(0, count($data)-1)];

	extract($x);

	$info = getimagesize($image);

	$s_year = intval($year);
	if($diff%10 == 0)
		$s_year = "<span class=\"b red\">{$year}</span>";

	if($s_year != '*' && $year && $diff)
		$desc = "<div class=\"small center\">{$s_year}: {$title} (".sklonn($diff,'год,года,лет').").</div>";
	else
		$desc = "<div class=\"small center\">{$title}</div>";

	$html = "
		<dl class=\"box w200\">
		<dd><a href=\"$url\"><img src=\"$image\" title=\"$title\" {$info[3]} /></a>
		$desc
		</dd>
		</dl>
";

	return $html;
}

// JS-плагины для топиков теперь в balancer_board_topic::pre_show()

function check_blacklisted_email()
{
	if(!($me = bors()->user()))
		return;

	$bl = config('mail.to.blacklist.domains');
	list($user, $domain) = explode('@', $me->email());
	if(in_array($domain, explode(' ', $bl)))
	{
		add_session_message(ec('Ваша почтовая система
			<a href="http://www.balancer.ru/support/2013/06/t88159--chyornye-spiski-pochtovykh-servisov.233.html">не принимает почту с Авиабазы</a>.
			Поменяйте почтового провайдера, если хотите продолжать общаться на форумах. С некорректным почтовым
			сервисом Вы не сможете изменить пароль, запросить его, если забудете, получать персональные
			сообщения и так далее. Подробности — <a href="http://www.balancer.ru/support/2013/06/t88159--chyornye-spiski-pochtovykh-servisov.233.html">на форуме</a>.'));
	}
}

// check_blacklisted_email();

config_set('client_profile', balancer_board_user_client_profile::by_cookies());