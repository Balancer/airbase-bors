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
